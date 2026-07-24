@props([
    'type' => 'success',
    'message' => null,
    'duration' => 4000,
])

@php
    $toastMessage = $message ?? session($type);
    $showToast = filled($toastMessage);
@endphp

@if($showToast)
<div
    x-data="{
        show: true,
        timeout: null,
        init() {
            this.$nextTick(() => lucide.createIcons());
            this.timeout = setTimeout(() => { this.show = false }, {{ $duration }});
        },
        dismiss() {
            clearTimeout(this.timeout);
            this.show = false;
        }
    }"
    x-on:mouseenter="clearTimeout(timeout)"
    x-on:mouseleave="timeout = setTimeout(() => show = false, 2000)"
    x-show="show"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 translate-y-4 translate-x-4"
    x-transition:enter-end="opacity-100 translate-y-0 translate-x-0"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100 translate-y-0 translate-x-0"
    x-transition:leave-end="opacity-0 translate-y-2 translate-x-4"
    class="fixed bottom-5 right-5 z-50 flex items-center gap-3 rounded-lg border px-4 py-3 shadow-lg backdrop-blur-sm max-w-sm
        @if($type === 'success') border-green-200 bg-green-50 text-green-800
        @elseif($type === 'error') border-red-200 bg-red-50 text-red-800
        @else border-gray-200 bg-gray-50 text-gray-800 @endif
    "
>
    <div class="shrink-0">
        @if($type === 'success')
            <i data-lucide="check-circle-2" class="w-5 h-5 text-green-500"></i>
        @elseif($type === 'error')
            <i data-lucide="x-circle" class="w-5 h-5 text-red-500"></i>
        @else
            <i data-lucide="info" class="w-5 h-5 text-gray-500"></i>
        @endif
    </div>

    <p class="text-sm font-medium">{{ $toastMessage }}</p>

    <button
        @click="dismiss()"
        class="shrink-0 ml-2 rounded p-0.5 transition-colors
            @if($type === 'success') hover:bg-green-100 text-green-600
            @elseif($type === 'error') hover:bg-red-100 text-red-600
            @else hover:bg-gray-100 text-gray-600 @endif
        "
    >
        <i data-lucide="x" class="w-4 h-4"></i>
    </button>

    <div
        class="absolute bottom-0 left-0 h-0.5 rounded-b-lg
            @if($type === 'success') bg-green-400
            @elseif($type === 'error') bg-red-400
            @else bg-gray-400 @endif
        "
        x-init="$nextTick(() => { $el.style.transition = 'width {{ $duration }}ms linear'; $el.style.width = '100%'; setTimeout(() => { $el.style.width = '0%' }, 50) })"
        style="width: 0%"
    ></div>
</div>
@endif
