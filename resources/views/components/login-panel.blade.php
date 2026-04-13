@props([
    'title' => '登录',
    'subtitle' => '',
    'action' => '',
    'submitText' => '登录',
    'usernameLabel' => '用户名',
    'passwordLabel' => '密码',
    'gradient' => 'from-indigo-500 to-purple-600',
    'icon' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z',
])

<div class="min-h-[80vh] flex items-center justify-center px-4 py-12">
    <div class="w-full max-w-md">
        {{-- Card with glassmorphism --}}
        <div class="relative">
            {{-- Decorative gradient blob --}}
            <div class="absolute -top-10 -left-10 w-40 h-40 bg-gradient-to-br {{ $gradient }} rounded-full opacity-20 blur-3xl"></div>
            <div class="absolute -bottom-10 -right-10 w-40 h-40 bg-gradient-to-br {{ $gradient }} rounded-full opacity-20 blur-3xl"></div>
            
            {{-- Main card --}}
            <div class="relative bg-white/80 backdrop-blur-xl rounded-2xl shadow-xl border border-white/20 overflow-hidden">
                {{-- Header with gradient --}}
                <div class="bg-gradient-to-r {{ $gradient }} px-8 py-8 text-center">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-white/20 backdrop-blur rounded-2xl mb-4">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icon }}"/>
                        </svg>
                    </div>
                    <h1 class="text-2xl font-bold text-white">{{ $title }}</h1>
                    @if($subtitle)
                        <p class="text-white/80 mt-2 text-sm">{{ $subtitle }}</p>
                    @endif
                </div>

                {{-- Demo info slot --}}
                @if(isset($demo) && $demo->isNotEmpty())
                    <div class="px-8 pt-6">
                        <div class="bg-gradient-to-r from-amber-50 to-orange-50 border border-amber-200/50 rounded-xl p-4">
                            <div class="flex items-start gap-3">
                                <div class="flex-shrink-0">
                                    <svg class="w-5 h-5 text-amber-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <div class="flex-1 text-sm">
                                    {{ $demo }}
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Form --}}
                <div class="px-8 py-6">
                    {{-- Validation Errors --}}
                    @if($errors->any())
                        <div class="mb-6 bg-red-50 border border-red-200/50 rounded-xl p-4">
                            <div class="flex items-start gap-3">
                                <svg class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <ul class="text-sm text-red-600 space-y-1">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endif

                    <form method="POST" action="{{ $action }}" class="space-y-5">
                        @csrf
                        
                        {{-- Username field --}}
                        <div>
                            <label for="username" class="block text-sm font-medium text-gray-700 mb-2">
                                {{ $usernameLabel }}
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                </div>
                                <input 
                                    type="text" 
                                    id="username" 
                                    name="username" 
                                    value="{{ old('username') }}"
                                    required
                                    autocomplete="username"
                                    class="w-full pl-12 pr-4 py-3 bg-gray-50/50 border border-gray-200 rounded-xl
                                           focus:bg-white focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100
                                           transition-all duration-200 outline-none
                                           placeholder:text-gray-400"
                                    placeholder="请输入{{ $usernameLabel }}"
                                >
                            </div>
                        </div>

                        {{-- Password field --}}
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                                {{ $passwordLabel }}
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                    </svg>
                                </div>
                                <input 
                                    type="password" 
                                    id="password" 
                                    name="password"
                                    required
                                    autocomplete="current-password"
                                    class="w-full pl-12 pr-4 py-3 bg-gray-50/50 border border-gray-200 rounded-xl
                                           focus:bg-white focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100
                                           transition-all duration-200 outline-none
                                           placeholder:text-gray-400"
                                    placeholder="请输入{{ $passwordLabel }}"
                                >
                            </div>
                        </div>

                        {{-- Extra slot (for additional fields or links) --}}
                        {{ $slot }}

                        {{-- Submit button --}}
                        <button 
                            type="submit"
                            class="w-full py-3.5 px-6 bg-gradient-to-r {{ $gradient }} text-white font-semibold rounded-xl
                                   shadow-lg shadow-indigo-500/25 hover:shadow-xl hover:shadow-indigo-500/30
                                   hover:scale-[1.02] active:scale-[0.98]
                                   transition-all duration-200"
                        >
                            {{ $submitText }}
                        </button>
                    </form>
                </div>

                {{-- Footer slot --}}
                @if(isset($footer))
                    <div class="px-8 pb-6 pt-2 text-center border-t border-gray-100">
                        {{ $footer }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
