<?php

use App\Services\InvoiceNumberExtractor;

beforeEach(function () {
    $this->extractor = new InvoiceNumberExtractor;
});

describe('InvoiceNumberExtractor::extract', function () {

    test('extracts invoice number from real OCR output', function () {
        $text = "No Inv\n:050C 102\nNo Vouchar :V 00022659";

        $result = $this->extractor->extract($text);

        expect($result)->toBe('050C 102');
    });

    test('extracts invoice number when No Inv and value on same line', function () {
        $text = 'No Inv: 050C 102';

        $result = $this->extractor->extract($text);

        expect($result)->toBe('050C 102');
    });

    test('extracts invoice number with multiple spaces', function () {
        $text = "No  Inv\n:  050C  102";

        $result = $this->extractor->extract($text);

        expect($result)->toBe('050C 102');
    });

    test('extracts invoice number with no space after colon', function () {
        $text = "No Inv\n:050C102";

        $result = $this->extractor->extract($text);

        expect($result)->toBe('050C102');
    });

    test('extracts numeric invoice number', function () {
        $text = "No Inv\n:12345678";

        $result = $this->extractor->extract($text);

        expect($result)->toBe('12345678');
    });

    test('extracts alphanumeric invoice number with dash', function () {
        $text = "No Inv\n:INV-2026-001";

        $result = $this->extractor->extract($text);

        expect($result)->toBe('INV-2026-001');
    });

    test('returns null when No Inv is not found', function () {
        $text = "No Vouchar :V 00022659\nTotal: 553.059.421";

        $result = $this->extractor->extract($text);

        expect($result)->toBeNull();
    });

    test('returns null for empty text', function () {
        $result = $this->extractor->extract('');

        expect($result)->toBeNull();
    });

    test('cleans OCR noise characters from invoice number', function () {
        $text = "No Inv\n:050C\\/, 102";

        $result = $this->extractor->extract($text);

        expect($result)->toBe('050C 102');
    });

    test('handles case insensitive No Inv', function () {
        $text = "no inv\n:ABC 123";

        $result = $this->extractor->extract($text);

        expect($result)->toBe('ABC 123');
    });

});

describe('InvoiceNumberExtractor::generateS3Filename', function () {

    test('generates S3 filename from invoice number', function () {
        $result = $this->extractor->generateS3Filename('050C 102', 'png');

        expect($result)->toBe('INV_050C 102.jpg');
    });

    test('converts png extension to jpg', function () {
        $result = $this->extractor->generateS3Filename('ABC123', 'png');

        expect($result)->toBe('INV_ABC123.jpg');
    });

    test('converts webp extension to jpg', function () {
        $result = $this->extractor->generateS3Filename('ABC123', 'webp');

        expect($result)->toBe('INV_ABC123.jpg');
    });

    test('keeps jpg extension as-is', function () {
        $result = $this->extractor->generateS3Filename('ABC123', 'jpg');

        expect($result)->toBe('INV_ABC123.jpg');
    });

    test('keeps jpeg extension as-is', function () {
        $result = $this->extractor->generateS3Filename('ABC123', 'jpeg');

        expect($result)->toBe('INV_ABC123.jpeg');
    });

    test('returns empty string when invoice number is null', function () {
        $result = $this->extractor->generateS3Filename(null, 'png');

        expect($result)->toBe('');
    });

});
