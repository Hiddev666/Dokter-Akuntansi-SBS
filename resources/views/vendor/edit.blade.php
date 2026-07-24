<x-app-layout>
    <div class="p-3">
        <div class="w-full flex items-center justify-between">
            <h1 class="text-2xl font-bold">{{ $pageName }}</h1>
        </div>

        <div class="mt-7">
            <div class="w-full border bg-white p-5 rounded">
                <form action="{{ route("vendors.update", $vendor) }}" method="POST" class="text-sm flex flex-col justify-center gap-3">
                    @csrf
                    @method('PUT')
                    <div class="flex justify-between items-center">
                        <div class="flex flex-col w-full">
                            <label for="name" class="text-gray-500">Nama <span class="text-red-800">*</span></label>
                            <input type="text" id="name" name="name" value="{{ old('name', $vendor->name) }}" class="p-2 border border-gray-300 rounded text-sm @error('name') border-red-500 @enderror" autofocus required>
                            <x-input-error :messages="$errors->get('name')" class="mt-1" />
                        </div>
                    </div>
                    <div class="flex justify-between items-center">
                        <div class="flex flex-col w-full">
                            <label for="description" class="text-gray-500">Deskripsi</label>
                            <textarea id="description" name="description" class="p-2 border border-gray-300 rounded text-sm @error('description') border-red-500 @enderror">{{ old('description', $vendor->description) }}</textarea>
                            <x-input-error :messages="$errors->get('description')" class="mt-1" />
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <x-primary-button type="submit" icon="save">
                            Simpan Perubahan
                        </x-primary-button>
                        <a href="{{ url()->previous() }}">
                            <x-secondary-button type="button" icon="x">
                                Batal
                            </x-secondary-button>
                        </a>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>
