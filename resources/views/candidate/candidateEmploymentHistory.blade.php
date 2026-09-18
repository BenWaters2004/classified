@extends('layout.candidate')

@section('form-content')
@php
    $today = now()->format('Y-m-d');

    $uk = isset($countries) ? $countries->firstWhere('id', 225) : null;

    $countryOptions = '<option value="">Please select…</option>';

    if ($uk) {
        $countryOptions .= '<option value="'.$uk->iso3.'">'.$uk->nicename.'</option>';
    }

    if (isset($countries)) {
        foreach ($countries as $country) {
            if ((int) $country->id === 225) continue;

            $countryOptions .= '<option value="'.
                e($country->iso3).'">'.
                e($country->nicename).
                '</option>';
        }
    }

    /*
     * Server errors use keys such as history.0.company.
     * We pass them to JavaScript so the dynamically-rendered row can show
     * the same validation message beside the correct field.
     */
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
    min-height:90px;
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

.small-note{
    font-size:13px;
    color:#6b7480;
}

/* Intro */
.history-intro{
    background:#f7f9fc;
    border:1px solid #e5e9f2;
    border-radius:8px;
    padding:16px 18px;
    margin-bottom:18px;
}

.history-intro p:last-child{
    margin-bottom:0;
}

/* Main status */
.coverage-panel{
    border:1px solid #dfe5ee;
    border-radius:8px;
    background:#fff;
    padding:15px 16px;
    margin-bottom:18px;
}

.coverage-panel.success{
    background:#f3fbf6;
    border-color:#bfe3ca;
}

.coverage-panel.warning{
    background:#fff9ed;
    border-color:#f2d39b;
}

.coverage-panel.error{
    background:#fff3f3;
    border-color:#efb7b7;
}

.coverage-head{
    display:flex;
    align-items:flex-start;
    justify-content:space-between;
    gap:16px;
    margin-bottom:10px;
}

.coverage-title{
    margin:0;
    color:#2C3C64;
    font-weight:700;
    font-size:16px;
}

.coverage-subtitle{
    color:#6b7480;
    font-size:13px;
    margin-top:3px;
}

.coverage-percent{
    flex:0 0 auto;
    font-weight:700;
    color:#2C3C64;
}

.coverage-bar{
    height:10px;
    background:#edf0f5;
    border-radius:999px;
    overflow:hidden;
}

.coverage-fill{
    height:100%;
    width:0;
    background:var(--brand);
    transition:width .2s ease;
}

.coverage-panel.success .coverage-fill{
    background:#4caf50;
}

.coverage-panel.warning .coverage-fill{
    background:#ff9800;
}

.missing-periods{
    margin-top:14px;
}

.missing-period{
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:12px;
    padding:10px 12px;
    border:1px solid #efd4a4;
    background:#fff;
    border-radius:6px;
    margin-top:8px;
}

.missing-period-date{
    color:#2C3C64;
    font-weight:600;
}

/* Actions */
.history-actions{
    display:flex;
    flex-wrap:wrap;
    gap:10px;
    justify-content:flex-end;
    margin-bottom:16px;
}

.btn-icon{
    display:inline-flex;
    align-items:center;
    gap:6px;
}

/* Validation */
.client-error-summary{
    border:1px solid #efb7b7;
    background:#fff3f3;
    border-radius:8px;
    padding:14px 16px;
    margin-bottom:18px;
}

.client-error-summary h4{
    color:#8a1f1f;
    margin:0 0 8px;
    font-size:16px;
}

