@props([
    'action' => '',
    'placeholder' => '搜索...',
    'value' => '',
    'name' => 'q',
    'label' => null,
    'showClear' => true,
    'clearUrl' => null,
])

<div {{ $attributes->merge(['class' => 'overflow-hidden rounded-2xl border border-slate-200/60 bg-white shadow-sm transition-all duration-300 hover:shadow-md']) }}>
    <form action="{{ $action }}" method="GET" class="p-4 sm:p-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end">
            {{-- Search input --}}
            <div class="flex-1">
                @if ($label)
                    <label for="{{ $name }}" class="mb-2 block text-sm font-medium text-slate-700">
                        {{ $label }}
                    </label>
                @endif
                <div class="group relative">
                    {{-- Search icon --}}
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">
                        <svg class="h-5 w-5 text-slate-400 transition-colors group-focus-within:text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    {{-- Input field --}}
                    <input
                        id="{{ $name }}"
                        name="{{ $name }}"
                        type="search"
                        value="{{ $value }}"
                        placeholder="{{ $placeholder }}"
                        class="h-12 w-full rounded-xl border border-slate-200 bg-slate-50/50 pl-12 pr-4 text-slate-700 placeholder-slate-400 transition-all duration-200 focus:border-indigo-400 focus:bg-white focus:outline-none focus:ring-4 focus:ring-indigo-500/10 sm:h-11"
                        autocomplete="off"
                    >
                    {{-- Loading spinner (hidden by default, can be shown via JS) --}}
                    <div id="searchSpinner" class="pointer-events-none absolute inset-y-0 right-4 hidden items-center">
                        <svg class="h-5 w-5 animate-spin text-indigo-500" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            {{-- Buttons --}}
            <div class="flex gap-2 sm:flex-shrink-0">
                {{-- Search button --}}
                <button
                    type="submit"
                    class="group/btn relative flex h-12 flex-1 items-center justify-center gap-2 overflow-hidden rounded-xl bg-gradient-to-r from-indigo-500 to-purple-600 px-6 text-sm font-semibold text-white shadow-lg shadow-indigo-500/25 transition-all duration-300 hover:scale-[1.02] hover:shadow-xl hover:shadow-indigo-500/30 active:scale-[0.98] sm:h-11 sm:flex-initial"
                >
                    {{-- Shine effect --}}
                    <span class="absolute inset-0 -translate-x-full bg-gradient-to-r from-transparent via-white/20 to-transparent transition-transform duration-500 group-hover/btn:translate-x-full"></span>
                    <svg class="h-4 w-4 transition-transform duration-200 group-hover/btn:scale-110" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <span>搜索</span>
                </button>

                {{-- Clear button (conditional) --}}
                @if ($showClear && $value)
                    <a
                        href="{{ $clearUrl ?: $action }}"
                        class="flex h-12 items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-4 text-sm font-medium text-slate-600 transition-all duration-200 hover:border-slate-300 hover:bg-slate-50 hover:text-slate-800 active:scale-[0.98] sm:h-11 sm:px-5"
                    >
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        <span class="hidden sm:inline">清空</span>
                    </a>
                @endif
            </div>
        </div>

        {{-- Optional slot for extra filters --}}
        @if ($slot->isNotEmpty())
            <div class="mt-4 border-t border-slate-100 pt-4">
                {{ $slot }}
            </div>
        @endif
    </form>
</div>
