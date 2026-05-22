<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Encuesta' }} | UNEG</title>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Alpine.js -->
    {{-- <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script> --}}

    <!-- Theme Store -->
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.store('theme', {
                init() {
                    const savedTheme = localStorage.getItem('theme');
                    const systemTheme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' :
                        'light';
                    this.theme = savedTheme || systemTheme;
                    this.updateTheme();
                },
                theme: 'light',
                toggle() {
                    this.theme = this.theme === 'light' ? 'dark' : 'light';
                    localStorage.setItem('theme', this.theme);
                    this.updateTheme();
                },
            });
        });
    </script>

</head>

<body>
    <div>
        <div class="flex-1 transition-all duration-300 ease-in-out"
            <header
                class="sticky top-0 flex w-full bg-white border-gray-200 z-99999 dark:border-gray-800 dark:bg-gray-900 xl:border-b"
                x-data="{
                    isApplicationMenuOpen: false,
                    toggleApplicationMenu() {
                        this.isApplicationMenuOpen = !this.isApplicationMenuOpen;
                    }
                }">
                <div class="flex items-center justify-center w-full px-3 py-3 border-b border-gray-200 dark:border-gray-800 lg:py-4">
                    <a href="/">
                        <img class="dark:hidden" src="/images/logo/logo-xl.png" alt="Logo" width="80" height="80"/>
                        <img class="hidden dark:block" src="/images/logo/logo-xl.png" alt="Logo" width="100" height="100"/>
                    </a>
                </div>
            </header>


            <div class="p-4 mx-auto max-w-(--breakpoint-2xl) md:p-6">
                @yield('content')
            </div>
        </div>

    </div>

</body>

@stack('scripts')

</html>
