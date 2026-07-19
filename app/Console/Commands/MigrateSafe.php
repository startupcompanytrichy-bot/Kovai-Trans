<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * migrate:safe
 *
 * Production-safe migration command for Render.com deployments.
 *
 * Problem: The PostgreSQL DB may already have tables (created by a previous
 * deploy or manual setup) but the `migrations` tracking table doesn't record
 * them. Running `migrate --force` then throws "relation already exists".
 *
 * Solution:
 *   1. Ensure the `migrations` table exists.
 *   2. For every migration file, if the migration is NOT recorded AND the
 *      primary table it would create already exists → mark it as run (batch 0).
 *   3. Then run `migrate --force` normally — it will skip already-recorded
 *      migrations and only run genuinely new ones.
 */
class MigrateSafe extends Command
{
    protected $signature   = 'migrate:safe';
    protected $description = 'Run migrations safely — pre-records existing tables to avoid duplicate-table errors';

    public function handle(): int
    {
        // 1. Ensure migrations table exists
        $this->call('migrate:install', ['--force' => true]);

        // 2. Scan migration files and mark already-existing ones as run
        $migrationPath = database_path('migrations');
        $files         = glob($migrationPath . '/*.php');
        sort($files);

        $marked = 0;
        foreach ($files as $file) {
            $migrationName = pathinfo($file, PATHINFO_FILENAME);

            // Already recorded — skip
            if (DB::table('migrations')->where('migration', $migrationName)->exists()) {
                continue;
            }

            // Guess the table name from the migration filename
            // Pattern: YYYY_MM_DD_HHMMSS_create_TABLENAME_table  or  ..._TABLENAME_table
            $tableName = $this->guessTableName($migrationName);

            if ($tableName && Schema::hasTable($tableName)) {
                DB::table('migrations')->insert([
                    'migration' => $migrationName,
                    'batch'     => 0, // batch 0 = pre-existing, won't be rolled back
                ]);
                $this->line("  <comment>Marked as run (table exists):</comment> {$migrationName}");
                $marked++;
            }
        }

        if ($marked > 0) {
            $this->info("Pre-recorded {$marked} existing migration(s).");
        }

        // 3. Run actual migrations — only genuinely new ones will execute
        $this->info('Running migrate --force...');
        $this->call('migrate', ['--force' => true]);

        return self::SUCCESS;
    }

    protected function guessTableName(string $migrationName): ?string
    {
        // Remove timestamp prefix: 0001_01_01_000000_create_users_table → create_users_table
        $name = preg_replace('/^\d{4}_\d{2}_\d{2}_\d{6}_/', '', $migrationName);

        // create_XXXX_table  → XXXX
        if (preg_match('/^create_(.+?)_table$/', $name, $m)) {
            return $m[1];
        }

        // add_X_to_XXXX_table → XXXX
        if (preg_match('/^add_.+?_to_(.+?)_table$/', $name, $m)) {
            return $m[1];
        }

        // make_X_nullable_in_XXXX_table → XXXX
        if (preg_match('/(?:_to_|_in_)(.+?)_table$/', $name, $m)) {
            return $m[1];
        }

        return null;
    }
}
