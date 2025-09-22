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
    
    @stack('head')
</head>
<body>
    <!-- Skip to main content for screen readers -->
    <a href="#main-content" class="sr-only focus-visible">Skip to main content</a>
    
    <!-- Main Header -->
    <header class="header" role="banner">
        <div class="header__container">
            <a href="{{ route('emc.db') }}" class="header__brand">
                Enterprise Database Management
            </a>
            
            <!-- User actions could go here -->
            <div class="header__actions">
                <span class="sr-only">User menu</span>
            </div>
        </div>
    </header>
    
    <!-- Primary Navigation -->
    <nav class="nav" role="navigation" aria-label="Primary navigation">
        <ul class="nav__list" role="list">
            <li class="nav__item">
                <a href="{{ route('emc.db') }}" 
                   class="nav__link {{ request()->routeIs('emc.db') ? 'nav__link--active' : '' }}"
                   @if(request()->routeIs('emc.db')) aria-current="page" @endif>
                    Database Management
                </a>
            </li>
            <li class="nav__item">
                <a href="{{ route('emc.tables') }}" 
                   class="nav__link {{ request()->routeIs('emc.tables') ? 'nav__link--active' : '' }}"
                   @if(request()->routeIs('emc.tables')) aria-current="page" @endif>
                    Tables and Views
                </a>
            </li>
            <li class="nav__item">
                <a href="{{ route('emc.files') }}" 
                   class="nav__link {{ request()->routeIs('emc.files') ? 'nav__link--active' : '' }}"
                   @if(request()->routeIs('emc.files')) aria-current="page" @endif>
                    File Management
                </a>
            </li>
            <li class="nav__item">
                <a href="{{ route('emc.reports') }}" 
                   class="nav__link {{ request()->routeIs('emc.reports') ? 'nav__link--active' : '' }}"
                   @if(request()->routeIs('emc.reports')) aria-current="page" @endif>
                    Report Management
                </a>
            </li>
            <li class="nav__item">
                <a href="{{ route('emc.ai') }}" 
                   class="nav__link {{ request()->routeIs('emc.ai') ? 'nav__link--active' : '' }}"
                   @if(request()->routeIs('emc.ai')) aria-current="page" @endif>
                    Artificial Intelligence Access
                </a>
            </li>
            <li class="nav__item">
                <a href="{{ route('emc.comms') }}" 
                   class="nav__link {{ request()->routeIs('emc.comms') ? 'nav__link--active' : '' }}"
                   @if(request()->routeIs('emc.comms')) aria-current="page" @endif>
                    Communications
                </a>
            </li>
            <li class="nav__item">
                <a href="{{ route('emc.settings') }}" 
                   class="nav__link {{ request()->routeIs('emc.settings') ? 'nav__link--active' : '' }}"
                   @if(request()->routeIs('emc.settings')) aria-current="page" @endif>
                    Preferences and Settings
                </a>
            </li>
        </ul>
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
        
        <!-- Activity Panel -->
        <aside class="activity" role="complementary" aria-label="Activity log">
            @include('emc.partials.activity')
        </aside>
    </div>
    
    <!-- Scripts -->
    <script src="{{ asset('js/emc.js') }}" defer></script>
    @stack('scripts')
</body>
</html>
