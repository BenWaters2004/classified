@extends('layout.admin')

@section('title', 'Organisation Settings')

@section('content')
@inject('checkAccess', 'App\Http\Controllers\Controller')

@if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

{{-- Delete modal (existing) --}}
<div class="modal modal-warning" id="deleteModal" data-id="0" data-type="">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">×</span></button>
        <h4 class="modal-title">WARNING</h4>
      </div>
      <div class="modal-body">
        <p id="deleteModalMessage">Are you sure you want to delete this item?</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline pull-left" id="confirmDelete">YES</button>
        <button type="button" class="btn btn-outline pull-left" data-dismiss="modal">No</button>
      </div>
    </div>
  </div>
</div>

{{-- ✅ Add Organisation modal --}}
@if ($checkAccess->checkAccess('superuser'))
<div class="modal fade" id="addOrgModal" tabindex="-1" role="dialog" aria-labelledby="addOrgModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document" style="max-width: 520px;">
    <div class="modal-content" style="border-radius: 10px;">
      <div class="modal-header" style="background:#2C3C64;color:#fff;border-top-left-radius:10px;border-top-right-radius:10px;">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color:white;opacity:1;">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title" id="addOrgModalLabel" style="font-weight:700;">
            <i class="fa fa-plus"></i> Add New Organisation
        </h4>
      </div>

      <form action="{{ url('/admin/saveOrganisation') }}" method="POST">
        @csrf
        <div class="modal-body" style="padding: 18px 20px;">
            <div class="form-group">
                <label for="organisationName" style="color:#C55359;">Organisation Name</label>
                <input type="text" class="form-control" name="organisationName" id="organisationName" required maxlength="255" placeholder="e.g. Acme Ltd">
            </div>

            <div class="form-group">
                <label for="organisationEmail" style="color:#C55359;">Organisation Email</label>
                <p class="text-muted" style="margin-bottom:8px;">
                    Used for organisation notifications / designated email list.
                </p>
                <input type="email" class="form-control" name="organisationEmail" id="organisationEmail" required maxlength="255" placeholder="e.g. hr@acme.co.uk">
            </div>

            <div class="alert alert-info" style="margin: 0;">
                <i class="fa fa-info-circle"></i>
                This will create the organisation with default security settings (MFA enabled).
            </div>
        </div>

        <div class="modal-footer" style="padding: 12px 20px;">
          <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn forceBgClassified">
            <i class="fa fa-check"></i> Create Organisation
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
@endif

