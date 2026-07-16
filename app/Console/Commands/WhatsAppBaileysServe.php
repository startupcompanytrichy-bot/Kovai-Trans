<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class WhatsAppBaileysServe extends Command
{
    protected $signature = 'whatsapp:baileys-serve
        {--port=3001 : Port for the Baileys Node.js server}';

    protected $description = 'Start the Baileys Node.js WhatsApp service';

    public function handle()
    {
        $port = $this->option('port');
        $dir = base_path('node-services/whatsapp-baileys');

        if (!is_dir($dir)) {
            $this->error("Baileys service directory not found: {$dir}");
            $this->line('Run: mkdir -p node-services/whatsapp-baileys && cd node-services/whatsapp-baileys && npm install');
            return Command::FAILURE;
        }

        $this->info('Starting Baileys WhatsApp service...');
        $this->line("Server will run at http://localhost:{$port}");
        $this->line('Scan the QR code when prompted to link WhatsApp.');
        $this->newLine();

        $cmd = sprintf('cd %s && set PORT=%d && node server.js', escapeshellarg($dir), $port);

        passthru($cmd, $exitCode);

        return $exitCode === 0 ? Command::SUCCESS : Command::FAILURE;
    }
}
