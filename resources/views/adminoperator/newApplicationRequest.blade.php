@extends('layout.admin')

@section('title', 'New Applicant Request')

@section('content')
<div class="container-fluid py-4">
    <div class="settings-card">
        <div class="settings-header">
            <h2>Send New Applicant Request</h2>
        </div>

        <!-- Tab Buttons -->
        <div class="tabs">
            <button class="tab-button active" data-tab="single">Single Applicant</button>
            <button class="tab-button" data-tab="bulk">Bulk Upload</button>
        </div>

        <!-- Single Applicant Tab -->
        <div id="single" class="tab-pane active">
            <form action="{{ env('APP_URL') }}adminoperator/sendApplicationRequest" method="POST" enctype="multipart/form-data">
                @csrf

                {{-- Email & Date --}}
                <div class="row">
                    <div class="form-group col-md-6 {{ $errors->has('emailAddress') ? 'has-error' : '' }}">
                        <label for="emailAddress">Email Address *</label>
                        <input type="email" name="emailAddress" class="form-control" value="{{ old('emailAddress') }}" title="Email Address" required>
                        @if ($errors->has('emailAddress'))
                            <p class="help-block">The email is already registered or invalid.</p>
                        @endif
                    </div>
                    <div class="form-group col-md-6 {{ $errors->has('crdate') ? 'has-error' : '' }}">
                        <label for="crdate" class="d-none">Clearance Request Date *</label>
                        <input type="text" name="crdate" id="crdate" class="form-control d-none" value="{{ date('d/m/Y', strtotime('+5 days')) }}" title="When does it need to be done by" required>
                        @if ($errors->has('crdate'))
                            <p class="help-block">Please enter a valid request date.</p>
                        @endif
                    </div>
                </div>

                {{-- Name & AANumber --}}
                <div class="row">
                    <div class="form-group col-md-6 {{ $errors->has('forename') ? 'has-error' : '' }}">
                        <label for="forename">Forename *</label>
                        <input type="text" name="forename" class="form-control" value="{{ old('forename') }}" title="Forename" required>
                    </div>
                    <div class="form-group col-md-6 {{ $errors->has('surname') ? 'has-error' : '' }}">
                        <label for="surname">Surname *</label>
                        <input type="text" name="surname" class="form-control" value="{{ old('surname') }}" title="Surname" required>
                    </div>
                </div>

                <div class="row">
                    <div class="form-group col-md-6 {{ $errors->has('aanumber') ? 'has-error' : '' }}">
                        <label for="aanumber">Approved Access Number *</label>
                        <input type="text" name="aanumber" class="form-control" value="{{ old('aanumber') }}" title="HR Reference number" required>
                    </div>
                    <div class="form-group col-md-6 {{ $errors->has('phone') ? 'has-error' : '' }}">
                        <label for="phone">Applicant Contact Number</label>
                        <input type="text" name="phone" class="form-control" value="{{ old('phone') }}" title="Phone number">
                    </div>
                </div>

                {{-- Organisation & Type --}}
                <div class="row">
                    <div class="form-group col-md-6">
                        <label for="organisationID">Organisation</label>
                        @inject('checkAccess', 'App\Http\Controllers\Controller')
                        @if ($checkAccess->checkAccess('superuser') || $checkAccess->checkAccess('siteuser'))
                            <select name="organisationID" class="form-control">
                                @foreach ($availableOrganisations as $organisation)
                                    <option value="{{ $organisation->id }}" {{ $organisation->id == Auth::user()->organisationID ? 'selected' : '' }}>
                                        {{ $organisation->organisationName }}
                                    </option>
                                @endforeach
                            </select>
                        @else
                            <input class="form-control" value="{{ $adminProfile->organisationName }}" disabled>
                            <input type="hidden" name="organisationID" value="{{ $adminProfile->organisationID }}">
                        @endif
                    </div>
                    <div class="form-group col-md-6">
                        <label for="applicationType">Application Type</label>
                        <select name="applicationType" class="form-control">
                            <!-- Options populated dynamically via JS -->
                        </select>
                    </div>
                </div>

                {{-- Yoti Checkbox --}}
                <div class="form-group" id="singleYotiGroup">
                    <div class="checkbox">
                        <label>
                            <input type="checkbox" name="useYoti" {{ old('useYoti') ? 'checked' : '' }}>
                            Use Yoti Digital Identity Verification
                        </label>
                    </div>
                    <p class="help-block">If not using Yoti, ID checks must be done manually and uploaded.</p>
                </div>

                {{-- Supporting Files --}}
                <div class="singleFileUpload">
                  <h5 class="mt-4">Supporting Files (PDF or Image)</h5>

                  <div id="uploadsRepeater" class="mb-3">
                    <div class="upload-row row align-items-end" data-row>
                      <div class="form-group col-md-6">
                        <label>File</label>
                        <input type="file" name="uploads[0][file]" class="form-control"
                              accept="application/pdf,image/*">
                        <small class="text-muted">Max 10MB. Allowed: PDF, JPG, JPEG, PNG, GIF</small>
                      </div>
                      <div class="form-group col-md-5">
                        <label>File Type</label>
                        <select name="uploads[0][type]" class="form-control">
                          <option value="">Select File Type</option>
                          <option value="misc">Misc</option>
                          <option value="secmx">Security Matrix</option>
                          <option value="mkden">MK Denial</option>
                        </select>
                      </div>
                      <div class="form-group col-md-1 d-flex">
                        <button type="button" class="btn btn-danger remove-upload" style="margin-top:25px;" disabled>
                          <i class="fa fa-trash"></i>
                        </button>
                      </div>
                    </div>
                  </div>

                  <button type="button" id="addUploadRow" class="btn btn-default">
                    <i class="fa fa-plus"></i> Add another file
                  </button>
                </div>


                <button type="submit" class="btn forceBgClassified mt-3">Send Application Request</button>
            </form>
        </div>

        {{-- Bulk Upload Tab --}}
        <div id="bulk" class="tab-pane">
          <script src="https://unpkg.com/@lottiefiles/dotlottie-wc@0.6.2/dist/dotlottie-wc.js" type="module"></script>
          <div class="stepper mb-4">
            <div class="step active" id="step-upload">
              <div class="circle">1</div>
              <span>Upload File</span>
            </div>
            <div class="step" id="step-preview">
              <div class="circle">2</div>
              <span>Preview</span>
            </div>
            <div class="step" id="step-complete">
              <div class="circle">3</div>
              <span>Complete</span>
            </div>
          </div>

          <div id="upload-section">
            <h5 style="color: #2C3C64;"><strong>Step 1: Upload File</strong></h5>
            <p>Upload an Excel(.xlsx) or .CSV file, the columns can be in any order. <a href="{{ env('APP_URL') }}Sample-file.xlsx" download>Download sample file</a>.</p>

            <form id="bulkUploadForm" enctype="multipart/form-data">
                @csrf
                <div class="upload-box text-center p-5 border rounded bg-light" id="uploadZone">
                    <input type="file" name="bulkFile" id="bulkFile" accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" class="d-none" hidden>
                    <div>
                        <i class="fas fa-cloud-upload-alt fa-3x mb-2 text-secondary"></i>
                        <p>Drop file here or <span class="text-primary" style="cursor:pointer;" onclick="document.getElementById('bulkFile').click()">click to upload</span></p>
                        <p class="text-muted small">Max 20,000 rows</p>
                    </div>
                </div>

                <div id="uploadStatus" class="mt-3"></div>

                <div class="text-end mt-4">
                  <button style="margin-top: 15px;" type="button" id="importButton" class="btn forceBgClassified" disabled>IMPORT</button>
                </div>
            </form>
          </div>

          <div id="processing-section" class="text-center d-none">
            <div class="lottie-wrapper">
              <dotlottie-wc 
                src="https://lottie.host/c31400ff-ccd2-42ff-b269-867a86678a5e/UbrDyIDVy6.lottie"
                speed="1" autoplay loop>
              </dotlottie-wc>
            </div>
            <p class="text-muted">Processing file...</p>
          </div>


          <div id="preview-section" class="mt-4 d-none">
            <div class="position-relative mb-3">
              <h5 style="color: #2C3C64;"><strong>Step 2: Preview</strong></h5>
              <button type="button" class="btn forceBgClassified" id="backToUpload" style="min-width: 100px;">
                <i class="fas fa-arrow-left"></i> Back
              </button>
            </div>
            <p>Check we have matched your columns to ours correctly.</p>
            <p style="color: green;"><strong><span id="applicantCount">0</span></strong> applicants found.</p>
            <div id="fieldMapping" class="mb-3"></div>

            <div id="invalidEmailWarningBox" class="mb-3"></div>

            <div class="row">
              <div class="form-group col-md-6">
                <label for="bulkOrg">Organisation</label>
                <select id="bulkOrg" class="form-control">
                  @foreach ($availableOrganisations as $organisation)
                    <option value="{{ $organisation->id }}" {{ $organisation->id == Auth::user()->organisationID ? 'selected' : '' }}>{{ $organisation->organisationName }}</option>
                  @endforeach
                </select>
              </div>
              <div class="form-group col-md-6">
                <label for="bulkType">Application Type</label>
                <select id="bulkType" class="form-control">
                  <!-- Options populated dynamically via JS -->
                </select>
              </div>
            </div>
            <div class="form-group mt-3" id="bulkYotiGroup">
              <div class="checkbox">
                <label>
                  <input type="checkbox" id="bulkUseYoti"> Use Yoti Digital Identity Verification
                </label>
              </div>
              <p class="help-block">If not using Yoti, ID checks must be done manually and uploaded.</p>
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
    margin-bottom: 1.5rem;
  }

  .settings-header h2 {
    margin: 0;
    color: #2C3C64;
    margin-bottom: 10px;
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
    padding: 1.5rem;
    background: #f9f9f9;
    border-radius: 8px;
    border: 1px solid #eee;
  }

  .tab-pane:not(.active) {
    display: none;
  }

  .text-muted {
      color: #6c757d;
      margin: 0;
  }

  .form-group label {
      color: #C55359;
  }

  .d-none {
      display: none !important;
  }

  .upload-box.dragover {
    background-color: #e6f7ff;
    border: 2px dashed #1890ff !important;
  }
  .upload-box {
    padding-block: 50px;
    border: 2px dashed #C55359;
  }

  #processing-section {
      margin-top: 2rem;
      background: #fff;
      border: 1px dashed #ccc;
      padding: 2rem;
      border-radius: 10px;
  }

  .lottie-wrapper {
    width: 300px;
    height: 300px;
    margin: 0 auto;
  }

  dotlottie-wc {
    width: 100%;
    height: 100%;
    display: block;
  }

  .stepper {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 40px;
    margin-bottom: 2rem;
  }

  .step {
    text-align: center;
    position: relative;
  }

  .step .circle {
    width: 35px;
    height: 35px;
    line-height: 35px;
    border-radius: 50%;
    background: #ccc;
    color: #fff;
    font-weight: bold;
    margin: 0 auto 5px;
    transition: all 0.3s ease;
  }

  .step.active .circle {
    background: #C55359;
  }

  .step span {
    font-size: 12px;
    color: #333;
  }

  /* Table appearance */
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

  .border-danger{
    border-color: red !important;
  }

  #bulk .text-center.mt-5 {
    padding: 2rem 0;
  }

  .checkmark-animation {
    display: flex;
    justify-content: center;
    align-items: center;
  }

  #backToUpload {
    right: 60px;
    top: 220px;
    position: absolute;
  }

  .upload-row + .upload-row { margin-top: 10px; }
  .singleFileUpload {
    background-color: white;
    padding: 2rem;
    margin-bottom: 15px;
    border-radius: 12px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.05);
  }
  .singleFileUpload h5 {
    color: #2C3C64;
    font-weight: bold;
  }

  @media screen and (max-width: 575px) {
    #backToUpload {
      display: none;
    }
  }

  select[title] {
    cursor: help;
  }
