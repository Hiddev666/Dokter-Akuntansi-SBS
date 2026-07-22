# Planning: Ekstraksi Nomor Invoice dari Hasil OCR → Upload ke Supabase Storage

## 1. Analisis OCR Result

**Contoh hasil OCR (`Untitlessssd (1).png.json`):**

```json
{
    "filename": "Untitlessssd (1).png",
    "text": "No Inv\n:050C 102\nNo Vouchar :V 00022659\n...",
    "processing_time_ms": "3928",
    "processed_at": "2026-07-22T01:03:12+00:00"
}
```

**Target:**
- Field `No Inv` pada teks OCR → value: `050C 102`
- Filename output ke Supabase: `INV_050C 102.jpg`

## 2. Pola Regex untuk Ekstraksi No Inv

Berdasarkan pola OCR, `No Inv` dan value-nya terpisah newline:

```
No Inv
:050C 102
```

Regex yang diperlukan:

```
/No\s+Inv\s*\n?\s*:\s*(.+)/i
```

| Komponen | Penjelasan |
|----------|-----------|
| `No\s+Inv` | Match literal "No Inv" dengan spasi fleksibel |
| `\s*\n?\s*` | Handle newline atau spasi antara label dan value |
| `:\s*` | Match titik dua sebagai pemisah |
| `(.+)` | Captura semua karakter setelahnya sebagai nomor invoice |

## 3. Modifikasi yang Diperlukan

### 3.1 Buat Service Baru: `InvoiceNumberExtractor`

**File:** `app/Services/InvoiceNumberExtractor.php`

**Responsibilitas:**
- Menerima raw OCR text
- Extract nomor invoice menggunakan regex
- Return invoice number atau `null` jika tidak ditemukan
- Bersihkan karakter OCR noise dari hasil ekstraksi

```php
// Pseudo logic
class InvoiceNumberExtractor
{
    public function extract(string $text): ?string
    {
        // 1. Apply regex /No\s+Inv\s*\n?\s*:\s*(.+)/i
        // 2. Trim hasil capture
        // 3. Bersihkan karakter noise OCR (contoh: "00023\/," → "00023")
        // 4. Return cleaned invoice number atau null
    }
}
```

### 3.2 Modifikasi Job: `ProcessScanFile`

**File:** `app/Jobs/ProcessScanFile.php`

**Perubahan alur:**

```
SEBELUM:
1. OCR extract text
2. Simpan JSON hasil OCR ke local disk
3. Upload original file ke S3 dengan nama ASLI (scanner/originals/{filename})
4. Hapus file incoming

SESUDAH:
1. OCR extract text
2. Simpan JSON hasil OCR ke local disk
3. Ekstrak nomor invoice dari text OCR
4. Jika nomor invoice ditemukan:
   - Format filename: "INV_{no_inv}.jpg" (contoh: "INV_050C 102.jpg")
   - Upload ke S3 dengan path: "scanner/originals/{formatted_filename}"
5. Jika nomor invoice TIDAK ditemukan:
   - Fallback: gunakan filename asli (backward compatible)
   - Log warning bahwa invoice number tidak terdeteksi
6. Hapus file incoming
```

### 3.3 Update JSON OCR Result

Tambahkan field `invoice_number` ke dalam JSON hasil OCR untuk keperluan audit/tracking:

```json
{
    "filename": "Untitlessssd (1).png",
    "invoice_number": "050C 102",
    "s3_filename": "INV_050C 102.jpg",
    "text": "No Inv\n:050C 102\n...",
    "processing_time_ms": "3928",
    "processed_at": "2026-07-22T01:03:12+00:00"
}
```

## 4. Handling Edge Cases

| Case | Strategi |
|------|----------|
| `No Inv` tidak ditemukan di text | Fallback ke filename asli, log warning |
| OCR noise pada value (contoh: `\/,`) | Regex pembersihan karakter spesial |
| Spasi/whitespace berlebih di value | `trim()` pada hasil ekstraksi |
| `No Inv` muncul di baris berbeda dengan `:` | Regex handle newline dengan `\n?\s*` |
| Filename mengandung spasi | Biarkan spasi (Supabase S3 mendukung URL-encoded spaces) |
| Invoice number sudah ada di S3 | Overwrite (idempotent upload) |

## 5. Urutan Implementasi

### Tahap 1: Buat Service InvoiceNumberExtractor
- [ ] Buat file `app/Services/InvoiceNumberExtractor.php`
- [ ] Implement regex extraction + cleanup
- [ ] Buat unit test untuk berbagai variasi OCR text

### Tahap 2: Modifikasi ProcessScanFile Job
- [ ] Inject `InvoiceNumberExtractor` ke job
- [ ] Tambahkan logic ekstraksi setelah OCR selesai
- [ ] Generate formatted filename (`INV_{no_inv}.jpg`)
- [ ] Ubah path upload S3 dari `scanner/originals/{filename}` → `scanner/originals/{s3_filename}`
- [ ] Tambahkan field `invoice_number` dan `s3_filename` ke JSON OCR result
- [ ] Tambahkan fallback jika invoice number tidak terdeteksi

### Tahap 3: Testing
- [ ] Unit test `InvoiceNumberExtractor` dengan berbagai input OCR
- [ ] Test integrasi `ProcessScanFile` job dengan mock OCR result
- [ ] Verify upload ke Supabase S3 dengan nama file baru

### Tahap 4: Logging & Monitoring
- [ ] Log info: nomor invoice yang terdeteksi + filename baru
- [ ] Log warning: invoice number tidak terdeteksi (fallback ke filename asli)
- [ ] Log error: gagal upload ke S3

## 6. File yang Berubah

| File | Action | Keterangan |
|------|--------|-----------|
| `app/Services/InvoiceNumberExtractor.php` | **BARU** | Service ekstraksi no inv dari text OCR |
| `app/Jobs/ProcessScanFile.php` | **EDIT** | Tambah logic ekstraksi + rename filename |
| `tests/Unit/InvoiceNumberExtractorTest.php` | **BARU** | Unit test untuk extractor |

## 7. Dependency

- **Tidak ada dependency baru** — semua menggunakan Laravel built-in (`Storage`, `Regex`)
- **Supabase S3 sudah terkonfigurasi** di `config/filesystems.php` (disk `s3`)
- **OCR.space API sudah terintegrasi** di `OcrService.php`