.client-error-summary ul{
    margin-bottom:0;
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

/* Entry cards */
.entry-card{
    border:1px solid #dfe5ee;
    border-radius:8px;
    margin-bottom:15px;
    background:#fff;
    overflow:hidden;
}

.entry-card.gap{
    border-left:4px solid var(--brand);
}

.entry-card.employment{
    border-left:4px solid #2C3C64;
}

.entry-head{
    display:flex;
    align-items:flex-start;
    justify-content:space-between;
    gap:12px;
    padding:12px 14px;
    background:#f7f9fc;
    border-bottom:1px solid #e5e9f2;
}

.entry-heading{
    min-width:0;
}

.entry-title{
    color:#2C3C64;
    font-weight:700;
    margin-bottom:2px;
}

.entry-summary{
    color:#6b7480;
    font-size:13px;
}

.entry-body{
    padding:15px;
}

.badge-type{
    display:inline-block;
    padding:3px 8px;
    margin-bottom:5px;
    border-radius:999px;
    background:#eef2ff;
    color:#2C3C64;
    font-size:12px;
}

.badge-gap{
    background:color-mix(in srgb,var(--brand) 14%,white);
    color:var(--brand);
}

.current-box{
    background:#f7f9fc;
    border:1px solid #e5e9f2;
    border-radius:6px;
    padding:10px 12px;
    margin-top:3px;
}

.inline-radios{
    display:flex;
    flex-wrap:wrap;
    gap:18px;
    align-items:center;
    padding-top:3px;
}

.inline-radios label{
    margin:0;
    font-weight:500;
}

.question-fieldset{
    border:0;
    padding:0;
    margin:0;
    min-width:0;
}

.question-fieldset legend{
    border:0;
    width:auto;
    padding:0;
    margin:0 0 7px;
    font-size:14px;
    font-weight:600;
    color:#2C3C64;
}

.optional-panel{
    background:#f7f9fc;
    border:1px solid #e5e9f2;
    border-radius:6px;
    padding:10px 12px;
    margin-top:8px;
}

.remove-entry{
    white-space:nowrap;
}

/* Country/address */
.address-help{
    font-size:12px;
    color:#6b7480;
    margin-top:5px;
}

/* Save */
.save-row{
    display:flex;
    flex-wrap:wrap;
    justify-content:flex-end;
    gap:10px;
}

/* Accessible visually hidden */
.sr-only-focusable:not(:focus):not(:active){
    position:absolute;
    width:1px;
    height:1px;
    padding:0;
    margin:-1px;
    overflow:hidden;
    clip:rect(0,0,0,0);
    white-space:nowrap;
    border:0;
}

@media(max-width:767px){
    .coverage-head,
    .entry-head,
    .missing-period{
        flex-direction:column;
        align-items:flex-start;
    }

    .history-actions,
    .save-row{
        justify-content:stretch;
    }

    .history-actions .btn,
    .save-row .btn{
        width:100%;
    }

    .remove-entry{
        padding-left:0;
    }
}
</style>

<form
    id="employmentHistoryForm"
    class="form-horizontal"
    method="POST"
    action="{{ route('candidate.employment.save') }}"
    novalidate
>
    @csrf

    {{-- Server-side error summary --}}
    @if ($errors->any())
        <div
            class="alert alert-danger"
            role="alert"
            aria-live="polite"
            aria-atomic="true"
        >
            <p>
                <strong>Your employment history could not be saved yet.</strong>
            </p>

            <ul style="margin-bottom:0">
                @foreach ($errors->getMessages() as $field => $messages)
                    @foreach ($messages as $message)
                        <li>
                            <a
                                href="#historyList"
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

    <div id="clientErrorSummary" class="client-error-summary hidden" role="alert" tabindex="-1">
        <h4>Please check your employment history</h4>
        <ul id="clientErrorList"></ul>
    </div>

    <div class="p-5 bg-light rounded">
        <div class="form-section">
            <h3 class="section-title">Employment &amp; activity history</h3>

            <div class="history-intro">
                <p>
                    <strong>Tell us what you have been doing during the last 5 years.</strong>
                </p>
                <p>
                    Add your employment and any other periods that need to be accounted for,
                    such as unemployment, education, travelling, caring responsibilities,
                    illness or time between roles.
                </p>
                <p>
                    Your history needs to cover the full five-year period without unexplained
                    gaps. Overlapping jobs are fine, and you do not need to add entries in date order.
                </p>
            </div>

            {{-- Coverage status --}}
            <div id="coveragePanel" class="coverage-panel warning" aria-live="polite">
                <div class="coverage-head">
                    <div>
                        <h4 id="coverageTitle" class="coverage-title">
                            Checking your five-year history…
                        </h4>
                        <div id="coverageSubtitle" class="coverage-subtitle">
                            Add your history below and we will show you exactly what is still missing.
                        </div>
                    </div>

                    <div id="coveragePercent" class="coverage-percent">0%</div>
                </div>

                <div class="coverage-bar" aria-hidden="true">
                    <div id="coverageFill" class="coverage-fill"></div>
                </div>

                <div id="missingPeriods" class="missing-periods"></div>
            </div>

            <div class="history-actions">
                <button type="button" id="addEmployment" class="btn btn-default btn-icon">
                    <i class="fa fa-briefcase" aria-hidden="true"></i>
                    Add employment
                </button>

                <button type="button" id="addGap" class="btn btn-default btn-icon">
                    <i class="fa fa-circle-plus" aria-hidden="true"></i>
                    Add other period / gap
                </button>
            </div>

            <div id="historyList"></div>

            <input
                type="hidden"
                id="historyJson"
                name="history_json"
                value="{{ old('history_json', '[]') }}"
            >
        </div>

        <div class="form-section">
            <div class="save-row">
                <a class="btn btn-default" href="{{ route('candidate.welcome') }}">
                    Back
                </a>

                <button type="submit" class="btn forceBgClassified">
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
    const COUNTRY_OPTIONS_HTML = @json($countryOptions);
    const SERVER_ERRORS = @json($serverErrors);

    const serverSeed = @json(
        old('history_json')
            ? json_decode(old('history_json', '[]'), true)
            : (isset($history) ? $history : [])
    );

    const form = document.getElementById('employmentHistoryForm');
    const listEl = document.getElementById('historyList');
    const hiddenEl = document.getElementById('historyJson');
    const addEmploymentBtn = document.getElementById('addEmployment');
    const addGapBtn = document.getElementById('addGap');

    const coveragePanel = document.getElementById('coveragePanel');
    const coverageTitle = document.getElementById('coverageTitle');
    const coverageSubtitle = document.getElementById('coverageSubtitle');
    const coveragePercent = document.getElementById('coveragePercent');
    const coverageFill = document.getElementById('coverageFill');
    const missingPeriodsEl = document.getElementById('missingPeriods');

    const errorSummary = document.getElementById('clientErrorSummary');
    const errorList = document.getElementById('clientErrorList');

    if (!form || !listEl || !hiddenEl) return;

    let rows = [];
    let initial = [];

    try {
        if (Array.isArray(serverSeed)) {
            initial = serverSeed;
        } else if (typeof serverSeed === 'string' && serverSeed.trim()) {
            initial = JSON.parse(serverSeed);
        }
    } catch (e) {
        initial = [];
    }

    function uid(){
        return 'r' + Math.random().toString(36).slice(2,10);
    }

    function esc(value){
        return String(value ?? '').replace(/[&<>"']/g, function(ch){
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
        const d = new Date(value + 'T12:00:00');
        return isNaN(d.getTime()) ? null : d;
    }

    function formatDate(value){
        const d = parseDate(value);
        if (!d) return '';

        return d.toLocaleDateString('en-GB', {
            day:'numeric',
            month:'short',
            year:'numeric'
        });
    }

    function isoDate(date){
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2,'0');
        const day = String(date.getDate()).padStart(2,'0');
        return year + '-' + month + '-' + day;
    }

    function addDays(date, days){
        const copy = new Date(date.getTime());
        copy.setDate(copy.getDate() + days);
        return copy;
    }

    function startOfToday(){
        const d = parseDate(TODAY);
        d.setHours(0,0,0,0);
        return d;
    }

    function fiveYearsAgo(){
        const d = startOfToday();
        d.setFullYear(d.getFullYear() - 5);
        return d;
    }

    function postcodeFormat(value){
        return String(value || '').toUpperCase();
    }

    function normaliseRow(row, index){
        if (row.type === 'gap') {
            return Object.assign({
                _id: uid(),
                _serverIndex: index,
                type: 'gap',
                from: '',
                to: '',
                reason: '',
                claiming_benefits: ''
            }, row);
        }

        return Object.assign({
            _id: uid(),
            _serverIndex: index,
            type: 'employment',
            from: '',
            to: '',
            is_current: false,
            company: '',
            company_email: '',
            company_email_unavailable: false,
            address: '',
            town: '',
            county: '',
            postcode: '',
            country: '',
            contact_referee: '',
            contact_reason: '',
            p60: ''
        }, row, {
            is_current: row.is_current === true
                || row.is_current === 1
                || row.is_current === '1',
            company_email_unavailable:
                row.company_email_unavailable === true
                || row.company_email_unavailable === 1
                || row.company_email_unavailable === '1'
        });
    }

    rows = (initial || []).map(normaliseRow);

    if (rows.length === 0) {
        rows.push(normaliseRow({type:'employment'}, 0));
    }

    function activeEnd(row){
        if (row.type === 'employment' && row.is_current) {
            return TODAY;
        }

        return row.to || '';
    }

    function sortRows(){
        rows.sort(function(a,b){
            const aEnd = parseDate(activeEnd(a));
            const bEnd = parseDate(activeEnd(b));

            if (aEnd && bEnd && bEnd.getTime() !== aEnd.getTime()) {
                return bEnd - aEnd;
            }

            const aFrom = parseDate(a.from);
            const bFrom = parseDate(b.from);

            if (aFrom && bFrom) return bFrom - aFrom;
            if (aFrom) return -1;
            if (bFrom) return 1;

            return 0;
        });
    }

    function rowLabel(row, index){
        if (row.type === 'gap') {
            const reason = (row.reason || '').trim();
            return reason || ('Other period / gap ' + (index + 1));
        }

        return (row.company || '').trim() || ('Employment ' + (index + 1));
    }

    function rowDateSummary(row){
        if (!row.from) return 'Dates not completed';

        const start = formatDate(row.from);

        if (row.type === 'employment' && row.is_current) {
            return start + ' – Present';
        }

        if (!row.to) return start + ' – end date not completed';

        return start + ' – ' + formatDate(row.to);
    }

    function selectedCountryOptions(value){
        if (!value) return COUNTRY_OPTIONS_HTML;

        const temp = document.createElement('select');
        temp.innerHTML = COUNTRY_OPTIONS_HTML;
        temp.value = value;

        return Array.from(temp.options).map(function(option){
            const selected = option.value === value ? ' selected' : '';
            return '<option value="' + esc(option.value) + '"' + selected + '>' +
                esc(option.textContent) +
                '</option>';
        }).join('');
    }

    function employmentCard(row, index){
        const emailUnavailable = !!row.company_email_unavailable;
        const noContact = row.contact_referee === 'no';

        return `
        <div
            id="history-card-${esc(row._id)}"
            class="entry-card employment"
            data-id="${esc(row._id)}"
            data-row-index="${index}"
        >
            <div class="entry-head">
                <div class="entry-heading">
                    <span class="badge-type">
                        <i class="fa fa-briefcase" aria-hidden="true"></i>
                        Employment
                    </span>

                    <div class="entry-title">${esc(rowLabel(row, index))}</div>
                    <div class="entry-summary">${esc(rowDateSummary(row))}</div>
                </div>

                <button
                    type="button"
                    class="btn btn-link text-danger remove-entry"
                    data-action="remove"
                >
                    <i class="fa fa-trash" aria-hidden="true"></i>
                    Remove
                </button>
            </div>

            <div class="entry-body">
                <div class="row field-row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="control-label" for="field-${esc(row._id)}-from">
                                Worked here from <span class="text-danger">*</span>
                            </label>

                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa-regular fa-calendar" aria-hidden="true"></i>
                                </span>

                                <input
                                    id="field-${esc(row._id)}-from"
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
                            <label class="control-label" for="field-${esc(row._id)}-to">
                                Worked here until
                                ${row.is_current ? '' : '<span class="text-danger">*</span>'}
                            </label>

                            <div class="input-group ${row.is_current ? 'hidden' : ''} input-to-wrap">
                                <span class="input-group-addon">
                                    <i class="fa-regular fa-calendar" aria-hidden="true"></i>
                                </span>

                                <input
                                    id="field-${esc(row._id)}-to"
                                    type="date"
                                    class="form-control input-to"
                                    value="${esc(row.to)}"
                                    max="${esc(TODAY)}"
                                    ${row.is_current ? 'disabled' : ''}
                                >
                            </div>

                            <div class="current-box">
                                <label style="margin:0;font-weight:500">
                                    <input
                                        type="checkbox"
                                        class="input-current"
                                        ${row.is_current ? 'checked' : ''}
                                    >
                                    I currently work here
                                </label>
                            </div>

                            <span class="help-block"></span>
                        </div>
                    </div>
                </div>

                <div class="row field-row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="control-label" for="field-${esc(row._id)}-company">
                                Employer / company name <span class="text-danger">*</span>
                            </label>

                            <input
                                id="field-${esc(row._id)}-company"
                                type="text"
                                class="form-control input-company"
                                value="${esc(row.company)}"
                                autocomplete="organization"
                            >

                            <span class="help-block"></span>
                        </div>
                    </div>

                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="control-label" for="field-${esc(row._id)}-email">
                                Employer contact email
                                ${emailUnavailable ? '' : '<span class="text-danger">*</span>'}
                            </label>

                            <input
                                id="field-${esc(row._id)}-email"
                                type="email"
                                class="form-control input-email"
                                value="${esc(row.company_email)}"
                                placeholder="hr@company.com"
                                ${emailUnavailable ? 'disabled' : ''}
                            >

                            <div class="current-box">
                                <label style="margin:0;font-weight:500">
                                    <input
                                        type="checkbox"
                                        class="input-email-unavailable"
                                        ${emailUnavailable ? 'checked' : ''}
                                    >
                                    I do not have an email address for this employer
                                </label>
                            </div>

                            <span class="help-block"></span>
                        </div>
                    </div>
                </div>

                <div class="row field-row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="control-label" for="field-${esc(row._id)}-address">
                                Employer address <span class="text-danger">*</span>
                            </label>

                            <input
                                id="field-${esc(row._id)}-address"
                                type="text"
                                class="form-control input-address employer-address-line1"
                                value="${esc(row.address)}"
                                autocomplete="street-address"
                            >

                            <span class="address-help">
                                Start typing to search for the address, or enter it manually.
                            </span>

                            <span class="help-block"></span>
                        </div>
                    </div>

                    <div class="col-sm-3">
                        <div class="form-group">
                            <label class="control-label" for="field-${esc(row._id)}-town">
                                Town / City <span class="text-danger">*</span>
                            </label>

                            <input
                                id="field-${esc(row._id)}-town"
                                type="text"
                                class="form-control input-town"
                                value="${esc(row.town)}"
                                autocomplete="address-level2"
                            >

                            <span class="help-block"></span>
                        </div>
                    </div>

                    <div class="col-sm-3">
                        <div class="form-group">
                            <label class="control-label" for="field-${esc(row._id)}-county">
                                County / State / Region
                            </label>

                            <input
                                id="field-${esc(row._id)}-county"
                                type="text"
                                class="form-control input-county"
                                value="${esc(row.county)}"
                                autocomplete="address-level1"
                            >

                            <span class="help-block"></span>
                        </div>
                    </div>
                </div>

                <div class="row field-row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="control-label" for="field-${esc(row._id)}-postcode">
                                Postcode / ZIP / postal code <span class="text-danger">*</span>
                            </label>

                            <input
                                id="field-${esc(row._id)}-postcode"
                                type="text"
                                class="form-control input-postcode"
                                value="${esc(row.postcode)}"
                                maxlength="20"
                                style="text-transform:uppercase"
                                autocomplete="postal-code"
                            >

                            <span class="help-block"></span>
                        </div>
                    </div>

                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="control-label" for="field-${esc(row._id)}-country">
                                Country <span class="text-danger">*</span>
                            </label>

                            <select
                                id="field-${esc(row._id)}-country"
                                class="form-control input-country"
                                autocomplete="country"
                            >
                                ${selectedCountryOptions(row.country)}
                            </select>

                            <span class="help-block"></span>
                        </div>
                    </div>
                </div>

                <div class="row field-row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <fieldset class="question-fieldset">
                                <legend>
                                    May we contact this employer to verify your employment?
                                    <span class="text-danger">*</span>
                                </legend>

                                <div class="inline-radios">
                                    <label>
                                        <input
                                            type="radio"
                                            class="input-contact"
                                            name="contact-${esc(row._id)}"
                                            value="yes"
                                            ${row.contact_referee === 'yes' ? 'checked' : ''}
                                        >
                                        Yes
                                    </label>

                                    <label>
                                        <input
                                            type="radio"
                                            class="input-contact"
                                            name="contact-${esc(row._id)}"
                                            value="no"
                                            ${row.contact_referee === 'no' ? 'checked' : ''}
                                        >
                                        No
                                    </label>
                                </div>
                            </fieldset>

                            <span class="help-block"></span>

                            <div class="optional-panel contact-reason-wrap ${noContact ? '' : 'hidden'}">
                                <label
                                    class="control-label"
                                    for="field-${esc(row._id)}-contact-reason"
                                >
                                    Why should we not contact this employer?
                                    <span class="text-danger">*</span>
                                </label>

                                <textarea
                                    id="field-${esc(row._id)}-contact-reason"
                                    class="form-control input-contact-reason"
                                    rows="3"
                                >${esc(row.contact_reason)}</textarea>

                                <span class="help-block"></span>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-6">
                        <div class="form-group">
                            <fieldset class="question-fieldset">
                                <legend>
                                    Have you uploaded a P60 for this employment?
                                    <span class="text-danger">*</span>
                                </legend>

                                <p class="small-note">
                                    A P60 can help us verify employment. Select No if you do not have one.
                                </p>

                                <div class="inline-radios">
                                    <label>
                                        <input
                                            type="radio"
                                            class="input-p60"
                                            name="p60-${esc(row._id)}"
                                            value="yes"
                                            ${row.p60 === 'yes' ? 'checked' : ''}
                                        >
                                        Yes
                                    </label>

                                    <label>
                                        <input
                                            type="radio"
                                            class="input-p60"
                                            name="p60-${esc(row._id)}"
                                            value="no"
                                            ${row.p60 === 'no' ? 'checked' : ''}
                                        >
                                        No
                                    </label>
                                </div>
                            </fieldset>

                            <span class="help-block"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>`;
    }

    function gapCard(row, index){
        return `
        <div
            id="history-card-${esc(row._id)}"
            class="entry-card gap"
            data-id="${esc(row._id)}"
            data-row-index="${index}"
        >
            <div class="entry-head">
                <div class="entry-heading">
                    <span class="badge-type badge-gap">
                        <i class="fa fa-circle-info" aria-hidden="true"></i>
                        Other period / gap
                    </span>

                    <div class="entry-title">${esc(rowLabel(row, index))}</div>
                    <div class="entry-summary">${esc(rowDateSummary(row))}</div>
                </div>

                <button
                    type="button"
                    class="btn btn-link text-danger remove-entry"
                    data-action="remove"
                >
                    <i class="fa fa-trash" aria-hidden="true"></i>
                    Remove
                </button>
            </div>

            <div class="entry-body">
                <div class="row field-row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="control-label" for="field-${esc(row._id)}-from">
                                Period started <span class="text-danger">*</span>
                            </label>

                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa-regular fa-calendar" aria-hidden="true"></i>
                                </span>

                                <input
                                    id="field-${esc(row._id)}-from"
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
                            <label class="control-label" for="field-${esc(row._id)}-to">
                                Period ended <span class="text-danger">*</span>
                            </label>

                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa-regular fa-calendar" aria-hidden="true"></i>
                                </span>

                                <input
                                    id="field-${esc(row._id)}-to"
                                    type="date"
                                    class="form-control input-to"
                                    value="${esc(row.to)}"
                                    max="${esc(TODAY)}"
                                >
                            </div>

                            <span class="help-block"></span>
                        </div>
                    </div>
                </div>

                <div class="row field-row">
                    <div class="col-sm-8">
                        <div class="form-group">
                            <label class="control-label" for="field-${esc(row._id)}-reason">
                                What were you doing during this period?
                                <span class="text-danger">*</span>
                            </label>

                            <textarea
                                id="field-${esc(row._id)}-reason"
                                class="form-control input-reason"
                                rows="3"
                                placeholder="For example: studying at university, travelling, caring responsibilities, unemployed, between roles..."
                            >${esc(row.reason)}</textarea>

                            <span class="help-block">
                                Describe the period in your own words.
                            </span>
                        </div>
                    </div>

                    <div class="col-sm-4">
                        <div class="form-group">
                            <fieldset class="question-fieldset">
                                <legend>
                                    Did you receive unemployment-related benefits during this period?
                                    <span class="text-danger">*</span>
                                </legend>

                                <div class="inline-radios">
                                    <label>
                                        <input
                                            type="radio"
                                            class="input-benefits"
                                            name="benefits-${esc(row._id)}"
                                            value="yes"
                                            ${row.claiming_benefits === 'yes' ? 'checked' : ''}
                                        >
                                        Yes
                                    </label>

                                    <label>
                                        <input
                                            type="radio"
                                            class="input-benefits"
                                            name="benefits-${esc(row._id)}"
                                            value="no"
                                            ${row.claiming_benefits === 'no' ? 'checked' : ''}
                                        >
                                        No
                                    </label>
                                </div>
                            </fieldset>

                            <span class="help-block"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>`;
    }

    function render(){
        sortRows();

        listEl.innerHTML = rows.map(function(row,index){
            return row.type === 'gap'
                ? gapCard(row,index)
                : employmentCard(row,index);
        }).join('');

        listEl.querySelectorAll('.entry-card').forEach(wireCard);

        wireAddressAutocomplete();
        serialise(false);
        applyServerErrors();
    }

    function rowByCard(card){
        if (!card) return null;
        const id = card.getAttribute('data-id');
        return rows.find(function(row){ return row._id === id; }) || null;
    }

    function fieldGroup(element){
        if (!element) return null;

        if (element.classList && element.classList.contains('form-group')) {
            return element;
        }

        return element.closest('.form-group');
    }

    function clearFieldError(element){
        const group = fieldGroup(element);
        if (!group) return;

        group.classList.remove('has-error');

        const help = group.querySelector(':scope > .help-block');
        if (help && help.dataset.defaultHelp !== '1') {
            help.textContent = '';
            help.style.display = '';
        }
    }

    function setFieldError(element, message){
        const group = fieldGroup(element);
        if (!group) return;

        group.classList.add('has-error');

        let help = group.querySelector(':scope > .help-block');

        if (!help) {
            help = document.createElement('span');
            help.className = 'help-block';
            group.appendChild(help);
        }

        help.textContent = message;
        help.style.display = 'block';
    }

    function setNestedFieldError(element, message){
        if (!element) return;

        const wrap = element.closest('.optional-panel');
        if (!wrap) {
            setFieldError(element, message);
            return;
        }

        wrap.classList.add('has-error');

        let help = wrap.querySelector(':scope > .help-block');

        if (!help) {
            help = document.createElement('span');
            help.className = 'help-block';
            wrap.appendChild(help);
        }

        help.textContent = message;
    }

    function clearCardErrors(card){
        card.querySelectorAll('.has-error').forEach(function(group){
            group.classList.remove('has-error');
        });

        card.querySelectorAll('.help-block').forEach(function(help){
            if (
                help.closest('.input-contact-reason') ||
                help.dataset.persistent === '1'
            ) {
                return;
            }

            /*
             * Keep explanatory notes if they already have static text.
             * Field validation will overwrite them only when necessary.
             */
        });
    }

    function validateEmail(value){
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value);
    }

    function validateCard(card){
        const row = rowByCard(card);
        if (!row) return [];

        const errors = [];

        function add(field, element, message, nested){
            errors.push({
                rowId: row._id,
                field: field,
                element: element,
                message: message
            });

            if (nested) setNestedFieldError(element, message);
            else setFieldError(element, message);
        }

        const from = card.querySelector('.input-from');
        const to = card.querySelector('.input-to');

        if (from) clearFieldError(from);
        if (to) clearFieldError(to);

        if (!row.from) {
            add('from', from, row.type === 'employment'
                ? 'Enter when you started working here.'
                : 'Enter when this period started.');
        }

        if (row.from && parseDate(row.from) > startOfToday()) {
            add('from', from, 'The start date cannot be in the future.');
        }

        const effectiveTo = activeEnd(row);

        if (!effectiveTo) {
            add('to', to, row.type === 'employment'
                ? 'Enter when this employment ended, or select that you currently work here.'
                : 'Enter when this period ended.');
        }

        if (effectiveTo && parseDate(effectiveTo) > startOfToday()) {
            add('to', to, 'The end date cannot be in the future.');
        }

        if (
            row.from &&
            effectiveTo &&
            parseDate(effectiveTo) < parseDate(row.from)
        ) {
            add('to', to, 'The end date cannot be before the start date.');
        }

        if (row.type === 'employment') {
            const company = card.querySelector('.input-company');
            const email = card.querySelector('.input-email');
            const address = card.querySelector('.input-address');
            const town = card.querySelector('.input-town');
            const postcode = card.querySelector('.input-postcode');
            const country = card.querySelector('.input-country');
            const contact = card.querySelector('.input-contact:checked');
            const p60 = card.querySelector('.input-p60:checked');
            const contactReason = card.querySelector('.input-contact-reason');

            [company,email,address,town,postcode,country].forEach(clearFieldError);

            if (!String(row.company || '').trim()) {
                add('company', company, 'Employer / company name is required.');
            }

            if (!row.company_email_unavailable) {
                if (!String(row.company_email || '').trim()) {
                    add(
                        'company_email',
                        email,
                        'Enter an employer contact email, or select that you do not have one.'
                    );
                } else if (!validateEmail(row.company_email)) {
                    add('company_email', email, 'Enter a valid email address.');
                }
            }

            if (!String(row.address || '').trim()) {
                add('address', address, 'Employer address is required.');
            }

            if (!String(row.town || '').trim()) {
                add('town', town, 'Town / city is required.');
            }

            if (!String(row.postcode || '').trim()) {
                add('postcode', postcode, 'Postcode / postal code is required.');
            }

            if (!String(row.country || '').trim()) {
                add('country', country, 'Please select the employer country.');
            }

            const contactGroup = card.querySelector('.input-contact')
                ? card.querySelector('.input-contact').closest('.form-group')
                : null;

            if (contactGroup) contactGroup.classList.remove('has-error');

            if (!row.contact_referee) {
                add(
                    'contact_referee',
                    contactGroup,
                    'Please choose whether this employer may be contacted.'
                );
            }

            if (
                row.contact_referee === 'no' &&
                !String(row.contact_reason || '').trim()
            ) {
                add(
                    'contact_reason',
                    contactReason,
                    'Please briefly explain why this employer should not be contacted.',
                    true
                );
            }

            const p60Group = card.querySelector('.input-p60')
                ? card.querySelector('.input-p60').closest('.form-group')
                : null;

            if (p60Group) p60Group.classList.remove('has-error');

            if (!row.p60) {
                add(
                    'p60',
                    p60Group,
                    'Please choose whether you have uploaded a P60.'
                );
            }
        } else {
            const reason = card.querySelector('.input-reason');
            const benefitsGroup = card.querySelector('.input-benefits')
                ? card.querySelector('.input-benefits').closest('.form-group')
                : null;

            clearFieldError(reason);
            if (benefitsGroup) benefitsGroup.classList.remove('has-error');

            if (!String(row.reason || '').trim()) {
                add(
                    'reason',
                    reason,
                    'Please describe what you were doing during this period.'
                );
            }

            if (!row.claiming_benefits) {
                add(
                    'claiming_benefits',
                    benefitsGroup,
                    'Please choose whether you received unemployment-related benefits.'
                );
            }
        }

        return errors;
    }

    function allIntervals(){
        return rows.map(function(row){
            const from = parseDate(row.from);
            const to = parseDate(activeEnd(row));

            if (!from || !to || to < from) return null;

            return {
                start: from,
                end: to
            };
        }).filter(Boolean);
    }

    function mergedIntervals(){
        const windowStart = fiveYearsAgo();
        const windowEnd = startOfToday();

        const intervals = allIntervals()
            .map(function(interval){
                const start = interval.start < windowStart
                    ? new Date(windowStart)
                    : new Date(interval.start);

                const end = interval.end > windowEnd
                    ? new Date(windowEnd)
                    : new Date(interval.end);

                if (end < windowStart || start > windowEnd || end < start) {
                    return null;
                }

                return {start:start,end:end};
            })
            .filter(Boolean)
            .sort(function(a,b){ return a.start - b.start; });

        const merged = [];

        intervals.forEach(function(interval){
            if (!merged.length) {
                merged.push({
                    start:new Date(interval.start),
                    end:new Date(interval.end)
                });
                return;
            }

            const last = merged[merged.length - 1];
            const nextAllowed = addDays(last.end, 1);

            if (interval.start <= nextAllowed) {
                if (interval.end > last.end) {
                    last.end = new Date(interval.end);
                }
            } else {
                merged.push({
                    start:new Date(interval.start),
                    end:new Date(interval.end)
                });
            }
        });

        return merged;
    }

    function coverageData(){
        const start = fiveYearsAgo();
        const end = startOfToday();
        const merged = mergedIntervals();

        const gaps = [];
        let cursor = new Date(start);

        merged.forEach(function(interval){
            if (interval.start > cursor) {
                gaps.push({
                    start:new Date(cursor),
                    end:addDays(interval.start,-1)
                });
            }

            const after = addDays(interval.end,1);
            if (after > cursor) cursor = after;
        });

        if (cursor <= end) {
            gaps.push({
                start:new Date(cursor),
                end:new Date(end)
            });
        }

        const totalDays = Math.round((end - start) / 86400000) + 1;

        let missingDays = 0;
        gaps.forEach(function(gap){
            missingDays += Math.round((gap.end - gap.start) / 86400000) + 1;
        });

        const coveredDays = Math.max(0, totalDays - missingDays);
        const percent = Math.max(
            0,
            Math.min(100, Math.round((coveredDays / totalDays) * 100))
        );

        return {
            percent:percent,
            gaps:gaps
        };
    }

    function renderCoverage(){
        const data = coverageData();

        coverageFill.style.width = data.percent + '%';
        coveragePercent.textContent = data.percent + '%';
        missingPeriodsEl.innerHTML = '';

        coveragePanel.classList.remove('success','warning','error');

        if (data.gaps.length === 0 && rows.length > 0) {
            coveragePanel.classList.add('success');

            coverageTitle.innerHTML =
                '<i class="fa fa-circle-check" aria-hidden="true"></i> ' +
                'Your last 5 years are fully covered';

            coverageSubtitle.textContent =
                'You can still review or add entries before saving.';
            return;
        }

        coveragePanel.classList.add(data.percent >= 80 ? 'warning' : 'error');

        if (rows.length === 0) {
            coverageTitle.textContent = 'Add your employment and other periods';
            coverageSubtitle.textContent =
                'We will calculate your five-year coverage as you enter the dates.';
            return;
        }

        coverageTitle.textContent =
            data.gaps.length === 1
                ? '1 period still needs to be accounted for'
                : data.gaps.length + ' periods still need to be accounted for';

        coverageSubtitle.textContent =
            'Add an employment entry or another period for each missing date range.';

        const heading = document.createElement('div');
        heading.innerHTML = '<strong>Missing periods</strong>';
        missingPeriodsEl.appendChild(heading);

        data.gaps.forEach(function(gap){
            const item = document.createElement('div');
            item.className = 'missing-period';

            const text = document.createElement('div');
            text.innerHTML =
                '<div class="missing-period-date">' +
                esc(formatDate(isoDate(gap.start))) +
                ' – ' +
                esc(formatDate(isoDate(gap.end))) +
                '</div>';

            const button = document.createElement('button');
            button.type = 'button';
            button.className = 'btn btn-default btn-sm';
            button.innerHTML =
                '<i class="fa fa-plus" aria-hidden="true"></i> Add this period';

            button.addEventListener('click', function(){
                addGapRow({
                    from:isoDate(gap.start),
                    to:isoDate(gap.end)
                }, true);
            });

            item.appendChild(text);
            item.appendChild(button);
            missingPeriodsEl.appendChild(item);
        });
    }

    function serialisableRows(){
        return rows.map(function(row){
            const copy = Object.assign({}, row);

            delete copy._id;
            delete copy._serverIndex;

            if (copy.type === 'employment') {
                copy.to = copy.is_current ? '' : (copy.to || '');
            }

            return copy;
        });
    }

    function serialise(updateCoverage){
        hiddenEl.value = JSON.stringify(serialisableRows());

        if (updateCoverage !== false) {
            renderCoverage();
        }
    }

    function wireCard(card){
        const row = rowByCard(card);
        if (!row) return;

        function bind(selector,event,handler){
            const el = card.querySelector(selector);
            if (!el) return;

            el.addEventListener(event,function(e){
                handler(e.target);
                serialise();
            });
        }

        bind('.input-from','change',function(el){
            row.from = el.value;
            render();
        });

        bind('.input-to','change',function(el){
            row.to = el.value;
            render();
        });

        bind('.input-company','input',function(el){
            row.company = el.value;
            updateCardHeader(card,row);
        });

        bind('.input-email','input',function(el){
            row.company_email = el.value;
        });

        bind('.input-address','input',function(el){
            row.address = el.value;
        });

        bind('.input-town','input',function(el){
            row.town = el.value;
        });

        bind('.input-county','input',function(el){
            row.county = el.value;
        });

        bind('.input-postcode','input',function(el){
            el.value = postcodeFormat(el.value);
            row.postcode = el.value;
        });

        bind('.input-country','change',function(el){
            row.country = el.value;
        });

        bind('.input-contact-reason','input',function(el){
            row.contact_reason = el.value;
        });

        bind('.input-reason','input',function(el){
            row.reason = el.value;
            updateCardHeader(card,row);
        });

        const current = card.querySelector('.input-current');
        if (current) {
            current.addEventListener('change',function(){
                row.is_current = current.checked;

                if (row.is_current) {
                    row.to = '';
                }

                render();
            });
        }

        const emailUnavailable = card.querySelector('.input-email-unavailable');
        if (emailUnavailable) {
            emailUnavailable.addEventListener('change',function(){
                row.company_email_unavailable = emailUnavailable.checked;

                if (row.company_email_unavailable) {
                    row.company_email = '';
                }

                render();
            });
        }

        card.querySelectorAll('.input-contact').forEach(function(input){
            input.addEventListener('change',function(){
                row.contact_referee = input.value;

                if (input.value === 'yes') {
                    row.contact_reason = '';
                }

                render();
            });
        });

        card.querySelectorAll('.input-p60').forEach(function(input){
            input.addEventListener('change',function(){
                row.p60 = input.value;
                serialise();
            });
        });

        card.querySelectorAll('.input-benefits').forEach(function(input){
            input.addEventListener('change',function(){
                row.claiming_benefits = input.value;
                serialise();
            });
        });

        const remove = card.querySelector('[data-action="remove"]');
        if (remove) {
            remove.addEventListener('click',function(){
                rows = rows.filter(function(item){
                    return item._id !== row._id;
                });

                render();
            });
        }
    }

    function updateCardHeader(card,row){
        const index = rows.indexOf(row);
        const title = card.querySelector('.entry-title');
        const summary = card.querySelector('.entry-summary');

        if (title) title.textContent = rowLabel(row,index);
        if (summary) summary.textContent = rowDateSummary(row);

        serialise();
    }

    function addEmploymentRow(prefill, focus){
        rows.push(normaliseRow(
            Object.assign({type:'employment'},prefill || {}),
            rows.length
        ));

        render();

        if (focus) {
            focusNewest('employment');
        }
    }

    function addGapRow(prefill, focus){
        rows.push(normaliseRow(
            Object.assign({type:'gap'},prefill || {}),
            rows.length
        ));

        render();

        if (focus) {
            focusNewest('gap');
        }
    }

    function focusNewest(type){
        const candidates = rows.filter(function(row){
            return row.type === type;
        });

        if (!candidates.length) return;

        const newest = candidates[candidates.length - 1];
        const card = document.getElementById('history-card-' + newest._id);

        if (!card) return;

        card.scrollIntoView({
            behavior:'smooth',
            block:'center'
        });

        const first = card.querySelector(
            type === 'employment'
                ? '.input-from'
                : '.input-reason'
        );

        if (first) {
            setTimeout(function(){ first.focus(); },300);
        }
    }

    function validateAll(){
        const errors = [];

        listEl.querySelectorAll('.entry-card').forEach(function(card){
            validateCard(card).forEach(function(error){
                errors.push(error);
            });
        });

        const coverage = coverageData();

        if (rows.length === 0) {
            errors.push({
                rowId:null,
                field:'history',
                element:null,
                message:'Add your employment and any other periods for the last five years.'
            });
        } else if (coverage.gaps.length > 0) {
            coverage.gaps.forEach(function(gap){
                errors.push({
                    rowId:null,
                    field:'coverage',
                    element:coveragePanel,
                    message:
                        'Account for the period ' +
                        formatDate(isoDate(gap.start)) +
                        ' to ' +
                        formatDate(isoDate(gap.end)) +
                        '.'
                });
            });
        }

        return errors;
    }

    function showErrorSummary(errors){
        errorList.innerHTML = '';

        if (!errors.length) {
            errorSummary.classList.add('hidden');
            return;
        }

        errors.forEach(function(error){
            const li = document.createElement('li');
            const button = document.createElement('button');

            button.type = 'button';
            button.textContent = error.message;

            button.addEventListener('click',function(){
                if (error.element) {
                    error.element.scrollIntoView({
                        behavior:'smooth',
                        block:'center'
                    });

                    if (typeof error.element.focus === 'function') {
                        setTimeout(function(){
                            try { error.element.focus(); } catch(e){}
                        },250);
                    }
                } else if (error.rowId) {
                    const card = document.getElementById(
                        'history-card-' + error.rowId
                    );

                    if (card) {
                        card.scrollIntoView({
                            behavior:'smooth',
                            block:'center'
                        });
                    }
                } else {
                    coveragePanel.scrollIntoView({
                        behavior:'smooth',
                        block:'center'
                    });
                }
            });

            li.appendChild(button);
            errorList.appendChild(li);
        });

        errorSummary.classList.remove('hidden');
        errorSummary.focus();
    }

    function applyServerErrors(){
        Object.keys(SERVER_ERRORS || {}).forEach(function(key){
            if (!key.startsWith('history.')) return;

            const parts = key.split('.');
            if (parts.length < 3) return;

            const serverIndex = parseInt(parts[1],10);
            const field = parts[2];

            const row = rows.find(function(item){
                return item._serverIndex === serverIndex;
            });

            if (!row) return;

            const card = document.getElementById(
                'history-card-' + row._id
            );

            if (!card) return;

            const map = {
                from:'.input-from',
                to:'.input-to',
                company:'.input-company',
                company_email:'.input-email',
                address:'.input-address',
                town:'.input-town',
                county:'.input-county',
                postcode:'.input-postcode',
                country:'.input-country',
                contact_reason:'.input-contact-reason',
                reason:'.input-reason'
            };

            const element = map[field]
                ? card.querySelector(map[field])
                : card;

            const messages = SERVER_ERRORS[key] || [];
            if (messages.length) {
                setFieldError(element,messages[0]);
            }
        });
    }

    addEmploymentBtn.addEventListener('click',function(){
        addEmploymentRow({},true);
    });

    addGapBtn.addEventListener('click',function(){
        addGapRow({},true);
    });

    form.addEventListener('submit',function(e){
        serialise();

        const errors = validateAll();

        if (errors.length) {
            e.preventDefault();
            e.stopPropagation();
            showErrorSummary(errors);
            return;
        }

        showErrorSummary([]);
    });

    document.querySelectorAll('.server-error-link').forEach(function(link){
        link.addEventListener('click',function(e){
            e.preventDefault();

            const field = link.dataset.errorField || '';

            if (field.startsWith('history.')) {
                const parts = field.split('.');
                const index = parseInt(parts[1],10);

                const row = rows.find(function(item){
                    return item._serverIndex === index;
                });

                if (row) {
                    const card = document.getElementById(
                        'history-card-' + row._id
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

            coveragePanel.scrollIntoView({
                behavior:'smooth',
                block:'center'
            });
        });
    });

    /* ---------------------------------------------------------------
     | Google Places address autocomplete
     | --------------------------------------------------------------- */

    const COUNTRY_ROWS = @json(
        isset($countries)
            ? $countries->map(fn($c) => [
                'iso2'=>$c->iso,
                'iso3'=>$c->iso3,
                'name'=>$c->nicename
            ])->values()
            : collect()
    );

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

    function googleComponent(components,type,shortName){
        const item = components.find(function(component){
            return component.types.includes(type);
        });

        if (!item) return '';

        return shortName ? item.short_name : item.long_name;
    }

    function makeEmployerAutocomplete(card){
        if (
            !window.google ||
            !google.maps ||
            !google.maps.places
        ) {
            return;
        }

        const line1 = card.querySelector('.input-address');
        const town = card.querySelector('.input-town');
        const county = card.querySelector('.input-county');
        const postcode = card.querySelector('.input-postcode');
        const country = card.querySelector('.input-country');

        if (!line1 || line1.dataset.autocompleteWired === '1') {
            return;
        }

        line1.dataset.autocompleteWired = '1';

        let restriction = null;

        if (country && country.value) {
            restriction = ISO3_TO_ISO2[country.value.toUpperCase()] || null;
        }

        const options = {
            types:['address'],
            fields:['address_components']
        };

        if (restriction) {
            options.componentRestrictions = {
                country:[restriction.toLowerCase()]
            };
        }

        const autocomplete = new google.maps.places.Autocomplete(
            line1,
            options
        );

        if (country) {
            country.addEventListener('change',function(){
                const iso2 = ISO3_TO_ISO2[
                    String(country.value || '').toUpperCase()
                ];

                try {
                    autocomplete.setComponentRestrictions(
                        iso2
                            ? {country:[iso2.toLowerCase()]}
                            : {country:[]}
                    );
                } catch(e){}
            });
        }

        autocomplete.addListener('place_changed',function(){
            const place = autocomplete.getPlace();

            if (!place || !place.address_components) return;

            const components = place.address_components;

            const number = googleComponent(
                components,
                'street_number',
                false
            );

            const route = googleComponent(
                components,
                'route',
                false
            );

            const newLine1 = [number,route].filter(Boolean).join(' ');

            const newTown =
                googleComponent(components,'postal_town',false) ||
                googleComponent(components,'locality',false) ||
                googleComponent(components,'sublocality',false);

            const newCounty =
                googleComponent(
                    components,
                    'administrative_area_level_2',
                    false
                ) ||
                googleComponent(
                    components,
                    'administrative_area_level_1',
                    false
                );

            const newPostcode = googleComponent(
                components,
                'postal_code',
                false
            );

            const iso2 = googleComponent(
                components,
                'country',
                true
            );

            const iso3 = iso2
                ? ISO2_TO_ISO3[iso2.toUpperCase()]
                : null;

            const row = rowByCard(card);
            if (!row) return;

            if (newLine1) {
                line1.value = newLine1;
                row.address = newLine1;
            }

            if (town) {
                town.value = newTown || '';
                row.town = town.value;
            }

            if (county) {
                county.value = newCounty || '';
                row.county = county.value;
            }

            if (postcode) {
                postcode.value = postcodeFormat(newPostcode || '');
                row.postcode = postcode.value;
            }

            if (country && iso3) {
                country.value = iso3;
                row.country = iso3;
            }

            serialise();
        });
    }

    function wireAddressAutocomplete(){
        listEl.querySelectorAll('.entry-card.employment').forEach(
            makeEmployerAutocomplete
        );
    }

    window.__initEmploymentPlacesAutocomplete = function(){
        wireAddressAutocomplete();
    };

    render();
    renderCoverage();
})();
</script>

{{-- Load Google Places only if it is not already present --}}
<script>
(function(){
    if (
        window.google &&
        google.maps &&
        google.maps.places
    ) {
        if (window.__initEmploymentPlacesAutocomplete) {
            window.__initEmploymentPlacesAutocomplete();
        }
        return;
    }

    if (document.getElementById('employmentPlacesScript')) return;

    var script = document.createElement('script');
    script.id = 'employmentPlacesScript';
    script.src =
        "https://maps.googleapis.com/maps/api/js?key={{ config('services.google.maps_places_key') }}" +
        "&libraries=places&callback=__initEmploymentPlacesAutocomplete";
    script.async = true;
    script.defer = true;

    document.head.appendChild(script);
})();
</script>
@endsection