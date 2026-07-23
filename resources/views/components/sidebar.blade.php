@php
    $navigation = [
        [
            'label' => 'Dashboard',
            'route' => 'dashboard',
            'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="7" height="9" x="3" y="3" rx="1"/><rect width="7" height="5" x="14" y="3" rx="1"/><rect width="7" height="9" x="14" y="12" rx="1"/><rect width="7" height="5" x="3" y="16" rx="1"/></svg>',
        ],
        [
            'label' => 'Master Data',
            'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1 0-5H20"/></svg>',
            'children' => [
                ['label' => 'Vendor', 'route' => 'vendors.index'],
            ],
        ],
        [
            'label' => 'Profile',
            'route' => 'profile.edit',
            'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>',
        ],
    ];

    $user = Auth::user();
@endphp

<aside
    x-data="{ openMenus: [] }"
    @toggle-sidebar.window="$store.sidebar.toggle()"
    @keydown.escape.window="$store.sidebar.collapsed = false"
    class="flex flex-col h-screen transition-all duration-300
           fixed inset-y-0 left-0 z-50
           bg-white border-r border-gray-200
           hidden md:flex"
    :class="$store.sidebar.collapsed ? 'w-20' : 'w-64'"
>
    <!-- Logo -->
    <div class="flex items-center h-16 px-4 border-b border-gray-200">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-3 overflow-hidden">
            <x-application-logo class="h-8 w-8 shrink-0 text-indigo-600" />
            <span
                class="font-semibold text-gray-900 whitespace-nowrap transition-opacity duration-200"
                :class="$store.sidebar.collapsed ? 'opacity-0 hidden' : 'opacity-100'"
            >{{ config('app.name', 'Laravel') }}</span>
        </a>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 overflow-y-auto py-4 px-3">
        <ul class="space-y-1">
            @foreach($navigation as $item)
                @if(isset($item['children']))
                    {{-- Collapsible Menu --}}
                    <li x-data="{ open: openMenus.includes('{{ $item['label'] }}') }">
                        <button
                            @click="
                                open = !open;
                                if (open) { openMenus.push('{{ $item['label'] }}') }
                                else { openMenus = openMenus.filter(m => m !== '{{ $item['label'] }}') }
                            "
                            @class([
                                'flex items-center gap-3 w-full rounded-lg px-3 py-2.5 text-sm font-medium transition-all duration-200',
                                'text-gray-600 hover:bg-gray-100 hover:text-gray-900',
                            ])
                            x-bind:title="$store.sidebar.collapsed ? '{{ $item['label'] }}' : ''"
                        >
                            <span class="shrink-0">{!! $item['icon'] !!}</span>
                            <span
                                class="flex-1 text-left whitespace-nowrap transition-opacity duration-200"
                                :class="$store.sidebar.collapsed ? 'opacity-0 hidden' : 'opacity-100'"
                            >{{ $item['label'] }}</span>
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                width="16"
                                height="16"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                class="shrink-0 transition-transform duration-200"
                                :class="open ? 'rotate-90' : ''"
                            ><path d="m9 18 6-6-6-6"/></svg>
                        </button>

                        {{-- Children --}}
                        <ul
                            x-show="open && !$store.sidebar.collapsed"
                            x-collapse
                            class="ml-4 mt-1 space-y-1 border-l border-gray-200 pl-3"
                        >
                            @foreach($item['children'] as $child)
                                <li>
                                    <a
                                        href="{{ route($child['route']) }}"
                                        @class([
                                            'flex items-center gap-2 rounded-lg px-3 py-2 text-sm font-medium transition-colors',
                                            'bg-indigo-50 text-indigo-700' => request()->routeIs($child['route']),
                                            'text-gray-500 hover:bg-gray-100 hover:text-gray-700' => !request()->routeIs($child['route']),
                                        ])
                                    >
                                        <span class="h-1.5 w-1.5 shrink-0 rounded-full bg-current"></span>
                                        <span>{{ $child['label'] }}</span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </li>
                @else
                    {{-- Regular Menu --}}
                    <li>
                        <a
                            href="{{ route($item['route']) }}"
                            @class([
                                'flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-all duration-200',
                                'bg-indigo-50 text-indigo-700' => request()->routeIs($item['route']),
                                'text-gray-600 hover:bg-gray-100 hover:text-gray-900' => !request()->routeIs($item['route']),
                            ])
                            x-bind:title="$store.sidebar.collapsed ? '{{ $item['label'] }}' : ''"
                        >
                            <span class="shrink-0">{!! $item['icon'] !!}</span>
                            <span
                                class="whitespace-nowrap transition-opacity duration-200"
                                :class="$store.sidebar.collapsed ? 'opacity-0 hidden' : 'opacity-100'"
                            >{{ $item['label'] }}</span>
                        </a>
                    </li>
                @endif
            @endforeach
        </ul>
    </nav>

    <!-- User Profile -->
    <div class="border-t border-gray-200 p-3">
        <div class="flex items-center gap-3">
            <div class="h-9 w-9 shrink-0 rounded-full bg-indigo-600 flex items-center justify-center text-sm font-medium text-white">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>
            <div
                class="flex-1 min-w-0 transition-opacity duration-200"
                :class="$store.sidebar.collapsed ? 'opacity-0 hidden' : 'opacity-100'"
            >
                <p class="text-sm font-medium text-gray-900 truncate">{{ $user->name }} ({{ auth()->user()->getRoleNames()->first() }})</p>
                <p class="text-xs text-gray-500 truncate">{{ $user->email }}</p>
            </div>
        </div>

        <form method="POST" action="{{ route('logout') }}" class="mt-3">
            @csrf
            <button
                type="submit"
                class="flex items-center gap-2 w-full rounded-lg px-3 py-2 text-sm font-medium text-gray-500 hover:bg-gray-100 hover:text-gray-700 transition-colors"
                x-bind:title="$store.sidebar.collapsed ? 'Log Out' : ''"
            >
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="shrink-0">
                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                    <polyline points="16 17 21 12 16 7"/>
                    <line x1="21" x2="9" y1="12" y2="12"/>
                </svg>
                <span
                    class="whitespace-nowrap transition-opacity duration-200"
                    :class="$store.sidebar.collapsed ? 'opacity-0 hidden' : 'opacity-100'"
                >Log Out</span>
            </button>
        </form>
    </div>

    <!-- Collapse Toggle -->
    <button
        @click="$store.sidebar.toggle()"
        class="hidden md:flex absolute -right-3 top-20 h-6 w-6 items-center justify-center rounded-full border border-gray-300 bg-white text-gray-600 hover:bg-gray-50 transition-colors"
    >
        <svg
            xmlns="http://www.w3.org/2000/svg"
            width="14"
            height="14"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="2"
            stroke-linecap="round"
            stroke-linejoin="round"
            class="transition-transform duration-300"
            :class="$store.sidebar.collapsed ? 'rotate-180' : ''"
        >
            <path d="m15 18-6-6 6-6"/>
        </svg>
    </button>