<div class="container-fluid py-4">
    <div class="settings-card">

        <!-- Header Row -->
        <div class="settings-header">
            <h2>Organisation Settings</h2>

            <div class="header-right">
                <select id="orgSwitcher" class="form-control">
                    @foreach ($organisations as $org)
                        <option value="{{ $org->id }}" {{ $org->id == $selectedOrgId ? 'selected' : '' }}>
                            {{ $org->organisationName }}
                        </option>
                    @endforeach
                </select>

                @if ($checkAccess->checkAccess('superuser'))
                    <button type="button" class="btn forceBgClassified" data-toggle="modal" data-target="#addOrgModal">
                        <i class="fa fa-plus"></i> Add New Organisation
                    </button>
                @endif
            </div>
        </div>

        <!-- Tabs -->
        <div class="tabs">
            <button class="tab-button active" data-tab="general">General</button>
            <button class="tab-button" data-tab="emails">Emails</button>
            <button class="tab-button" data-tab="users">Users</button>
        </div>

        <!-- Tab Content -->
        <div class="tab-content">
            <div id="general" class="tab-pane active">
                <div class="tab-content-section">
                    @if (in_array('superuser', $currentUserRoles))
                        <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#deleteModal" data-orgid="{{ $selectedOrgId }}" title="Delete Organisation" id="deleteOrgButton">
                            <i class="fa fa-times"></i> Delete Organisation
                        </button>
                    @endif

                    <form action="{{ env('APP_URL') }}admin/settings/organisation/update" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="organisationID" value="{{ $selectedOrgId }}">

                        <div class="form-group">
                            <label for="organisationName">Organisation Name</label>
                            <p class="text-muted">Please contact screening@thinkbitgroup.co.uk if you would like to change the organisation name.</p>
                            <input type="text" name="organisationName" id="organisationName" class="form-control name-width-limit"
                                value="{{ $organisationDetails->organisationName ?? '' }}"
                                {{ in_array('superuser', $currentUserRoles) ? '' : 'disabled' }}>
                        </div>

                        @if ($checkAccess->checkAccess('superuser'))
                            <br>
                            <div class="form-group">
                                <label for="mfaAuthUser">MFA for Candidates</label>
                                <select name="mfaAuthUser" id="mfaAuthUser" class="form-control name-width-limit">
                                    <option value="1" {{ $organisationDetails->mfaAuthUser == 1 ? 'selected' : '' }}>Yes</option>
                                    <option value="0" {{ $organisationDetails->mfaAuthUser == 0 ? 'selected' : '' }}>No</option>
                                </select>
                            </div>
                        @endif

                        <br>

                        <div class="form-group">
                            <label for="logo">Organisation Logo (optional)</label>
                            <p><i class="fa-solid fa-circle-info"></i> This logo will be displayed on reports and candidate pages, we recommend using a logo with a transparent background. By default, the Get ClassifIeD logo will be used.</p>

                            <div class="d-flex gap-4 flex-wrap logo-options">
                                {{-- Default logo --}}
                                <div id="defaultLogoBox" class="logo-box selectable-logo {{ empty($organisationDetails->logo) ? 'selected' : '' }}" data-type="default">
                                    <img src="{{ env('APP_URL') }}images/logo_{{ str_replace(' ', '', strtolower(env('APP_NAME'))) }}.webp" alt="Default Logo">
                                    <span class="checkmark"><i class="fa-solid fa-check"></i></span>
                                    <p class="text-center mt-2">Default</p>
                                </div>

                                {{-- Upload area --}}
                                <div id="customLogoBox" class="logo-box selectable-logo {{ !empty($organisationDetails->logo) ? 'selected' : '' }}" data-type="custom">
                                    <img id="customLogoPreview"
                                        src="{{ !empty($organisationDetails->logo) ? url('/admin/logo/' . $organisationDetails->logo) : '' }}"
                                        alt="Custom Logo"
                                        style="{{ empty($organisationDetails->logo) ? 'display: none;' : '' }}">
                                    <div id="uploadPlaceholder" class="text-center text-muted" style="{{ empty($organisationDetails->logo) ? 'padding-top: 3rem;' : 'display:none;' }}">
                                        Click to upload
                                    </div>
                                    <span class="checkmark"><i class="fa-solid fa-check"></i></span>
                                    <p class="text-center mt-2">Your Logo</p>
                                </div>
                            </div>

                            <!-- Inputs -->
                            <input type="file" name="logo" id="logo" class="d-none" accept="image/*">
                            <input type="hidden" name="deleteLogo" id="deleteLogo" value="0">
                        </div>

                        {{-- Brand colour --}}
                        @php
                            $brandHex = strtoupper($organisationDetails->brand_color ?? '#C55359');
                            $brandHex = \Illuminate\Support\Str::startsWith($brandHex, '#') ? $brandHex : '#'.$brandHex;

                            $brandPalette = [
                                '#C55359','#2C3C64','#0D6EFD','#28A745','#17A2B8',
                                '#6F42C1','#FF9800','#E91E63','#00A86B','#222222',
                            ];
                        @endphp

                        <div class="form-group">
                            <label for="brand_color_picker">Brand colour</label>
                            <p class="text-muted">Used across the candidate portal (buttons, focus, accents). Choose a preset or pick a custom colour.</p>

                            <div class="brand-swatch-grid">
                                @foreach ($brandPalette as $hex)
                                    <button type="button"
                                            class="brand-swatch {{ $brandHex === $hex ? 'selected' : '' }}"
                                            data-hex="{{ $hex }}"
                                            style="--swatch: {{ $hex }};"
                                            aria-label="Use {{ $hex }}"
                                            title="{{ $hex }}">
                                    </button>
                                @endforeach

                                <label class="brand-custom" title="Choose a custom colour">
                                    <input type="color" id="brand_color_picker" value="{{ $brandHex }}" aria-label="Custom colour picker">
                                    <span>Custom</span>
                                </label>
                            </div>

                            <div class="brand-preview">
                                <span class="chip" style="background: {{ $brandHex }}"></span>
                                <code id="brand_hex_preview">{{ $brandHex }}</code>
                            </div>

                            <input type="hidden" name="brand_color" id="brand_color" value="{{ $brandHex }}">
                        </div>

                        @if ($checkAccess->checkAccess('superuser'))
                            @php
                                $appTypeOptions = [
                                    'DBS' => 'UK Criminal Record (Basic DBS, England & Wales)',
                                    'BPSS' => 'Baseline Personnel Security Standard (BPSS)',
                                    'YOTI' => 'Digital Identity Check (YOTI)',
                                    'BPSS_REVAL_ONSITE' => 'BPSS Reval onsite',
                                    'BPSS_REVAL_OFFSITE' => 'BPSS Reval offsite',
                                ];
                                $selectedAppTypes = $organisationDetails->allowed_app_types ? json_decode($organisationDetails->allowed_app_types, true) : [];
                            @endphp

                            <br>
                            <div class="form-group">
                                <label>Allowed Application Types</label>
                                <p class="text-muted">Controls which application types this organisation can access.</p>

                                <div class="app-types-columns">
                                    @foreach ($appTypeOptions as $value => $label)
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="allowedAppTypes[]" value="{{ $value }}" id="appType_{{ $value }}" {{ in_array($value, $selectedAppTypes, true) ? 'checked' : '' }}>
                                            <label class="form-check-label allowedChecks" for="appType_{{ $value }}">{{ $label }}</label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                        <br>

                        <div class="form-group">
                            <label>Renewal & Data Retention</label>
                            <p class="text-muted">
                                Configure how often screening should be renewed and how long applicant
                                information should be retained for this organisation.
                            </p>

                            <div class="row renewal-settings">

                                <div class="col-md-4">
                                    <div class="renewal-setting-card">
                                        <label for="permanent_employee_renewal_years">
                                            Permanent Employee Renewal Period
                                        </label>

                                        <div class="input-group">
                                            <input
                                                type="number"
                                                name="permanent_employee_renewal_years"
                                                id="permanent_employee_renewal_years"
                                                class="form-control"
                                                min="1"
                                                max="100"
                                                step="1"
                                                value="{{ old(
                                                    'permanent_employee_renewal_years',
                                                    $organisationDetails->permanent_employee_renewal_years ?? 10
                                                ) }}"
                                                required
                                            >

                                            <span class="input-group-addon">years</span>
                                        </div>

                                        <p class="help-block">
                                            How often permanent employees should undergo renewal screening.
                                            Default: 10 years.
                                        </p>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="renewal-setting-card">
                                        <label for="contractor_renewal_years">
                                            Contractor Renewal Period
                                        </label>

                                        <div class="input-group">
                                            <input
                                                type="number"
                                                name="contractor_renewal_years"
                                                id="contractor_renewal_years"
                                                class="form-control"
                                                min="1"
                                                max="100"
                                                step="1"
                                                value="{{ old(
                                                    'contractor_renewal_years',
                                                    $organisationDetails->contractor_renewal_years ?? 3
                                                ) }}"
                                                required
                                            >

                                            <span class="input-group-addon">years</span>
                                        </div>

                                        <p class="help-block">
                                            How often contractors should undergo renewal screening.
                                            Default: 3 years.
                                        </p>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="renewal-setting-card">
                                        <label for="data_retention_years">
                                            Data Retention Period
                                        </label>

                                        <div class="input-group">
                                            <input
                                                type="number"
                                                name="data_retention_years"
                                                id="data_retention_years"
                                                class="form-control"
                                                min="2"
                                                max="100"
                                                step="1"
                                                value="{{ old(
                                                    'data_retention_years',
                                                    $organisationDetails->data_retention_years ?? 2
                                                ) }}"
                                                {{ in_array('superuser', $currentUserRoles) ? '' : 'disabled' }}
                                                required
                                            >

                                            <span class="input-group-addon">years</span>
                                        </div>

                                        <p class="help-block">
                                            How long applicant data should be retained after completion.
                                            Minimum: 2 years.
                                        </p>
                                    </div>
                                </div>

                            </div>
                        </div>

                        <button type="submit" class="btn forceBgClassified mt-3">Save Changes</button>
                    </form>
                </div>
            </div>

            <div id="emails" class="tab-pane">
                <p>This feature is currently being improved further</p>
                <form action="{{ route('admin.organisationSettings.saveEmail') }}" method="POST">
                    @csrf
                    <input type="hidden" name="organisationID" value="{{ $selectedOrgId }}">

                    <div class="form-group">
                        <label for="emailTemplateKey">Select Email Type</label>
                        <select id="emailTemplateKey" name="template_key" class="form-control" onchange="loadTemplate(this.value)">
                            <option value="registration">Registration</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Use Default Email?</label>
                        <select name="use_default" id="use_default" class="form-control" onchange="toggleEditor(this.value)">
                            <option value="1">Yes</option>
                            <option value="0">No (use custom email)</option>
                        </select>
                    </div>

                    <input type="hidden" id="default_content" value="">

                    <div class="form-group" id="editorContainer">
                        <label for="custom_content">Custom Email</label>
                        <textarea name="custom_content" id="custom_content" rows="12" class="form-control">{{ $custom_content ?? '' }}</textarea>
                    </div>

                    <div id="registrationReminder" class="alert alert-warning" style="display: none;">
                        <strong>Reminder:</strong> The custom registration email must include the <code>@{{ $registrationLink }}</code> placeholder so candidates can register.
                    </div>

                    <button type="submit" class="btn forceBgClassified mt-3">Save Email Template</button>
                </form>

                <h5 class="mt-4">Preview</h5>
                <iframe id="emailPreview" style="width: 100%; height: 500px; border: 1px solid #ccc;" title="Preview Email"></iframe>
            </div>

            <div id="users" class="tab-pane">
                <div class="users-toolbar">
                    <input type="text" id="userSearch" class="form-control" placeholder="Search users..." style="max-width: 300px;">
                    <a href="{{ url('/users/addUser') }}" class="btn forceBgClassified">
                        <i class="fa fa-plus"></i> Add New User
                    </a>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="usersTable">
                        <thead class="thead-light">
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Position</th>
                                <th>Phone</th>
                                <th>Account Type</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($organisationAdmins as $admin)
                                <tr>
                                    <td>{{ $admin->title }} {{ $admin->firstName }} {{ $admin->lastName }}</td>
                                    <td>{{ $admin->email }}</td>
                                    <td>{{ $admin->position ?? ' ' }}</td>
                                    <td>{{ $admin->phoneNumber ?? ' ' }}</td>
                                    <td>{{ $admin->role }}</td>
                                    <td>
                                        <a href="{{ env('APP_URL') }}users/viewProfile/{{ $admin->id }}"><button type="button" class="btn btn-success" title="View User"><i class="fa fa-search"></i></button></a>
                                        <a href="{{ env('APP_URL') }}users/editProfile/{{ $admin->id }}"><button type="button" class="btn btn-warning" title="Edit User"><i class="fa fa-edit"></i></button></a>
                                        @if(\Auth::user()->id != $admin->id)
                                            <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#deleteModal" data-userid="{{ $admin->id }}" title="Delete User"><i class="fa fa-times"></i></button>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                            @if ($organisationAdmins->isEmpty())
                                <tr><td colspan="6" class="text-center text-muted">No admins found for this organisation.</td></tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection

