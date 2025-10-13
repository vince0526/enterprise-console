<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Enterprise Database Management Console">
    <title>@yield('title', 'EMC - Enterprise Management Console')</title>

    <!-- Preload critical resources -->
    <link rel="preload" href="{{ asset('css/emc.css') }}" as="style">

    <!-- Stylesheets -->
    <link rel="stylesheet" href="{{ asset('css/emc.css') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script>
        // Initialize theme from local storage before paint
        (function() {
            try {
                const root = document.documentElement;
                const savedTheme = localStorage.getItem('emc.theme');
                const savedAccent = localStorage.getItem('emc.accent');
                const savedNeutral = localStorage.getItem('emc.neutral');
                if (savedTheme) root.setAttribute('data-theme', savedTheme);
                if (savedAccent) root.setAttribute('data-accent', savedAccent);
                if (savedNeutral) root.setAttribute('data-neutral', savedNeutral);
            } catch (e) {
                /* no-op */
            }
        })();
    </script>
    @stack('head')
</head>

<body>
    <!-- Skip to main content for screen readers -->
    <a href="#main-content" class="sr-only focus-visible">Skip to main content</a>

    <!-- Main Header -->
    <header class="header" role="banner">
        <div class="header__container">
            <a href="{{ \Illuminate\Support\Facades\Route::has('emc.core.index') ? route('emc.core.index') : route('emc.db') }}"
                class="header__brand">
                Enterprise Database Management
            </a>

            <!-- User actions -->
            <div class="header__actions">
                <!-- Activity Log Dropdown -->
                <div class="activity-dropdown">
                    <button class="activity-dropdown__toggle" type="button" id="activity-toggle" aria-expanded="false"
                        aria-haspopup="true" title="View Activity Log">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                            <polyline points="14,2 14,8 20,8"></polyline>
                            <line x1="16" y1="13" x2="8" y2="13"></line>
                            <line x1="16" y1="17" x2="8" y2="17"></line>
                            <polyline points="10,9 9,9 8,9"></polyline>
                        </svg>
                        <span class="sr-only">Activity Log</span>
                    </button>

                    <div class="activity-dropdown__menu" id="activity-menu" role="menu"
                        aria-labelledby="activity-toggle">
                        <div class="activity-dropdown__header">
                            <h3>Activity Log</h3>
                            <button class="activity-dropdown__pin" type="button" title="Pin activity log"
                                aria-label="Pin activity log">
                                📌
                            </button>
                        </div>
                        <div class="activity-dropdown__content">
                            <ul role="list">
                                <li><strong>Current Page:</strong> {{ request()->path() }}</li>
                                <li><strong>Environment:</strong>
                                    {{ env('DEV_AUTO_LOGIN') ? 'Development' : 'Production' }}</li>
                                <li><strong>Timestamp:</strong> {{ now()->format('Y-m-d H:i:s') }}</li>
                                <li><strong>Browser:</strong>
                                    {{ request()->header('User-Agent') ? Str::limit(request()->header('User-Agent'), 50) : 'Unknown' }}
                                </li>
                                <li><strong>IP Address:</strong> {{ request()->ip() }}</li>
                                <li><strong>Request Method:</strong> {{ request()->method() }}</li>
                            </ul>
                            <div class="activity-dropdown__footer">
                                <small>Real-time activity monitoring</small>
                            </div>
                        </div>
                    </div>
                </div>
                <span class="sr-only">User menu</span>
            </div>
        </div>
    </header>

    <!-- Primary Navigation -->
    <nav class="nav" role="navigation" aria-label="Primary navigation">
        <div class="nav__viewport" id="top-nav-viewport">
            <ul class="nav__list" role="list">
                <li class="nav__item">
                    <!-- Theme controls -->
                    <div class="theme-switcher">
                        <button type="button" class="btn btn--small" id="theme-toggle"
                            title="Toggle theme">Theme</button>
                        <select id="accent-select" class="form__select" style="width:auto" title="Accent color">
                            <option value="blue">Blue</option>
                            <option value="teal">Teal</option>
                            <option value="violet">Violet</option>
                            <option value="rose">Rose</option>
                        </select>
                    </div>
                    <a href="{{ \Illuminate\Support\Facades\Route::has('emc.core.index') ? route('emc.core.index') : route('emc.db') }}"
                        class="nav__link {{ request()->routeIs('emc.core.*') ? 'nav__link--active' : '' }}"
                        @if (request()->routeIs('emc.core.*')) aria-current="page" @endif>
                        Core Databases
                    </a>
                </li>
                <li class="nav__item">
                    <a href="{{ route('emc.db') }}"
                        class="nav__link {{ request()->routeIs('emc.db') ? 'nav__link--active' : '' }}"
                        @if (request()->routeIs('emc.db')) aria-current="page" @endif>
                        Database Management
                    </a>
                </li>
                <li class="nav__item">
                    <a href="{{ route('emc.tables') }}"
                        class="nav__link {{ request()->routeIs('emc.tables') ? 'nav__link--active' : '' }}"
                        @if (request()->routeIs('emc.tables')) aria-current="page" @endif>
                        Tables and Views
                    </a>
                </li>
                <li class="nav__item">
                    <a href="{{ route('emc.files') }}"
                        class="nav__link {{ request()->routeIs('emc.files') ? 'nav__link--active' : '' }}"
                        @if (request()->routeIs('emc.files')) aria-current="page" @endif>
                        File Management
                    </a>
                </li>
                <li class="nav__item">
                    <a href="{{ route('emc.reports') }}"
                        class="nav__link {{ request()->routeIs('emc.reports') ? 'nav__link--active' : '' }}"
                        @if (request()->routeIs('emc.reports')) aria-current="page" @endif>
                        Report Management
                    </a>
                </li>
                <li class="nav__item">
                    <a href="{{ route('emc.ai') }}"
                        class="nav__link {{ request()->routeIs('emc.ai') ? 'nav__link--active' : '' }}"
                        @if (request()->routeIs('emc.ai')) aria-current="page" @endif>
                        Artificial Intelligence Access
                    </a>
                </li>
                <li class="nav__item">
                    <a href="{{ route('emc.comms') }}"
                        class="nav__link {{ request()->routeIs('emc.comms') ? 'nav__link--active' : '' }}"
                        @if (request()->routeIs('emc.comms')) aria-current="page" @endif>
                        Communications
                    </a>
                </li>
                <li class="nav__item">
                    <a href="{{ route('emc.settings') }}"
                        class="nav__link {{ request()->routeIs('emc.settings') ? 'nav__link--active' : '' }}"
                        @if (request()->routeIs('emc.settings')) aria-current="page" @endif>
                        Preferences and Settings
                    </a>
                </li>
            </ul>
            <div class="nav__indicator" id="nav-indicator" aria-hidden="true"></div>
        </div>
        <button class="nav__scroll nav__scroll--prev" type="button" aria-label="Scroll left" title="Scroll left"
            data-direction="-1">⟨</button>
        <button class="nav__scroll nav__scroll--next" type="button" aria-label="Scroll right" title="Scroll right"
            data-direction="1">⟩</button>
    </nav>

    <!-- Submodule Navigation -->
    @include('emc.partials.submodule')

    <!-- Main Layout Grid -->
    <div class="layout">
        <!-- Sidebar Navigation -->
        <aside class="sidebar" role="complementary" aria-label="Secondary navigation">
            @include('emc.partials.sidebar')
        </aside>

        <!-- Main Content Area -->
        <main class="content" id="main-content" role="main">
            <div class="content__frame">
                @yield('content')
            </div>
        </main>

        <!-- Right Summary Panel (10%) -->
        <aside class="summary" role="complementary" aria-label="Summary panel">
            @include('emc.partials.summary')
        </aside>
    </div>

    <!-- Scripts -->
    <script src="{{ asset('js/emc.js') }}" defer></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const root = document.documentElement;
            const themeBtn = document.getElementById('theme-toggle');
            const accentSel = document.getElementById('accent-select');
            const navViewport = document.getElementById('top-nav-viewport');
            const navIndicator = document.getElementById('nav-indicator');
            const scrollPrev = document.querySelector('.nav__scroll--prev');
            const scrollNext = document.querySelector('.nav__scroll--next');
            if (themeBtn) {
                themeBtn.addEventListener('click', function() {
                    const prefersLight = window.matchMedia && window.matchMedia(
                        '(prefers-color-scheme: light)').matches;
                    const cur = root.getAttribute('data-theme') || (prefersLight ? 'light' : 'dark');
                    const next = cur === 'light' ? 'dark' : 'light';
                    root.setAttribute('data-theme', next);
                    try {
                        localStorage.setItem('emc.theme', next);
                    } catch (e) {}
                });
            }
            if (accentSel) {
                const saved = root.getAttribute('data-accent') || 'blue';
                accentSel.value = saved;
                accentSel.addEventListener('change', function() {
                    root.setAttribute('data-accent', this.value);
                    try {
                        localStorage.setItem('emc.accent', this.value);
                    } catch (e) {}
                });
            }

            function moveIndicator() {
                if (!navViewport || !navIndicator) return;
                const active = navViewport.querySelector('.nav__link--active');
                if (!active) { navIndicator.style.width = 0; return; }
                const rect = active.getBoundingClientRect();
                const parentRect = navViewport.getBoundingClientRect();
                navIndicator.style.left = (rect.left - parentRect.left) + 'px';
                navIndicator.style.width = rect.width + 'px';
            }
            moveIndicator();
            window.addEventListener('resize', moveIndicator);
            const obs = new MutationObserver(moveIndicator);
            if (navViewport) obs.observe(navViewport, { attributes: true, subtree: true, attributeFilter: ['class'] });

            function smoothScroll(dir) {
                if (!navViewport) return;
                navViewport.scrollBy({ left: (dir < 0 ? -240 : 240), behavior: 'smooth' });
                setTimeout(moveIndicator, 280);
            }
            if (scrollPrev) scrollPrev.addEventListener('click', () => smoothScroll(-1));
            if (scrollNext) scrollNext.addEventListener('click', () => smoothScroll(1));
        });
    </script>
    @stack('scripts')
</body>

</html>
