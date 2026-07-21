<?php

namespace App\Console\Commands;

use App\Jobs\ProcessScanFile;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

#[Signature('app:monitor-scanner')]
#[Description('Monitor FTP Scanner and dispatch OCR jobs')]
class MonitorScanner extends Command
{
    protected $signature = 'scanner:monitor';

    protected $description = 'Monitor FTP Scanner';

    protected $allowedExtensions = ['png', 'jpg', 'jpeg', 'webp'];

    public function handle()
    {
        $files = Storage::disk('ftp_scanner')->files();

        foreach ($files as $file) {
            $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));

            if (!in_array($extension, $this->allowedExtensions)) {
                $this->warn("Skipping non-image file: {$file}");
                Storage::disk('ftp_scanner')->delete($file);
                continue;
            }

            $this->info("Downloading: {$file}");

            $content = Storage::disk('ftp_scanner')->get($file);
            Storage::disk('local')->put("scanner/incoming/{$file}", $content);
            Storage::disk('ftp_scanner')->delete($file);

            ProcessScanFile::dispatch($file);
            $this->info("OCR job dispatched for: {$file}");
        }
    }
}
