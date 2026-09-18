@extends('layout.admin')

@section('title', 'Candidate Messaging')

@section('content')
<div class="container-fluid">

    @if($errors->any())
        <div class="alert alert-danger">
            <strong>There was a problem:</strong>
            <ul style="margin-top:8px;">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
    @if(session('error')) <div class="alert alert-danger">{{ session('error') }}</div> @endif

    <section class="content">
        <div class="cm-card">
            <div class="cm-card__header">
                <div>
                    <h3 class="cm-title">Candidate Messaging</h3>
                    <p class="cm-subtitle">Filter and select recipients. Your selection stays while you search and filter.</p>
                </div>
                <div class="cm-header-actions">
                    <span class="cm-badge">
                        Selected: <span id="selectedCount">0</span>
                    </span>
                    <button type="button" class="btn btn-danger btn-sm" id="clearAllGlobalBtn">
                        Clear all selected
                    </button>
                </div>
            </div>

            @php
                $isAdminOnlyType = in_array(($filters['userType'] ?? 'candidate'), ['siteuser', 'superuser']);
                $currentSortBy = $filters['sort_by'] ?? 'name';
                $currentSortDir = $filters['sort_dir'] ?? 'asc';

                $nextDir = function($col) use ($currentSortBy, $currentSortDir) {
                    if ($currentSortBy !== $col) return 'asc';
                    return $currentSortDir === 'asc' ? 'desc' : 'asc';
                };
            @endphp

            {{-- Filters --}}
            <form method="GET" action="{{ route('candidateMessaging.index') }}" class="cm-filters">
                <div class="cm-filter">
                    <label>User Type</label>
                    <select name="userType" id="userTypeFilter" class="form-control">
                        <option value="candidate" {{ ($filters['userType'] ?? '') === 'candidate' ? 'selected' : '' }}>Candidate</option>
                        <option value="siteuser" {{ ($filters['userType'] ?? '') === 'siteuser' ? 'selected' : '' }}>Site Admin</option>
                        <option value="superuser" {{ ($filters['userType'] ?? '') === 'superuser' ? 'selected' : '' }}>Super Admin</option>
                        <option value="all" {{ ($filters['userType'] ?? '') === 'all' ? 'selected' : '' }}>All</option>
                    </select>
                </div>

                <div class="cm-filter">
                    <label>Organisation</label>
                    <select name="organisation_id" class="form-control">
                        <option value="">All Organisations</option>
                        @foreach($organisations as $org)
                            <option value="{{ $org->id }}" {{ (string)($filters['organisation_id'] ?? '') === (string)$org->id ? 'selected' : '' }}>
                                {{ $org->organisationName }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="cm-filter">
                    <label>Application Status</label>
                    <select name="application_status" id="statusFilter" class="form-control" {{ $isAdminOnlyType ? 'disabled' : '' }}>
                        <option value="">All</option>
                        <option value="pending_registration" {{ ($filters['application_status'] ?? '') === 'pending_registration' ? 'selected' : '' }}>Pending Registration</option>
                        <option value="in_progress" {{ ($filters['application_status'] ?? '') === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="complete" {{ ($filters['application_status'] ?? '') === 'complete' ? 'selected' : '' }}>Complete</option>
                    </select>
                    @if($isAdminOnlyType)
                        <small class="text-muted">Not applicable for admin user types.</small>
                    @endif
                </div>

                <div class="cm-filter cm-filter--search">
                    <label>Search</label>
                    <input type="text" name="search" class="form-control" placeholder="Name / email / phone" value="{{ $filters['search'] ?? '' }}">
                </div>

                <input type="hidden" name="sort_by" value="{{ $filters['sort_by'] ?? 'name' }}">
                <input type="hidden" name="sort_dir" value="{{ $filters['sort_dir'] ?? 'asc' }}">

                <div class="cm-filter cm-filter--button">
                    <label class="cm-hidden-label">Filter</label>
                    <button type="submit" class="btn cm-btn-primary btn-block">Apply</button>
                </div>
            </form>

            {{-- Recipient table + continue --}}
            <form method="POST" action="{{ route('candidateMessaging.compose') }}" id="recipientForm">
                @csrf

                <div class="table-responsive cm-table-wrap">
                    <table class="table table-bordered table-striped cm-table">
                        <thead>
                            <tr>
                                <th style="width:60px;">Select</th>
                                <th>
                                    <a class="cm-sort"
                                       href="{{ route('candidateMessaging.index', array_merge(request()->query(), ['sort_by' => 'name', 'sort_dir' => $nextDir('name')])) }}">
                                        Name
                                        @if($currentSortBy === 'name') <i class="fa fa-sort-{{ $currentSortDir === 'asc' ? 'asc' : 'desc' }}"></i> @endif
                                    </a>
                                </th>
                                <th>
                                    <a class="cm-sort"
                                       href="{{ route('candidateMessaging.index', array_merge(request()->query(), ['sort_by' => 'email', 'sort_dir' => $nextDir('email')])) }}">
                                        Email
                                        @if($currentSortBy === 'email') <i class="fa fa-sort-{{ $currentSortDir === 'asc' ? 'asc' : 'desc' }}"></i> @endif
                                    </a>
                                </th>
                                <th>
                                    <a class="cm-sort"
                                       href="{{ route('candidateMessaging.index', array_merge(request()->query(), ['sort_by' => 'phone', 'sort_dir' => $nextDir('phone')])) }}">
                                        Phone
                                        @if($currentSortBy === 'phone') <i class="fa fa-sort-{{ $currentSortDir === 'asc' ? 'asc' : 'desc' }}"></i> @endif
                                    </a>
                                </th>
                                <th>
                                    <a class="cm-sort"
                                       href="{{ route('candidateMessaging.index', array_merge(request()->query(), ['sort_by' => 'organisation', 'sort_dir' => $nextDir('organisation')])) }}">
                                        Organisation
                                        @if($currentSortBy === 'organisation') <i class="fa fa-sort-{{ $currentSortDir === 'asc' ? 'asc' : 'desc' }}"></i> @endif
                                    </a>
                                </th>
                                <th>
                                    <a class="cm-sort"
                                       href="{{ route('candidateMessaging.index', array_merge(request()->query(), ['sort_by' => 'status', 'sort_dir' => $nextDir('status')])) }}">
                                        Status
                                        @if($currentSortBy === 'status') <i class="fa fa-sort-{{ $currentSortDir === 'asc' ? 'asc' : 'desc' }}"></i> @endif
                                    </a>
                                </th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($candidates as $row)
                                @php
                                    $recipientKey = $row->source_type . ':' . $row->source_id;
                                    $status = $row->application_status_filter ?? null;
                                @endphp
                                <tr>
                                    <td class="cm-check-cell">
                                        <input type="checkbox" class="row-checkbox" value="{{ $recipientKey }}">
                                    </td>
                                    <td>{{ trim(($row->forename ?? '') . ' ' . ($row->surname ?? '')) }}</td>
                                    <td>{{ $row->email ?? '-' }}</td>
                                    <td>{{ $row->phone ?: '-' }}</td>
                                    <td>{{ $row->organisationName ?? '-' }}</td>
                                    <td>
                                        @if($status === 'pending_registration')
                                            <span class="label label-warning">Pending</span>
                                        @elseif($status === 'in_progress')
                                            <span class="label label-primary">In progress</span>
                                        @elseif($status === 'complete')
                                            <span class="label label-success">Complete</span>
                                        @else
                                            <span class="label label-default">N/A</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="6">No users found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($candidates->total() > 0)
                    <div class="cm-pagination">
                        <div class="text-muted">
                            Showing {{ $candidates->firstItem() }} to {{ $candidates->lastItem() }} of {{ $candidates->total() }}
                            (Page {{ $candidates->currentPage() }} of {{ $candidates->lastPage() }})
                        </div>
                        <div>
                            {{ $candidates->links() }}
                        </div>
                    </div>
                @endif

                {{-- JS injects selected_users[] here before submit --}}
                <div id="selectedUsersHiddenContainer"></div>

                <div class="cm-footer">
                    <button type="submit" class="btn cm-btn-primary">
                        Continue with selected users
                    </button>
                </div>
            </form>
        </div>
    </section>
</div>
@endsection

@section('pageCSS')
<style>
/* Modern card */
.cm-card{
    background:#fff;
    border-radius:14px;
    box-shadow:0 10px 30px rgba(16,24,40,0.06);
    padding:18px;
}
.cm-card__header{
    display:flex;
    align-items:flex-start;
    justify-content:space-between;
    gap:12px;
    border-bottom:1px solid #eef2f7;
    padding-bottom:14px;
    margin-bottom:14px;
}
.cm-title{ margin:0; color:#2C3C64; font-weight:700; }
.cm-subtitle{ margin:6px 0 0; color:#6b7280; }
.cm-header-actions{ display:flex; align-items:center; gap:10px; flex-wrap:wrap; }
.cm-badge{
    display:inline-flex; align-items:center; gap:6px;
    padding:6px 10px; border-radius:999px;
    background:#eef2ff; color:#2C3C64; font-weight:600;
}
.cm-filters{
    display:flex; flex-wrap:wrap; gap:12px;
    padding:12px; border:1px solid #eef2f7; border-radius:12px;
    margin-bottom:14px;
}
.cm-filter{ flex:1 1 180px; min-width:180px; }
.cm-filter--search{ flex:2 1 260px; min-width:260px; }
.cm-filter--button{ flex:0 0 140px; min-width:140px; }
.cm-filter label{ color:#2C3C64; font-weight:600; font-size:13px; }
.cm-hidden-label{ visibility:hidden; }

.cm-btn-primary{
    background:#C55359; color:#fff; border:none;
    padding:10px 14px; border-radius:10px; font-weight:700;
}
.cm-btn-primary:hover{ opacity:0.92; color:#fff; }

.cm-table-wrap{ border-radius:12px; overflow:hidden; border:1px solid #eef2f7; }
.cm-table{ margin-bottom:0; }
.cm-table thead th{ background:#fbfcff; }
.cm-sort{ color:#2C3C64; text-decoration:none; font-weight:700; }
.cm-sort:hover{ color:#C55359; }
.cm-check-cell{ text-align:center; vertical-align:middle; }
.cm-check-cell input{ transform:scale(1.15); }

.cm-pagination{
    display:flex; justify-content:space-between; align-items:center;
    gap:12px; margin-top:12px; flex-wrap:wrap;
}
.cm-footer{
    display:flex; justify-content:flex-end;
    margin-top:14px; padding-top:14px; border-top:1px solid #eef2f7;
}
@media (max-width:768px){
    .cm-card{ padding:14px; }
    .cm-filter--button{ flex:1 1 100%; min-width:100%; }
}
</style>
@endsection

@section('pageJavascript')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const STORAGE_KEY = 'candidateMessagingSelectedUsers_v1';

    const userType = document.getElementById('userTypeFilter');
    const statusFilter = document.getElementById('statusFilter');

    const clearAllGlobalBtn = document.getElementById('clearAllGlobalBtn');
    const countEl = document.getElementById('selectedCount');
    const form = document.getElementById('recipientForm');
    const hiddenContainer = document.getElementById('selectedUsersHiddenContainer');

    function rowBoxes() {
        return Array.from(document.querySelectorAll('.row-checkbox'));
    }

    function getSelectedSet() {
        try {
            const parsed = JSON.parse(localStorage.getItem(STORAGE_KEY) || '[]');
            return new Set(Array.isArray(parsed) ? parsed : []);
        } catch (e) {
            return new Set();
        }
    }

    function saveSelectedSet(setObj) {
        localStorage.setItem(STORAGE_KEY, JSON.stringify(Array.from(setObj)));
    }

    function syncStatusEnabled() {
        const v = userType.value;
        const disable = (v === 'siteuser' || v === 'superuser');
        statusFilter.disabled = disable;
        if (disable) statusFilter.value = '';
    }

    function renderSelectionsOnCurrentPage() {
        const selected = getSelectedSet();
        rowBoxes().forEach(cb => cb.checked = selected.has(cb.value));
    }

    function updateSelectedCount() {
        countEl.textContent = getSelectedSet().size;
    }

    function toggleSingleSelection(value, isChecked) {
        const selected = getSelectedSet();
        if (isChecked) selected.add(value);
        else selected.delete(value);
        saveSelectedSet(selected);
        updateSelectedCount();
    }

    // init
    if (userType && statusFilter) syncStatusEnabled();
    renderSelectionsOnCurrentPage();
    updateSelectedCount();

    if (userType) userType.addEventListener('change', syncStatusEnabled);

    if (clearAllGlobalBtn) {
        clearAllGlobalBtn.addEventListener('click', function () {
            localStorage.removeItem(STORAGE_KEY);
            renderSelectionsOnCurrentPage();
            updateSelectedCount();
        });
    }

    document.addEventListener('change', function (e) {
        if (e.target.classList.contains('row-checkbox')) {
            toggleSingleSelection(e.target.value, e.target.checked);
        }
    });

    // submit persistent set
    if (form) {
        form.addEventListener('submit', function (e) {
            const selected = Array.from(getSelectedSet());

            if (selected.length === 0) {
                e.preventDefault();
                alert('Please select at least one user.');
                return;
            }

            hiddenContainer.innerHTML = '';
            selected.forEach(key => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'selected_users[]';
                input.value = key;
                hiddenContainer.appendChild(input);
            });
        });
    }

    @if(session('success'))
        localStorage.removeItem(STORAGE_KEY);
        renderSelectionsOnCurrentPage();
        updateSelectedCount();
    @endif
});
</script>
@endsection