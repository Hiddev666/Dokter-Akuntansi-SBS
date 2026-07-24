<x-app-layout>
    <div class="p-3">
        <div class="w-full flex items-center justify-between">
            <h1 class="text-2xl font-bold">{{ $pageName }}</h1>
            <a href="{{ route('document-types.create') }}">
                <x-primary-button type="button" icon="circle-plus">Buat Jenis Dokumen Baru</x-primary-button>
            </a>
        </div>
        <x-toast type="success" />
        <x-toast type="error" />

        <div class="mt-7">
            <div class="w-full border bg-white p-5 rounded">
                <table class="w-full text-sm">
                    <thead>
                        <td class="text-gray-500 p-2 text-start">ID</td>
                        <td class="text-gray-500 p-2 text-start">Nama</td>
                        <td class="text-gray-500 p-2 text-start">Deskripsi</td>
                        <td class="text-gray-500 p-2 text-end">Action</td>
                    </thead>
                    <tbody>
                        @foreach ($documentTypes as $documentType)
                        <tr class="border-t font-semibold hover:bg-gray-100">
                            <td class="p-2 py-4">{{ $documentType->id }}</td>
                            <td class="p-2 py-4">{{ $documentType->name }}</td>
                            <td class="p-2 py-4">{{ $documentType->description ?? '-' }}</td>
                            <td class="p-2 py-4 flex items-center gap-1 justify-end">
                                <a href="{{ route('document-types.edit', $documentType) }}">
                                    <button type="button" class="p-2 bg-orange-600 rounded">
                                        <span class="shrink-0 text-white"><i data-lucide="pencil" class="w-3 h-3"></i></span>
                                    </button>
                                </a>
                                <form action="{{ route('document-types.destroy', $documentType) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus jenis dokumen ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 bg-red-600 rounded">
                                        <span class="shrink-0 text-white"><i data-lucide="trash" class="w-3 h-3"></i></span>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="mt-4">
                    {{ $documentTypes->links() }}
                </div>
            </div>
        </div>

    </div>
</x-app-layout>
