@extends('layout.candidate')

@section('form-content')
@php
    $today = now()->format('Y-m-d');

    $uk = isset($countries) ? $countries->firstWhere('id', 225) : null;

    $sortedPhoneCodes = collect($phoneCodes ?? [])
        ->map(fn($code) => (string) $code)
        ->unique()
        ->sort(function ($a, $b) {
            if ($a === '44') return -1;
            if ($b === '44') return 1;
            return (int) $a <=> (int) $b;
        })
        ->values();

    $seedHistory = isset($academicHistory)
        ? collect($academicHistory)->values()
        : collect();

    $oldJson = old('academic_json');

    if ($oldJson) {
        try {
            $decoded = json_decode($oldJson, true, 512, JSON_THROW_ON_ERROR);
            $seedHistory = collect(is_array($decoded) ? $decoded : []);
        } catch (\Throwable $e) {
            // Keep the server-provided history if old JSON cannot be decoded.
        }
    }

    $hasAcademic = old(
        'has_academic',
        $hasAcademicAnswer
            ?? ($seedHistory->isNotEmpty() ? 'yes' : null)
    );

    $serverErrors = $errors->getMessages();
@endphp

<style>
.form-section{
    padding:20px 20px 5px;
    margin-bottom:20px;
}

.section-title{
    color:#2C3C64;
    font-weight:700;
    margin:0 0 15px;
    display:flex;
    align-items:center;
    gap:10px;
}

.section-title:after{
    content:"";
    flex:1;
    height:1px;
    background:#e5e9f2;
}

.field-row{
    margin-left:-10px;
    margin-right:-10px;
}

.field-row .form-group{
    padding-left:10px;
    padding-right:10px;
    margin-bottom:15px;
}

.form-control{
    height:40px;
}

textarea.form-control{
    height:auto;
    min-height:88px;
    resize:vertical;
}

.form-control:focus{
    border-color:var(--brand)!important;
    -webkit-box-shadow:
        inset 0 1px 1px rgba(0,0,0,.075),
        0 0 8px color-mix(in srgb,var(--brand) 60%,transparent)!important;
    box-shadow:
        inset 0 1px 1px rgba(0,0,0,.075),
        0 0 8px color-mix(in srgb,var(--brand) 60%,transparent)!important;
}

.control-label{
    font-weight:600;
    color:#2C3C64;
}

.help-block{
    margin-top:6px;
    color:#6b7480;
}

.has-error .control-label,
.has-error .help-block{
    color:#b94a48;
}

.has-error .form-control{
    border-color:#b94a48;
    box-shadow:none;
}

.hidden{
    display:none!important;
}

/* Intro / yes-no */
.academic-intro{
    background:#f7f9fc;
    border:1px solid #e5e9f2;
    border-radius:8px;
    padding:16px 18px;
    margin-bottom:18px;
}

.academic-intro p:last-child{
    margin-bottom:0;
}

.question-card{
    border:1px solid #dfe5ee;
    border-radius:8px;
    background:#fff;
    padding:15px 16px;
    margin-bottom:18px;
}

.question-card fieldset{
    border:0;
    padding:0;
    margin:0;
}

.question-card legend{
    border:0;
    width:auto;
    padding:0;
    margin:0 0 10px;
    font-size:15px;
    color:#2C3C64;
    font-weight:700;
}

.radio-options{
    display:flex;
    flex-wrap:wrap;
    gap:20px;
}

.radio-options label{
    margin:0;
    font-weight:500;
}

/* Entries */
.academic-toolbar{
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:12px;
    margin-bottom:14px;
}

.academic-toolbar-text{
    color:#6b7480;
    font-size:13px;
}

.entry-card{
    border:1px solid #dfe5ee;
    border-radius:8px;
    background:#fff;
    overflow:hidden;
    margin-bottom:16px;
}

.entry-card.complete{
    border-color:#bfe3ca;
}

.entry-card.error{
    border-color:#efb7b7;
}

.entry-head{
    display:flex;
    align-items:flex-start;
    justify-content:space-between;
    gap:12px;
    padding:13px 15px;
    background:#f7f9fc;
    border-bottom:1px solid #e5e9f2;
}

.entry-heading{
    min-width:0;
}

.entry-type{
    display:inline-block;
    margin-bottom:4px;
    padding:3px 8px;
    border-radius:999px;
    background:#eef2ff;
    color:#2C3C64;
    font-size:12px;
    font-weight:700;
}

.entry-title{
    color:#2C3C64;
    font-weight:700;
    font-size:15px;
    margin-bottom:2px;
}

.entry-summary{
    color:#6b7480;
    font-size:13px;
}

.entry-status{
    display:inline-flex;
    align-items:center;
    gap:5px;
    padding:5px 9px;
    border-radius:999px;
    font-size:12px;
    font-weight:700;
    white-space:nowrap;
}

.entry-status.neutral{
    background:#eef2ff;
    color:#2C3C64;
}

.entry-status.success{
    background:#eaf7ee;
    color:#246b38;
}

.entry-status.error{
    background:#fdecec;
    color:#a94442;
}

.entry-body{
    padding:15px;
}

.current-box,
.unknown-box{
    background:#f7f9fc;
    border:1px solid #e5e9f2;
    border-radius:6px;
    padding:10px 12px;
    margin-top:8px;
}

.current-box label,
.unknown-box label{
    margin:0;
    font-weight:500;
}

.contact-panel{
    border-top:1px solid #edf0f5;
    padding-top:15px;
    margin-top:5px;
}

.contact-panel-title{
    color:#2C3C64;
    font-weight:700;
    margin-bottom:4px;
}

.contact-panel-note{
    color:#6b7480;
    font-size:13px;
    margin-bottom:14px;
}

.phone-row{
    display:flex;
    align-items:stretch;
}

.phone-code{
    flex:0 0 120px;
}

.phone-code select{
    border-top-right-radius:0;
    border-bottom-right-radius:0;
}

.phone-number{
    flex:1;
    border-top-left-radius:0!important;
    border-bottom-left-radius:0!important;
    border-left:0;
}