@section('pageCSS')
<style>
    .settings-card {
        background: white;
        padding: 2rem;
        border-radius: 12px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.05);
        margin-block: 2rem;
    }

    .settings-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
        margin-bottom: 1.5rem;
    }

    .settings-header h2 {
        margin: 0;
        color: #2C3C64;
    }

    .header-right {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 12px;
        flex-wrap: wrap;
    }

    #orgSwitcher {
        padding: 0.5rem;
        border-radius: 6px;
        border: 1px solid #ccc;
        min-width: 260px;
    }

    .tabs {
        display: flex;
        gap: 10px;
        border-bottom: 2px solid #e4e4e4;
        margin-bottom: 1rem;
    }

    .tab-button {
        background: none;
        border: none;
        font-weight: 600;
        color: #2C3C64;
        padding: 0.75rem 1.2rem;
        cursor: pointer;
        border-bottom: 3px solid transparent;
        transition: all 0.3s ease;
    }

    .tab-button.active {
        border-bottom: 3px solid #C55359;
        color: #C55359;
    }

    .tab-content {
        padding-top: 0.5rem;
    }

    .tab-pane {
        display: none;
    }

    .tab-pane.active {
        display: block;
        background: #f9f9f9;
        padding: 1.5rem;
        border-radius: 8px;
        border: 1px solid #eee;
    }

    .text-muted {
        color: #6c757d;
        margin: 0;
    }

    .form-group label {
        color: #C55359;
    }

    .logo-options {
        display: flex;
        gap: 2rem;
        margin-top: 1rem;
    }

    .logo-box.selected {
        border-color: #C55359;
        box-shadow: 0 0 0 2px rgba(197, 83, 89, 0.3);
    }

    .logo-box {
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        background: white;
        border-radius: 8px;
        padding: 1rem;
        border: 2px solid #ddd;
        max-width: 160px;
        height: 160px;
        flex: 1 1 140px;
        position: relative;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .logo-box img,
    #uploadPlaceholder {
        max-height: 80px;
        max-width: 100%;
        object-fit: contain;
        margin-bottom: 0.5rem;
    }

    .logo-box .checkmark {
        position: absolute;
        top: 8px;
        right: 8px;
        background: #C55359;
        color: white;
        border-radius: 50%;
        width: 20px;
        height: 20px;
        font-size: 12px;
        display: none;
        align-items: center;
        justify-content: center;
    }

    .logo-box.selected .checkmark {
        display: flex;
    }

    .d-none {
        display: none !important;
    }

    .name-width-limit {
        max-width: 350px;
    }

    .users-toolbar {
        display: flex;
        justify-content: flex-end;
        gap: 1rem;
        flex-wrap: wrap;
        margin-bottom: 1rem;
    }

    #usersTable {
        background: white;
        border: 2px solid #2C3C64;
    }

    #usersTable thead {
        background-color: #2C3C64;
        color: white;
    }

    #usersTable th, #usersTable td {
        border-color: #2C3C64 !important;
    }

    #deleteOrgButton {
        float: right;
        margin-bottom: 1rem;
    }

    /* Brand colour picker */
    .brand-swatch-grid { display:flex; gap:10px; flex-wrap:wrap; align-items:center; margin-top:.5rem; }
    .brand-swatch {
      width:36px; height:36px; border-radius:50%;
      border:2px solid #ddd; background: var(--swatch); position:relative; cursor:pointer;
      transition: box-shadow .15s ease, transform .05s ease;
    }
    .brand-swatch:hover { box-shadow: 0 0 0 3px rgba(0,0,0,.06); }
    .brand-swatch:active { transform: scale(.98); }

    .brand-swatch.selected {
      outline: 3px solid currentColor; color: var(--swatch);
      box-shadow: 0 0 0 3px rgba(0,0,0,.05);
    }
    .brand-swatch.selected::after {
      content:'\f00c'; font: normal normal normal 12px/1 FontAwesome;
      color:#fff; position:absolute; right:-6px; bottom:-6px;
      background: var(--swatch); width:18px; height:18px; border-radius:50%;
      display:flex; align-items:center; justify-content:center;
    }

    .brand-custom {
      display:inline-flex; align-items:center; gap:8px;
      padding:6px 10px; border:1px dashed #ccc; border-radius:6px; background:#fff;
    }
    .brand-custom input[type="color"] { width:36px; height:36px; border:none; padding:0; background:none; cursor:pointer; }

    .brand-preview { margin-top:8px; }
    .brand-preview .chip {
      display:inline-block; width:28px; height:14px; border-radius:4px; vertical-align:middle;
      margin-right:8px; border:1px solid rgba(0,0,0,.1);
    }

    .app-types-columns {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 0.5rem 1.5rem;
    }

    .app-types-columns .form-check {
        margin-bottom: 0;
        display: flex;
        align-items: center;
    }

    .allowedChecks {
        color: #000 !important;
        font-weight: 500;
        margin-bottom: 0 !important;
        margin-left: 2px;
    }

    .renewal-settings {
        margin-top: 1rem;
    }

    .renewal-setting-card {
        background: #fff;
        border: 1px solid #e5e5e5;
        border-radius: 8px;
        padding: 16px;
        height: 100%;
        margin-bottom: 15px;
    }

    .renewal-setting-card > label {
        color: #2C3C64;
        font-weight: 600;
        margin-bottom: 8px;
        display: block;
    }

    .renewal-setting-card .input-group {
        max-width: 220px;
    }

    .renewal-setting-card .input-group-addon {
        background: #f5f5f5;
        color: #555;
    }

    .renewal-setting-card .help-block {
        color: #777;
        font-size: 13px;
        margin-top: 8px;
        margin-bottom: 0;
    }
