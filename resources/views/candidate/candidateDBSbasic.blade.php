@extends('layout.candidate')

@section('form-content')
@php
    $uk = $countries->firstWhere('id', 225);

    // Options used by dynamically-added previous-address rows.
    // Keep "Please select…" first so a country is never silently assumed.
    $countryOptions = '<option value="">Please select…</option>';
    if ($uk) {
        $countryOptions .= '<option value="'.$uk->iso3.'">'.$uk->nicename.'</option>';
    }
    foreach ($countries as $c) {
        if ($c->id == 225) continue;
        $countryOptions .= '<option value="'.$c->iso3.'">'.$c->nicename.'</option>';
    }

    // Helper to print countries while retaining a selected ISO3 value.
    $renderCountryOptions = function ($selectedIso3) use ($countries) {
        $html = '<option value="">Please select…</option>';
        $uk = $countries->firstWhere('id', 225);

        if ($uk) {
            $html .= '<option value="'.$uk->iso3.'"'.
                     ($selectedIso3 === $uk->iso3 ? ' selected' : '').
                     '>'.$uk->nicename.'</option>';
        }

        foreach ($countries as $c) {
            if ($c->id == 225) continue;
            $html .= '<option value="'.$c->iso3.'"'.
                     ($selectedIso3 === $c->iso3 ? ' selected' : '').
                     '>'.$c->nicename.'</option>';
        }

        return $html;
    };

    // Error-summary links need to point to actual form controls/sections.
    $errorTarget = function ($field) {
        if ($field === 'history') return 'history_section';
        if ($field === 'has_dbs_profile') return 'has_dbs_profile_group';
        if ($field === 'paper_certificate') return 'paper_certificate_group';
        if ($field === 'paper_address_choice') return 'paper_address_choice_group';

        return str_replace('.', '_', $field);
    };

    $today = now()->format('Y-m-d');
@endphp

<script>
    var COUNTRY_OPTIONS_HTML = @json($countryOptions);
</script>

