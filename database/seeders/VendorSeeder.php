<?php

namespace Database\Seeders;

use App\Models\Vendor;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class VendorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $vendors = [
            [
                'name' => 'MADHANI TALATAH NUSANTARA',
                'slug' => 'madhani-talatah-nusantara',
                'description' => 'PT Madhani Talatah Nusantara',
            ],
            [
                'name' => 'UNITED TRACTOR',
                'slug' => 'united-tractor',
                'description' => 'PT United Tractor',
            ],
            [
                'name' => 'PUTRA PERKASA ABADI',
                'slug' => 'putra-perkasa-abadi',
                'description' => 'PT Putra Perkasa Abadi',
            ],
            [
                'name' => 'PUSAKA BUMI TRANSPORTAS',
                'slug' => 'pusaka-bumi-transportasi',
                'description' => 'PT Pusaka Bumi Transportasi',
            ],
            [
                'name' => 'AITI MITRA UTAMA',
                'slug' => 'aiti-mitra-utama',
                'description' => 'PT Aiti Mitra Utama',
            ],
        ];

        foreach ($vendors as $vendor) {
            Vendor::create($vendor);
            $vendorName = $vendor["name"];
            Storage::disk('ftp_final')->makeDirectory("INVOICE/{$vendorName}");
        }
    }
}
