<x-app-layout>
    <div class="p-3">
        <div class="w-full flex items-center justify-between">
            <h1 class="text-2xl font-bold">{{ $pageName }}</h1>
            <x-primary-button>+ Tambah Vendor Baru</x-primary-button>
        </div>

        <div class="mt-7">
            <div class="w-full border bg-white p-5 rounded">
                <table class="w-full text-sm">
                    <thead>
                        <td class="text-gray-500 p-2 text-start">ID</td>
                        <td class="text-gray-500 p-2 text-start">Nama</td>
                        <td class="text-gray-500 p-2 text-start">Deskripsi</td>
                        <td class="text-gray-500 p-2">Action</td>
                    </thead>
                    <tbody>
                        <tr class="border-t">
                            <td class="p-2 py-4">1</td>
                            <td class="p-2 py-4">PT ABC DEF GH</td>
                            <td class="p-2 py-4">Lorem ipsum dolor, sit amet consectetur adipisicing elit. Quia, nisi.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</x-app-layout>
