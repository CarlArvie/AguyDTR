<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'App' }}</title>
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
    <style>
        .nav-link {
            position: relative;
            transition: color 200ms ease;
        }

        .nav-link::after {
            content: "";
            position: absolute;
            left: 0;
            bottom: -2px;
            height: 2px;
            width: 0;
            background: #f97316;
            transition: width 200ms ease;
        }

        .nav-link:hover::after,
        .nav-link:focus-visible::after {
            width: 100%;
        }

        .nav-link:focus-visible {
            outline: 2px solid #f97316;
            outline-offset: 4px;
        }

        .nav-link.is-active {
            color: #fb923c;
        }

        .nav-link.is-active::after {
            width: 100%;
        }

        .fade-up {
            animation: fade-up 500ms ease both;
        }

        @keyframes fade-up {
            from {
                opacity: 0;
                transform: translateY(8px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

                #auth-loading-overlay[hidden] {
            display: none;
        }
    </style>
</head>
<body class="min-h-screen bg-black">

    <div class="flex items-center justify-between p-4 bg-[rgb(38,38,38)] rounded-b-lg">
        <a href="{{ route('dashboard') }}" class="flex items-center mx-2 my-2" data-show-loader="true">
            <img src="{{ asset('images/logo.png') }}" alt="Logo" class="h-8 w-8 mr-2">
            <span class="self-center text-xl font-semibold text-orange-600">booklet</span>
        </a>
        <nav class="flex items-center" >
            <a href="{{ route('dashboard') }}" class="nav-link px-4 py-2 mx-2 my-2 text-orange-600 {{ request()->routeIs('dashboard') ? 'is-active' : '' }}" data-show-loader="true">Home</a>
            <a href="{{ route('time-entries.create') }}" class="nav-link px-4 py-2 mx-2 my-2 text-orange-600 {{ request()->routeIs('time-entries.*') ? 'is-active' : '' }}" data-show-loader="true">Add Time In & Out</a>
            <a href="{{ route('about') }}" class="nav-link px-4 py-2 mx-2 my-2 text-orange-600" data-show-loader="true">about</a>
        </nav>
        <nav class="flex items-center">
            <a href="{{ route('profile') }}" class="nav-link px-4 py-2 mx-2 my-2 text-orange-600 {{ request()->routeIs('profile') ? 'is-active' : '' }}" data-show-loader="true">Profile</a>
            <form method="POST" action="{{ route('logout') }}" class="mx-2 my-2">
                @csrf
                <button type="submit" class="nav-link px-4 py-2 text-orange-600" data-show-loader="true">Logout</button>
            </form>
        </nav>
    </div>
    <div id="auth-loading-overlay" class="fixed inset-0 z-50 flex items-center justify-center bg-black/45 backdrop-blur-sm" hidden>
        <div class="flex items-center gap-3 rounded-lg bg-white px-5 py-3 text-sm font-medium text-orange-600 shadow-lg">
            <span class="h-5 w-5 animate-spin rounded-full border-2 border-orange-600 border-t-transparent"></span>
            Processing, please wait...
        </div>
    </div>

    <main class="mx-auto max-w-3xl px-4 py-8">
        {{ $slot }}
    </main>



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
</body>
</html>
