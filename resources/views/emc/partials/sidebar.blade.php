<h2 class="sidebar__title">Submenu</h2>
<nav aria-label="Sub-navigation">
  <ul class="sidebar__nav" role="list">
    @php($route = request()->route()?->getName())
    @switch($route)
      @case('emc.core.index')
        <li class="sidebar__nav-item">
          <a href="{{ route('emc.core.index') }}" class="sidebar__nav-link {{ request('tab','registry')==='registry' ? 'sidebar__nav-link--active' : '' }}" aria-current="page">Registry</a>
        </li>
        <li class="sidebar__nav-item">
          <a href="{{ route('emc.core.index', ['tab' => 'ownership']) }}" class="sidebar__nav-link {{ request('tab')==='ownership' ? 'sidebar__nav-link--active' : '' }}">Ownership</a>
        </li>
        <li class="sidebar__nav-item">
          <a href="{{ route('emc.core.index', ['tab' => 'lifecycle']) }}" class="sidebar__nav-link {{ request('tab')==='lifecycle' ? 'sidebar__nav-link--active' : '' }}">Lifecycle</a>
        </li>
        <li class="sidebar__nav-item">
          <a href="{{ route('emc.core.index', ['tab' => 'links']) }}" class="sidebar__nav-link {{ request('tab')==='links' ? 'sidebar__nav-link--active' : '' }}">Linked Connections</a>
        </li>
        @break
      @case('emc.db')
        <li class="sidebar__nav-item">
          <a href="{{ route('emc.db') }}" class="sidebar__nav-link {{ request()->is('emc/db') && !request()->has('submenu') ? 'sidebar__nav-link--active' : '' }}" aria-current="page">Connections</a>
        </li>
        <li class="sidebar__nav-item">
          <a href="{{ route('emc.db', ['submenu' => 'backup']) }}" class="sidebar__nav-link {{ request()->get('submenu') === 'backup' ? 'sidebar__nav-link--active' : '' }}">Backup & Restore</a>
        </li>
        <li class="sidebar__nav-item">
          <a href="{{ route('emc.db', ['submenu' => 'performance']) }}" class="sidebar__nav-link {{ request()->get('submenu') === 'performance' ? 'sidebar__nav-link--active' : '' }}">Performance Monitor</a>
        </li>
        <li class="sidebar__nav-item">
          <a href="{{ route('emc.db', ['submenu' => 'query']) }}" class="sidebar__nav-link {{ request()->get('submenu') === 'query' ? 'sidebar__nav-link--active' : '' }}">Query Runner</a>
        </li>
        <li class="sidebar__nav-item">
          <a href="{{ route('emc.db', ['submenu' => 'replication']) }}" class="sidebar__nav-link {{ request()->get('submenu') === 'replication' ? 'sidebar__nav-link--active' : '' }}">Replication & Clustering</a>
        </li>
        @break
      @case('emc.tables')
        <li class="sidebar__nav-item">
          <a href="#" class="sidebar__nav-link sidebar__nav-link--active" aria-current="page">Table List</a>
        </li>
        <li class="sidebar__nav-item">
          <a href="#" class="sidebar__nav-link">Create Table</a>
        </li>
        <li class="sidebar__nav-item">
          <a href="#" class="sidebar__nav-link">Relations</a>
        </li>
        <li class="sidebar__nav-item">
          <a href="#" class="sidebar__nav-link">Import</a>
        </li>
        @break
      @case('emc.files')
        <li class="sidebar__nav-item">
          <a href="#" class="sidebar__nav-link sidebar__nav-link--active" aria-current="page">Find Files</a>
        </li>
        <li class="sidebar__nav-item">
          <a href="#" class="sidebar__nav-link">Create File</a>
        </li>
        <li class="sidebar__nav-item">
          <a href="#" class="sidebar__nav-link">Create Folder</a>
        </li>
        @break
      @case('emc.users')
        <li class="sidebar__nav-item">
          <a href="#" class="sidebar__nav-link sidebar__nav-link--active" aria-current="page">Browse</a>
        </li>
        <li class="sidebar__nav-item">
          <a href="#" class="sidebar__nav-link">Create User</a>
        </li>
        @break
      @case('emc.reports')
        <li class="sidebar__nav-item">
          <a href="#" class="sidebar__nav-link sidebar__nav-link--active" aria-current="page">Summaries</a>
        </li>
        <li class="sidebar__nav-item">
          <a href="#" class="sidebar__nav-link">Designer</a>
        </li>
        @break
      @case('emc.ai')
        <li class="sidebar__nav-item">
          <a href="#" class="sidebar__nav-link sidebar__nav-link--active" aria-current="page">Providers</a>
        </li>
        <li class="sidebar__nav-item">
          <a href="#" class="sidebar__nav-link">Conversations</a>
        </li>
        @break
      @case('emc.comms')
        <li class="sidebar__nav-item">
          <a href="#" class="sidebar__nav-link sidebar__nav-link--active" aria-current="page">Mailboxes</a>
        </li>
        <li class="sidebar__nav-item">
          <a href="#" class="sidebar__nav-link">Chat</a>
        </li>
        <li class="sidebar__nav-item">
          <a href="#" class="sidebar__nav-link">Spreadsheet</a>
        </li>
        <li class="sidebar__nav-item">
          <a href="#" class="sidebar__nav-link">Documents</a>
        </li>
        @break
      @case('emc.settings')
        <li class="sidebar__nav-item">
          <a href="#" class="sidebar__nav-link sidebar__nav-link--active" aria-current="page">Themes</a>
        </li>
        <li class="sidebar__nav-item">
          <a href="#" class="sidebar__nav-link">Layout</a>
        </li>
        @break
      @case('emc.about')
        <li class="sidebar__nav-item">
          <a href="#" class="sidebar__nav-link sidebar__nav-link--active" aria-current="page">Overview</a>
        </li>
        @break
      @default
        <li class="sidebar__nav-item">
          <a href="#" class="sidebar__nav-link sidebar__nav-link--active" aria-current="page">Overview</a>
        </li>
    @endswitch
  </ul>
</nav>