<style>
/* Shared form styling */
.form-section{padding:20px 20px 5px;margin-bottom:20px}
.section-title{color:#2C3C64;font-weight:700;margin:0 0 15px;display:flex;align-items:center;gap:10px}
.section-title:after{content:"";flex:1;height:1px;background:#e5e9f2}
.field-row{margin-left:-10px;margin-right:-10px}
.field-row .form-group{padding-left:10px;padding-right:10px;margin-bottom:15px}
.form-control{height:40px}
.form-control:focus{
    border-color:var(--brand)!important;
    -webkit-box-shadow:inset 0 1px 1px rgba(0,0,0,.075),0 0 8px color-mix(in srgb,var(--brand) 60%,transparent)!important;
    box-shadow:inset 0 1px 1px rgba(0,0,0,.075),0 0 8px color-mix(in srgb,var(--brand) 60%,transparent)!important;
}
.control-label{font-weight:600;color:#2C3C64;text-align:left!important;margin-bottom:6px}
.help-block{margin-top:6px;color:#6b7480}
.has-error .control-label,.has-error .help-block{color:#b94a48}
.has-error .form-control{border-color:#b94a48;box-shadow:none}
.hidden{display:none!important}
.small-muted{font-size:13px;color:#6b7480}

/* Intro / guidance */
.page-intro{
    background:#f7f9fc;
    border:1px solid #e5e9f2;
    border-radius:8px;
    padding:16px 18px;
    margin:0 20px 20px;
}
.page-intro p:last-child{margin-bottom:0}
.optional-note{
    background:#f7f9fc;
    border-left:4px solid var(--brand);
    border-radius:4px;
    padding:12px 14px;
    margin-bottom:18px;
}

/* Consent */
.consent-intro{
    background:#f7f9fc;
    border:1px solid #e5e9f2;
    border-radius:8px;
    padding:14px 16px;
    margin-bottom:16px;
}
.consent-card{
    border:1px solid #dfe5ee;
    border-radius:8px;
    background:#fff;
    margin-bottom:14px;
    overflow:hidden;
}
.consent-card-head{
    display:flex;
    gap:12px;
    align-items:flex-start;
    padding:14px 16px 10px;
}
.consent-letter{
    width:34px;
    height:34px;
    flex:0 0 34px;
    border-radius:50%;
    display:flex;
    align-items:center;
    justify-content:center;
    background:#eef2ff;
    color:#2C3C64;
    font-weight:700;
}
.consent-card-title{font-weight:700;color:#2C3C64;margin:0 0 4px}
.consent-card-summary{margin:0;color:#555}
.consent-details{
    margin:0 16px 14px 62px;
    padding:10px 12px;
    background:#f8f9fa;
    border-radius:6px;
}
.consent-details summary{cursor:pointer;font-weight:600;color:#2C3C64}
.consent-details p{margin:10px 0 0}
.consent-action{
    border-top:1px solid #e5e9f2;
    padding:12px 16px;
    background:#fafbfc;
    margin: 0 16px 14px 62px;
}
.consent-action label{font-weight:600;color:#2C3C64}
.consent-action .form-control{max-width:230px;margin-top:6px}
.terms-box{
    border:1px solid #dfe5ee;
    border-radius:8px;
    padding:14px 16px;
    background:#fff;
    margin-top:16px;
}

/* Guided optional evidence */
.evidence-choice{
    border:1px solid #dfe5ee;
    border-radius:8px;
    background:#fff;
    padding:14px 16px;
    margin-bottom:14px;
}
.evidence-choice legend,
.option-fieldset legend{
    width:auto;
    border:0;
    margin:0 0 8px;
    padding:0;
    font-size:14px;
    font-weight:700;
    color:#2C3C64;
}
.radio-options{
    display:flex;
    flex-wrap:wrap;
    gap:18px;
    align-items:center;
}
.radio-options label{font-weight:500;margin:0}
.evidence-panel{
    margin-top:14px;
    padding-top:14px;
    border-top:1px solid #edf0f5;
}
.question-help{
    border:0;
    background:transparent;
    padding:0 3px;
    margin-left:3px;
    color:#6b7480;
    cursor:help;
}
.question-help:hover,.question-help:focus{color:var(--brand);outline:2px solid transparent}

/* Address history */
.addr-card{
    border:1px solid #dfe5ee;
    border-radius:8px;
    margin-bottom:14px;
    background:#fff;
    overflow:hidden;
}
.addr-card-header{
    display:flex;
    justify-content:space-between;
    align-items:center;
    gap:12px;
    padding:12px 14px;
    background:#f7f9fc;
    border-bottom:1px solid #e5e9f2;
}
.addr-card-title{font-weight:700;color:#2C3C64}
.addr-card-summary{font-size:13px;color:#6b7480;margin-top:2px}
.addr-card-body{padding:14px}
.addr-card .remove-history{margin-top:2px}
.progress-note{
    background:#f7f9fc;
    border:1px dashed #d7deea;
    border-radius:6px;
    padding:10px 12px;
    margin-bottom:12px;
}
.badge-soft{
    display:inline-block;
    padding:3px 8px;
    border-radius:999px;
    background:#eef2ff;
    color:#2C3C64;
    font-size:12px;
}
.coverage-box{
    border:1px solid #dfe5ee;
    border-radius:8px;
    padding:12px 14px;
    margin-top:14px;
}
.coverage-box.success{background:#f3fbf6;border-color:#bfe3ca}
.coverage-box.warning{background:#fff9ed;border-color:#f2d39b}
.coverage-box.error{background:#fff3f3;border-color:#efb7b7}
.coverage-box strong{display:block;color:#2C3C64;margin-bottom:4px}
.coverage-gaps{margin:8px 0 0;padding-left:20px}

/* DBS options */
.option-fieldset{
    border:1px solid #dfe5ee;
    border-radius:8px;
    background:#fff;
    padding:14px 16px;
    margin-bottom:14px;
}
.certificate-explainer{
    background:#f7f9fc;
    border:1px solid #e5e9f2;
    border-radius:8px;
    padding:12px 14px;
    margin-bottom:14px;
}

/* Accessibility / responsive */
.error-summary a{text-decoration:underline}
.client-error{
    display:block;
    margin-top:6px;
    color:#b94a48;
    font-weight:600;
}
.consent-action.has-error,
.terms-box.has-error{
    border-color:#b94a48;
    background:#fff7f7;
}
.consent-action.has-error .form-control{
    border-color:#b94a48;
    box-shadow:none;
}
.consent-feedback{
    margin-bottom:16px;
}
@media(max-width:600px){
    .consent-card-head{padding:12px}
    .consent-details{margin:0 12px 12px}
    .consent-action{padding:12px}
    .consent-action .form-control{max-width:none}
    .radio-options{gap:12px}
    .addr-card-header{align-items:flex-start;flex-direction:column}
}
</style>

@php
    // When we return from validation or want to show the rest immediately
    $savedConsentOk = ($userDetails->privacy_policy ?? 0)
        && ($userDetails->consent_basic_check ?? 0)
        && ($userDetails->declaration_by_applicant ?? 0)
        && ($userDetails->terms_accepted ?? 0);

    $hasConsentErrors = $errors->has('consent_a')
        || $errors->has('consent_b')
        || $errors->has('consent_c')
        || $errors->has('accept_terms');

    $showPurpose = !$hasConsentErrors && (
        old('consent_ack') === '1'
        || session('show_purpose') === true
        || $savedConsentOk
    );

    // Determine if address history is needed (< 5 years at current address)
    $moveIn = isset($userDetails->current_address_from) ? \Carbon\Carbon::parse($userDetails->current_address_from) : null;
    $needsHistory = !$moveIn || $moveIn->gt(\Carbon\Carbon::now()->subYears(5));

    // Prefer old() after validation, else use DB rows
    $oldHistory = collect(old('history', []));
    if ($oldHistory->isNotEmpty()) {
        $historyPrefill = $oldHistory->map(function($row){
            return [
                'line1'    => $row['line1']    ?? '',
                'line2'    => $row['line2']    ?? '',
                'city'     => $row['city']     ?? '',
                'county'   => $row['county']   ?? '',
                'postcode' => $row['postcode'] ?? '',
                'country'  => $row['country']  ?? '', // ISO3
                // ensure yyyy-mm-dd for type="date"
                'from'     => $row['from']     ?? '',
                'to'       => $row['to']       ?? '',
            ];
        })->values();
    } else {
        $historyPrefill = isset($previousAddresses)
            ? collect($previousAddresses)->map(function($a){
                return [
                    'line1'    => $a->previous_address_line_1 ?? '',
                    'line2'    => $a->previous_address_line_2 ?? '',
                    'city'     => $a->previous_address_town   ?? '',
                    'county'   => $a->previous_address_county ?? '',
                    'postcode' => $a->previous_address_postcode ? strtoupper($a->previous_address_postcode) : '',
                    'country'  => $a->previous_address_country ?? '', // ISO3 already
                    'from'     => $a->previous_address_from ? \Illuminate\Support\Str::of($a->previous_address_from)->substr(0,10) : '',
                    'to'       => $a->previous_address_to   ? \Illuminate\Support\Str::of($a->previous_address_to)->substr(0,10)   : '',
                ];
            })->values()
            : collect();
    }
@endphp

<form class="form-horizontal" method="POST" action="{{ route('candidate.dbsbasic.save') }}" novalidate>
    @csrf

    {{-- Error summary with links to the actual field/section --}}
    @if ($errors->any())
        <div class="alert alert-danger error-summary" role="alert" aria-live="polite" aria-atomic="true">
            <p><strong>Please check the following before continuing:</strong></p>
            <ul class="m-0">
                @foreach ($errors->getMessages() as $field => $messages)
                    @foreach ($messages as $message)
                        <li>
                            <a href="#{{ $errorTarget($field) }}" class="text-danger">
                                {{ $message }}
                            </a>
                        </li>
                    @endforeach
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Hidden flag toggled by JS when consent is valid --}}
    <input type="hidden" id="consent_ack" name="consent_ack" value="{{ old('consent_ack', $showPurpose ? '1' : '0') }}">

    {{-- ========== STEP 1: CONSENT ========== --}}
    <div id="consent_section" class="{{ $showPurpose ? 'hidden' : '' }}">
        <div class="form-section">
            <h3 class="section-title">Consent to process your Basic DBS check</h3>

            <div class="consent-intro">
                <p><strong>Before we can continue, we need three confirmations from you.</strong></p>
                <p style="margin-bottom:0"><strong>Please complete the declaration with your explicit consent typed into boxes A, B & C below</strong>, confirming that you consent to BluescreenIT Ltd on behalf of your company accessing your Personal Data and providing to Disclosure & Barring Service (DBS), <strong>we are not able to review and submit your application to DBS without your written consent.</strong></p>
                <p style="margin-bottom:0">The Privacy policy below outlines how your personal data will be used by DBS (Disclosure and Barring Service) and outlines your rights under the General Data Protection Regulation.</p>
            </div>

            <div id="consent_feedback" class="alert alert-danger consent-feedback hidden"
                 role="alert" aria-live="assertive"></div>

            {{-- A: Privacy --}}
            <div class="consent-card">
                <div class="consent-card-head">
                    <div class="consent-letter" aria-hidden="true">A</div>
                    <div>
                        <p class="consent-card-title">Privacy and use of your personal information</p>
                        <p class="consent-card-summary">
                            Confirm that you have read the DBS privacy information and understand how your personal data will be processed.
                        </p>
                    </div>
                </div>

                <details class="consent-details">
                    <summary>Read the full privacy declaration</summary>
                    <p>
                        I have read the Basic DBS Check Processing Privacy Policy
                        <a href="https://www.gov.uk/government/publications/dbs-privacy-policies" target="_blank" rel="noopener">
                        https://www.gov.uk/government/publications/dbs-privacy-policies
                        </a> and I understand how DBS will process my personal data.
                    </p>
                </details>

                <div class="consent-action {{ $errors->has('consent_a') ? 'has-error' : '' }}">
                    <label for="consent_a">To confirm, type <strong>I CONFIRM</strong></label>
                    <input
                        id="consent_a"
                        name="consent_a"
                        type="text"
                        class="form-control"
                        value="{{ old('consent_a', ($userDetails->privacy_policy ?? 0) ? 'I CONFIRM' : '') }}"
                        autocomplete="off"
                        required
                    >
                    @if($errors->has('consent_a'))
                        <span class="help-block">{{ $errors->first('consent_a') }}</span>
                    @else
                        <span class="help-block">Capitalisation does not matter.</span>
                    @endif
                    <span id="consent_a_error" class="client-error hidden" role="alert"></span>
                </div>
            </div>

            {{-- B: Electronic result --}}
            <div class="consent-card">
                <div class="consent-card-head">
                    <div class="consent-letter" aria-hidden="true">B</div>
                    <div>
                        <p class="consent-card-title">Electronic DBS result</p>
                        <p class="consent-card-summary">
                            Confirm that DBS may send the electronic result of your Basic check to the Responsible Organisation handling your application.
                        </p>
                    </div>
                </div>

                <details class="consent-details">
                    <summary>Read the full electronic-result declaration</summary>
                    <p>
                        By confirming your acceptance of the below declaration, you are giving us consent to receive an e-result regarding your basic DBS application. If consent is not given you have the option to complete a basic check via
                        <a href="https://www.gov.uk/DBS" target="_blank" rel="noopener">www.gov.uk/DBS</a>
                        and I understand how DBS will process my personal data.<br><br>
                        I consent to the DBS providing an electronic result directly to the responsible organisation that has submitted my application. I understand that an electronic result contains a message that indicates either the certificate does not contain criminal record information or to await certificate which will indicate that my certificate contains criminal record information. In some cases the responsible organisation may provide this information directly to my employer prior to me receiving my certificate.<br><br>
                        I understand if I do not consent to an electronic result being issued to the responsible organisation submitting my application that I must not proceed with this application and I should apply directly to DBS
                        <a href="https://www.gov.uk/request-copy-criminal-record" target="_blank" rel="noopener">Request a basic DBS check - GOV.UK (www.gov.uk)</a><br><br>
                        I understand that to withdraw my consent whilst my application is in progress I must contact the DBS helpline 03000 200 190. My application will then be withdrawn.
                    </p>
                </details>

                <div class="consent-action {{ $errors->has('consent_b') ? 'has-error' : '' }}">
                    <label for="consent_b">To agree, type <strong>I AGREE</strong></label>
                    <input
                        id="consent_b"
                        name="consent_b"
                        type="text"
                        class="form-control"
                        value="{{ old('consent_b', ($userDetails->consent_basic_check ?? 0) ? 'I AGREE' : '') }}"
                        autocomplete="off"
                        required
                    >
                    @if($errors->has('consent_b'))
                        <span class="help-block">{{ $errors->first('consent_b') }}</span>
                    @else
                        <span class="help-block">Capitalisation does not matter.</span>
                    @endif
                    <span id="consent_b_error" class="client-error hidden" role="alert"></span>
                </div>
            </div>

            {{-- C: Applicant declaration --}}
            <div class="consent-card">
                <div class="consent-card-head">
                    <div class="consent-letter" aria-hidden="true">C</div>
                    <div>
                        <p class="consent-card-title">Applicant declaration</p>
                        <p class="consent-card-summary">
                            Confirm that the information you provide for this application is complete and true.
                        </p>
                    </div>
                </div>

                <details class="consent-details">
                    <summary>Read the full applicant declaration</summary>
                    <p>
                        As the applicant you must explicitly confirm that you have provided complete and true information in support of this application.<br><br>
                        I have provided complete and true information in support of the application, and I understand that knowingly making a false statement for this purpose is a criminal offence.
                    </p>
                </details>

                <div class="consent-action {{ $errors->has('consent_c') ? 'has-error' : '' }}">
                    <label for="consent_c">To confirm, type <strong>I CONFIRM</strong></label>
                    <input
                        id="consent_c"
                        name="consent_c"
                        type="text"
                        class="form-control"
                        value="{{ old('consent_c', ($userDetails->declaration_by_applicant ?? 0) ? 'I CONFIRM' : '') }}"
                        autocomplete="off"
                        required
                    >
                    @if($errors->has('consent_c'))
                        <span class="help-block">{{ $errors->first('consent_c') }}</span>
                    @else
                        <span class="help-block">Capitalisation does not matter.</span>
                    @endif
                    <span id="consent_c_error" class="client-error hidden" role="alert"></span>
                </div>
            </div>

            <div class="terms-box form-group {{ $errors->has('accept_terms') ? 'has-error' : '' }}">
                <div class="checkbox" style="margin:0">
                    <label>
                        <input
                            type="checkbox"
                            id="accept_terms"
                            name="accept_terms"
                            value="1"
                            {{ old('accept_terms', $userDetails->terms_accepted ?? 0) ? 'checked' : '' }}
                            required
                        >
                        I confirm that I have read and accept the declarations above.
                    </label>
                </div>
                @if($errors->has('accept_terms'))
                    <span class="help-block">{{ $errors->first('accept_terms') }}</span>
                @endif
                <span id="accept_terms_error" class="client-error hidden" role="alert"></span>
            </div>

            <div class="form-group m-t-20">
                <div class="col-sm-12" style="padding-left:0">
                    <a class="btn btn-default" href="{{ route('candidate.welcome') }}">Back</a>
                    <button type="button" id="consent_continue" class="btn forceBgClassified">
                        Continue <i class="fa fa-arrow-right"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- ========== STEP 2+: REST OF FORM (visible after consent) ========== --}}
    <div id="rest_wrapper" class="{{ $showPurpose ? '' : 'hidden' }}"
         data-needs-history="{{ $needsHistory ? '1' : '0' }}"
         data-current-from="{{ $moveIn ? $moveIn->format('Y-m-d') : '' }}">

        <div id="consent_saved_banner" class="alert alert-success hidden"
             role="status" aria-live="polite" style="margin:0 20px 15px;">
            <i class="fa fa-check-circle" aria-hidden="true"></i>
            <strong>Consent saved.</strong> You can now continue with your DBS details.
        </div>

        {{-- PURPOSE FOR APPLICATION --}}
        <div id="purpose_section" class="form-section">
            <h3 class="section-title">Purpose for application</h3>

            <div class="row field-row">
                <div class="col-sm-6">
                    <div class="form-group {{ $errors->has('purpose') ? 'has-error' : '' }}">
                        <label for="purpose" class="control-label">Purpose <span class="text-danger">*</span></label>
                        <select id="purpose" name="purpose" class="form-control" required>
                            <option value="">Please select…</option>
                            <option value="employment"
                                {{ old('purpose', $userDetails->purpose_of_check ?? 'employment') === 'employment' ? 'selected' : '' }}>
                                Employment
                            </option>
                        </select>
                        @if($errors->has('purpose'))<span class="help-block">{{ $errors->first('purpose') }}</span>@endif
                    </div>
                </div>

                <div class="col-sm-6">
                    <div class="form-group {{ $errors->has('employment_sector_id') ? 'has-error' : '' }}">
                        <label for="employment_sector_id" class="control-label">Employment sector <span class="text-danger">*</span></label>
                        <select id="employment_sector_id" name="employment_sector_id" class="form-control" required>
                            <option value="">Please select…</option>
                            @foreach($employmentSectors as $s)
                                <option value="{{ $s->id }}"
                                {{ (string)old('employment_sector_id', (string)($userDetails->employment_sector ?? '')) === (string)$s->id ? 'selected' : '' }}>
                                {{ $s->employment_sector_name }}
                                </option>
                            @endforeach
                        </select>
                        @if($errors->has('employment_sector_id'))<span class="help-block">{{ $errors->first('employment_sector_id') }}</span>@endif
                    </div>
                </div>
            </div>

            <div class="row field-row">
                <div class="col-sm-6">
                    <div class="form-group {{ $errors->has('position_applied_for') ? 'has-error' : '' }}">
                        <label for="position_applied_for" class="control-label">Position applied for <span class="text-danger">*</span></label>
                        <input id="position_applied_for" name="position_applied_for" type="text" class="form-control"
                            value="{{ old('position_applied_for', $userDetails->position_applied_for ?? '') }}" required>
                        @if($errors->has('position_applied_for'))<span class="help-block">{{ $errors->first('position_applied_for') }}</span>@endif
                    </div>
                </div>

                <div class="col-sm-6">
                    <div class="form-group {{ $errors->has('employer_name') ? 'has-error' : '' }}">
                        <label for="employer_name" class="control-label">Name of employer <span class="text-danger">*</span></label>
                        <input id="employer_name" name="employer_name" type="text" class="form-control"
                            value="{{ old('employer_name', $userDetails->dbs_employer_name ?? ($userDetails->name_of_employer ?? '')) }}" required>
                        @if($errors->has('employer_name'))<span class="help-block">{{ $errors->first('employer_name') }}</span>@endif
                    </div>
                </div>
            </div>
        </div>

        {{-- GUIDED OPTIONAL IDENTITY INFORMATION --}}
        @php
            $niChoice = old('has_ni_number');
            if ($niChoice === null && !empty($userDetails->supporting_nino)) $niChoice = '1';

            $licenceChoice = old('has_driving_licence');
            if ($licenceChoice === null && (
                !empty($userDetails->supporting_dln) ||
                ($userDetails->supporting_dln_type ?? null) !== null ||
                !empty($userDetails->supporting_dln_issue_date)
            )) $licenceChoice = '1';

            $passportChoice = old('has_passport');
            if ($passportChoice === null && (
                !empty($userDetails->supporting_passport) ||
                !empty($userDetails->supporting_passport_country) ||
                !empty($userDetails->supporting_passport_date)
            )) $passportChoice = '1';
        @endphp

        <div id="evidence_section" class="form-section">
            <h3 class="section-title">Identity information</h3>

            <div class="optional-note">
                <strong>This section is guided by what you have available.</strong><br>
                Tell us which documents or identifiers you have. If you select <strong>Yes</strong>, the related
                details will appear and must be completed together. If you do not have an item, select <strong>No</strong>.
            </div>

            {{-- National Insurance number --}}
            <fieldset class="evidence-choice" id="has_ni_number_group">
                <legend>Do you have a National Insurance number?</legend>
                <div class="radio-options">
                    <label>
                        <input type="radio" name="has_ni_number" value="1" {{ (string)$niChoice === '1' ? 'checked' : '' }} required>
                        Yes
                    </label>
                    <label>
                        <input type="radio" name="has_ni_number" value="0" {{ (string)$niChoice === '0' ? 'checked' : '' }}>
                        No
                    </label>
                </div>

                <div id="ni_panel" class="evidence-panel {{ (string)$niChoice === '1' ? '' : 'hidden' }}">
                    <div class="row field-row">
                        <div class="col-sm-6">
                            <div class="form-group {{ $errors->has('ni_number') ? 'has-error' : '' }}">
                                <label for="ni_number" class="control-label">National Insurance number</label>
                                <input
                                    id="ni_number"
                                    name="ni_number"
                                    type="text"
                                    class="form-control"
                                    value="{{ old('ni_number', $userDetails->supporting_nino ?? '') }}"
                                    placeholder="AB123456C"
                                    maxlength="13"
                                    pattern="^[A-CEGHJ-PR-TW-Z]{2}\s?\d{2}\s?\d{2}\s?\d{2}\s?[A-D]$"
                                >
                                @if($errors->has('ni_number'))
                                    <span class="help-block">{{ $errors->first('ni_number') }}</span>
                                @else
                                    <span class="help-block">Example: AB123456C. Spaces are allowed.</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </fieldset>

            {{-- Driving licence --}}
            <fieldset class="evidence-choice" id="has_driving_licence_group">
                <legend>Do you have a driving licence?</legend>
                <div class="radio-options">
                    <label>
                        <input type="radio" name="has_driving_licence" value="1" {{ (string)$licenceChoice === '1' ? 'checked' : '' }} required>
                        Yes
                    </label>
                    <label>
                        <input type="radio" name="has_driving_licence" value="0" {{ (string)$licenceChoice === '0' ? 'checked' : '' }}>
                        No
                    </label>
                </div>

                <div id="licence_panel" class="evidence-panel {{ (string)$licenceChoice === '1' ? '' : 'hidden' }}">
                    <p class="help-block" style="margin-top:0">
                        Please enter all three licence details below.
                    </p>

                    <div class="row field-row">
                        <div class="col-sm-4">
                            <div class="form-group {{ $errors->has('licence_origin') ? 'has-error' : '' }}">
                                <label for="licence_origin" class="control-label">Issuing country/type</label>
                                @php
                                    $dlOrigin = old(
                                        'licence_origin',
                                        !empty($userDetails->supporting_dln)
                                            ? (string)($userDetails->supporting_dln_type ?? '')
                                            : ''
                                    );
                                @endphp
                                <select id="licence_origin" name="licence_origin" class="form-control">
                                    <option value="">Please select…</option>
                                    <option value="1" {{ $dlOrigin == '1' ? 'selected' : '' }}>UK licence</option>
                                    <option value="0" {{ $dlOrigin == '0' ? 'selected' : '' }}>Non-UK licence</option>
                                </select>
                                @if($errors->has('licence_origin'))
                                    <span class="help-block">{{ $errors->first('licence_origin') }}</span>
                                @endif
                            </div>
                        </div>

                        <div class="col-sm-4">
                            <div class="form-group {{ $errors->has('driving_licence_number') ? 'has-error' : '' }}">
                                <label for="driving_licence_number" class="control-label">Driving licence number</label>
                                <input
                                    id="driving_licence_number"
                                    name="driving_licence_number"
                                    type="text"
                                    class="form-control"
                                    value="{{ old('driving_licence_number', $userDetails->supporting_dln ?? '') }}"
                                >
                                @if($errors->has('driving_licence_number'))
                                    <span class="help-block">{{ $errors->first('driving_licence_number') }}</span>
                                @endif
                            </div>
                        </div>

                        <div class="col-sm-4">
                            <div class="form-group {{ $errors->has('licence_issue_date') ? 'has-error' : '' }}">
                                <label for="licence_issue_date" class="control-label">Date licence was issued</label>
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa-regular fa-calendar" aria-hidden="true"></i></span>
                                    <input
                                        id="licence_issue_date"
                                        name="licence_issue_date"
                                        type="date"
                                        class="form-control"
                                        value="{{ old('licence_issue_date', $userDetails->supporting_dln_issue_date ?? '') }}"
                                        max="{{ $today }}"
                                    >
                                </div>
                                @if($errors->has('licence_issue_date'))
                                    <span class="help-block">{{ $errors->first('licence_issue_date') }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </fieldset>

            {{-- Passport --}}
            <fieldset class="evidence-choice" id="has_passport_group">
                <legend>Do you have a passport?</legend>
                <div class="radio-options">
                    <label>
                        <input type="radio" name="has_passport" value="1" {{ (string)$passportChoice === '1' ? 'checked' : '' }} required>
                        Yes
                    </label>
                    <label>
                        <input type="radio" name="has_passport" value="0" {{ (string)$passportChoice === '0' ? 'checked' : '' }}>
                        No
                    </label>
                </div>

                <div id="passport_panel" class="evidence-panel {{ (string)$passportChoice === '1' ? '' : 'hidden' }}">
                    <p class="help-block" style="margin-top:0">
                        Please enter all three passport details below.
                    </p>

                    <div class="row field-row">
                        <div class="col-sm-4">
                            <div class="form-group {{ $errors->has('passport_country') ? 'has-error' : '' }}">
                                <label for="passport_country" class="control-label">Passport issuing country</label>
                                @php $passIso = old('passport_country', $userDetails->supporting_passport_country ?? ''); @endphp
                                <select id="passport_country" name="passport_country" class="form-control">
                                    <option value="">Please select…</option>
                                    @if($uk)
                                        <option value="{{ $uk->iso3 }}" {{ $passIso === $uk->iso3 ? 'selected' : '' }}>{{ $uk->nicename }}</option>
                                    @endif
                                    @foreach($countries as $c)
                                        @continue($c->id == 225)
                                        <option value="{{ $c->iso3 }}" {{ $passIso === $c->iso3 ? 'selected' : '' }}>{{ $c->nicename }}</option>
                                    @endforeach
                                </select>
                                @if($errors->has('passport_country'))
                                    <span class="help-block">{{ $errors->first('passport_country') }}</span>
                                @endif
                            </div>
                        </div>

                        <div class="col-sm-4">
                            <div class="form-group {{ $errors->has('passport_number') ? 'has-error' : '' }}">
                                <label for="passport_number" class="control-label">Passport number</label>
                                <input
                                    id="passport_number"
                                    name="passport_number"
                                    type="text"
                                    class="form-control"
                                    value="{{ old('passport_number', $userDetails->supporting_passport ?? '') }}"
                                >
                                @if($errors->has('passport_number'))
                                    <span class="help-block">{{ $errors->first('passport_number') }}</span>
                                @endif
                            </div>
                        </div>

                        <div class="col-sm-4">
                            <div class="form-group {{ $errors->has('passport_issue_date') ? 'has-error' : '' }}">
                                <label for="passport_issue_date" class="control-label">Date passport was issued</label>
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa-regular fa-calendar" aria-hidden="true"></i></span>
                                    <input
                                        id="passport_issue_date"
                                        name="passport_issue_date"
                                        type="date"
                                        class="form-control"
                                        value="{{ old('passport_issue_date', $userDetails->supporting_passport_date ?? '') }}"
                                        max="{{ $today }}"
                                    >
                                </div>
                                @if($errors->has('passport_issue_date'))
                                    <span class="help-block">{{ $errors->first('passport_issue_date') }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </fieldset>
        </div>

        {{-- ADDRESS HISTORY (conditional) --}}
        <div id="history_section" class="form-section {{ $needsHistory ? '' : 'hidden' }}">
            <h3 class="section-title">Address history for the last 5 years</h3>

            <div class="progress-note">
                <span class="badge-soft">Why we need this</span>
                <div style="margin-top:6px">
                    Your current address starts on
                    <strong>{{ $moveIn ? $moveIn->format('d/m/Y') : 'an unknown date' }}</strong>.
                    Because that does not cover the full last five years, please add your previous addresses so there are
                    <strong>no gaps</strong> in the five-year period.
                </div>
            </div>

            @if($errors->has('history'))
                <div class="alert alert-danger" role="alert">
                    {{ $errors->first('history') }}
                </div>
            @endif

            <div id="history_list">
                @if($historyPrefill->count())
                    @foreach($historyPrefill as $i => $h)
                        <div class="addr-card" data-index="{{ $i }}">
                            <div class="addr-card-header">
                                <div>
                                    <div class="addr-card-title">Previous address <span class="address-number">{{ $loop->iteration }}</span></div>
                                    <div class="addr-card-summary">Add the dates you lived at this address.</div>
                                </div>
                                <button type="button" class="btn btn-link text-danger remove-history">
                                    <i class="fa fa-trash"></i> Remove
                                </button>
                            </div>

                            <div class="addr-card-body">
                                <div class="row field-row">
                                    <div class="col-sm-6">
                                        <div class="form-group {{ $errors->has("history.$i.line1") ? 'has-error' : '' }}">
                                            <label for="history_{{ $i }}_line1" class="control-label">Address line 1 <span class="text-danger">*</span></label>
                                            <input
                                                id="history_{{ $i }}_line1"
                                                type="text"
                                                class="form-control"
                                                name="history[{{ $i }}][line1]"
                                                value="{{ $h['line1'] }}"
                                            >
                                            @if($errors->has("history.$i.line1"))
                                                <span class="help-block">{{ $errors->first("history.$i.line1") }}</span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="col-sm-6">
                                        <div class="form-group {{ $errors->has("history.$i.line2") ? 'has-error' : '' }}">
                                            <label for="history_{{ $i }}_line2" class="control-label">Address line 2</label>
                                            <input
                                                id="history_{{ $i }}_line2"
                                                type="text"
                                                class="form-control"
                                                name="history[{{ $i }}][line2]"
                                                value="{{ $h['line2'] }}"
                                            >
                                            @if($errors->has("history.$i.line2"))
                                                <span class="help-block">{{ $errors->first("history.$i.line2") }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="row field-row">
                                    <div class="col-sm-4">
                                        <div class="form-group {{ $errors->has("history.$i.city") ? 'has-error' : '' }}">
                                            <label for="history_{{ $i }}_city" class="control-label">Town/City <span class="text-danger">*</span></label>
                                            <input
                                                id="history_{{ $i }}_city"
                                                type="text"
                                                class="form-control"
                                                name="history[{{ $i }}][city]"
                                                value="{{ $h['city'] }}"
                                            >
                                            @if($errors->has("history.$i.city"))
                                                <span class="help-block">{{ $errors->first("history.$i.city") }}</span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="col-sm-4">
                                        <div class="form-group {{ $errors->has("history.$i.county") ? 'has-error' : '' }}">
                                            <label for="history_{{ $i }}_county" class="control-label">County</label>
                                            <input
                                                id="history_{{ $i }}_county"
                                                type="text"
                                                class="form-control"
                                                name="history[{{ $i }}][county]"
                                                value="{{ $h['county'] }}"
                                            >
                                            @if($errors->has("history.$i.county"))
                                                <span class="help-block">{{ $errors->first("history.$i.county") }}</span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="col-sm-4">
                                        <div class="form-group {{ $errors->has("history.$i.postcode") ? 'has-error' : '' }}">
                                            <label for="history_{{ $i }}_postcode" class="control-label">Postcode / postal code <span class="text-danger">*</span></label>
                                            <input
                                                id="history_{{ $i }}_postcode"
                                                type="text"
                                                class="form-control"
                                                name="history[{{ $i }}][postcode]"
                                                value="{{ $h['postcode'] }}"
                                                style="text-transform:uppercase"
                                                maxlength="20"
                                            >
                                            @if($errors->has("history.$i.postcode"))
                                                <span class="help-block">{{ $errors->first("history.$i.postcode") }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="row field-row">
                                    <div class="col-sm-4">
                                        <div class="form-group {{ $errors->has("history.$i.country") ? 'has-error' : '' }}">
                                            <label for="history_{{ $i }}_country" class="control-label">Country <span class="text-danger">*</span></label>
                                            <select
                                                id="history_{{ $i }}_country"
                                                class="form-control"
                                                name="history[{{ $i }}][country]"
                                            >
                                                {!! $renderCountryOptions($h['country']) !!}
                                            </select>
                                            @if($errors->has("history.$i.country"))
                                                <span class="help-block">{{ $errors->first("history.$i.country") }}</span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="col-sm-4">
                                        <div class="form-group {{ $errors->has("history.$i.from") ? 'has-error' : '' }}">
                                            <label for="history_{{ $i }}_from" class="control-label">Lived here from <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <span class="input-group-addon"><i class="fa-regular fa-calendar" aria-hidden="true"></i></span>
                                                <input
                                                    id="history_{{ $i }}_from"
                                                    type="date"
                                                    class="form-control hist-from"
                                                    name="history[{{ $i }}][from]"
                                                    value="{{ $h['from'] }}"
                                                    max="{{ $today }}"
                                                >
                                            </div>
                                            @if($errors->has("history.$i.from"))
                                                <span class="help-block">{{ $errors->first("history.$i.from") }}</span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="col-sm-4">
                                        <div class="form-group {{ $errors->has("history.$i.to") ? 'has-error' : '' }}">
                                            <label for="history_{{ $i }}_to" class="control-label">Lived here until <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <span class="input-group-addon"><i class="fa-regular fa-calendar" aria-hidden="true"></i></span>
                                                <input
                                                    id="history_{{ $i }}_to"
                                                    type="date"
                                                    class="form-control hist-to"
                                                    name="history[{{ $i }}][to]"
                                                    value="{{ $h['to'] }}"
                                                    max="{{ $today }}"
                                                >
                                            </div>
                                            @if($errors->has("history.$i.to"))
                                                <span class="help-block">{{ $errors->first("history.$i.to") }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>

            <div class="text-right">
                <button type="button" class="btn btn-default" id="add_history">
                    <i class="fa fa-plus"></i> Add another previous address
                </button>
            </div>

            <div id="history_coverage_box" class="coverage-box warning" aria-live="polite">
                <strong id="history_coverage_title">Checking your 5-year address history…</strong>
                <div id="history_coverage"></div>
                <ul id="history_gaps" class="coverage-gaps hidden"></ul>
            </div>
        </div>

        {{-- DBS DETAILS --}}
        <div id="dbs_section" class="form-section">
            <h3 class="section-title">DBS details and certificate preferences</h3>

            {{-- DBS Profile ID --}}
            @php
                $hasProfile = old('has_dbs_profile', (string)($userDetails->user_dbs_profile_id_available ?? '0'));
            @endphp

            <fieldset id="has_dbs_profile_group" class="option-fieldset {{ $errors->has('has_dbs_profile') ? 'has-error' : '' }}">
                <legend>
                    Do you have a DBS Profile ID?
                    <button
                        type="button"
                        class="question-help"
                        data-toggle="tooltip"
                        data-placement="top"
                        title="A DBS Profile ID is an identifier associated with your DBS online profile. If you do not have one, or you are unsure, select No."
                        aria-label="What is a DBS Profile ID?"
                    >
                        <i class="fa-regular fa-circle-question" aria-hidden="true"></i>
                    </button>
                </legend>

                <p class="help-block" style="margin-top:0">
                    If you have previously been given a DBS Profile ID, select Yes and enter it below.
                    If you do not have one or are unsure, select No.
                </p>

                <div class="radio-options">
                    <label>
                        <input type="radio" name="has_dbs_profile" value="1" {{ $hasProfile === '1' ? 'checked' : '' }} required>
                        Yes
                    </label>
                    <label>
                        <input type="radio" name="has_dbs_profile" value="0" {{ $hasProfile !== '1' ? 'checked' : '' }}>
                        No
                    </label>
                </div>

                @if($errors->has('has_dbs_profile'))
                    <span class="help-block">{{ $errors->first('has_dbs_profile') }}</span>
                @endif

                <div id="dbs_profile_wrap" class="evidence-panel {{ $hasProfile === '1' ? '' : 'hidden' }}">
                    <div class="row field-row">
                        <div class="col-sm-6">
                            <div class="form-group {{ $errors->has('dbs_profile_id') ? 'has-error' : '' }}">
                                <label for="dbs_profile_id" class="control-label">DBS Profile ID number</label>
                                <input
                                    id="dbs_profile_id"
                                    name="dbs_profile_id"
                                    type="text"
                                    class="form-control"
                                    value="{{ old('dbs_profile_id', $userDetails->user_dbs_profile_id ?? '') }}"
                                >
                                @if($errors->has('dbs_profile_id'))
                                    <span class="help-block">{{ $errors->first('dbs_profile_id') }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </fieldset>

            {{-- Paper certificate --}}
            @php
                $paper = old('paper_certificate', (string)($userDetails->supporting_paper_certificate ?? '0'));
                $savedChoiceRaw = old('paper_address_choice', (string)($userDetails->user_paper_certificate_different_address ?? '0'));
                $choiceStr = ($savedChoiceRaw === '1' || $savedChoiceRaw === 'different') ? 'different' : 'current';
            @endphp

            <div class="certificate-explainer">
                <strong>Paper certificate</strong>
                <p style="margin:6px 0 0">
                    Your Basic DBS application can also include a paper certificate sent by post.
                    If you request one, you can choose whether it is sent to your current address or a different delivery address.
                </p>
            </div>

            <fieldset id="paper_certificate_group" class="option-fieldset {{ $errors->has('paper_certificate') ? 'has-error' : '' }}">
                <legend>Would you like to receive a paper certificate?</legend>

                <div class="radio-options">
                    <label>
                        <input type="radio" name="paper_certificate" value="1" {{ $paper === '1' ? 'checked' : '' }} required>
                        Yes
                    </label>
                    <label>
                        <input type="radio" name="paper_certificate" value="0" {{ $paper !== '1' ? 'checked' : '' }}>
                        No
                    </label>
                </div>

                @if($errors->has('paper_certificate'))
                    <span class="help-block">{{ $errors->first('paper_certificate') }}</span>
                @endif
            </fieldset>

            <div id="paper_options" class="{{ $paper === '1' ? '' : 'hidden' }}">
                <fieldset id="paper_address_choice_group" class="option-fieldset {{ $errors->has('paper_address_choice') ? 'has-error' : '' }}">
                    <legend>Where should the paper certificate be sent?</legend>

                    <div class="radio-options">
                        <label>
                            <input
                                type="radio"
                                name="paper_address_choice"
                                value="current"
                                {{ $choiceStr === 'current' ? 'checked' : '' }}
                            >
                            My current address
                        </label>

                        <label>
                            <input
                                type="radio"
                                name="paper_address_choice"
                                value="different"
                                {{ $choiceStr === 'different' ? 'checked' : '' }}
                            >
                            A different address
                        </label>
                    </div>

                    @if($errors->has('paper_address_choice'))
                        <span class="help-block">{{ $errors->first('paper_address_choice') }}</span>
                    @endif
                </fieldset>

                {{-- Current address summary --}}
                <div id="paper_current_wrap" class="consent-intro {{ $choiceStr === 'different' ? 'hidden' : '' }}">
                    <strong>Current address</strong><br>
                    {{ $userDetails->address_line_1 ?? '—' }}<br>
                    @if(!empty($userDetails->address_line_2))
                        {{ $userDetails->address_line_2 }}<br>
                    @endif
                    {{ $userDetails->address_town ?? '—' }}
                    @if(!empty($userDetails->address_county))
                        , {{ $userDetails->address_county }}
                    @endif
                    <br>
                    {{ strtoupper($userDetails->address_postcode ?? '') }}<br>
                    {{ optional($countries->firstWhere('iso3', $userDetails->address_country ?? 'GBR'))->nicename ?? 'United Kingdom' }}
                </div>

                {{-- Different address --}}
                <div id="paper_diff_wrap" class="addr-card {{ $choiceStr === 'different' ? '' : 'hidden' }}">
                    <div class="addr-card-header">
                        <div>
                            <div class="addr-card-title">Different delivery address</div>
                            <div class="addr-card-summary">Complete all required fields for the address where the certificate should be posted.</div>
                        </div>
                    </div>

                    <div class="addr-card-body">
                        <div class="row field-row">
                            <div class="col-sm-6">
                                <div class="form-group {{ $errors->has('paper_recipient') ? 'has-error' : '' }}">
                                    <label for="paper_recipient" class="control-label">Recipient name <span class="text-danger">*</span></label>
                                    <input
                                        id="paper_recipient"
                                        name="paper_recipient"
                                        type="text"
                                        class="form-control paper-diff-required"
                                        value="{{ old('paper_recipient', $userDetails->certificate_address_recipient_name ?? '') }}"
                                    >
                                    @if($errors->has('paper_recipient'))
                                        <span class="help-block">{{ $errors->first('paper_recipient') }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="row field-row">
                            <div class="col-sm-4">
                                <div class="form-group {{ $errors->has('paper_line1') ? 'has-error' : '' }}">
                                    <label for="paper_line1" class="control-label">Address line 1 <span class="text-danger">*</span></label>
                                    <input
                                        id="paper_line1"
                                        name="paper_line1"
                                        type="text"
                                        class="form-control paper-diff-required"
                                        value="{{ old('paper_line1', $userDetails->certificate_address_line_1 ?? '') }}"
                                    >
                                    @if($errors->has('paper_line1'))
                                        <span class="help-block">{{ $errors->first('paper_line1') }}</span>
                                    @endif
                                </div>
                            </div>

                            <div class="col-sm-4">
                                <div class="form-group {{ $errors->has('paper_line2') ? 'has-error' : '' }}">
                                    <label for="paper_line2" class="control-label">Address line 2</label>
                                    <input
                                        id="paper_line2"
                                        name="paper_line2"
                                        type="text"
                                        class="form-control"
                                        value="{{ old('paper_line2', $userDetails->certificate_address_line_2 ?? '') }}"
                                    >
                                    @if($errors->has('paper_line2'))
                                        <span class="help-block">{{ $errors->first('paper_line2') }}</span>
                                    @endif
                                </div>
                            </div>

                            <div class="col-sm-4">
                                <div class="form-group {{ $errors->has('paper_city') ? 'has-error' : '' }}">
                                    <label for="paper_city" class="control-label">Town/City <span class="text-danger">*</span></label>
                                    <input
                                        id="paper_city"
                                        name="paper_city"
                                        type="text"
                                        class="form-control paper-diff-required"
                                        value="{{ old('paper_city', $userDetails->certificate_address_town ?? '') }}"
                                    >
                                    @if($errors->has('paper_city'))
                                        <span class="help-block">{{ $errors->first('paper_city') }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="row field-row">
                            <div class="col-sm-4">
                                <div class="form-group {{ $errors->has('paper_county') ? 'has-error' : '' }}">
                                    <label for="paper_county" class="control-label">County</label>
                                    <input
                                        id="paper_county"
                                        name="paper_county"
                                        type="text"
                                        class="form-control"
                                        value="{{ old('paper_county', $userDetails->certificate_address_county ?? '') }}"
                                    >
                                    @if($errors->has('paper_county'))
                                        <span class="help-block">{{ $errors->first('paper_county') }}</span>
                                    @endif
                                </div>
                            </div>

                            <div class="col-sm-4">
                                <div class="form-group {{ $errors->has('paper_postcode') ? 'has-error' : '' }}">
                                    <label for="paper_postcode" class="control-label">Postcode / postal code <span class="text-danger">*</span></label>
                                    <input
                                        id="paper_postcode"
                                        name="paper_postcode"
                                        type="text"
                                        class="form-control paper-diff-required"
                                        value="{{ old('paper_postcode', $userDetails->certificate_address_postcode ?? '') }}"
                                        style="text-transform:uppercase"
                                        maxlength="20"
                                    >
                                    @if($errors->has('paper_postcode'))
                                        <span class="help-block">{{ $errors->first('paper_postcode') }}</span>
                                    @endif
                                </div>
                            </div>

                            <div class="col-sm-4">
                                <div class="form-group {{ $errors->has('paper_country') ? 'has-error' : '' }}">
                                    <label for="paper_country" class="control-label">Country <span class="text-danger">*</span></label>
                                    @php $paperIso = old('paper_country', $userDetails->certificate_address_country ?? ''); @endphp
                                    <select id="paper_country" name="paper_country" class="form-control paper-diff-required">
                                        <option value="">Please select…</option>
                                        @if($uk)
                                            <option value="{{ $uk->iso3 }}" {{ $paperIso === $uk->iso3 ? 'selected' : '' }}>{{ $uk->nicename }}</option>
                                        @endif
                                        @foreach($countries as $c)
                                            @continue($c->id == 225)
                                            <option value="{{ $c->iso3 }}" {{ $paperIso === $c->iso3 ? 'selected' : '' }}>{{ $c->nicename }}</option>
                                        @endforeach
                                    </select>
                                    @if($errors->has('paper_country'))
                                        <span class="help-block">{{ $errors->first('paper_country') }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <fieldset class="option-fieldset">
                <legend>Responsible Organisation access</legend>
                <div class="checkbox" style="margin:0">
                    <label>
                        <input
                            type="checkbox"
                            name="ro_may_view_first"
                            value="1"
                            {{ old('ro_may_view_first', ($userDetails->dbs_consent ?? 0)) ? 'checked' : '' }}
                        >
                        I consent to the Responsible Organisation viewing my certificate result before I receive the certificate.
                    </label>
                </div>
            </fieldset>
        </div>

        {{-- FINAL ACTIONS --}}
        <div class="form-group m-t-20">
            <div class="col-sm-12">
                <a class="btn btn-default" href="{{ route('candidate.welcome') }}">Back</a>
                <button type="submit" class="btn forceBgClassified">
                    Save and continue <i class="fa fa-arrow-right"></i>
                </button>
            </div>
        </div>
    </div>
</form>

<script>
(function(){
    var form = document.querySelector('form.form-horizontal');

    /* -------------------------------------------------------------
     | Consent gate
     | ------------------------------------------------------------- */
    var consentSaveUrl = "{{ route('candidate.dbsbasic.consent.save') }}";
    var csrfToken = form ? form.querySelector('input[name="_token"]').value : '';

    function setConsentError(el, msg){
        if (!el) return;

        var container = el.closest('.consent-action, .terms-box, .form-group');
        var errorEl = document.getElementById(el.id + '_error');

        if (container) {
            container.classList.toggle('has-error', !!msg);
        }

        if (msg) {
            el.setAttribute('aria-invalid', 'true');
            if (errorEl) {
                errorEl.textContent = msg;
                errorEl.classList.remove('hidden');
            }
        } else {
            el.removeAttribute('aria-invalid');
            if (errorEl) {
                errorEl.textContent = '';
                errorEl.classList.add('hidden');
            }
        }
    }

    function showConsentFeedback(type, message){
        var feedback = document.getElementById('consent_feedback');
        if (!feedback) return;

        feedback.className = 'alert consent-feedback alert-' + type;
        feedback.textContent = message;
        feedback.classList.remove('hidden');
    }

    function hideConsentFeedback(){
        var feedback = document.getElementById('consent_feedback');
        if (!feedback) return;
        feedback.classList.add('hidden');
        feedback.textContent = '';
    }

    function matches(value, phrase){
        return new RegExp('^\\s*' + phrase.replace(' ', '\\s*') + '\\s*$', 'i').test(value || '');
    }

    function validateConsent(){
        var a = document.getElementById('consent_a');
        var b = document.getElementById('consent_b');
        var c = document.getElementById('consent_c');
        var chk = document.getElementById('accept_terms');
        var ok = true;
        var firstInvalid = null;

        hideConsentFeedback();

        if (!a || !matches(a.value, 'I CONFIRM')) {
            if (a) setConsentError(a, 'Please type "I CONFIRM" to confirm the privacy declaration.');
            firstInvalid = firstInvalid || a;
            ok = false;
        } else {
            setConsentError(a, null);
        }

        if (!b || !matches(b.value, 'I AGREE')) {
            if (b) setConsentError(b, 'Please type "I AGREE" to agree to the electronic DBS result declaration.');
            firstInvalid = firstInvalid || b;
            ok = false;
        } else {
            setConsentError(b, null);
        }

        if (!c || !matches(c.value, 'I CONFIRM')) {
            if (c) setConsentError(c, 'Please type "I CONFIRM" to confirm the applicant declaration.');
            firstInvalid = firstInvalid || c;
            ok = false;
        } else {
            setConsentError(c, null);
        }

        if (!chk || !chk.checked) {
            if (chk) setConsentError(chk, 'Please tick this box to confirm that you accept the declarations above.');
            firstInvalid = firstInvalid || chk;
            ok = false;
        } else {
            setConsentError(chk, null);
        }

        if (!ok) {
            showConsentFeedback('danger', 'Please correct the highlighted consent fields before continuing.');

            if (firstInvalid) {
                firstInvalid.scrollIntoView({ behavior:'smooth', block:'center' });
                setTimeout(function(){ firstInvalid.focus(); }, 250);
            }
        }

        return ok;
    }

    function applyServerConsentErrors(errors){
        var firstInvalid = null;

        ['consent_a', 'consent_b', 'consent_c', 'accept_terms'].forEach(function(field){
            var el = document.getElementById(field);
            var messages = errors && errors[field] ? errors[field] : [];

            if (messages.length) {
                setConsentError(el, messages[0]);
                firstInvalid = firstInvalid || el;
            } else {
                setConsentError(el, null);
            }
        });

        if (firstInvalid) {
            firstInvalid.scrollIntoView({ behavior:'smooth', block:'center' });
            setTimeout(function(){ firstInvalid.focus(); }, 250);
        }
    }

    async function saveConsent(){
        var a = document.getElementById('consent_a');
        var b = document.getElementById('consent_b');
        var c = document.getElementById('consent_c');
        var chk = document.getElementById('accept_terms');

        var response = await fetch(consentSaveUrl, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                consent_a: a ? a.value : '',
                consent_b: b ? b.value : '',
                consent_c: c ? c.value : '',
                accept_terms: chk && chk.checked ? 1 : 0
            })
        });

        var data = {};
        try {
            data = await response.json();
        } catch (e) {
            data = {};
        }

        if (response.status === 422) {
            applyServerConsentErrors(data.errors || {});
            showConsentFeedback('danger', data.message || 'Please correct the highlighted consent fields.');
            return false;
        }

        if (!response.ok || !data.ok) {
            throw new Error(data.message || 'Your consent could not be saved.');
        }

        return true;
    }

    var consentBtn = document.getElementById('consent_continue');
    if (consentBtn) {
        consentBtn.addEventListener('click', async function(){
            if (!validateConsent()) return;

            var originalHtml = consentBtn.innerHTML;
            consentBtn.disabled = true;
            consentBtn.innerHTML = 'Saving… <i class="fa fa-spinner fa-spin" aria-hidden="true"></i>';

            try {
                var saved = await saveConsent();
                if (!saved) return;

                document.getElementById('consent_ack').value = '1';

                var consentSection = document.getElementById('consent_section');
                var restWrapper = document.getElementById('rest_wrapper');
                var savedBanner = document.getElementById('consent_saved_banner');

                if (consentSection) consentSection.classList.add('hidden');
                if (restWrapper) restWrapper.classList.remove('hidden');
                if (savedBanner) savedBanner.classList.remove('hidden');

                var firstField = document.getElementById('purpose');
                if (firstField) {
                    firstField.scrollIntoView({ behavior:'smooth', block:'center' });
                    setTimeout(function(){ firstField.focus(); }, 250);
                }
            } catch (err) {
                showConsentFeedback(
                    'danger',
                    err && err.message
                        ? err.message
                        : 'We could not save your consent. Please try again.'
                );
            } finally {
                consentBtn.disabled = false;
                consentBtn.innerHTML = originalHtml;
            }
        });
    }

    // Clear consent errors as soon as the candidate fixes the field.
    var consentA = document.getElementById('consent_a');
    var consentB = document.getElementById('consent_b');
    var consentC = document.getElementById('consent_c');
    var acceptTerms = document.getElementById('accept_terms');

    if (consentA) {
        consentA.addEventListener('input', function(){
            if (matches(this.value, 'I CONFIRM')) setConsentError(this, null);
        });
    }
    if (consentB) {
        consentB.addEventListener('input', function(){
            if (matches(this.value, 'I AGREE')) setConsentError(this, null);
        });
    }
    if (consentC) {
        consentC.addEventListener('input', function(){
            if (matches(this.value, 'I CONFIRM')) setConsentError(this, null);
        });
    }
    if (acceptTerms) {
        acceptTerms.addEventListener('change', function(){
            if (this.checked) setConsentError(this, null);
        });
    }

    /* -------------------------------------------------------------
     | Guided optional identity information
     | ------------------------------------------------------------- */
    function checkedValue(name){
        var el = document.querySelector('input[name="' + name + '"]:checked');
        return el ? el.value : null;
    }

    function configureConditionalPanel(radioName, panelId, fieldIds){
        var panel = document.getElementById(panelId);

        function update(){
            var yes = checkedValue(radioName) === '1';
            if (panel) panel.classList.toggle('hidden', !yes);

            fieldIds.forEach(function(id){
                var field = document.getElementById(id);
                if (!field) return;

                field.disabled = !yes;
                if (yes) field.setAttribute('required', 'required');
                else field.removeAttribute('required');
            });
        }

        document.querySelectorAll('input[name="' + radioName + '"]').forEach(function(radio){
            radio.addEventListener('change', update);
        });

        update();
    }

    configureConditionalPanel('has_ni_number', 'ni_panel', [
        'ni_number'
    ]);

    configureConditionalPanel('has_driving_licence', 'licence_panel', [
        'licence_origin',
        'driving_licence_number',
        'licence_issue_date'
    ]);

    configureConditionalPanel('has_passport', 'passport_panel', [
        'passport_country',
        'passport_number',
        'passport_issue_date'
    ]);

    /* -------------------------------------------------------------
     | DBS Profile ID
     | ------------------------------------------------------------- */
    function updateDbsProfile(){
        var yes = checkedValue('has_dbs_profile') === '1';
        var wrap = document.getElementById('dbs_profile_wrap');
        var input = document.getElementById('dbs_profile_id');

        if (wrap) wrap.classList.toggle('hidden', !yes);

        if (input) {
            input.disabled = !yes;
            if (yes) input.setAttribute('required', 'required');
            else input.removeAttribute('required');
        }
    }

    document.querySelectorAll('input[name="has_dbs_profile"]').forEach(function(radio){
        radio.addEventListener('change', updateDbsProfile);
    });
    updateDbsProfile();

    /* -------------------------------------------------------------
     | Paper certificate options
     | ------------------------------------------------------------- */
    function updatePaperOptions(){
        var wantsPaper = checkedValue('paper_certificate') === '1';
        var options = document.getElementById('paper_options');
        var currentWrap = document.getElementById('paper_current_wrap');
        var diffWrap = document.getElementById('paper_diff_wrap');
        var addressRadios = document.querySelectorAll('input[name="paper_address_choice"]');
        var useDifferent = checkedValue('paper_address_choice') === 'different';

        if (options) options.classList.toggle('hidden', !wantsPaper);

        addressRadios.forEach(function(radio){
            radio.disabled = !wantsPaper;
            if (wantsPaper) radio.setAttribute('required', 'required');
            else radio.removeAttribute('required');
        });

        if (!wantsPaper) useDifferent = false;

        if (currentWrap) currentWrap.classList.toggle('hidden', !wantsPaper || useDifferent);
        if (diffWrap) diffWrap.classList.toggle('hidden', !wantsPaper || !useDifferent);

        document.querySelectorAll('.paper-diff-required').forEach(function(field){
            var required = wantsPaper && useDifferent;
            field.disabled = !wantsPaper || !useDifferent;
            if (required) field.setAttribute('required', 'required');
            else field.removeAttribute('required');
        });

        // Optional different-address fields should also be disabled when not in use,
        // otherwise an old hidden value can be submitted accidentally.
        ['paper_line2', 'paper_county'].forEach(function(id){
            var field = document.getElementById(id);
            if (field) field.disabled = !wantsPaper || !useDifferent;
        });
    }

    document.querySelectorAll('input[name="paper_certificate"], input[name="paper_address_choice"]').forEach(function(radio){
        radio.addEventListener('change', updatePaperOptions);
    });
    updatePaperOptions();

    /* -------------------------------------------------------------
     | Five-year continuous address history
     | ------------------------------------------------------------- */
    var wrapper = document.getElementById('rest_wrapper');
    var needHistory = wrapper && wrapper.dataset.needsHistory === '1';
    var historySection = document.getElementById('history_section');
    var list = document.getElementById('history_list');
    var addBtn = document.getElementById('add_history');
    var coverageBox = document.getElementById('history_coverage_box');
    var coverageTitle = document.getElementById('history_coverage_title');
    var coverageText = document.getElementById('history_coverage');
    var gapsList = document.getElementById('history_gaps');
    var idx = list ? list.querySelectorAll('.addr-card').length : 0;

    if (!needHistory && historySection) {
        historySection.classList.add('hidden');
    }

    function parseLocalDate(value){
        if (!value) return null;
        var parts = value.split('-');
        if (parts.length !== 3) return null;

        var d = new Date(
            parseInt(parts[0], 10),
            parseInt(parts[1], 10) - 1,
            parseInt(parts[2], 10)
        );

        return isNaN(d.getTime()) ? null : d;
    }

    function startOfToday(){
        var d = new Date();
        d.setHours(0,0,0,0);
        return d;
    }

    function addDays(date, days){
        var d = new Date(date.getTime());
        d.setDate(d.getDate() + days);
        return d;
    }

    function formatDate(date){
        if (!date) return '';
        return String(date.getDate()).padStart(2, '0') + '/' +
               String(date.getMonth() + 1).padStart(2, '0') + '/' +
               date.getFullYear();
    }

    function previousAddressTemplate(i){
        return '' +
        '<div class="addr-card" data-index="' + i + '">' +
            '<div class="addr-card-header">' +
                '<div>' +
                    '<div class="addr-card-title">Previous address <span class="address-number"></span></div>' +
                    '<div class="addr-card-summary">Add the dates you lived at this address.</div>' +
                '</div>' +
                '<button type="button" class="btn btn-link text-danger remove-history">' +
                    '<i class="fa fa-trash"></i> Remove' +
                '</button>' +
            '</div>' +
            '<div class="addr-card-body">' +
                '<div class="row field-row">' +
                    '<div class="col-sm-6"><div class="form-group">' +
                        '<label for="history_' + i + '_line1" class="control-label">Address line 1 <span class="text-danger">*</span></label>' +
                        '<input id="history_' + i + '_line1" type="text" class="form-control" name="history[' + i + '][line1]" required>' +
                    '</div></div>' +
                    '<div class="col-sm-6"><div class="form-group">' +
                        '<label for="history_' + i + '_line2" class="control-label">Address line 2</label>' +
                        '<input id="history_' + i + '_line2" type="text" class="form-control" name="history[' + i + '][line2]">' +
                    '</div></div>' +
                '</div>' +

                '<div class="row field-row">' +
                    '<div class="col-sm-4"><div class="form-group">' +
                        '<label for="history_' + i + '_city" class="control-label">Town/City <span class="text-danger">*</span></label>' +
                        '<input id="history_' + i + '_city" type="text" class="form-control" name="history[' + i + '][city]" required>' +
                    '</div></div>' +
                    '<div class="col-sm-4"><div class="form-group">' +
                        '<label for="history_' + i + '_county" class="control-label">County</label>' +
                        '<input id="history_' + i + '_county" type="text" class="form-control" name="history[' + i + '][county]">' +
                    '</div></div>' +
                    '<div class="col-sm-4"><div class="form-group">' +
                        '<label for="history_' + i + '_postcode" class="control-label">Postcode / postal code <span class="text-danger">*</span></label>' +
                        '<input id="history_' + i + '_postcode" type="text" class="form-control" name="history[' + i + '][postcode]" style="text-transform:uppercase" maxlength="20" required>' +
                    '</div></div>' +
                '</div>' +

                '<div class="row field-row">' +
                    '<div class="col-sm-4"><div class="form-group">' +
                        '<label for="history_' + i + '_country" class="control-label">Country <span class="text-danger">*</span></label>' +
                        '<select id="history_' + i + '_country" class="form-control" name="history[' + i + '][country]" required>' +
                            COUNTRY_OPTIONS_HTML +
                        '</select>' +
                    '</div></div>' +
                    '<div class="col-sm-4"><div class="form-group">' +
                        '<label for="history_' + i + '_from" class="control-label">Lived here from <span class="text-danger">*</span></label>' +
                        '<div class="input-group">' +
                            '<span class="input-group-addon"><i class="fa-regular fa-calendar" aria-hidden="true"></i></span>' +
                            '<input id="history_' + i + '_from" type="date" class="form-control hist-from" name="history[' + i + '][from]" max="{{ $today }}" required>' +
                        '</div>' +
                    '</div></div>' +
                    '<div class="col-sm-4"><div class="form-group">' +
                        '<label for="history_' + i + '_to" class="control-label">Lived here until <span class="text-danger">*</span></label>' +
                        '<div class="input-group">' +
                            '<span class="input-group-addon"><i class="fa-regular fa-calendar" aria-hidden="true"></i></span>' +
                            '<input id="history_' + i + '_to" type="date" class="form-control hist-to" name="history[' + i + '][to]" max="{{ $today }}" required>' +
                        '</div>' +
                    '</div></div>' +
                '</div>' +
            '</div>' +
        '</div>';
    }

    function updateAddressLabels(){
        if (!list) return;

        list.querySelectorAll('.addr-card').forEach(function(card, index){
            var number = card.querySelector('.address-number');
            var summary = card.querySelector('.addr-card-summary');
            var from = parseLocalDate(card.querySelector('.hist-from')?.value || '');
            var to = parseLocalDate(card.querySelector('.hist-to')?.value || '');

            if (number) number.textContent = index + 1;

            if (summary) {
                summary.textContent = (from && to)
                    ? formatDate(from) + ' – ' + formatDate(to)
                    : 'Add the dates you lived at this address.';
            }
        });
    }

    function setHistoryRequired(){
        if (!list || !needHistory) return;

        list.querySelectorAll('.addr-card').forEach(function(card){
            [
                'input[name*="[line1]"]',
                'input[name*="[city]"]',
                'input[name*="[postcode]"]',
                'select[name*="[country]"]',
                '.hist-from',
                '.hist-to'
            ].forEach(function(selector){
                var field = card.querySelector(selector);
                if (field) field.setAttribute('required', 'required');
            });
        });
    }

    function calculateCoverage(){
        var today = startOfToday();
        var requiredStart = new Date(today.getTime());
        requiredStart.setFullYear(requiredStart.getFullYear() - 5);

        var intervals = [];
        var incompleteRows = 0;
        var invalidRanges = 0;

        var currentFrom = parseLocalDate(wrapper ? wrapper.dataset.currentFrom : '');
        if (currentFrom) {
            intervals.push({
                start: currentFrom,
                end: today,
                label: 'Current address'
            });
        }

        if (list) {
            list.querySelectorAll('.addr-card').forEach(function(card, index){
                var from = parseLocalDate(card.querySelector('.hist-from')?.value || '');
                var to = parseLocalDate(card.querySelector('.hist-to')?.value || '');

                if (!from || !to) {
                    incompleteRows++;
                    return;
                }

                if (to < from) {
                    invalidRanges++;
                    return;
                }

                intervals.push({
                    start: from,
                    end: to,
                    label: 'Previous address ' + (index + 1)
                });
            });
        }

        // Only intervals touching the required five-year window matter.
        intervals = intervals
            .filter(function(interval){
                return interval.end >= requiredStart && interval.start <= today;
            })
            .map(function(interval){
                return {
                    start: interval.start < requiredStart ? requiredStart : interval.start,
                    end: interval.end > today ? today : interval.end,
                    label: interval.label
                };
            })
            .sort(function(a, b){
                return a.start - b.start;
            });

        var gaps = [];
        var cursor = requiredStart;

        intervals.forEach(function(interval){
            if (interval.end < cursor) return;

            if (interval.start > cursor) {
                gaps.push({
                    start: new Date(cursor.getTime()),
                    end: addDays(interval.start, -1)
                });
            }

            var nextUncovered = addDays(interval.end, 1);
            if (nextUncovered > cursor) cursor = nextUncovered;
        });

        if (cursor <= today) {
            gaps.push({
                start: new Date(cursor.getTime()),
                end: today
            });
        }

        return {
            ok: gaps.length === 0 && incompleteRows === 0 && invalidRanges === 0,
            gaps: gaps,
            incompleteRows: incompleteRows,
            invalidRanges: invalidRanges,
            hasCurrentFrom: !!currentFrom
        };
    }

    function renderCoverage(){
        if (!needHistory || !coverageBox || !coverageTitle || !coverageText || !gapsList) {
            return true;
        }

        updateAddressLabels();

        var state = calculateCoverage();
        gapsList.innerHTML = '';
        gapsList.classList.add('hidden');

        if (state.ok) {
            coverageBox.className = 'coverage-box success';
            coverageTitle.textContent = 'Your five-year address history is complete.';
            coverageText.textContent = 'Your current and previous addresses cover the full last five years without any gaps.';
            return true;
        }

        if (state.invalidRanges > 0) {
            coverageBox.className = 'coverage-box error';
            coverageTitle.textContent = 'One or more address date ranges need correcting.';
            coverageText.textContent = 'The "Lived here until" date cannot be earlier than the "Lived here from" date.';
            return false;
        }

        if (state.incompleteRows > 0) {
            coverageBox.className = 'coverage-box warning';
            coverageTitle.textContent = 'Complete the dates for each previous address.';
            coverageText.textContent = 'We can check for gaps once each previous address has both a start and end date.';
        } else {
            coverageBox.className = 'coverage-box error';
            coverageTitle.textContent = 'There are gaps in your five-year address history.';
            coverageText.textContent = 'Please add or adjust previous addresses to cover the periods shown below.';
        }

        if (state.gaps.length) {
            gapsList.classList.remove('hidden');

            state.gaps.forEach(function(gap){
                var li = document.createElement('li');
                li.textContent = 'Missing address coverage: ' + formatDate(gap.start) + ' to ' + formatDate(gap.end);
                gapsList.appendChild(li);
            });
        }

        return false;
    }

    function addHistoryRow(){
        if (!list) return;

        list.insertAdjacentHTML('beforeend', previousAddressTemplate(idx++));
        var cards = list.querySelectorAll('.addr-card');
        var newCard = cards[cards.length - 1];

        if (window.DBS_ADDR && newCard) {
            window.DBS_ADDR.wireHistoryCard(newCard);
        }

        setHistoryRequired();
        updateAddressLabels();
        renderCoverage();

        var first = newCard ? newCard.querySelector('input[name*="[line1]"]') : null;
        if (first) {
            first.scrollIntoView({ behavior:'smooth', block:'center' });
            setTimeout(function(){ first.focus(); }, 250);
        }
    }

    if (needHistory && list) {
        if (idx === 0) addHistoryRow();

        list.addEventListener('click', function(e){
            var remove = e.target.closest('.remove-history');
            if (!remove) return;

            var card = remove.closest('.addr-card');
            if (card) card.remove();

            updateAddressLabels();
            renderCoverage();
        });

        list.addEventListener('input', function(e){
            if (e.target.classList.contains('hist-from') || e.target.classList.contains('hist-to')) {
                renderCoverage();
            }
        });

        list.addEventListener('change', function(){
            renderCoverage();
        });

        if (addBtn) addBtn.addEventListener('click', addHistoryRow);

        setHistoryRequired();
        updateAddressLabels();
        renderCoverage();
    }

    /* -------------------------------------------------------------
     | Form submission guard
     | ------------------------------------------------------------- */
    if (form) {
        form.addEventListener('submit', function(e){
            // If the consent screen is still visible, don't let a browser submit
            // the hidden second half without completing the consent gate.
            var consentSection = document.getElementById('consent_section');
            if (consentSection && !consentSection.classList.contains('hidden')) {
                if (!validateConsent()) {
                    e.preventDefault();
                    e.stopPropagation();
                    return;
                }
            }

            if (needHistory && !renderCoverage()) {
                e.preventDefault();
                e.stopPropagation();

                if (coverageBox) {
                    coverageBox.scrollIntoView({ behavior:'smooth', block:'center' });
                }
            }
        });
    }

    /* -------------------------------------------------------------
     | Accessible help/error behaviour
     | ------------------------------------------------------------- */
    if (window.jQuery && typeof window.jQuery.fn.tooltip === 'function') {
        window.jQuery('[data-toggle="tooltip"]').tooltip({
            container: 'body',
            trigger: 'hover focus'
        });
    }

    document.querySelectorAll('.error-summary a[href^="#"]').forEach(function(link){
        link.addEventListener('click', function(){
            var target = document.querySelector(this.getAttribute('href'));
            if (!target) return;

            setTimeout(function(){
                if (typeof target.focus === 'function') {
                    if (!target.hasAttribute('tabindex') &&
                        !/^(INPUT|SELECT|TEXTAREA|BUTTON|A)$/.test(target.tagName)) {
                        target.setAttribute('tabindex', '-1');
                    }
                    target.focus();
                }
            }, 0);
        });
    });
})();
</script>

<script>
/**
 * DBS Address Autocomplete
 * - Wires Google Places to:
 *   1) Paper certificate "different address" fields (paper_line1,...)
 *   2) Each Address History card (history[i][...])
 * - Uses your $countries list (ISO2<->ISO3) so the <select> stays correct.
 * - Safe if Google script is already on the page (no double-loading).
 */
(function(){
  // ---- Build ISO2 <-> ISO3 maps from your countries ----
  const COUNTRY_ROWS = @json(
      $countries->map(fn($c) => ['iso2'=>$c->iso, 'iso3'=>$c->iso3, 'name'=>$c->nicename])->values()
  );
  const ISO2_TO_ISO3 = {};
  const ISO3_TO_ISO2 = {};
  COUNTRY_ROWS.forEach(c => {
    if (c.iso2) ISO2_TO_ISO3[c.iso2.toUpperCase()] = c.iso3?.toUpperCase() || null;
    if (c.iso3) ISO3_TO_ISO2[c.iso3.toUpperCase()] = c.iso2?.toUpperCase() || null;
  });

  function component(components, type){
    const c = components.find(x => x.types.includes(type));
    return c ? c.long_name : '';
  }
  function componentShort(components, type){
    const c = components.find(x => x.types.includes(type));
    return c ? c.short_name : '';
  }
  function buildLine1(components){
    const num = component(components, 'street_number');
    const route = component(components, 'route');
    if (num && route) return num + ' ' + route;
    return route || num || '';
  }
  function buildLine2(components){
    const subprem = component(components, 'subpremise');
    const prem    = component(components, 'premise');
    const neigh   = component(components, 'neighborhood');
    return [subprem, prem, neigh].filter(Boolean).join(', ');
  }
  function preferTown(components){
    return component(components, 'locality')
        || component(components, 'postal_town')
        || component(components, 'sublocality')
        || component(components, 'sublocality_level_1')
        || '';
  }
  function preferCounty(components){
    return component(components, 'administrative_area_level_2')
        || component(components, 'administrative_area_level_1')
        || '';
  }
  function setSelectCountryByIso3(selectEl, iso3){
    if (!selectEl || !iso3) return;
    if (selectEl.value !== iso3) {
      selectEl.value = iso3;
      selectEl.dispatchEvent(new Event('change', { bubbles:true }));
    }
  }

  /**
   * Attach a Places Autocomplete to a given "line1" input, and fill the provided fields.
   * opts = {
   *    line1, line2, city, county, post, countrySelect  (DOM elements)
   * }
   * Country restriction follows current <select> (ISO3 -> ISO2).
   */
  function makeAutocomplete(opts){
    if (!(window.google && google.maps && google.maps.places)) return null;
    const { line1, line2, city, county, post, countrySelect } = opts;
    if (!line1) return null;

    // Determine initial restriction from select (default GB)
    let restrictIso2 = 'GB';
    if (countrySelect && countrySelect.value) {
      const iso2 = ISO3_TO_ISO2[countrySelect.value.toUpperCase()];
      if (iso2) restrictIso2 = iso2;
    }

    const ac = new google.maps.places.Autocomplete(line1, {
      types: ['address'],
      fields: ['address_components', 'geometry'],
      componentRestrictions: { country: [restrictIso2.toLowerCase()] }
    });

    if (countrySelect) {
      countrySelect.addEventListener('change', function(){
        try {
          const iso3 = (countrySelect.value || '').toUpperCase();
          const iso2 = ISO3_TO_ISO2[iso3] || null;
          if (iso2 && ac && ac.setComponentRestrictions) {
            ac.setComponentRestrictions({ country: [iso2.toLowerCase()] });
          } else {
            ac.setComponentRestrictions({ country: [] }); // unrestrict
          }
        } catch(e){}
      });
    }

    ac.addListener('place_changed', function(){
      const place = ac.getPlace();
      if (!place || !place.address_components) return;
      const comps = place.address_components;

      const newLine1 = buildLine1(comps);
      if (newLine1) line1.value = newLine1;

      if (line2) line2.value = buildLine2(comps);
      if (city)  city.value  = preferTown(comps);
      if (county) county.value = preferCounty(comps);

      if (post) {
        const code = componentShort(comps, 'postal_code') || component(comps, 'postal_code') || '';
        post.value = code.toUpperCase();
        post.dispatchEvent(new Event('input', { bubbles:true })); // let any formatters run
      }

      const iso2 = componentShort(comps, 'country');
      const iso3 = iso2 ? (ISO2_TO_ISO3[iso2.toUpperCase()] || null) : null;
      if (countrySelect && iso3) setSelectCountryByIso3(countrySelect, iso3);
    });

    return ac;
  }

  // ----- Public helpers for your page script to call after it adds DOM -----
  window.DBS_ADDR = {
    // Wire the "paper certificate different address" block
    wirePaperAddress: function(){
      const line1   = document.getElementById('paper_line1');
      if (!line1) return; // block not visible
      makeAutocomplete({
        line1,
        line2:   document.getElementById('paper_line2'),
        city:    document.getElementById('paper_city'),
        county:  document.getElementById('paper_county'),
        post:    document.getElementById('paper_postcode'),
        countrySelect: document.getElementById('paper_country')
      });
    },
    // Wire a single Address History card element (pass the .addr-card node)
    wireHistoryCard: function(cardEl){
      if (!cardEl) return;
      const line1 = cardEl.querySelector('input[name*="[line1]"]');
      if (!line1) return;
      makeAutocomplete({
        line1,
        line2:   cardEl.querySelector('input[name*="[line2]"]'),
        city:    cardEl.querySelector('input[name*="[city]"]'),
        county:  cardEl.querySelector('input[name*="[county]"]'),
        post:    cardEl.querySelector('input[name*="[postcode]"]'),
        countrySelect: cardEl.querySelector('select[name*="[country]"]')
      });
    }
  };

  // ----- Callback Google will call (unique name for DBS page) -----
  window.__initDBSPlacesAutocomplete = function(){
    // Paper "different address"
    DBS_ADDR.wirePaperAddress();

    // Any pre-rendered history cards (e.g., from old() values)
    document.querySelectorAll('#history_list .addr-card').forEach(function(card){
      DBS_ADDR.wireHistoryCard(card);
    });
  };

  // If Google is already loaded (another page part added it), run immediately.
  if (window.google && google.maps && google.maps.places) {
    try { __initDBSPlacesAutocomplete(); } catch(e){}
  }
})();
</script>

<!-- Load Google Maps Places if not already present -->
<script>
(function(){
  if (window.google && google.maps && google.maps.places) return; // already loaded
  var s = document.createElement('script');
  s.src = "https://maps.googleapis.com/maps/api/js?key={{ config('services.google.maps_places_key') }}&libraries=places&callback=__initDBSPlacesAutocomplete";
  s.async = true; s.defer = true;
  document.head.appendChild(s);
})();
</script>
@endsection