<?php

namespace App\Jobs;

use App\Services\OcrService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Exception;

class ProcessScanFile implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $timeout = 120;

    public int $backoff = 30;

    public function __construct(
        public string $filename,
    ) {}

    public function handle(OcrService $ocr): void
    {
        $incomingPath = "scanner/incoming/{$this->filename}";
        $fullPath = storage_path("app/private/{$incomingPath}");

        if (!file_exists($fullPath)) {
            Log::warning("File not found for OCR processing", ['file' => $this->filename]);
            return;
        }

        $uploadedFile = new UploadedFile($fullPath, $this->filename, mime_content_type($fullPath), null, true);

        $result = $ocr->extractText($uploadedFile);

        if (empty($result['success'])) {
            throw new Exception('OCR failed: ' . json_encode($result));
        }

        $ocrData = [
            'filename' => $this->filename,
            'text' => $result['text'] ?? '',
            'processing_time_ms' => $result['processing_time_ms'] ?? null,
            'processed_at' => now()->toIso8601String(),
        ];

        Storage::disk('local')->put(
            "scanner/ocr-results/{$this->filename}.json",
            json_encode($ocrData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
        );

        $content = file_get_contents($fullPath);
        Storage::disk('s3')->put("scanner/originals/{$this->filename}", $content);

        Storage::disk('local')->delete($incomingPath);

        Log::info("OCR processed successfully", [
            'filename' => $this->filename,
            'processing_time_ms' => $ocrData['processing_time_ms'],
        ]);
    }

    public function failed(?Throwable $exception): void
    {
        Log::error("OCR job failed permanently", [
            'filename' => $this->filename,
            'error' => $exception?->getMessage(),
        ]);
    }
}
