<?php

namespace App\Jobs;

use App\Models\Vendor;
use App\Services\InvoiceNumberExtractor;
use App\Services\OcrSearchService;
use App\Services\OcrService;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\UploadedFile;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ProcessScanFile implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $timeout = 120;

    public int $backoff = 30;

    public function __construct(
        public string $filename,
    ) {}

    public function handle(OcrService $ocr, InvoiceNumberExtractor $extractor, OcrSearchService $search): void
    {
        $incomingPath = "scanner/incoming/{$this->filename}";
        $fullPath = storage_path("app/private/{$incomingPath}");
        $vendorNames = Vendor::pluck('name')->toArray();

        if (! file_exists($fullPath)) {
            Log::warning('File not found for OCR processing', ['file' => $this->filename]);

            return;
        }

        $uploadedFile = new UploadedFile($fullPath, $this->filename, mime_content_type($fullPath), null, true);

        $result = $ocr->extractText($uploadedFile);

        if (empty($result['success'])) {
            throw new Exception('OCR failed: '.json_encode($result));
        }

        $ocrText = $result['text'] ?? '';
        $invoiceNumber = $extractor->extract($ocrText);

        $vendorMatch = $search->searchData(
            [['text' => $ocrText]],
            $vendorNames
        )->first();
        $vendorName = strtoupper($vendorMatch['matches']->first()['keyword']) ?? 'UNDEFINED';

        $originalExtension = pathinfo($this->filename, PATHINFO_EXTENSION);
        $s3Filename = $extractor->generateS3Filename($vendorName, $invoiceNumber, $originalExtension);

        $ocrData = [
            'filename' => $this->filename,
            'invoice_number' => $invoiceNumber,
            'vendor_name' => $vendorName,
            's3_filename' => $s3Filename ?: $this->filename,
            'text' => $ocrText,
            'processing_time_ms' => $result['processing_time_ms'] ?? null,
            'processed_at' => now()->toIso8601String(),
        ];

        Storage::disk('local')->put(
            "scanner/ocr-results/{$this->filename}.json",
            json_encode($ocrData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
        );

        $uploadFilename = ''.$s3Filename ?: $this->filename;

        $content = file_get_contents($fullPath);
        Storage::disk('s3')->put("scanner/originals/{$uploadFilename}", $content);
        Storage::disk('ftp_final')->put("INVOICE/{$vendorName}/{$uploadFilename}", $content);

        Storage::disk('local')->delete($incomingPath);

        Log::info('OCR processed successfully', [
            'filename' => $this->filename,
            'invoice_number' => $invoiceNumber,
            'vendor_name' => $vendorName,
            's3_filename' => $uploadFilename,
            'processing_time_ms' => $ocrData['processing_time_ms'],
        ]);
    }

    public function failed(?Throwable $exception): void
    {
        Log::error('OCR job failed permanently', [
            'filename' => $this->filename,
            'error' => $exception?->getMessage(),
        ]);
    }
}
