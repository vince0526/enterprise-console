@php
  $map = [
    'emc.core' => 'Core Databases',
    'emc.db' => 'Database Management',
    'emc.tables' => 'Tables and Views',
    'emc.files' => 'File Management',
    'emc.users' => 'User Management',
    'emc.reports' => 'Report Management',
    'emc.ai' => 'Artificial Intelligence Access',
    'emc.comms' => 'Communications',
    'emc.settings' => 'Preferences and Settings',
    'emc.activity' => 'Activity Log',
    'emc.about' => 'About',
  ];
  use Illuminate\Support\Str;
  $routeName = request()->route()?->getName();
  $base = $routeName ? Str::of($routeName)->explode('.') ->take(2)->implode('.') : null;
  $title = $map[$base] ?? ($map[$routeName] ?? 'Enterprise Management Console');
@endphp

<div class="submodule-bar" role="navigation" aria-label="Submodule navigation">
  <div class="submodule-bar__container">
    <h1 class="submodule-bar__title">{{ $title }}</h1>
    <div class="submodule-bar__actions">
      <a href="{{ route('emc.model-html') }}" 
         target="_blank" 
         class="submodule-bar__link"
         rel="noopener noreferrer">
        Model
      </a>
      <span class="submodule-bar__separator" aria-hidden="true">|</span>
      <a href="{{ route('emc.layout-html') }}" 
         target="_blank" 
         class="submodule-bar__link"
         rel="noopener noreferrer">
        Word
      </a>
    </div>
  </div>
</div>
