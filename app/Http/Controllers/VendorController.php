<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreVendorRequest;
use App\Http\Requests\UpdateVendorRequest;
use App\Models\Vendor;
use Exception;
use Illuminate\Support\Facades\Storage;

class VendorController extends Controller
{
    public function index()
    {
        $pageName = 'Vendor';
        $vendors = Vendor::paginate(10);

        return view('vendor.index', compact('pageName', 'vendors'));
    }

    public function create()
    {
        $pageName = 'Buat Vendor';

        return view('vendor.create', compact('pageName'));
    }

    public function store(StoreVendorRequest $request)
    {
        try {
            $vendor = Vendor::create($request->validated());
            Storage::disk('ftp_final')->makeDirectory("INVOICE/{$vendor->name}");

            return redirect()->route('vendors.index')->with('success', 'Vendor baru berhasil disimpan!');
        } catch (Exception $err) {
            return redirect()->route('vendors.index')->with('error', $err->getMessage());
        }
    }

    public function edit(Vendor $vendor)
    {
        $pageName = 'Edit Vendor';

        return view('vendor.edit', compact('pageName', 'vendor'));
    }

    public function update(UpdateVendorRequest $request, Vendor $vendor)
    {
        try {
            Storage::disk('ftp_final')->move("INVOICE/{$vendor->name}", "INVOICE/{$request->name}");
            $vendor->update($request->validated());

            return redirect()->route('vendors.index')->with('success', 'Vendor berhasil diperbarui!');
        } catch (Exception $err) {
            return redirect()->route('vendors.index')->with('error', $err->getMessage());
        }
    }

    public function destroy(Vendor $vendor)
    {
        try {
            $vendor->delete();

            return redirect()->route('vendors.index')->with('success', 'Vendor berhasil dihapus!');
        } catch (Exception $err) {
            return redirect()->route('vendors.index')->with('error', $err->getMessage());
        }
    }
}