</style>
@endsection


@section('pageJavascript')
<script>
  let orgAllowedTypes = {
  @foreach ($availableOrganisations as $organisation)
  '{{ $organisation->id }}': {!! $organisation->allowed_app_types ?? '[]' !!},
  @endforeach
  };
  let appOptions = [
  {value: '1', label: 'DBS Only', requires: ['DBS']},
  {value: '2', label: 'DBS and BPSS', requires: ['DBS', 'BPSS']},
  {value: '4', label: 'BPSS Revalidation Onsite', requires: ['DBS', 'BPSS', 'BPSS_REVAL_ONSITE']},
  {value: '5', label: 'BPSS Revalidation Offsite', requires: ['DBS', 'BPSS', 'BPSS_REVAL_OFFSITE']},
  ];
  function updateAppTypes(appSelect, yotiGroup, orgId) {
  let allowed = orgAllowedTypes[orgId] || [];
  appSelect.innerHTML = '';
  let hasOptions = false;
  appOptions.forEach(opt => {
  if (opt.requires.every(r => allowed.includes(r))) {
  let option = document.createElement('option');
  option.value = opt.value;
  option.text = opt.label;
  appSelect.add(option);
  hasOptions = true;
  }
  });
  if (!hasOptions) {
  let option = document.createElement('option');
  option.disabled = true;
  option.selected = true;
  option.text = 'No application types available';
  appSelect.add(option);
  }
  appSelect.disabled = !hasOptions;
  let showYoti = allowed.includes('YOTI');
  yotiGroup.style.display = showYoti ? 'block' : 'none';
  if (!showYoti) {
  yotiGroup.querySelector('input[type="checkbox"]').checked = false;
  }
  }
  // For single tab
  let singleOrgElem = document.querySelector('select[name="organisationID"]') || document.querySelector('input[name="organisationID"]');
  let singleAppSelect = document.querySelector('select[name="applicationType"]');
  let singleYotiGroup = document.getElementById('singleYotiGroup');
  let singleOrgId = singleOrgElem.value;
  if (singleOrgElem.tagName === 'SELECT') {
  singleOrgElem.addEventListener('change', (e) => updateAppTypes(singleAppSelect, singleYotiGroup, e.target.value));
  }
  updateAppTypes(singleAppSelect, singleYotiGroup, singleOrgId);
  // For bulk tab
  let bulkOrgElem = document.getElementById('bulkOrg');
  let bulkAppSelect = document.getElementById('bulkType');
  let bulkYotiGroup = document.getElementById('bulkYotiGroup');
  let bulkOrgId = bulkOrgElem.value;
  if (bulkOrgElem.tagName === 'SELECT') {
  bulkOrgElem.addEventListener('change', (e) => updateAppTypes(bulkAppSelect, bulkYotiGroup, e.target.value));
  }
  updateAppTypes(bulkAppSelect, bulkYotiGroup, bulkOrgId);

  // Tab switching
  document.querySelectorAll('.tab-button').forEach(btn => {
      btn.addEventListener('click', function () {
          document.querySelectorAll('.tab-button').forEach(b => b.classList.remove('active'));
          document.querySelectorAll('.tab-pane').forEach(p => p.classList.remove('active'));
          btn.classList.add('active');
          document.getElementById(btn.dataset.tab).classList.add('active');
      });
  });

  // Drop zone setup
  const dropZone = document.getElementById('uploadZone');
  const fileInput = document.getElementById('bulkFile');

  // Highlight on dragover
  dropZone.addEventListener('dragover', (e) => {
    e.preventDefault();
    dropZone.classList.add('dragover');
  });

  dropZone.addEventListener('dragleave', () => {
    dropZone.classList.remove('dragover');
  });

  dropZone.addEventListener('drop', (e) => {
    e.preventDefault();
    dropZone.classList.remove('dragover');

    const file = e.dataTransfer.files[0];
    if (file) {
      fileInput.files = e.dataTransfer.files;

      // Trigger file input change event manually
      const event = new Event('change', { bubbles: true });
      fileInput.dispatchEvent(event);
    }
  });

  fileInput.addEventListener('change', function () {
    const file = this.files[0];
    if (!file) return;

    const formData = new FormData();
    formData.append('bulkFile', file);
    formData.append('_token', '{{ csrf_token() }}');

    document.getElementById('upload-section').classList.add('d-none');
    document.getElementById('processing-section').classList.remove('d-none');
    document.getElementById('preview-section').classList.add('d-none');

    let showPreview = () => fetch("{{ url('/adminoperator/previewApplicantsFromFile') }}", {
      method: "POST",
      body: formData,
    })
    .then(res => res.json())
    .then(data => {
      if (data.error) {
        document.getElementById('processing-section').classList.add('d-none');
        document.getElementById('upload-section').classList.remove('d-none');
        document.getElementById('uploadStatus').innerHTML = `<p class="text-danger">${data.error}</p>`;
      } else {
        window.previewedApplicants = data.applicants;
        window.matchedMappings = data.mappings;

        document.getElementById('step-upload').classList.remove('active');
        document.getElementById('step-preview').classList.add('active');

        setTimeout(() => {
          document.getElementById('processing-section').classList.add('d-none');
          document.getElementById('preview-section').classList.remove('d-none');
          document.getElementById('applicantCount').textContent = data.total;
          document.getElementById('uploadStatus').innerHTML = `<p class="text-success">${data.total} applicants found.</p>`;

          if (!data.headers || data.headers.length === 0) {
            document.getElementById('fieldMapping').innerHTML = `<p class="text-danger">No headers found in file. Please check your file format.</p>`;
            return;
          }

          document.getElementById('backToUpload').addEventListener('click', () => {
            document.getElementById('preview-section').classList.add('d-none');
            document.getElementById('upload-section').classList.remove('d-none');

            document.getElementById('step-preview').classList.remove('active');
            document.getElementById('step-upload').classList.add('active');
          });

          // Render mapping table
          let html = '<table class="table table-bordered" id="usersTable"><thead><tr><th>Required Field</th><th>Matched Column</th></tr></thead><tbody>';
          for (const [required, matched] of Object.entries(data.mappings)) {
            const matchFound = matched && data.headers.includes(matched);
            const isValidMatch = matched && data.headers.includes(matched);
          const selectClass = isValidMatch ? 'form-control' : 'form-control border-danger';
          const selectTitle = isValidMatch ? '' : 'No confident match — please select manually';

            const conf = data.confidence[required] || { score: 0, type: 'low' };
            let badge = '';

            if (conf.type === 'high') {
              badge = `<span class="label label-success" style="margin-left: 10px;">Auto-matched (${conf.score}% Confidence)</span>`;
            } else if (conf.type === 'medium') {
              badge = `<span class="label label-warning" style="margin-left: 10px;">Possible match (${conf.score}% Confidence)</span>`;
            } else {
              badge = `<span class="label label-danger" style="margin-left: 10px;">No confident match</span>`;
            }

            html += `<tr>
              <td>${required} ${badge}</td>
              <td>
                <select name="mapping[${required}]" class="${selectClass}" ${selectTitle ? `title="${selectTitle}"` : ''}>
                  ${data.headers.map(h => `<option value="${h}" ${h === matched ? 'selected' : ''}>${h}</option>`).join('')}
                </select>
              </td>
            </tr>`;
          }
          html += '</tbody></table>';
          document.getElementById('fieldMapping').innerHTML = html;

          // Create submit button wrapper
          const submitBtnWrapper = document.createElement('div');
          submitBtnWrapper.classList.add('text-end', 'mt-4');
          submitBtnWrapper.innerHTML = '<button type="button" class="btn btn-success" id="bulkSubmitBtn">Submit Applicants</button>';
          document.getElementById('preview-section').appendChild(submitBtnWrapper);

          window.matchedHeaders = data.headers;

          // ✅ Now attach the click listener AFTER the button is in the DOM
          document.getElementById('bulkSubmitBtn').addEventListener('click', () => {
            let updatedMappings = {};
            document.querySelectorAll('#usersTable select').forEach(select => {
              const field = select.name.match(/mapping\[(.*?)\]/)[1];
              updatedMappings[field] = select.value;
            });

            const hasEmail = !!updatedMappings['Email'];
            const hasAAN = !!updatedMappings['Approved Access Number'];
            const hasSplitName = !!updatedMappings['Forename'] && !!updatedMappings['Surname'];
            const hasSingleName = (window.matchedHeaders || []).some(h =>
              ['name', 'full name', 'fullname', 'applicant name', 'candidate name']
                .includes(String(h).toLowerCase().trim())
            );

            if (!hasEmail || !hasAAN || (!hasSplitName && !hasSingleName)) {
              alert("Please map Email, Approved Access Number, and either (Forename + Surname) or a single Name column.");
              return;
            }

            submitBulkApplicants(window.previewedApplicants, updatedMappings);
          });

          document.getElementById('step-upload').classList.remove('active');
          document.getElementById('step-preview').classList.add('active');
          document.getElementById('importButton').disabled = false;
        }, 2000);
      }
    })
    .catch(() => {
      document.getElementById('processing-section').classList.add('d-none');
      document.getElementById('upload-section').classList.remove('d-none');
      document.getElementById('uploadStatus').innerHTML = '<p class="text-danger">There was a problem processing the file.</p>';
    });

    showPreview();
  });

  function submitBulkApplicants(applicantsData, mappings) {
    const submitButton = document.querySelector('#preview-section button.btn-success');
    submitButton.disabled = true;
    submitButton.innerText = 'Submitting...';

    const animationWrapper = document.createElement('div');
    animationWrapper.className = 'text-center my-4';
    animationWrapper.innerHTML = `
      <dotlottie-wc src="https://lottie.host/c31400ff-ccd2-42ff-b269-867a86678a5e/UbrDyIDVy6.lottie" speed="1" autoplay loop style="width:200px;height:200px;"></dotlottie-wc>
      <p class="text-muted">Submitting applicants...</p>
    `;
    document.getElementById('preview-section').appendChild(animationWrapper);

    // Find index of each field in headers
    const headerRow = window.matchedHeaders || [];
    const emailIndex = headerRow.indexOf(mappings['Email']);
    const forenameIndex = headerRow.indexOf(mappings['Forename']);
    const surnameIndex = headerRow.indexOf(mappings['Surname']);
    const aanumberIndex = headerRow.indexOf(mappings['Approved Access Number']);
    const phoneIndex = mappings['Phone Number'] ? headerRow.indexOf(mappings['Phone Number']) : -1;

    // Optional fallback full-name headers (when first/last not split in file)
    const fullNameHeaderCandidates = ['name', 'full name', 'fullname', 'applicant name', 'candidate name'];
    const fullNameIndex = headerRow.findIndex(h =>
      fullNameHeaderCandidates.includes(String(h).toLowerCase().trim())
    );

    let transformedApplicants = [];
    let invalidEmails = [];
    let invalidPhones = [];
    let missingAANumbers = [];

    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    const phoneRegex = /^[0-9]{6,20}$/;

    function splitFullName(fullName) {
      const parts = String(fullName || '').trim().replace(/\s+/g, ' ').split(' ').filter(Boolean);

      if (parts.length === 0) return { forename: '', surname: '' };
      if (parts.length === 1) return { forename: parts[0], surname: '' };

      return {
        forename: parts[0],
        surname: parts.slice(1).join(' ')
      };
    }

    function normalisePhone(phoneRaw) {
      let phone = String(phoneRaw || '').trim();

      // Remove tel: prefix (case-insensitive), e.g. tel:07123456789 / TEL:07123456789
      phone = phone.replace(/^tel:\s*/i, '');

      // Remove common separators/spaces so "07123 456789" can be accepted
      phone = phone.replace(/[\s\-().]/g, '');

      return phone;
    }

    applicantsData.forEach((row, index) => {
      const email = row[emailIndex]?.trim() || '';
      const aanumber = row[aanumberIndex]?.trim() || '';

      let forename = forenameIndex !== -1 ? (row[forenameIndex]?.trim() || '') : '';
      let surname = surnameIndex !== -1 ? (row[surnameIndex]?.trim() || '') : '';

      // If forename/surname missing, try to split from single full-name column
      if ((!forename || !surname) && fullNameIndex !== -1) {
        const fullName = row[fullNameIndex]?.trim() || '';
        const split = splitFullName(fullName);

        if (!forename) forename = split.forename;
        if (!surname) surname = split.surname;
      }

      const phoneRaw = phoneIndex !== -1 ? (row[phoneIndex]?.trim() || '') : '';
      const phone = normalisePhone(phoneRaw);

      let valid = true;

      if (!emailRegex.test(email)) {
        invalidEmails.push(email || `(row ${index + 2})`);
        valid = false;
      }

      // Accept blank phone; if present must be digits 6-20 after normalisation
      if (phone && !phoneRegex.test(phone)) {
        invalidPhones.push(phoneRaw || `(row ${index + 2})`);
        valid = false;
      }

      if (!aanumber) {
        missingAANumbers.push(`(row ${index + 2})`);
        valid = false;
      }

      if (valid) {
        transformedApplicants.push({ email, forename, surname, aanumber, phone });
      }
    });

    const payload = {
      applicants: transformedApplicants,
      organisationID: document.getElementById('bulkOrg').value,
      applicationType: document.getElementById('bulkType').value,
      useYoti: document.getElementById('bulkUseYoti').checked,
      _token: '{{ csrf_token() }}'
    };

    const invalidEmailWarningHTML = invalidEmails.length > 0 ? `
    <div class="alert alert-warning mt-3">
      <strong>⚠ ${invalidEmails.length} invalid email(s) were skipped.</strong> These rows were not submitted.
    </div>` : '';

    const aanumberWarning = missingAANumbers.length > 0
    ? `<div class="alert alert-warning mt-3"><strong>⚠ ${missingAANumbers.length} missing Approved Access Number(s) were skipped.</strong></div>`
    : '';

    const phoneWarning = invalidPhones.length > 0
    ? `<div class="alert alert-warning mt-3"><strong>⚠ ${invalidPhones.length} invalid phone number(s) were skipped.</strong></div>`
    : '';

    const allWarnings = invalidEmailWarningHTML + phoneWarning + aanumberWarning;

    fetch("{{ url('/adminoperator/sendBulkApplicationRequest') }}", {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(payload)
    })
    .then(res => res.json())
    .then(result => {
      // ✅ Visually transition to Step 3
      document.getElementById('step-preview').classList.remove('active');
      document.getElementById('step-complete').classList.add('active');

      // ✅ Hide the entire preview UI
      document.getElementById('preview-section').classList.add('d-none');

      const invalidEmailWarningHTML = invalidEmails.length > 0 ? `
      <div class="alert alert-warning mt-3">
        <strong>⚠ ${invalidEmails.length} invalid email(s) were skipped.</strong> These rows were not submitted.
      </div>` : '';


      // ✅ Create a new dedicated result section
      const completeSection = document.createElement('div');
      completeSection.className = 'text-center mt-5';

      completeSection.innerHTML = `
        <div class="checkmark-animation mb-3">
          <dotlottie-wc
            src="https://lottie.host/b6581881-9b33-4b26-8b32-365b4884031b/Hk9X1rMINg.lottie"
            style="width: 300px;height: 300px"
            speed="0.5"
            autoplay
            loop
          ></dotlottie-wc>
        </div>

        ${allWarnings}

        <p class="text-success mt-2">${result.inserted.length} applicants submitted successfully.</p>
        <p class="text-muted">${result.duplicates.length} duplicate(s) skipped.</p>

        ${result.inserted.length > 0 ? `
          <details class="mt-3 mb-2">
            <summary style="cursor: pointer; color: #28a745;"><strong>View submitted applicants</strong></summary>
            <ul class="list-unstyled mt-2 small">
              ${result.inserted.map(a => `<li>${a.email}</li>`).join('')}
            </ul>
          </details>` : ''}

        ${result.duplicates.length > 0 ? `
          <details class="mb-3">
            <summary style="cursor: pointer; color: #6c757d;"><strong>View duplicates skipped</strong></summary>
            <ul class="list-unstyled mt-2 small">
              ${result.duplicates.map(d => `<li>${d.email}</li>`).join('')}
            </ul>
          </details>` : ''}

        <div class="mt-4" style="margin-top: 10px;">
          <button class="btn forceBgClassified" onclick="resetBulkFlow()">Upload Another File</button>
        </div>
      `;

      // ✅ Insert into DOM just below stepper or wherever fits best
      document.getElementById('bulk').appendChild(completeSection);

      submitButton.disabled = false;
      submitButton.innerText = 'Submit Applicants';
    })

    .catch(() => {
      animationWrapper.innerHTML = `<p class="text-danger">An error occurred while submitting applicants.</p>`;
      submitButton.disabled = false;
      submitButton.innerText = 'Submit Applicants';
    });
  }

  function resetBulkFlow() {
    document.getElementById('step-complete').classList.remove('active');
    document.getElementById('step-upload').classList.add('active');
    document.getElementById('upload-section').classList.remove('d-none');

    // Remove success message section
    document.querySelectorAll('#bulk .mt-5.text-center, #bulk .mt-5.d-flex').forEach(el => el.remove());

    // Clear form and file input
    document.getElementById('bulkUploadForm').reset();
    fileInput.value = '';
  }

  (function () {
    const repeater = document.getElementById('uploadsRepeater');
    const addBtn = document.getElementById('addUploadRow');

    function renumberRows() {
      const rows = repeater.querySelectorAll('[data-row]');
      rows.forEach((row, idx) => {
        row.querySelectorAll('input[name], select[name]').forEach(el => {
          // rename uploads[?][field] -> uploads[idx][field]
          el.name = el.name.replace(/uploads\[\d+]/, `uploads[${idx}]`);
        });
        // Enable remove button if more than 1 row, else disable
        const removeBtn = row.querySelector('.remove-upload');
        if (removeBtn) removeBtn.disabled = rows.length === 1;
      });
    }

    addBtn.addEventListener('click', () => {
      const last = repeater.querySelector('[data-row]:last-child');
      const clone = last.cloneNode(true);

      // Clear cloned inputs
      clone.querySelectorAll('input[type="file"]').forEach(i => i.value = '');
      clone.querySelectorAll('select').forEach(s => s.selectedIndex = 0);

      repeater.appendChild(clone);
      renumberRows();
    });

    repeater.addEventListener('click', (e) => {
      if (e.target.closest('.remove-upload')) {
        const rows = repeater.querySelectorAll('[data-row]');
        if (rows.length > 1) {
          e.target.closest('[data-row]').remove();
          renumberRows();
        }
      }
    });

    // Initial state
    renumberRows();
  })();
</script>
@endsection
