@extends('emc.layout')

@section('title', 'Core Databases - EMC')

@section('content')
<style>
.tabs {
  margin-top: 1.5rem;
}
.tabs__nav {
  display: flex;
  border-bottom: 1px solid var(--color-border);
  margin-bottom: 1.5rem;
}
.tabs__link {
  padding: 0.5rem 1rem;
  margin-bottom: -1px;
  border: 1px solid transparent;
  color: var(--color-text-secondary);
  text-decoration: none;
}
.tabs__link--active {
  color: var(--color-text-primary);
  border-color: var(--color-border) var(--color-border) #fff var(--color-border);
  border-top-left-radius: 4px;
  border-top-right-radius: 4px;
}
.sub-section {
  margin-bottom: 2rem;
  padding: 1rem;
  border: 1px solid var(--color-border-light);
  border-radius: 4px;
  background-color: var(--color-background-light);
}
.sub-section__title {
  margin-top: 0;
  margin-bottom: 1rem;
  font-size: 1.1rem;
  border-bottom: 1px solid var(--color-border-light);
  padding-bottom: 0.5rem;
}
.sub-section__title strong {
    color: var(--color-primary);
}
</style>

<div class="content-frame">
  <div class="content">
    @if(session('status'))
      <div class="alert alert--success" style="margin-bottom: 1rem; padding: 0.75rem; background: #d4edda; border: 1px solid #c3e6cb; border-radius: 4px; color: #155724;">
        {{ session('status') }}
      </div>
    @endif


    <div class="tabs">
      <div class="tabs__content">
        @if($activeTab === 'registry')
          <div class="toolbar">
            <div class="toolbar__left">
              <h3 class="toolbar__title">Database Registry</h3>
            </div>
            <div class="toolbar__right">
              <button class="btn btn--primary" onclick="resetCoreDbForm(); openModal('coreDbModal')">+ Add Core Database</button>
              <button class="btn btn--secondary" onclick="refreshCoreDbs()">↻ Refresh</button>
            </div>
          </div>

          <div class="table-container" id="coreDbsTable">
            <table class="table">
              <thead class="table__header">
                <tr>
                  <th class="table__header-cell">Name</th>
                  <th class="table__header-cell">Environment</th>
                  <th class="table__header-cell">Platform</th>
                  <th class="table__header-cell">Owner</th>
                  <th class="table__header-cell">Lifecycle</th>
                  <th class="table__header-cell">Linked Connection</th>
                  <th class="table__header-cell">Status</th>
                  <th class="table__header-cell">Actions</th>
                </tr>
              </thead>
              <tbody id="coreDbRows">
                @forelse($coreDbs as $db)
                <tr class="table__row">
                  <td class="table__cell"><strong>{{ data_get($db,'name') }}</strong><div style="font-size: var(--font-size-xs); color: var(--color-text-secondary);">{{ data_get($db,'description') }}</div></td>
                  <td class="table__cell">{{ data_get($db,'environment') }}</td>
                  <td class="table__cell">{{ data_get($db,'platform') }}</td>
                  <td class="table__cell">{{ data_get($db,'owner') }}</td>
                  <td class="table__cell">{{ data_get($db,'lifecycle') }}</td>
                  <td class="table__cell">
                    @php($lc = data_get($db,'linked_connection'))
                    @if(!empty($lc) && isset($connectionByName) && $connectionByName->has($lc))
                      @php($conn = $connectionByName->get($lc))
                      <a href="{{ url('/emc/db?connection_id='.$conn->id) }}" title="Open Database Connection">{{ $lc }}</a>
                    @else
                      {{ $lc ?? '—' }}
                    @endif
                  </td>
                  @php($status = data_get($db,'status'))
                  @php($statusClass = $status === 'healthy' ? 'status--success' : ($status === 'warning' ? 'status--warning' : 'status--info'))
                  <td class="table__cell"><span class="status {{ $statusClass }}">{{ $status ? ucfirst($status) : 'N/A' }}</span></td>
                  <td class="table__cell">
                    <div class="table__actions">
                      <button class="btn btn--small btn--secondary" onclick='editCoreDb({!! json_encode($db->toArray(), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) !!})'>Edit</button>
                      @if(Route::has('emc.core.destroy'))
                      <form method="POST" action="{{ route('emc.core.destroy', $db) }}" onsubmit="return confirm('Delete this core database?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn--small btn--danger">Delete</button>
                      </form>
                      @endif
                    </div>
                  </td>
                </tr>
                @empty
                <tr><td class="table__cell" colspan="8" style="color: var(--color-text-secondary);">No core databases yet.</td></tr>
                @endforelse
              </tbody>
            </table>
          </div>

        @elseif($activeTab === 'ownership')
          @foreach($coreDbs as $db)
          <div class="sub-section">
            <h4 class="sub-section__title">Ownership: <strong>{{ $db->name }}</strong></h4>
            <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem;">
              <div>
                <table class="table">
                  <thead class="table__header">
                    <tr>
                      <th>Owner Name/Team</th>
                      <th>Role</th>
                      <th>Effective Date</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    @forelse($db->owners as $owner)
                    <tr>
                      <td>{{ $owner->owner_name }}</td>
                      <td>{{ $owner->role }}</td>
                      <td>{{ \Carbon\Carbon::parse($owner->effective_date)->format('Y-m-d') }}</td>
                      <td>
                        <form method="POST" action="{{ route('emc.core.owners.destroy', $owner) }}" onsubmit="return confirm('Delete this owner entry?')">
                          @csrf
                          @method('DELETE')
                          <button type="submit" class="btn btn--small btn--danger">Delete</button>
                        </form>
                      </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" style="color: var(--color-text-secondary);">No ownership records.</td></tr>
                    @endforelse
                  </tbody>
                </table>
              </div>
              <div>
                <h5>Add New Owner</h5>
                <form method="POST" action="{{ route('emc.core.owners.store') }}">
                  @csrf
                  <input type="hidden" name="core_database_id" value="{{ $db->id }}">
                  <div class="form__group">
                    <label class="form__label">Owner Name/Team</label>
                    <input name="owner_name" class="form__input" required>
                  </div>
                  <div class="form__group">
                    <label class="form__label">Role</label>
                    <input name="role" class="form__input" placeholder="e.g., Technical, Business" required>
                  </div>
                  <div class="form__group">
                    <label class="form__label">Effective Date</label>
                    <input type="date" name="effective_date" class="form__input" value="{{ date('Y-m-d') }}" required>
                  </div>
                  <button type="submit" class="btn btn--primary">Add Owner</button>
                </form>
              </div>
            </div>
          </div>
          @endforeach

        @elseif($activeTab === 'lifecycle')
          @foreach($coreDbs as $db)
          <div class="sub-section">
            <h4 class="sub-section__title">Lifecycle Events: <strong>{{ $db->name }}</strong></h4>
            <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem;">
              <div>
                <table class="table">
                  <thead class="table__header">
                    <tr>
                      <th>Event</th>
                      <th>Details</th>
                      <th>Effective Date</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    @forelse($db->lifecycleEvents as $event)
                    <tr>
                      <td>{{ $event->event_type }}</td>
                      <td>{{ $event->details }}</td>
                      <td>{{ \Carbon\Carbon::parse($event->effective_date)->format('Y-m-d') }}</td>
                      <td>
                        <form method="POST" action="{{ route('emc.core.lifecycle-events.destroy', $event) }}" onsubmit="return confirm('Delete this lifecycle event?')">
                          @csrf
                          @method('DELETE')
                          <button type="submit" class="btn btn--small btn--danger">Delete</button>
                        </form>
                      </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" style="color: var(--color-text-secondary);">No lifecycle events.</td></tr>
                    @endforelse
                  </tbody>
                </table>
              </div>
              <div>
                <h5>Add New Lifecycle Event</h5>
                <form method="POST" action="{{ route('emc.core.lifecycle-events.store') }}">
                  @csrf
                  <input type="hidden" name="core_database_id" value="{{ $db->id }}">
                  <div class="form__group">
                    <label class="form__label">Event Type</label>
                    <input name="event_type" class="form__input" placeholder="e.g., Created, Decommissioned" required>
                  </div>
                  <div class="form__group">
                    <label class="form__label">Details</label>
                    <textarea name="details" class="form__textarea" rows="2"></textarea>
                  </div>
                  <div class="form__group">
                    <label class="form__label">Effective Date</label>
                    <input type="date" name="effective_date" class="form__input" value="{{ date('Y-m-d') }}" required>
                  </div>
                  <button type="submit" class="btn btn--primary">Add Event</button>
                </form>
              </div>
            </div>
          </div>
          @endforeach

        @elseif($activeTab === 'links')
          @foreach($coreDbs as $db)
          <div class="sub-section">
            <h4 class="sub-section__title">Linked Connections & Policies: <strong>{{ $db->name }}</strong></h4>
            <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem;">
              <div>
                <table class="table">
                  <thead class="table__header">
                    <tr>
                      <th>Linked Connection</th>
                      <th>Link Type</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    @forelse($db->links as $link)
                    <tr>
                      <td>
                        @if($link->databaseConnection)
                          <a href="{{ url('/emc/db?connection_id='.$link->databaseConnection->id) }}" title="Open Database Connection">{{ $link->databaseConnection->name }}</a>
                        @else
                          {{ $link->linked_connection_name }}
                        @endif
                      </td>
                      <td>{{ $link->link_type }}</td>
                      <td>
                        <form method="POST" action="{{ route('emc.core.links.destroy', $link) }}" onsubmit="return confirm('Delete this link?')">
                          @csrf
                          @method('DELETE')
                          <button type="submit" class="btn btn--small btn--danger">Delete</button>
                        </form>
                      </td>
                    </tr>
                    @empty
                    <tr><td colspan="3" style="color: var(--color-text-secondary);">No links.</td></tr>
                    @endforelse
                  </tbody>
                </table>
              </div>
              <div>
                <h5>Add New Link</h5>
                <form method="POST" action="{{ route('emc.core.links.store') }}">
                  @csrf
                  <input type="hidden" name="core_database_id" value="{{ $db->id }}">
                  <div class="form__group">
                    <label class="form__label">Linked Connection Name</label>
                    <input name="linked_connection_name" class="form__input" list="connectionSuggestions" required>
                  </div>
                  <div class="form__group">
                    <label class="form__label">Link Type</label>
                    <input name="link_type" class="form__input" placeholder="e.g., Primary, Replica, Policy" required>
                  </div>
                  <button type="submit" class="btn btn--primary">Add Link</button>
                </form>
              </div>
            </div>
          </div>
          @endforeach
        @endif
      </div>
    </div>

    <!-- Modal for create/edit -->
    <div class="modal" id="coreDbModal" aria-hidden="true" role="dialog" aria-labelledby="coreDbModalTitle">
      <div class="modal__backdrop" onclick="closeModal('coreDbModal')"></div>
      <div class="modal__container" role="document">
        <div class="modal__header">
          <h3 class="modal__title" id="coreDbModalTitle">Add Core Database</h3>
          <button class="modal__close" aria-label="Close" onclick="closeModal('coreDbModal')">×</button>
        </div>
        <div class="modal__body">
          <form id="coreDbForm" method="POST" action="{{ route('emc.core.store') }}">
            @csrf
            <input type="hidden" id="coreDbForm__method" name="_method" value="POST" />
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: var(--space-md);">
              <div class="form__group">
                <label class="form__label form__label--required">Name</label>
                <input name="name" class="form__input" required />
              </div>
              <div class="form__group">
                <label class="form__label form__label--required">Environment</label>
                <select name="environment" class="form__select" required>
                  <option value="Production">Production</option>
                  <option value="Staging">Staging</option>
                  <option value="Development">Development</option>
                </select>
              </div>
              <div class="form__group">
                <label class="form__label form__label--required">Platform</label>
                <select name="platform" class="form__select" required>
                  <option value="MySQL">MySQL</option>
                  <option value="PostgreSQL">PostgreSQL</option>
                  <option value="SQL Server">SQL Server</option>
                  <option value="MongoDB">MongoDB</option>
                </select>
              </div>
              <div class="form__group">
                <label class="form__label">Owner</label>
                <input name="owner" class="form__input" placeholder="Team or Person" />
              </div>
              <div class="form__group">
                <label class="form__label">Lifecycle</label>
                <select name="lifecycle" class="form__select">
                  <option value="Long-lived">Long-lived</option>
                  <option value="Temporary">Temporary</option>
                  <option value="Archived">Archived</option>
                </select>
              </div>
              <div class="form__group">
                <label class="form__label">Linked Connection</label>
                <input name="linked_connection" class="form__input" placeholder="database_connections.name" list="connectionSuggestions" />
                <datalist id="connectionSuggestions">
                  <!-- Will be populated by JavaScript -->
                </datalist>
              </div>
            </div>
            <div class="form__group">
              <label class="form__label">Description</label>
              <textarea name="description" class="form__textarea" rows="3" placeholder="Purpose, usage scope, SLAs..."></textarea>
            </div>
          </form>
        </div>
        <div class="modal__footer">
          <button class="btn" onclick="resetCoreDbForm(); closeModal('coreDbModal')">Cancel</button>
          <button class="btn btn--primary" onclick="document.getElementById('coreDbForm').submit()">Save</button>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
  function refreshCoreDbs(){ location.reload(); }
  function setSelectValue(selectEl, value){ if(!selectEl) return; for(const o of selectEl.options){ if(o.value===value){ o.selected = true; break; } } }
  
  function populateConnectionSuggestions() {
    // Fetch database connections for auto-suggest
    fetch('/api/v1/companies/1/database-connections', {
      headers: { 'Accept': 'application/json' }
    })
    .then(response => response.ok ? response.json() : null)
    .then(data => {
      const datalist = document.getElementById('connectionSuggestions');
      if (data && data.data && datalist) {
        datalist.innerHTML = '';
        data.data.forEach(conn => {
          const option = document.createElement('option');
          option.value = conn.name || conn.id;
          datalist.appendChild(option);
        });
      }
    })
    .catch(() => {}); // Silent fail if API not available
  }
  
  function editCoreDb(row){
    const form = document.getElementById('coreDbForm');
    const title = document.getElementById('coreDbModalTitle');
    const method = document.getElementById('coreDbForm__method');
    if(!row || !row.id){ return; }
    title.textContent = 'Edit Core Database';
    form.action = '{{ route('emc.core.index') }}' + '/' + row.id;
    method.value = 'PATCH';
    form.querySelector('[name="name"]').value = row.name || '';
    setSelectValue(form.querySelector('[name="environment"]'), row.environment || '');
    setSelectValue(form.querySelector('[name="platform"]'), row.platform || '');
    form.querySelector('[name="owner"]').value = row.owner || '';
    setSelectValue(form.querySelector('[name="lifecycle"]'), row.lifecycle || '');
    form.querySelector('[name="linked_connection"]').value = row.linked_connection || '';
    form.querySelector('[name="description"]').value = row.description || '';
    openModal('coreDbModal');
  }
  
  function resetCoreDbForm(){
    const form = document.getElementById('coreDbForm');
    const method = document.getElementById('coreDbForm__method');
    const title = document.getElementById('coreDbModalTitle');
    form.action = '{{ route('emc.core.store') }}';
    method.value = 'POST';
    title.textContent = 'Add Core Database';
    form.reset();
  }
  
  // Initialize on page load
  document.addEventListener('DOMContentLoaded', function() {
    populateConnectionSuggestions();
  });
</script>
@endsection