</aside>

<!-- Mobile Overlay -->
<div
    x-data="{ open: false }"
    @toggle-sidebar.window="open = !open"
    @keydown.escape.window="open = false"
    class="md:hidden"
>
    <!-- Backdrop -->
    <div
        x-show="open"
        x-transition:enter="transition-opacity ease-linear duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity ease-linear duration-300"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 bg-gray-900/50"
        style="display: none;"
        @click="open = false"
    ></div>

    <!-- Mobile Sidebar -->
    <div
        x-show="open"
        x-transition:enter="transition-transform ease-in-out duration-300"
        x-transition:enter-start="-translate-x-full"
        x-transition:enter-end="translate-x-0"
        x-transition:leave="transition-transform ease-in-out duration-300"
        x-transition:leave-start="translate-x-0"
        x-transition:leave-end="-translate-x-full"
        class="fixed inset-y-0 left-0 z-50 w-64 bg-white md:hidden"
        style="display: none;"
    >
        <!-- Logo -->
        <div class="flex items-center justify-between h-16 px-4 border-b border-gray-200">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
                <x-application-logo class="h-8 w-8 text-indigo-600" />
                <span class="font-semibold text-gray-900">{{ config('app.name', 'Laravel') }}</span>
            </a>
            <button @click="open = false" class="p-1 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M18 6 6 18"/><path d="m6 6 12 12"/>
                </svg>
            </button>
        </div>

        <!-- Mobile Navigation -->
        <nav class="flex-1 overflow-y-auto py-4 px-3">
            <ul class="space-y-1">
                @foreach($navigation as $item)
                    @if(isset($item['children']))
                        {{-- Collapsible Menu --}}
                        <li x-data="{ open: false }">
                            <button
                                @click="open = !open"
                                class="flex items-center gap-3 w-full rounded-lg px-3 py-2.5 text-sm font-medium text-gray-600 hover:bg-gray-100 hover:text-gray-900 transition-colors"
                            >
                                <span class="shrink-0">{!! $item['icon'] !!}</span>
                                <span class="flex-1 text-left">{{ $item['label'] }}</span>
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    width="16"
                                    height="16"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    class="shrink-0 transition-transform duration-200"
                                    :class="open ? 'rotate-90' : ''"
                                ><path d="m9 18 6-6-6-6"/></svg>
                            </button>

                            {{-- Children --}}
                            <ul
                                x-show="open"
                                x-collapse
                                class="ml-4 mt-1 space-y-1 border-l border-gray-200 pl-3"
                            >
                                @foreach($item['children'] as $child)
                                    <li>
                                        <a
                                            href="{{ route($child['route']) }}"
                                            @click="open = false"
                                            @class([
                                                'flex items-center gap-2 rounded-lg px-3 py-2 text-sm font-medium transition-colors',
                                                'bg-indigo-50 text-indigo-700' => request()->routeIs($child['route']),
                                                'text-gray-500 hover:bg-gray-100 hover:text-gray-700' => !request()->routeIs($child['route']),
                                            ])
                                        >
                                            <span class="h-1.5 w-1.5 shrink-0 rounded-full bg-current"></span>
                                            <span>{{ $child['label'] }}</span>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </li>
                    @else
                        {{-- Regular Menu --}}
                        <li>
                            <a
                                href="{{ route($item['route']) }}"
                                @click="open = false"
                                @class([
                                    'flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-colors',
                                    'bg-indigo-50 text-indigo-700' => request()->routeIs($item['route']),
                                    'text-gray-600 hover:bg-gray-100 hover:text-gray-900' => !request()->routeIs($item['route']),
                                ])
                            >
                                <span class="shrink-0">{!! $item['icon'] !!}</span>
                                <span>{{ $item['label'] }}</span>
                            </a>
                        </li>
                    @endif
                @endforeach
            </ul>
        </nav>

        <!-- Mobile User Profile -->
        <div class="border-t border-gray-200 p-3">
            <div class="flex items-center gap-3 mb-3">
                <div class="h-9 w-9 shrink-0 rounded-full bg-indigo-600 flex items-center justify-center text-sm font-medium text-white">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-900 truncate">{{ $user->name }}{{ auth()->user()->getRoleNames()->first() }}</p>
                    <p class="text-xs text-gray-500 truncate">{{ $user->email }}</p>
                </div>
            </div>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button
                    type="submit"
                    class="flex items-center gap-2 w-full rounded-lg px-3 py-2 text-sm font-medium text-gray-500 hover:bg-gray-100 hover:text-gray-700 transition-colors"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="shrink-0">
                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                        <polyline points="16 17 21 12 16 7"/>
                        <line x1="21" x2="9" y1="12" y2="12"/>
                    </svg>
                    <span>Log Out</span>
                </button>
            </form>
        </div>
    </div>
</div>