.remove-entry{
    white-space:nowrap;
}

/* Empty/no-history states */
.empty-state{
    border:1px dashed #cbd3df;
    border-radius:8px;
    padding:24px 18px;
    text-align:center;
    color:#6b7480;
    background:#fbfcfe;
}

.empty-state i{
    display:block;
    font-size:28px;
    color:#2C3C64;
    margin-bottom:10px;
}

.no-history-state{
    border:1px solid #bfe3ca;
    background:#f3fbf6;
    border-radius:8px;
    padding:14px 16px;
    color:#315d3d;
}

/* Errors */
.client-error-summary{
    border:1px solid #efb7b7;
    background:#fff3f3;
    border-radius:8px;
    padding:14px 16px;
    margin-bottom:18px;
}

.client-error-summary h4{
    margin:0 0 8px;
    color:#8a1f1f;
    font-size:16px;
}

.client-error-summary ul{
    margin:0;
    padding-left:20px;
}

.client-error-summary button{
    border:0;
    background:transparent;
    color:#a94442;
    padding:0;
    text-decoration:underline;
    text-align:left;
}

.error-summary a{
    text-decoration:underline;
}

/* Actions */
.form-actions{
    display:flex;
    flex-wrap:wrap;
    justify-content:flex-end;
    gap:10px;
}

@media(max-width:768px){
    .form-horizontal .control-label{
        text-align:left;
        margin-bottom:6px;
    }

    .academic-toolbar,
    .entry-head{
        flex-direction:column;
        align-items:flex-start;
    }

    .academic-toolbar .btn,
    .form-actions .btn{
        width:100%;
    }

    .phone-row{
        display:block;
    }

    .phone-code{
        width:100%;
    }

    .phone-code select,
    .phone-number{
        width:100%;
        border-radius:4px!important;
        border-left:1px solid #ccc;
    }

    .phone-number{
        margin-top:8px;
    }
}
</style>

<form
    id="academicHistoryForm"
    class="form-horizontal"
    method="POST"
    action="{{ route('candidate.academic.save') }}"
    novalidate
>
    @csrf

    {{-- Server-side error summary --}}
    @if ($errors->any())
        <div
            class="alert alert-danger error-summary"
            role="alert"
            aria-live="polite"
            aria-atomic="true"
        >
            <p>
                <strong>Your academic history could not be saved yet.</strong>
            </p>

            <ul style="margin-bottom:0">
                @foreach ($errors->getMessages() as $field => $messages)
                    @foreach ($messages as $message)
                        <li>
                            <a
                                href="#academicHistorySection"
                                class="text-danger server-error-link"
                                data-error-field="{{ $field }}"
                            >
                                {{ $message }}
                            </a>
                        </li>
                    @endforeach
                @endforeach
            </ul>
        </div>
    @endif

    <div
        id="clientErrorSummary"
        class="client-error-summary hidden"
        role="alert"
        tabindex="-1"
    >
        <h4>Please check your academic history</h4>
        <ul id="clientErrorList"></ul>
    </div>

    <div class="p-5 bg-light rounded">
        <div
            class="form-section"
            id="academicHistorySection"
        >
            <h3 class="section-title">Academic history</h3>

            <div class="academic-intro">
                <p>
                    <strong>
                        Tell us about any school, college or university you attended during the last five years.
                    </strong>
                </p>

                <p>
                    Add each institution separately. You can include more than one school,
                    college or university if needed. We use these details to help verify
                    your academic history.
                </p>
            </div>

            <div class="question-card {{ $errors->has('has_academic') ? 'has-error' : '' }}">
                <fieldset>
                    <legend>
                        Have you attended a school, college or university during the last five years?
                        <span class="text-danger">*</span>
                    </legend>

                    <div class="radio-options">
                        <label>
                            <input
                                type="radio"
                                name="has_academic"
                                id="has_academic_yes"
                                value="yes"
                                {{ $hasAcademic === 'yes' ? 'checked' : '' }}
                            >
                            Yes
                        </label>

                        <label>
                            <input
                                type="radio"
                                name="has_academic"
                                id="has_academic_no"
                                value="no"
                                {{ $hasAcademic === 'no' ? 'checked' : '' }}
                            >
                            No
                        </label>
                    </div>

                    @if($errors->has('has_academic'))
                        <span class="help-block">
                            {{ $errors->first('has_academic') }}
                        </span>
                    @else
                        <span class="help-block"></span>
                    @endif
                </fieldset>
            </div>

            <div
                id="academicYesPanel"
                class="{{ $hasAcademic === 'yes' ? '' : 'hidden' }}"
            >
                <div class="academic-toolbar">
                    <div class="academic-toolbar-text">
                        Add every institution you attended during the five-year period.
                    </div>

                    <button
                        type="button"
                        id="addInstitution"
                        class="btn btn-default"
                    >
                        <i class="fa fa-plus" aria-hidden="true"></i>
                        Add institution
                    </button>
                </div>

                <div id="academicList"></div>

                <div
                    id="academicEmpty"
                    class="empty-state hidden"
                >
                    <i class="fa fa-graduation-cap" aria-hidden="true"></i>
                    <strong>No institutions added yet.</strong>
                    <div>
                        Select <strong>Add institution</strong> to enter your academic history.
                    </div>
                </div>
            </div>

            <div
                id="academicNoPanel"
                class="no-history-state {{ $hasAcademic === 'no' ? '' : 'hidden' }}"
            >
                <i class="fa fa-circle-check" aria-hidden="true"></i>
                You have confirmed that you have not attended school, college or university
                during the last five years. You can save and continue.
            </div>

            <input
                type="hidden"
                id="academicJson"
                name="academic_json"
                value="{{ old('academic_json', $seedHistory->toJson()) }}"
            >
        </div>

        <div class="form-section">
            <div class="form-actions">
                <a
                    class="btn btn-default"
                    href="{{ route('candidate.welcome') }}"
                >
                    Back
                </a>

                <button
                    type="submit"
                    class="btn forceBgClassified"
                >
                    Save and continue
                    <i class="fa fa-arrow-right" aria-hidden="true"></i>
                </button>
            </div>
        </div>
    </div>
