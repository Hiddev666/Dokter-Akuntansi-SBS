<?php

namespace App\Http\Controllers;

use App\Services\OcrService;
use Illuminate\Http\Request;

class OcrController extends Controller
{
    public function extract(Request $request, OcrService $ocr)
    {
        $request->validate([
            'file' => 'required|image|mimes:jpg,jpeg,png,webp|max:10240',
        ]);

        try {
            $result = $ocr->extractText($request->file('file'));
            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
