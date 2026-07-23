<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="p-3">
        <div class="w-full">
            <h1 class="text-2xl font-bold">Halo, {{ auth()->user()->name }}!</h1>
            <p class="m-0 p-0 text-sm">Selamat Datang Kembali di Dokter Akuntansi SBS</p>
        </div>
    </div>
</x-app-layout>