</form>

<script>
(function(){
    'use strict';

    const TODAY = @json($today);
    const SERVER_ERRORS = @json($serverErrors);

    const COUNTRY_ROWS = @json(
        isset($countries)
            ? $countries->map(fn($c) => [
                'iso2'=>$c->iso,
                'iso3'=>$c->iso3,
                'name'=>$c->nicename
            ])->values()
            : collect()
    );

    const PHONE_CODES = @json($sortedPhoneCodes->values());

    const SEED_ROWS = @json($seedHistory->values());

    const form = document.getElementById('academicHistoryForm');
    const yesRadio = document.getElementById('has_academic_yes');
    const noRadio = document.getElementById('has_academic_no');
    const yesPanel = document.getElementById('academicYesPanel');
    const noPanel = document.getElementById('academicNoPanel');
    const listEl = document.getElementById('academicList');
    const emptyEl = document.getElementById('academicEmpty');
    const addBtn = document.getElementById('addInstitution');
    const hiddenEl = document.getElementById('academicJson');

    const errorSummary = document.getElementById('clientErrorSummary');
    const errorList = document.getElementById('clientErrorList');

    if (!form || !listEl || !hiddenEl) return;

    const ISO2_TO_ISO3 = {};
    const ISO3_TO_ISO2 = {};

    COUNTRY_ROWS.forEach(function(country){
        if (country.iso2) {
            ISO2_TO_ISO3[country.iso2.toUpperCase()] =
                country.iso3 ? country.iso3.toUpperCase() : null;
        }

        if (country.iso3) {
            ISO3_TO_ISO2[country.iso3.toUpperCase()] =
                country.iso2 ? country.iso2.toUpperCase() : null;
        }
    });

    function uid(){
        return 'a' + Math.random().toString(36).slice(2,10);
    }

    function esc(value){
        return String(value ?? '').replace(/[&<>"']/g,function(ch){
            return {
                '&':'&amp;',
                '<':'&lt;',
                '>':'&gt;',
                '"':'&quot;',
                "'":'&#039;'
            }[ch];
        });
    }

    function parseDate(value){
        if (!value) return null;

        const date = new Date(value + 'T12:00:00');
        return isNaN(date.getTime()) ? null : date;
    }

    function formatDate(value){
        const date = parseDate(value);
        if (!date) return '';

        return date.toLocaleDateString('en-GB',{
            month:'short',
            year:'numeric'
        });
    }

    function todayDate(){
        return parseDate(TODAY);
    }

    function fiveYearsAgo(){
        const date = todayDate();
        date.setFullYear(date.getFullYear() - 5);
        return date;
    }

    function normaliseRow(row,index){
        return Object.assign({
            _id:uid(),
            _serverIndex:index,
            type:'',
            name:'',
            course:'',
            from:'',
            to:'',
            is_current:false,

            contact_unknown:false,
            contact_name:'',
            contact_email:'',
            contact_phone_country_code:'44',
            contact_phone:'',

            address_line1:'',
            address_line2:'',
            town:'',
            county:'',
            postcode:'',
            country:''
        },row || {},{
            _id:(row && row._id) ? row._id : uid(),

            _serverIndex:index,

            is_current:
                row && (
                    row.is_current === true ||
                    row.is_current === 1 ||
                    row.is_current === '1'
                ),

            contact_unknown:
                row && (
                    row.contact_unknown === true ||
                    row.contact_unknown === 1 ||
                    row.contact_unknown === '1'
                )
        });
    }

    let rows = [];

    try {
        const oldValue = hiddenEl.value;

        if (oldValue && oldValue.trim()) {
            const decoded = JSON.parse(oldValue);

            if (Array.isArray(decoded)) {
                rows = decoded.map(normaliseRow);
            }
        }
    } catch(e){
        rows = (SEED_ROWS || []).map(normaliseRow);
    }

    if (!rows.length && Array.isArray(SEED_ROWS)) {
        rows = SEED_ROWS.map(normaliseRow);
    }

    function selected(value,current){
        return String(value) === String(current)
            ? ' selected'
            : '';
    }

    function checked(value){
        return value ? ' checked' : '';
    }

    function phoneCodeOptions(current){
        let html = '<option value="">Code</option>';

        PHONE_CODES.forEach(function(code){
            html +=
                '<option value="' + esc(code) + '"' +
                selected(code,current) +
                '>+' + esc(code) + '</option>';
        });

        return html;
    }

    function countryOptions(current){
        let html =
            '<option value="">Please select…</option>';

        const uk = COUNTRY_ROWS.find(function(country){
            return String(country.iso3 || '').toUpperCase() === 'GBR';
        });

        if (uk) {
            html +=
                '<option value="' + esc(uk.iso3) + '"' +
                selected(uk.iso3,current) +
                '>' + esc(uk.name) + '</option>';
        }

        COUNTRY_ROWS.forEach(function(country){
            if (
                uk &&
                String(country.iso3) === String(uk.iso3)
            ) {
                return;
            }

            html +=
                '<option value="' + esc(country.iso3 || '') + '"' +
                selected(country.iso3,current) +
                '>' + esc(country.name || '') + '</option>';
        });

        return html;
    }

    function typeLabel(type){
        return {
            school:'School',
            college:'College',
            university:'University',
            other:'Other education'
        }[type] || 'Education';
    }

    function dateSummary(row){
        if (!row.from) {
            return 'Dates not completed';
        }

        const start = formatDate(row.from);

        if (row.is_current) {
            return start + ' – Present';
        }

        if (!row.to) {
            return start + ' – end date not completed';
        }

        return start + ' – ' + formatDate(row.to);
    }

    function cardHtml(row,index){
        const currentClass =
            row.is_current ? 'hidden' : '';

        const contactClass =
            row.contact_unknown ? 'hidden' : '';

        return `
        <div
            class="entry-card"
            id="academic-card-${esc(row._id)}"
            data-id="${esc(row._id)}"
            data-row-index="${index}"
        >
            <div class="entry-head">
                <div class="entry-heading">
                    <span class="entry-type">
                        <i class="fa fa-graduation-cap" aria-hidden="true"></i>
                        ${esc(typeLabel(row.type))}
                    </span>

                    <div class="entry-title">
                        ${esc(row.name || ('Institution ' + (index + 1)))}
                    </div>

                    <div class="entry-summary">
                        ${esc(dateSummary(row))}
                    </div>
                </div>

                <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap">
                    <span
                        class="entry-status neutral"
                        data-role="status"
                    >
                        <i class="fa fa-circle-dot" aria-hidden="true"></i>
                        Needs details
                    </span>

                    <button
                        type="button"
                        class="btn btn-link text-danger remove-entry"
                        data-action="remove"
                    >
                        <i class="fa fa-trash" aria-hidden="true"></i>
                        Remove
                    </button>
                </div>
            </div>

            <div class="entry-body">
                <div class="row field-row">
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label
                                class="control-label"
                                for="academic_${index}_type"
                            >
                                Institution type
                                <span class="text-danger">*</span>
                            </label>

                            <select
                                id="academic_${index}_type"
                                class="form-control input-type"
                            >
                                <option value="">Please select…</option>
                                <option value="school"${selected('school',row.type)}>School</option>
                                <option value="college"${selected('college',row.type)}>College</option>
                                <option value="university"${selected('university',row.type)}>University</option>
                                <option value="other"${selected('other',row.type)}>Other education</option>
                            </select>

                            <span class="help-block"></span>
                        </div>
                    </div>

                    <div class="col-sm-8">
                        <div class="form-group">
                            <label
                                class="control-label"
                                for="academic_${index}_name"
                            >
                                Institution name
                                <span class="text-danger">*</span>
                            </label>

                            <input
                                id="academic_${index}_name"
                                type="text"
                                class="form-control input-name"
                                value="${esc(row.name)}"
                                autocomplete="organization"
                            >

                            <span class="help-block"></span>
                        </div>
                    </div>
                </div>

                <div class="row field-row">
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label
                                class="control-label"
                                for="academic_${index}_course"
                            >
                                Course / qualification
                            </label>

                            <input
                                id="academic_${index}_course"
                                type="text"
                                class="form-control input-course"
                                value="${esc(row.course)}"
                                placeholder="e.g. BSc (Hons) Computer Science, A Levels, GCSEs"
                            >

                            <span class="help-block">
                                Optional, but helpful for verification.
                            </span>
                        </div>
                    </div>
                </div>

                <div class="row field-row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label
                                class="control-label"
                                for="academic_${index}_from"
                            >
                                Attended from
                                <span class="text-danger">*</span>
                            </label>

                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa-regular fa-calendar" aria-hidden="true"></i>
                                </span>

                                <input
                                    id="academic_${index}_from"
                                    type="date"
                                    class="form-control input-from"
                                    value="${esc(row.from)}"
                                    max="${esc(TODAY)}"
                                >
                            </div>

                            <span class="help-block"></span>
                        </div>
                    </div>

                    <div class="col-sm-6">
                        <div class="form-group">
                            <label
                                class="control-label"
                                for="academic_${index}_to"
                            >
                                Attended until
                                ${row.is_current ? '' : '<span class="text-danger">*</span>'}
                            </label>

                            <div class="${currentClass} to-wrap">
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa-regular fa-calendar" aria-hidden="true"></i>
                                    </span>

                                    <input
                                        id="academic_${index}_to"
                                        type="date"
                                        class="form-control input-to"
                                        value="${esc(row.to)}"
                                        max="${esc(TODAY)}"
                                        ${row.is_current ? 'disabled' : ''}
                                    >
                                </div>
                            </div>

                            <div class="current-box">
                                <label>
                                    <input
                                        type="checkbox"
                                        class="input-current"
                                        ${checked(row.is_current)}
                                    >
                                    I currently attend this institution
                                </label>
                            </div>

                            <span class="help-block"></span>
                        </div>
                    </div>
                </div>

                <div class="contact-panel">
                    <div class="contact-panel-title">
                        Verification contact
                    </div>

                    <div class="contact-panel-note">
                        Provide the institution's current contact details where you can.
                        A department such as Student Records, Registry or School Office is fine.
                    </div>

                    <div class="unknown-box">
                        <label>
                            <input
                                type="checkbox"
                                class="input-contact-unknown"
                                ${checked(row.contact_unknown)}
                            >
                            I do not know or cannot find the institution's contact details
                        </label>
                    </div>

                    <div class="${contactClass} contact-fields">
                        <div class="row field-row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label
                                        class="control-label"
                                        for="academic_${index}_contact_name"
                                    >
                                        Contact name / department
                                    </label>

                                    <input
                                        id="academic_${index}_contact_name"
                                        type="text"
                                        class="form-control input-contact-name"
                                        value="${esc(row.contact_name)}"
                                        placeholder="e.g. Student Records, Registry, School Office"
                                    >

                                    <span class="help-block">
                                        Optional if you only have a general email or phone number.
                                    </span>
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label
                                        class="control-label"
                                        for="academic_${index}_contact_email"
                                    >
                                        Contact email
                                    </label>

                                    <div class="input-group">
                                        <span class="input-group-addon">
                                            <i class="fa-solid fa-envelope" aria-hidden="true"></i>
                                        </span>

                                        <input
                                            id="academic_${index}_contact_email"
                                            type="email"
                                            class="form-control input-contact-email"
                                            value="${esc(row.contact_email)}"
                                            placeholder="records@example.ac.uk"
                                        >
                                    </div>

                                    <span class="help-block">
                                        Provide an email or phone number.
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="row field-row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label
                                        class="control-label"
                                        for="academic_${index}_contact_phone"
                                    >
                                        Contact phone
                                    </label>

                                    <div class="phone-row">
                                        <div class="phone-code">
                                            <select
                                                id="academic_${index}_contact_phone_country_code"
                                                class="form-control input-phone-code"
                                                aria-label="Phone country code"
                                            >
                                                ${phoneCodeOptions(row.contact_phone_country_code)}
                                            </select>
                                        </div>

                                        <input
                                            id="academic_${index}_contact_phone"
                                            type="tel"
                                            class="form-control phone-number input-contact-phone"
                                            value="${esc(row.contact_phone)}"
                                            inputmode="tel"
                                            placeholder="e.g. 01752 123456"
                                        >
                                    </div>

                                    <span class="help-block">
                                        Provide a phone number or email address.
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="row field-row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label
                                        class="control-label"
                                        for="academic_${index}_address_line1"
                                    >
                                        Institution address line 1
                                        <span class="text-danger">*</span>
                                    </label>

                                    <input
                                        id="academic_${index}_address_line1"
                                        type="text"
                                        class="form-control input-address-line1"
                                        value="${esc(row.address_line1)}"
                                        autocomplete="street-address"
                                    >

                                    <span class="help-block">
                                        Start typing to search, or enter it manually.
                                    </span>
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label
                                        class="control-label"
                                        for="academic_${index}_address_line2"
                                    >
                                        Institution address line 2
                                    </label>

                                    <input
                                        id="academic_${index}_address_line2"
                                        type="text"
                                        class="form-control input-address-line2"
                                        value="${esc(row.address_line2)}"
                                        autocomplete="address-line2"
                                    >

                                    <span class="help-block"></span>
                                </div>
                            </div>
                        </div>

                        <div class="row field-row">
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label
                                        class="control-label"
                                        for="academic_${index}_town"
                                    >
                                        Town / City
                                        <span class="text-danger">*</span>
                                    </label>

                                    <input
                                        id="academic_${index}_town"
                                        type="text"
                                        class="form-control input-town"
                                        value="${esc(row.town)}"
                                        autocomplete="address-level2"
                                    >

                                    <span class="help-block"></span>
                                </div>
                            </div>

                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label
                                        class="control-label"
                                        for="academic_${index}_county"
                                    >
                                        County / State / Region
                                    </label>

                                    <input
                                        id="academic_${index}_county"
                                        type="text"
                                        class="form-control input-county"
                                        value="${esc(row.county)}"
                                        autocomplete="address-level1"
                                    >

                                    <span class="help-block"></span>
                                </div>
                            </div>

                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label
                                        class="control-label"
                                        for="academic_${index}_postcode"
                                    >
                                        Postcode / ZIP / postal code
                                        <span class="text-danger">*</span>
                                    </label>

                                    <input
                                        id="academic_${index}_postcode"
                                        type="text"
                                        class="form-control input-postcode"
                                        value="${esc(row.postcode)}"
                                        maxlength="20"
                                        autocomplete="postal-code"
                                        style="text-transform:uppercase"
                                    >

                                    <span class="help-block"></span>
                                </div>
                            </div>
                        </div>

                        <div class="row field-row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label
                                        class="control-label"
                                        for="academic_${index}_country"
                                    >
                                        Country
                                        <span class="text-danger">*</span>
                                    </label>

                                    <select
                                        id="academic_${index}_country"
                                        class="form-control input-country"
                                        autocomplete="country"
                                    >
                                        ${countryOptions(row.country)}
                                    </select>

                                    <span class="help-block"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>`;
    }

    function rowByCard(card){
        const id = card ? card.dataset.id : null;

        return rows.find(function(row){
            return row._id === id;
        }) || null;
    }

    function serialisableRows(){
        return rows.map(function(row){
            const copy = Object.assign({},row);

            delete copy._id;
            delete copy._serverIndex;

            if (copy.is_current) {
                copy.to = '';
            }

            return copy;
        });
    }

    function serialise(){
        hiddenEl.value = JSON.stringify(
            serialisableRows()
        );
    }

    function render(){
        listEl.innerHTML = rows.map(cardHtml).join('');

        emptyEl.classList.toggle(
            'hidden',
            rows.length !== 0
        );

        listEl
            .querySelectorAll('.entry-card')
            .forEach(wireCard);

        wireAddressAutocomplete();
        serialise();
        refreshAllStatuses();
        applyServerErrors();
    }

    function setFieldError(element,message){
        if (!element) return;

        const group = element.closest('.form-group');

        if (!group) return;

        group.classList.toggle(
            'has-error',
            !!message
        );

        if (message) {
            element.setAttribute(
                'aria-invalid',
                'true'
            );
        } else {
            element.removeAttribute(
                'aria-invalid'
            );
        }

        let help = group.querySelector('.help-block');

        if (!help) {
            help = document.createElement('span');
            help.className = 'help-block';
            group.appendChild(help);
        }

        if (message) {
            help.textContent = message;
            help.style.display = 'block';
        }
    }

    function clearFieldError(element){
        if (!element) return;

        const group = element.closest('.form-group');

        if (!group) return;

        group.classList.remove('has-error');
        element.removeAttribute('aria-invalid');
    }

    function validateEmail(value){
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(
            String(value || '').trim()
        );
    }

    function phoneDigits(value){
        return String(value || '').replace(/\D+/g,'');
    }

    function validateCard(card,showErrors){
        const row = rowByCard(card);

        if (!row) {
            return {
                ok:false,
                errors:[]
            };
        }

        const errors = [];

        function add(field,selector,message){
            const element = card.querySelector(selector);

            errors.push({
                rowId:row._id,
                field:field,
                element:element,
                message:message
            });

            if (showErrors) {
                setFieldError(element,message);
            }
        }

        [
            '.input-type',
            '.input-name',
            '.input-course',
            '.input-from',
            '.input-to',
            '.input-contact-email',
            '.input-phone-code',
            '.input-contact-phone',
            '.input-address-line1',
            '.input-town',
            '.input-postcode',
            '.input-country'
        ].forEach(function(selector){
            clearFieldError(
                card.querySelector(selector)
            );
        });

        if (!String(row.type || '').trim()) {
            add(
                'type',
                '.input-type',
                'Please select the institution type.'
            );
        }

        if (!String(row.name || '').trim()) {
            add(
                'name',
                '.input-name',
                'Institution name is required.'
            );
        }

        const from = parseDate(row.from);

        if (!from) {
            add(
                'from',
                '.input-from',
                'Enter when you started attending this institution.'
            );
        } else if (from > todayDate()) {
            add(
                'from',
                '.input-from',
                'The start date cannot be in the future.'
            );
        }

        const effectiveTo =
            row.is_current
                ? TODAY
                : row.to;

        const to = parseDate(effectiveTo);

        if (!to) {
            add(
                'to',
                '.input-to',
                'Enter when you stopped attending, or select that you currently attend this institution.'
            );
        } else if (to > todayDate()) {
            add(
                'to',
                '.input-to',
                'The end date cannot be in the future.'
            );
        } else if (from && to < from) {
            add(
                'to',
                '.input-to',
                'The end date cannot be before the start date.'
            );
        } else if (to < fiveYearsAgo()) {
            add(
                'to',
                '.input-to',
                'This education ended more than five years ago, so you do not need to include it.'
            );
        }

        if (!row.contact_unknown) {
            const email = String(
                row.contact_email || ''
            ).trim();

            const phone = phoneDigits(
                row.contact_phone
            );

            if (!email && !phone) {
                add(
                    'contact_email',
                    '.input-contact-email',
                    'Provide an email address or phone number, or select that you do not know the contact details.'
                );
            }

            if (email && !validateEmail(email)) {
                add(
                    'contact_email',
                    '.input-contact-email',
                    'Enter a valid institution email address.'
                );
            }

            if (phone) {
                if (!row.contact_phone_country_code) {
                    add(
                        'contact_phone_country_code',
                        '.input-phone-code',
                        'Select a phone country code.'
                    );
                }

                if (
                    phone.length < 5 ||
                    phone.length > 18
                ) {
                    add(
                        'contact_phone',
                        '.input-contact-phone',
                        'Enter a valid institution phone number.'
                    );
                }
            }

            if (!String(row.address_line1 || '').trim()) {
                add(
                    'address_line1',
                    '.input-address-line1',
                    'Institution address line 1 is required.'
                );
            }

            if (!String(row.town || '').trim()) {
                add(
                    'town',
                    '.input-town',
                    'Town / city is required.'
                );
            }

            if (!String(row.postcode || '').trim()) {
                add(
                    'postcode',
                    '.input-postcode',
                    'Postcode / postal code is required.'
                );
            }

            if (!String(row.country || '').trim()) {
                add(
                    'country',
                    '.input-country',
                    'Please select the institution country.'
                );
            }
        } else {
            const email = String(
                row.contact_email || ''
            ).trim();

            if (
                email &&
                !validateEmail(email)
            ) {
                add(
                    'contact_email',
                    '.input-contact-email',
                    'Enter a valid institution email address.'
                );
            }
        }

        return {
            ok:errors.length === 0,
            errors:errors
        };
    }

    function refreshCardStatus(card){
        const result = validateCard(
            card,
            false
        );

        const row = rowByCard(card);

        if (!row) return;

        const status = card.querySelector(
            '[data-role="status"]'
        );

        const title = card.querySelector(
            '.entry-title'
        );

        const type = card.querySelector(
            '.entry-type'
        );

        const summary = card.querySelector(
            '.entry-summary'
        );

        if (title) {
            title.textContent =
                row.name ||
                'Institution';
        }

        if (type) {
            type.innerHTML =
                '<i class="fa fa-graduation-cap" aria-hidden="true"></i> ' +
                esc(typeLabel(row.type));
        }

        if (summary) {
            const parts = [];

            if (row.course) {
                parts.push(row.course);
            }

            parts.push(dateSummary(row));

            summary.textContent =
                parts.filter(Boolean).join(' · ');
        }

        card.classList.remove(
            'complete',
            'error'
        );

        if (result.ok) {
            card.classList.add('complete');

            status.className =
                'entry-status success';

            status.innerHTML =
                '<i class="fa fa-circle-check" aria-hidden="true"></i> Complete';
        } else {
            status.className =
                'entry-status neutral';

            status.innerHTML =
                '<i class="fa fa-circle-dot" aria-hidden="true"></i> Needs details';
        }
    }

    function refreshAllStatuses(){
        listEl
            .querySelectorAll('.entry-card')
            .forEach(refreshCardStatus);
    }

    function updateCardData(card){
        const row = rowByCard(card);

        if (!row) return;

        const get = function(selector){
            return card.querySelector(selector);
        };

        row.type =
            get('.input-type')?.value || '';

        row.name =
            get('.input-name')?.value || '';

        row.course =
            get('.input-course')?.value || '';

        row.from =
            get('.input-from')?.value || '';

        row.to =
            row.is_current
                ? ''
                : (get('.input-to')?.value || '');

        row.contact_name =
            get('.input-contact-name')?.value || '';

        row.contact_email =
            get('.input-contact-email')?.value || '';

        row.contact_phone_country_code =
            get('.input-phone-code')?.value || '';

        row.contact_phone =
            get('.input-contact-phone')?.value || '';

        row.address_line1 =
            get('.input-address-line1')?.value || '';

        row.address_line2 =
            get('.input-address-line2')?.value || '';

        row.town =
            get('.input-town')?.value || '';

        row.county =
            get('.input-county')?.value || '';

        row.postcode =
            String(
                get('.input-postcode')?.value || ''
            ).toUpperCase();

        row.country =
            get('.input-country')?.value || '';

        serialise();
        refreshCardStatus(card);
    }

    function wireCard(card){
        const row = rowByCard(card);

        if (!row) return;

        card.addEventListener(
            'input',
            function(event){
                const target = event.target;

                if (
                    !target.matches(
                        'input,select,textarea'
                    )
                ) {
                    return;
                }

                updateCardData(card);
            }
        );

        card.addEventListener(
            'change',
            function(event){
                const target = event.target;

                if (
                    target.classList.contains(
                        'input-current'
                    )
                ) {
                    row.is_current = target.checked;

                    if (row.is_current) {
                        row.to = '';
                    }

                    render();
                    return;
                }

                if (
                    target.classList.contains(
                        'input-contact-unknown'
                    )
                ) {
                    row.contact_unknown =
                        target.checked;

                    render();
                    return;
                }

                updateCardData(card);

                if (
                    target.classList.contains(
                        'input-country'
                    )
                ) {
                    wireAddressAutocomplete();
                }
            }
        );

        const remove = card.querySelector(
            '[data-action="remove"]'
        );

        if (remove) {
            remove.addEventListener(
                'click',
                function(){
                    rows = rows.filter(
                        function(item){
                            return item._id !== row._id;
                        }
                    );

                    render();
                }
            );
        }
    }

    function addInstitution(prefill){
        rows.push(
            normaliseRow(
                Object.assign(
                    {
                        type:'',
                        contact_phone_country_code:'44'
                    },
                    prefill || {}
                ),
                rows.length
            )
        );

        render();

        const row = rows[rows.length - 1];
        const card = document.getElementById(
            'academic-card-' + row._id
        );

        if (card) {
            card.scrollIntoView({
                behavior:'smooth',
                block:'center'
            });

            setTimeout(function(){
                card.querySelector(
                    '.input-type'
                )?.focus();
            },250);
        }
    }

    function hasAcademicValue(){
        if (yesRadio.checked) return 'yes';
        if (noRadio.checked) return 'no';
        return '';
    }

    function updateMode(){
        const value = hasAcademicValue();

        yesPanel.classList.toggle(
            'hidden',
            value !== 'yes'
        );

        noPanel.classList.toggle(
            'hidden',
            value !== 'no'
        );

        if (
            value === 'yes' &&
            rows.length === 0
        ) {
            emptyEl.classList.remove('hidden');
        }

        if (value === 'no') {
            errorSummary.classList.add('hidden');
        }
    }

    function validateForm(){
        const errors = [];
        const mode = hasAcademicValue();

        if (!mode) {
            errors.push({
                element:yesRadio,
                message:
                    'Please tell us whether you have attended school, college or university during the last five years.'
            });

            return errors;
        }

        if (mode === 'no') {
            return errors;
        }

        if (rows.length === 0) {
            errors.push({
                element:addBtn,
                message:
                    'Please add at least one school, college or university.'
            });

            return errors;
        }

        listEl
            .querySelectorAll('.entry-card')
            .forEach(function(card){
                const result = validateCard(
                    card,
                    true
                );

                result.errors.forEach(
                    function(error){
                        errors.push(error);
                    }
                );

                card.classList.toggle(
                    'error',
                    !result.ok
                );
            });

        return errors;
    }

    function showErrors(errors){
        errorList.innerHTML = '';

        if (!errors.length) {
            errorSummary.classList.add(
                'hidden'
            );
            return;
        }

        errors.forEach(function(error){
            const li = document.createElement('li');
            const button = document.createElement('button');

            button.type = 'button';
            button.textContent = error.message;

            button.addEventListener(
                'click',
                function(){
                    if (error.element) {
                        error.element.scrollIntoView({
                            behavior:'smooth',
                            block:'center'
                        });

                        setTimeout(function(){
                            try {
                                error.element.focus();
                            } catch(e){}
                        },250);
                    }
                }
            );

            li.appendChild(button);
            errorList.appendChild(li);
        });

        errorSummary.classList.remove(
            'hidden'
        );

        errorSummary.focus();
    }

    function serverFieldSelector(field){
        const map = {
            type:'.input-type',
            name:'.input-name',
            course:'.input-course',
            from:'.input-from',
            to:'.input-to',
            contact_email:'.input-contact-email',
            contact_phone_country_code:'.input-phone-code',
            contact_phone:'.input-contact-phone',
            address_line1:'.input-address-line1',
            town:'.input-town',
            postcode:'.input-postcode',
            country:'.input-country'
        };

        return map[field] || null;
    }

    function applyServerErrors(){
        Object.keys(
            SERVER_ERRORS || {}
        ).forEach(function(key){
            if (!key.startsWith('academic.')) {
                return;
            }

            const parts = key.split('.');

            if (parts.length < 3) return;

            const index = parseInt(
                parts[1],
                10
            );

            const field = parts[2];

            const row = rows[index];

            if (!row) return;

            const card = document.getElementById(
                'academic-card-' + row._id
            );

            if (!card) return;

            const selector =
                serverFieldSelector(field);

            const element =
                selector
                    ? card.querySelector(selector)
                    : null;

            const messages =
                SERVER_ERRORS[key] || [];

            if (
                element &&
                messages.length
            ) {
                setFieldError(
                    element,
                    messages[0]
                );

                card.classList.add(
                    'error'
                );
            }
        });
    }

    yesRadio.addEventListener(
        'change',
        updateMode
    );

    noRadio.addEventListener(
        'change',
        updateMode
    );

    addBtn.addEventListener(
        'click',
        function(){
            addInstitution({});
        }
    );

    form.addEventListener(
        'submit',
        function(event){
            serialise();

            const errors = validateForm();

            if (errors.length) {
                event.preventDefault();
                event.stopPropagation();

                showErrors(errors);
            } else {
                showErrors([]);
            }
        }
    );

    document
        .querySelectorAll(
            '.server-error-link'
        )
        .forEach(function(link){
            link.addEventListener(
                'click',
                function(event){
                    event.preventDefault();

                    const field =
                        link.dataset.errorField || '';

                    if (
                        field.startsWith(
                            'academic.'
                        )
                    ) {
                        const parts =
                            field.split('.');

                        const index =
                            parseInt(
                                parts[1],
                                10
                            );

                        const row =
                            rows[index];

                        if (row) {
                            const card =
                                document.getElementById(
                                    'academic-card-' +
                                    row._id
                                );

                            if (card) {
                                card.scrollIntoView({
                                    behavior:'smooth',
                                    block:'center'
                                });

                                return;
                            }
                        }
                    }

                    document
                        .getElementById(
                            'academicHistorySection'
                        )
                        .scrollIntoView({
                            behavior:'smooth',
                            block:'start'
                        });
                }
            );
        });

    document.addEventListener(
        'blur',
        function(event){
            if (
                event.target &&
                event.target.classList.contains(
                    'input-postcode'
                )
            ) {
                event.target.value =
                    String(
                        event.target.value || ''
                    )
                    .toUpperCase()
                    .trim();

                const card =
                    event.target.closest(
                        '.entry-card'
                    );

                if (card) {
                    updateCardData(card);
                }
            }

            if (
                event.target &&
                event.target.matches(
                    'input[type="text"],input[type="email"],input[type="tel"]'
                )
            ) {
                event.target.value =
                    String(
                        event.target.value || ''
                    ).trim();

                const card =
                    event.target.closest(
                        '.entry-card'
                    );

                if (card) {
                    updateCardData(card);
                }
            }
        },
        true
    );

    /* ---------------------------------------------------------------
     | Google Places autocomplete
     | --------------------------------------------------------------- */

    function component(
        components,
        type,
        shortName
    ){
        const item = components.find(
            function(component){
                return component.types &&
                    component.types.includes(type);
            }
        );

        if (!item) return '';

        return shortName
            ? item.short_name
            : item.long_name;
    }

    function buildLine1(components){
        return [
            component(
                components,
                'street_number',
                false
            ),

            component(
                components,
                'route',
                false
            )
        ].filter(Boolean).join(' ');
    }

    function buildLine2(components){
        return [
            component(
                components,
                'subpremise',
                false
            ),

            component(
                components,
                'premise',
                false
            ),

            component(
                components,
                'neighborhood',
                false
            )
        ].filter(Boolean).join(', ');
    }

    function townFrom(components){
        return (
            component(
                components,
                'postal_town',
                false
            ) ||

            component(
                components,
                'locality',
                false
            ) ||

            component(
                components,
                'sublocality',
                false
            ) ||

            component(
                components,
                'sublocality_level_1',
                false
            )
        );
    }

    function countyFrom(components){
        return (
            component(
                components,
                'administrative_area_level_2',
                false
            ) ||

            component(
                components,
                'administrative_area_level_1',
                false
            )
        );
    }

    function wireOneAutocomplete(card){
        if (
            !window.google ||
            !google.maps ||
            !google.maps.places
        ) {
            return;
        }

        const address =
            card.querySelector(
                '.input-address-line1'
            );

        const addressLine2 =
            card.querySelector(
                '.input-address-line2'
            );

        const town =
            card.querySelector(
                '.input-town'
            );

        const county =
            card.querySelector(
                '.input-county'
            );

        const postcode =
            card.querySelector(
                '.input-postcode'
            );

        const country =
            card.querySelector(
                '.input-country'
            );

        if (
            !address ||
            address.dataset.placesWired === '1'
        ) {
            return;
        }

        address.dataset.placesWired = '1';

        const options = {
            types:['address'],
            fields:['address_components']
        };

        if (
            country &&
            country.value &&
            ISO3_TO_ISO2[
                country.value.toUpperCase()
            ]
        ) {
            options.componentRestrictions = {
                country:[
                    ISO3_TO_ISO2[
                        country.value.toUpperCase()
                    ].toLowerCase()
                ]
            };
        }

        const autocomplete =
            new google.maps.places.Autocomplete(
                address,
                options
            );

        if (country) {
            country.addEventListener(
                'change',
                function(){
                    try {
                        const iso2 =
                            ISO3_TO_ISO2[
                                String(
                                    country.value || ''
                                ).toUpperCase()
                            ];

                        autocomplete
                            .setComponentRestrictions(
                                iso2
                                    ? {
                                        country:[
                                            iso2.toLowerCase()
                                        ]
                                    }
                                    : {country:[]}
                            );
                    } catch(e){}
                }
            );
        }

        autocomplete.addListener(
            'place_changed',
            function(){
                const place =
                    autocomplete.getPlace();

                if (
                    !place ||
                    !place.address_components
                ) {
                    return;
                }

                const components =
                    place.address_components;

                const line1 =
                    buildLine1(components);

                const line2 =
                    buildLine2(components);

                const newTown =
                    townFrom(components);

                const newCounty =
                    countyFrom(components);

                const newPostcode =
                    component(
                        components,
                        'postal_code',
                        false
                    );

                const iso2 =
                    component(
                        components,
                        'country',
                        true
                    );

                const iso3 =
                    iso2
                        ? ISO2_TO_ISO3[
                            iso2.toUpperCase()
                        ]
                        : null;

                if (line1) {
                    address.value = line1;
                }

                if (addressLine2) {
                    addressLine2.value =
                        line2 || '';
                }

                if (town) {
                    town.value =
                        newTown || '';
                }

                if (county) {
                    county.value =
                        newCounty || '';
                }

                if (postcode) {
                    postcode.value =
                        String(
                            newPostcode || ''
                        ).toUpperCase();
                }

                if (
                    country &&
                    iso3
                ) {
                    country.value = iso3;
                }

                updateCardData(card);
            }
        );
    }

    function wireAddressAutocomplete(){
        listEl
            .querySelectorAll('.entry-card')
            .forEach(wireOneAutocomplete);
    }

    window.__initAcademicPlacesAutocomplete =
        function(){
            wireAddressAutocomplete();
        };

    updateMode();
    render();
})();
</script>

<script>
(function(){
    if (
        window.google &&
        google.maps &&
        google.maps.places
    ) {
        if (
            window.__initAcademicPlacesAutocomplete
        ) {
            window.__initAcademicPlacesAutocomplete();
        }

        return;
    }

    if (
        document.getElementById(
            'academicPlacesScript'
        )
    ) {
        return;
    }

    const script =
        document.createElement('script');

    script.id =
        'academicPlacesScript';

    script.src =
        "https://maps.googleapis.com/maps/api/js?key={{ config('services.google.maps_places_key') }}" +
        "&libraries=places&callback=__initAcademicPlacesAutocomplete";

    script.async = true;
    script.defer = true;

    document.head.appendChild(script);
})();
</script>
@endsection