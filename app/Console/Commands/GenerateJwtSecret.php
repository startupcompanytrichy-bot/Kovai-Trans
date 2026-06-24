<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

class GenerateJwtSecret extends Command
{
    protected $signature = 'jwt:secret {--force : Overwrite existing JWT_SECRET}';

    protected $description = 'Set JWT_SECRET in the .env file';

    public function handle(): int
    {
        $path = base_path('.env');

        if (! is_file($path)) {
            $this->error('.env file not found.');

            return self::FAILURE;
        }

        $env = file_get_contents($path);

        if (preg_match('/^JWT_SECRET=(.+)$/m', $env, $matches) && trim($matches[1]) !== '' && ! $this->option('force')) {
            $this->warn('JWT_SECRET already exists. Use --force to overwrite.');

            return self::SUCCESS;
        }

        $secret = base64_encode(Str::random(64));

        if (preg_match('/^JWT_SECRET=.*$/m', $env)) {
            $env = preg_replace('/^JWT_SECRET=.*$/m', 'JWT_SECRET='.$secret, $env);
        } else {
            $env .= PHP_EOL.'JWT_SECRET='.$secret.PHP_EOL;
        }

        file_put_contents($path, $env);

        $this->info('JWT secret set successfully.');

        return self::SUCCESS;
    }
}
