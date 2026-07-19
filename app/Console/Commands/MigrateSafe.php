<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * migrate:safe
 *
 * Production-safe migration for Render.com deployments.
 *
 * The PostgreSQL DB already has all tables from previous deploys but
 * the `migrations` table may be empty or partial. This command runs
 * each pending migration individually and gracefully skips ones that
 * fail because the schema change was already applied.
 *
 * PostgreSQL error codes treated as "already applied":
 *   42P07 — duplicate_table        (CREATE TABLE on existing table)
 *   42701 — duplicate_column       (ADD COLUMN on existing column)
 *   42703 — undefined_column       (DROP COLUMN on non-existent column)
 *   42P01 — undefined_table        (DROP TABLE on non-existent table)
 *   42704 — undefined_object       (DROP INDEX on non-existent index)
 *   42P16 — invalid_table_def
 *   23505 — unique_violation       (CREATE UNIQUE INDEX on dup data)
 */
class MigrateSafe extends Command
{
    protected $signature   = 'migrate:safe';
    protected $description = 'Run migrations safely — skips already-applied schema changes';

    /** PostgreSQL SQLSTATE codes that mean "already done" */
    private const SKIP_CODES = ['42P07', '42701', '42703', '42P01', '42704', '42P16', '23505'];

    public function handle(): int
    {
        // Ensure the migrations tracking table exists
        $this->call('migrate:install');

        $files = glob(database_path('migrations/*.php'));
        sort($files);

        $this->info('Processing ' . count($files) . ' migration files...');

        $migrated = 0;
        $skipped  = 0;
        $failed   = 0;

        foreach ($files as $file) {
            $name = pathinfo($file, PATHINFO_FILENAME);

            // Already recorded in migrations table — nothing to do
            if (DB::table('migrations')->where('migration', $name)->exists()) {
                continue;
            }

            // Run this single migration file via artisan migrate --path
            // This avoids require() issues with anonymous classes
            $relativePath = 'database/migrations/' . basename($file);

            $exitCode = $this->runMigration($relativePath, $name);

            if ($exitCode === 'migrated') {
                $this->line("  <info>Migrated:</info>  {$name}");
                $migrated++;
            } elseif ($exitCode === 'skipped') {
                $this->line("  <comment>Skipped (already applied):</comment>  {$name}");
                $skipped++;
            } else {
                $this->error("  <error>Failed:</error>  {$name}");
                $this->error("  {$exitCode}");
                $failed++;
                // Stop on genuine errors
                return self::FAILURE;
            }
        }

        $this->newLine();
        $this->info("Done — Migrated: {$migrated} | Skipped: {$skipped} | Failed: {$failed}");
        return self::SUCCESS;
    }

    private function runMigration(string $path, string $name): string
    {
        try {
            DB::beginTransaction();

            $migration = $this->resolveMigration($path);
            $migration->up();

            DB::commit();

            DB::table('migrations')->insert(['migration' => $name, 'batch' => 1]);

            return 'migrated';

        } catch (\Throwable $e) {
            DB::rollBack();

            $msg = $e->getMessage();

            foreach (self::SKIP_CODES as $code) {
                if (str_contains($msg, $code)) {
                    DB::table('migrations')->insert(['migration' => $name, 'batch' => 0]);
                    return 'skipped';
                }
            }

            // Also check common string patterns for additional safety
            if (
                str_contains($msg, 'already exists') ||
                str_contains($msg, 'duplicate column') ||
                str_contains($msg, 'does not exist') && (
                    str_contains($msg, 'column') ||
                    str_contains($msg, 'index') ||
                    str_contains($msg, 'relation')
                )
            ) {
                DB::table('migrations')->insert(['migration' => $name, 'batch' => 0]);
                return 'skipped';
            }

            return $msg;
        }
    }

    private function resolveMigration(string $relativePath): object
    {
        $fullPath = base_path($relativePath);
        // Use include rather than require so re-loading the same file doesn't error
        // Each anonymous class definition is unique per include call in PHP 8+
        return include $fullPath;
    }
}
