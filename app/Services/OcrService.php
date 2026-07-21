<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Exception;

class OcrService
{
    protected string $apiKey;

    protected string $endpoint = 'https://api.ocr.space/parse/image';

    public function __construct()
    {
        $this->apiKey = config('services.ocr.api_key');
    }

    public function extractText(UploadedFile $file, string $lang = 'eng'): array
    {
        $response = Http::timeout(60)
            ->attach('file', file_get_contents($file->getRealPath()), $file->getClientOriginalName())
            ->post($this->endpoint, [
                'apikey' => $this->apiKey,
            ]);

        if ($response->failed()) {
            throw new Exception('OCR service error: ' . $response->body());
        }

        $data = $response->json();

        if ($data['IsErroredOnProcessing'] ?? false) {
            $errorMsg = is_array($data['ErrorMessage'] ?? null)
                ? implode(', ', $data['ErrorMessage'])
                : ($data['ErrorMessage'] ?? 'Unknown error');
            throw new Exception('OCR processing error: ' . $errorMsg);
        }

        $parsed = $data['ParsedResults'][0] ?? null;

        if (!$parsed || ($parsed['FileParseExitCode'] ?? 0) !== 1) {
            $parseError = is_array($parsed['ErrorMessage'] ?? null)
                ? implode(', ', $parsed['ErrorMessage'])
                : ($parsed['ErrorMessage'] ?? 'No results');
            throw new Exception('OCR parse failed: ' . $parseError);
        }

        return [
            'success' => true,
            'text' => $parsed['ParsedText'] ?? '',
            'exit_code' => $data['OCRExitCode'] ?? null,
            'processing_time_ms' => $data['ProcessingTimeInMilliseconds'] ?? null,
        ];
    }
}
