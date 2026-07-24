<x-app-layout>
    <div class="p-3">
        <div class="w-full flex items-center justify-between">
            <h1 class="text-2xl font-bold">{{ $pageName }}</h1>
        </div>
        <x-toast type="success" />
        <x-toast type="error" />

        <div class="mt-7">
            <div class="w-full border bg-white p-5 rounded">
                {{-- Breadcrumb --}}
                @if (!empty($breadcrumbs))
                <div class="mb-7 flex items-center gap-1 text-sm text-gray-500">
                    <a href="{{ route('file-managements.index') }}" class="hover:text-indigo-600 transition flex items-center gap-1">
                        <i data-lucide="home" class="w-4 h-4"></i>
                        <span class="font-medium text-gray-700">HOME</span>
                    </a>
                    @foreach ($breadcrumbs as $crumb)
                    <span class="shrink-0"><i data-lucide="chevron-right" class="w-3 h-3"></i></span>
                    @if ($loop->last)
                    <span class="font-medium text-gray-700">{{ $crumb['label'] }}</span>
                    @else
                    <a href="{{ route('file-managements.index') }}?path={{ urlencode($crumb['path']) }}" class="hover:text-indigo-600 transition">
                        {{ $crumb['label'] }}
                    </a>
                    @endif
                    @endforeach
                </div>
                @else
                <div class="mb-7 flex items-center gap-1 text-sm text-gray-500">
                    <a href="{{ route('file-managements.index') }}" class="hover:text-indigo-600 transition flex items-center gap-1">
                        <i data-lucide="home" class="w-4 h-4"></i>
                        <span class="font-medium text-gray-700">HOME</span>
                    </a>
                </div>
                @endif

                <div class="">
                    {{-- Root: folder list --}}
                    @if (!isset($files))
                    @if (empty($directories))
                    <div class="w-full border bg-white p-5 rounded text-sm">
                        <div class="flex justify-center items-center gap-2 py-8 text-gray-400">
                            <span class="shrink-0"><i data-lucide="folder-open" class="w-5 h-5"></i></span>
                            <p>Belum ada folder vendor</p>
                        </div>
                    </div>
                    @else
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
                        @foreach ($directories as $directory)
                        <a
                            href="{{ route('file-managements.index') }}?path={{ urlencode($directory['path']) }}"
                            class="group block border bg-gray-50 rounded-lg p-4 transition hover:shadow-md hover:border-indigo-300">
                            <div class="flex flex-col items-center gap-2 text-center">
                                <div class="p-3 rounded-full bg-indigo-50 text-indigo-500 group-hover:bg-indigo-100 transition">
                                    <i data-lucide="folder" class="w-8 h-8"></i>
                                </div>
                                <div class="w-full">
                                    <p class="font-semibold text-sm truncate" title="{{ $directory['name'] }}">
                                        {{ $directory['name'] }}
                                    </p>
                                </div>
                            </div>
                        </a>
                        @endforeach
                    </div>
                    @endif

                    {{-- Inside folder --}}
                    @else
                    {{-- Subdirectories --}}
                    @if (!empty($directories))
                    <div class="mb-6">
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">Folder</p>
                        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
                            @foreach ($directories as $directory)
                            <a
                                href="{{ route('file-managements.index') }}?path={{ urlencode($directory['path']) }}"
                                class="group block border bg-gray-50 rounded-lg p-4 transition hover:shadow-md hover:border-indigo-300">
                                <div class="flex flex-col items-center gap-2 text-center">
                                    <div class="p-3 rounded-full bg-indigo-50 text-indigo-500 group-hover:bg-indigo-100 transition">
                                        <i data-lucide="folder" class="w-8 h-8"></i>
                                    </div>
                                    <p class="font-semibold text-sm" title="{{ $directory['name'] }}">
                                        {{ $directory['name'] }}
                                    </p>
                                </div>
                            </a>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    {{-- Files --}}
                    <div>
                        @if (empty($files) && empty($directories))
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">File</p>
                        <div class="w-full border bg-white p-5 rounded text-sm">
                            <div class="flex justify-center items-center gap-2 py-8 text-gray-400">
                                <span class="shrink-0"><i data-lucide="file-x" class="w-5 h-5"></i></span>
                                <p>Tidak ada file di folder ini</p>
                            </div>
                        </div>
                        @else
                        @if(!empty($files))
                        <div class="w-full border bg-white rounded text-sm overflow-hidden">
                            <table class="w-full">
                                <thead>
                                    <tr class="border-b">
                                        <td class="text-gray-500 p-3 text-start font-medium">Nama File</td>
                                        <td class="text-gray-500 p-3 text-start font-medium">Ekstensi</td>
                                        <td class="text-gray-500 p-3 text-end font-medium">Ukuran</td>
                                        <td class="text-gray-500 p-3 text-end font-medium">Action</td>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($files as $file)
                                    <tr class="border-b last:border-b-0 hover:bg-gray-50 transition">
                                        <td class="p-3 flex items-center gap-2">
                                            <span class="shrink-0 text-gray-400">
                                                @if (in_array($file['extension'], ['jpg', 'jpeg', 'png', 'webp']))
                                                <i data-lucide="image" class="w-4 h-4"></i>
                                                @else
                                                <i data-lucide="file" class="w-4 h-4"></i>
                                                @endif
                                            </span>
                                            <span class="font-medium truncate" title="{{ $file['name'] }}">{{ $file['name'] }}</span>
                                        </td>
                                        <td class="p-3 text-gray-400 uppercase">{{ $file['extension'] ?: '-' }}</td>
                                        <td class="p-3 text-end text-gray-500">{{ $file['size'] > 0 ? number_format($file['size'] / 1024 / 1024, 1).' MB' : '-' }}</td>
                                        <td class="p-3 text-end text-gray-500">
                                            <button type="submit" class="p-2 bg-indigo-700 rounded">
                                                <span class="shrink-0 text-white"><i data-lucide="download" class="w-4 h-4"></i></span>
                                            </button>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @endif
                        @endif
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