</style>
@endsection

@section('pageJavascript')
<script src="https://cdn.tiny.cloud/1/d7f1y603g6xg6ub9a9agvb462o89bprz4bykfduod7gbtg4f/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
<script>
    tinymce.init({
        selector: '#custom_content',
        menubar: false,
        plugins: 'link lists code',
        toolbar: 'undo redo | bold italic underline | bullist numlist | link | code | placeholders',
        height: 300,
        setup: function (editor) {
            editor.ui.registry.addMenuButton('placeholders', {
                text: 'Insert Placeholder',
                fetch: function (callback) {
                    const items = [
                        { type: 'menuitem', text: 'First Name', onAction: () => editor.insertContent('@{{ $firstName }}') },
                        { type: 'menuitem', text: 'Last Name', onAction: () => editor.insertContent('@{{ $lastName }}') },
                        { type: 'menuitem', text: 'Registration Link', onAction: () => editor.insertContent('@{{ $registrationLink }}') },
                        { type: 'menuitem', text: 'Login Link', onAction: () => editor.insertContent('@{{ $loginLink }}') }
                    ];
                    callback(items);
                }
            });

            editor.on('input', function () { updatePreview(); });

            editor.on('init', function () {
                const initialKey = document.getElementById('emailTemplateKey').value;
                loadTemplate(initialKey);
            });
        }
    });

    // Handle organisation switch
    document.getElementById('orgSwitcher').addEventListener('change', function () {
        const orgId = this.value;
        window.location.href = `?org=${orgId}`;
    });

    // Handle tabs
    document.querySelectorAll('.tab-button').forEach(btn => {
        btn.addEventListener('click', function () {
            document.querySelectorAll('.tab-button').forEach(b => b.classList.remove('active'));
            document.querySelectorAll('.tab-pane').forEach(p => p.classList.remove('active'));
            this.classList.add('active');
            document.getElementById(this.dataset.tab).classList.add('active');
        });
    });

    // Logo logic
    const defaultLogoBox = document.getElementById('defaultLogoBox');
    const customLogoBox = document.getElementById('customLogoBox');
    const fileInput = document.getElementById('logo');
    const deleteLogo = document.getElementById('deleteLogo');
    const customPreview = document.getElementById('customLogoPreview');
    const uploadPlaceholder = document.getElementById('uploadPlaceholder');

    defaultLogoBox?.addEventListener('click', () => {
        deleteLogo.value = '1';
        defaultLogoBox.classList.add('selected');
        customLogoBox.classList.remove('selected');
    });

    customLogoBox?.addEventListener('click', () => { fileInput.click(); });

    fileInput?.addEventListener('change', (e) => {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function (e) {
                customPreview.src = e.target.result;
                customPreview.style.display = 'block';
                uploadPlaceholder.style.display = 'none';
                customLogoBox.classList.add('selected');
                defaultLogoBox.classList.remove('selected');
                deleteLogo.value = '0';
            };
            reader.readAsDataURL(file);
        }
    });

    document.getElementById('userSearch')?.addEventListener('input', function () {
        const query = this.value.toLowerCase();
        document.querySelectorAll('#usersTable tbody tr').forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(query) ? '' : 'none';
        });
    });

    $('#deleteModal').on('show.bs.modal', function(e) {
        const button = e.relatedTarget;
        const modal = $(this);

        if (button.dataset.userid) {
            modal.attr("data-id", button.dataset.userid);
            modal.attr("data-type", "user");
            $('#deleteModalMessage').text("Are you sure you want to delete this user?");
        } else if (button.dataset.orgid) {
            modal.attr("data-id", button.dataset.orgid);
            modal.attr("data-type", "organisation");
            $('#deleteModalMessage').text("Are you sure you want to delete this organisation? This cannot be undone.");
        }
    });

    $(document).on("click", "#confirmDelete", function () {
        const modal = $("#deleteModal");
        const id = modal.attr("data-id");
        const type = modal.attr("data-type");

        if (type === "user") deleteUser(id);
        if (type === "organisation") deleteOrganisation(id);
    });

    function deleteUser(userID){
        $.ajax({
            url: "{{ env('APP_URL') }}" + "users/deleteUser",
            method: "POST",
            data: {"_token":"{{ csrf_token() }}", "userID":userID},
            success: function(result){
                if (result == 1){
                    $("#user_block_"+userID).remove();
                    $('#deleteModal').modal('toggle');
                }
            },
            error: function(){ $('#deleteModal').modal('toggle'); },
        });
    }

    function toggleEditor(value) {
        const showEditor = value == '0';
        document.getElementById('editorContainer').style.display = showEditor ? 'block' : 'none';
        updatePreview();
    }

    function loadTemplate(templateKey) {
        fetch(`/admin/settings/organisation/load-email?org={{ $selectedOrgId }}&key=${templateKey}`)
            .then(response => response.json())
            .then(data => {
                const isDefault = data.use_default ? '1' : '0';

                document.getElementById('use_default').value = isDefault;
                document.getElementById('default_content').value = data.default_content;

                if (tinymce.get('custom_content')) {
                    tinymce.get('custom_content').setContent(data.custom_content ?? '');
                } else {
                    document.getElementById('custom_content').value = data.custom_content ?? '';
                }

                toggleEditor(isDefault);
                updatePreview();

                const reminder = document.getElementById('registrationReminder');
                reminder.style.display = (templateKey === 'registration' && isDefault === '0') ? 'block' : 'none';
            });
    }

    function updatePreview() {
        const useDefault = document.getElementById('use_default').value == '1';
        const content = useDefault
            ? document.getElementById('default_content').value
            : (tinymce.get('custom_content') ? tinymce.get('custom_content').getContent() : '');

        const iframe = document.getElementById('emailPreview');
        if (!iframe) return;

        const doc = iframe.contentDocument || iframe.contentWindow.document;
        doc.open(); doc.write(content); doc.close();
    }

    document.getElementById('use_default')?.addEventListener('change', updatePreview);

    document.getElementById('deleteOrgButton')?.addEventListener('click', function () {
        const orgId = {{ $selectedOrgId }};
        const modal = $('#deleteModal');
        modal.attr('data-id', orgId);
        modal.attr('data-type', 'organisation');
        document.getElementById('deleteModalMessage').textContent = "Are you sure you want to delete this organisation? This cannot be undone.";
        modal.modal('show');
    });

    function deleteOrganisation(orgId) {
        $.ajax({
            url: "{{ env('APP_URL') }}" + "admin/deleteOrganisation",
            method: "POST",
            data: {"_token":"{{ csrf_token() }}", "organisationID":orgId},
            success: function () { window.location.href = "{{ url('/admin/settings/organisation') }}"; },
            error: function () { alert('Failed to delete organisation.'); }
        });
    }

    (function(){
      const swatches = document.querySelectorAll('.brand-swatch');
      const picker   = document.getElementById('brand_color_picker');
      const hidden   = document.getElementById('brand_color');
      const preview  = document.getElementById('brand_hex_preview');
      const chip     = document.querySelector('.brand-preview .chip');

      function normalise(hex){
        if (!hex) return '#C55359';
        hex = hex.trim();
        if (/^#[0-9a-f]{3}$/i.test(hex) || /^#[0-9a-f]{6}$/i.test(hex)) return hex.toUpperCase();
        if (/^[0-9a-f]{3}$/i.test(hex) || /^[0-9a-f]{6}$/i.test(hex))   return ('#'+hex).toUpperCase();
        return '#C55359';
      }

      function setBrand(hex){
        const value = normalise(hex);
        if (hidden) hidden.value = value;
        if (picker) picker.value = value;
        if (preview) preview.textContent = value;
        if (chip) chip.style.background = value;

        document.documentElement.style.setProperty('--brand', value);

        document.querySelectorAll('.brand-swatch.selected').forEach(el => el.classList.remove('selected'));
        const match = Array.from(swatches).find(el => el.dataset.hex.toUpperCase() === value.toUpperCase());
        if (match) match.classList.add('selected');
      }

      swatches.forEach(btn => btn.addEventListener('click', () => setBrand(btn.dataset.hex)));
      picker?.addEventListener('input', e => setBrand(e.target.value));
      setBrand(hidden?.value);
    })();
</script>
@endsection