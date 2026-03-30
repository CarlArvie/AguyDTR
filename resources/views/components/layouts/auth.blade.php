<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Auth' }}</title>
    @php
        $manifestPath = public_path('build/manifest.json');
        $manifest = file_exists($manifestPath) ? json_decode(file_get_contents($manifestPath), true) : [];
        $cssFile = $manifest['resources/css/app.css']['file'] ?? null;
        $jsFile = $manifest['resources/js/app.js']['file'] ?? null;
    @endphp
    @if ($cssFile)
        <link rel="stylesheet" href="{{ asset('build/' . $cssFile) }}">
    @endif
    @if ($jsFile)
        <script src="{{ asset('build/' . $jsFile) }}" defer></script>
    @endif
    @livewireStyles
    <style>
        #auth-loading-overlay[hidden] {
            display: none;
        }
    </style>
</head>
<body
    class="min-h-screen bg-gray-100 bg-cover bg-center bg-no-repeat"
    style="background-image: url('{{ asset('images/bg-d.jpg') }}')"
>
    <div class="mx-auto flex min-h-screen w-full max-w-md items-center px-4 py-8">
        <div class="w-full rounded-xl bg-transparent bg-blur-md  bg-opacity-50 p-6 shadow-sm" style="backdrop-filter: blur(100px); opacity: 1;">
            <h1 class="mb-1 text-2xl font-semibold text-orange-600 text-center">{{ $heading ?? 'Welcome' }}</h1>
            @isset($subheading)
                <p class="mb-6 text-sm text-orange-600">{{ $subheading }}</p>
            @endisset

            @if (session('status'))
                <div class="mb-4 rounded-md bg-green-50 px-3 py-2 text-sm text-green-700">
                    {{ session('status') }}
                </div>
            @endif

            {{ $slot }}
        </div>
    </div>

    <div id="auth-loading-overlay" class="fixed inset-0 z-50 flex items-center justify-center bg-black/45 backdrop-blur-sm" hidden>
        <div class="flex items-center gap-3 rounded-lg bg-white px-5 py-3 text-sm font-medium text-orange-600 shadow-lg">
            <span class="h-5 w-5 animate-spin rounded-full border-2 border-orange-600 border-t-transparent"></span>
            Processing, please wait...
        </div>
    </div>

    <script>
        (function () {
            const overlay = document.getElementById('auth-loading-overlay');
            if (!overlay) return;

            const showOverlay = () => {
                overlay.hidden = false;
                document.body.classList.add('overflow-hidden');
            };

            const hideOverlay = () => {
                overlay.hidden = true;
                document.body.classList.remove('overflow-hidden');
            };

            document.addEventListener('submit', function (event) {
                const form = event.target;
                if (!(form instanceof HTMLFormElement)) return;
                if (form.dataset.noLoader === 'true') return;
                showOverlay();
            });

            window.addEventListener('pageshow', hideOverlay);
        })();
    </script>
    @livewireScripts
</body>
</html>
