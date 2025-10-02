<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#111827">
    <title>@yield('title', 'Enterprise Console')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script>
        // Initialize dark mode based on saved preference or OS setting
        (function() {
            try {
                const ls = localStorage.getItem('theme');
                const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
                if (ls === 'dark' || (!ls && prefersDark)) {
                    document.documentElement.classList.add('dark');
                } else {
                    document.documentElement.classList.remove('dark');
                }
            } catch (_) {}
        })();
    </script>
    @stack('head')
    <style>
        .skip-link {
            position: absolute;
            left: -9999px;
            top: auto;
            width: 1px;
            height: 1px;
            overflow: hidden;
        }

        .skip-link:focus {
            position: static;
            width: auto;
            height: auto;
            padding: .5rem .75rem;
            background: #111827;
            color: #fff;
            z-index: 50;
        }
    </style>
</head>

<body class="min-h-screen antialiased bg-gray-50 text-gray-900 dark:bg-gray-900 dark:text-gray-100">
    <a href="#main-content" class="skip-link">Skip to main content</a>
    <main id="main-content" role="main">
        @yield('content')
    </main>
    @stack('scripts')
</body>

</html>
