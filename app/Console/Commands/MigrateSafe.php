<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * migrate:safe
 *
 * Production-safe migration for Render.com deployments.
 *
 * Problem: The PostgreSQL DB already has all tables from a previous deploy,
 * but the `migrations` tracking table is empty (new container, existing DB).
 * Running `migrate --force` throws "relation already exists" on every table.
 *
 * Solution: Mark ALL migration files as already-run (batch 0) upfront,
 * then run `migrate --force` — it will only execute genuinely new migrations
 * (ones added after the DB was last set up).
 *
 * This is safe because:
 * - If a table truly doesn't exist yet, it won't be in the migrations table
 *   after our pre-population (we only mark files, not check tables).
 *   Wait — we mark ALL files, so new ones would be wrongly skipped.
 *
 * Revised approach:
 * - Mark only migrations whose tables/columns ALREADY EXIST in the DB.
 * - For each migration not yet recorded: try running it; if it fails with
 *   "already exists", mark it as run and continue.
 */
class MigrateSafe extends Command
{
    protected $signature   = 'migrate:safe';
    protected $description = 'Run migrations safely — skips already-existing tables gracefully';

    public function handle(): int
    {
        // Ensure migrations table exists
        $this->call('migrate:install');

        $migrationPath = database_path('migrations');
        $files         = glob($migrationPath . '/*.php');
        sort($files);

        $this->info('Checking ' . count($files) . ' migration files...');

        foreach ($files as $file) {
            $migrationName = pathinfo($file, PATHINFO_FILENAME);

            // Already recorded — skip
            if (DB::table('migrations')->where('migration', $migrationName)->exists()) {
                continue;
            }

            // Try to run this single migration
            try {
                DB::beginTransaction();
                // Load and run the migration
                $migration = require $file;
                $migration->up();
                DB::commit();

                // Record it
                DB::table('migrations')->insert([
                    'migration' => $migrationName,
                    'batch'     => 1,
                ]);
                $this->line("  <info>Migrated:</info>  {$migrationName}");

            } catch (\Throwable $e) {
                DB::rollBack();

                $msg = $e->getMessage();

                // PostgreSQL "already exists" errors — table/column/index already there
                if (
                    str_contains($msg, 'already exists') ||
                    str_contains($msg, 'duplicate column') ||
                    str_contains($msg, '42P07') ||  // duplicate_table
                    str_contains($msg, '42701') ||  // duplicate_column
                    str_contains($msg, '42P01')     // undefined_table on drop (already dropped)
                ) {
                    // Mark as run so it won't be attempted again
                    DB::table('migrations')->insert([
                        'migration' => $migrationName,
                        'batch'     => 0,
                    ]);
                    $this->line("  <comment>Skipped (already exists):</comment> {$migrationName}");
                } else {
                    // Real error — stop and report
                    $this->error("  Migration failed: {$migrationName}");
                    $this->error("  Error: {$msg}");
                    return self::FAILURE;
                }
            }
        }

        $this->info('All migrations processed successfully.');
        return self::SUCCESS;
    }
}
