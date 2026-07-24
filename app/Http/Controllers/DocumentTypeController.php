<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDocumentTypeRequest;
use App\Http\Requests\UpdateDocumentTypeRequest;
use App\Models\DocumentType;
use Illuminate\Support\Facades\Storage;
use Exception;

class DocumentTypeController extends Controller
{
    public function index()
    {
        $pageName = 'Jenis Dokumen';
        $documentTypes = DocumentType::paginate(10);

        return view('document-type.index', compact('pageName', 'documentTypes'));
    }

    public function create()
    {
        $pageName = 'Buat Jenis Dokumen';

        return view('document-type.create', compact('pageName'));
    }

    public function store(StoreDocumentTypeRequest $request)
    {
        try {
            $documentType = DocumentType::create($request->validated());
            Storage::disk('ftp_final')->makeDirectory("{$documentType->name}");

            return redirect()->route('document-types.index')->with('success', 'Jenis dokumen baru berhasil disimpan!');
        } catch (Exception $err) {
            return redirect()->route('document-types.index')->with('error', $err->getMessage());
        }
    }

    public function edit(DocumentType $documentType)
    {
        $pageName = 'Edit Jenis Dokumen';

        return view('document-type.edit', compact('pageName', 'documentType'));
    }

    public function update(UpdateDocumentTypeRequest $request, DocumentType $documentType)
    {
        try {
            Storage::disk('ftp_final')->move("{$documentType->name}", "INVOICE/{$request->name}");
            $documentType->update($request->validated());

            return redirect()->route('document-types.index')->with('success', 'Jenis dokumen berhasil diperbarui!');
        } catch (Exception $err) {
            return redirect()->route('document-types.index')->with('error', $err->getMessage());
        }
    }

    public function destroy(DocumentType $documentType)
    {
        try {
            Storage::disk('ftp_final')->deleteDirectory("{$documentType->name}");
            $documentType->delete();

            return redirect()->route('document-types.index')->with('success', 'Jenis dokumen berhasil dihapus!');
        } catch (Exception $err) {
            return redirect()->route('document-types.index')->with('error', $err->getMessage());
            }
            }
}
