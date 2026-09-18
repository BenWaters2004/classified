@extends('layout.candidate')

@section('form-content')
@php
    $today = now()->format('Y-m-d');

    $uk = isset($countries) ? $countries->firstWhere('id', 225) : null;

    $countryOptions = '<option value="">Please select…</option>';
    if ($uk) {
        $countryOptions .= '<option value="'.e($uk->iso3).'">'.e($uk->nicename).'</option>';
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

    $sortedPhoneCodes = collect($phoneCodes ?? [])
        ->map(fn($code) => (string) $code)
        ->unique()
        ->sort(function ($a, $b) {
            if ($a === '44') return -1;
            if ($b === '44') return 1;
            return (int) $a <=> (int) $b;
        })
        ->values();

    $dbRefs = [
        $personalRef1 ?? null,
        $personalRef2 ?? null,
    ];

    $refs = collect([0, 1])->map(function ($i) use ($dbRefs, $today) {
        $db = $dbRefs[$i];

        $savedPhone = preg_replace(
            '/\D+/',
            '',
            (string) data_get($db, 'referee_contact_number', '')
        );

        $savedCode = (string) data_get(
            $db,
            'referee_contact_country_code',
            ''
        );

        if ($savedCode === '' && str_starts_with($savedPhone, '44')) {
            $savedCode = '44';
            $savedPhone = substr($savedPhone, 2);
        } elseif ($savedCode === '') {
            $savedCode = '44';
        } elseif (
            $savedPhone !== '' &&
            str_starts_with($savedPhone, $savedCode)
        ) {
            $savedPhone = substr($savedPhone, strlen($savedCode));
        }

        $stillKnownSaved = data_get($db, 'still_known');

        if ($stillKnownSaved === null) {
            $savedTo = data_get($db, 'date_to');
            $stillKnownSaved = !$savedTo
                || substr((string) $savedTo, 0, 10) === $today;
        }

        return [
            'name' => old(
                "refs.$i.name",
                data_get($db, 'referee_name')
            ),

            'relationship' => old(
                "refs.$i.relationship",
                data_get($db, 'relationship')
            ),

            'known_from' => old(
                "refs.$i.known_from",
                data_get($db, 'date_from')
            ),

            'known_to' => old(
                "refs.$i.known_to",
                data_get($db, 'date_to')
            ),

            'still_known' => old(
                "refs.$i.still_known",
                $stillKnownSaved ? '1' : '0'
            ),

            'email' => old(
                "refs.$i.email",
                data_get($db, 'referee_email')
            ),

            'phone_country_code' => old(
                "refs.$i.phone_country_code",
                $savedCode
            ),

            'phone' => old(
                "refs.$i.phone",
                $savedPhone
            ),

            'address' => old(
                "refs.$i.address",
                data_get($db, 'referee_address_line')
            ),

            'address_line2' => old(
                "refs.$i.address_line2",
                data_get($db, 'referee_address_line_2')
            ),

            'town' => old(
                "refs.$i.town",
                data_get($db, 'referee_address_town')
            ),

            'county' => old(
                "refs.$i.county",
                data_get($db, 'referee_address_county')
            ),

            'postcode' => old(
                "refs.$i.postcode",
                data_get($db, 'referee_address_postcode')
            ),

            'country' => old(
                "refs.$i.country",
                data_get($db, 'referee_address_country')
            ),
        ];
    });

    $errorTarget = function ($field) {
        if (preg_match('/^refs\.(\d+)\.([A-Za-z0-9_]+)$/', $field, $m)) {
            return 'refs_'.$m[1].'_'.$m[2];
        }

        return 'personalReferencesForm';
    };
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

/* Guidance */
.reference-guide{
    border:1px solid #dfe5ee;
    border-radius:8px;
    background:#f7f9fc;
    padding:16px 18px;
    margin-bottom:18px;
}

.reference-guide h4{
    margin:0 0 10px;
    color:#2C3C64;
    font-weight:700;
}

.reference-guide ul{
    margin:0;
    padding-left:22px;
}

.reference-guide li{
    margin-bottom:5px;
}

.reference-guide li:last-child{
    margin-bottom:0;
}

.reference-note{
    margin-top:12px;
    background:#fff9ed;
    border:1px solid #f2d39b;
    border-radius:6px;
    padding:10px 12px;
    color:#6c5200;
}

/* Cards */
.ref-card{
    border:1px solid #dfe5ee;
    border-radius:8px;
    overflow:hidden;
    background:#fff;
    margin-bottom:18px;
}

.ref-card-head{
    display:flex;
    align-items:flex-start;
    justify-content:space-between;
    gap:12px;
    padding:14px 16px;
    border-bottom:1px solid #e5e9f2;
    background:#f7f9fc;
}

.ref-title{
    color:#2C3C64;
    font-weight:700;
    font-size:16px;
    margin-bottom:3px;
}

.ref-subtitle{
    color:#6b7480;
    font-size:13px;
}

.ref-card-body{
    padding:16px;
}

.ref-status{
    display:inline-flex;
    align-items:center;
    gap:6px;
    border-radius:999px;
    padding:5px 10px;
    font-size:12px;
    font-weight:700;
    white-space:nowrap;
}

.ref-status.neutral{
    background:#eef2ff;
    color:#2C3C64;
}

.ref-status.success{
    background:#eaf7ee;
    color:#246b38;
}

.ref-status.warning{
    background:#fff4da;
    color:#8a6200;
}

.ref-status.error{
    background:#fdecec;
    color:#a94442;
}

.eligibility-box{
    border:1px solid #dfe5ee;
    border-radius:7px;
    padding:11px 13px;
    margin-bottom:16px;
    background:#fafbfc;
}

.eligibility-box.success{
    border-color:#bfe3ca;
    background:#f3fbf6;
}

.eligibility-box.warning{
    border-color:#f2d39b;
    background:#fff9ed;
}

.eligibility-box.error{
    border-color:#efb7b7;
    background:#fff3f3;
}

.eligibility-title{
    font-weight:700;
    color:#2C3C64;
    margin-bottom:4px;
}

.eligibility-details{
    font-size:13px;
    color:#5f6874;
}

/* Phone */
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

.phone-row .phone-number{
    flex:1;
    border-top-left-radius:0;
    border-bottom-left-radius:0;
    border-left:0;
}

/* Still know */
.still-known-box{
    background:#f7f9fc;
    border:1px solid #e5e9f2;
    border-radius:6px;
    padding:10px 12px;
    margin-top:8px;
}

.still-known-box label{
    margin:0;
    font-weight:500;
}

/* Error summary */
.error-summary a{
    text-decoration:underline;
}

/* Save actions */
.form-actions{
    display:flex;
    flex-wrap:wrap;
    justify-content:flex-end;
    gap:10px;
}

/* Mobile */
@media(max-width:768px){
    .form-horizontal .control-label{
        text-align:left;
        margin-bottom:6px;
    }

    .ref-card-head{
        flex-direction:column;
    }

    .phone-row{
        display:block;
    }

    .phone-code{
        width:100%;
    }

    .phone-code select,
    .phone-row .phone-number{
        width:100%;
        border-radius:4px!important;
        border-left:1px solid #ccc;
    }

    .phone-row .phone-number{
        margin-top:8px;
    }

    .form-actions .btn{
        width:100%;
    }
}
</style>

<form
    id="personalReferencesForm"
    class="form-horizontal"
    method="POST"
    action="{{ route('candidate.references.personal.save') }}"
    novalidate
>
    @csrf

    @if ($errors->any())
        <div
            class="alert alert-danger error-summary"
            role="alert"
            aria-live="polite"
            aria-atomic="true"
        >
            <p>
                <strong>Please check the following before continuing:</strong>
            </p>

            <ul style="margin-bottom:0">
                @foreach ($errors->getMessages() as $field => $messages)
                    @foreach ($messages as $message)
                        <li>
                            <a
                                href="#{{ $errorTarget($field) }}"
                                class="text-danger"
                            >
                                {{ $message }}
                            </a>
                        </li>
                    @endforeach
                @endforeach
            </ul>
        </div>
    @endif

    @if (session('warnings'))
        <div class="alert alert-warning" role="status">
            <strong>Your references were saved, but please note:</strong>
            <ul style="margin:8px 0 0">
                @foreach ((array) session('warnings') as $warning)
                    <li>{{ $warning }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="p-5 bg-light rounded">
        <div class="form-section">
            <h3 class="section-title">Personal references</h3>

            <div class="reference-guide">
                <h4>Who can I use as a personal referee?</h4>

                <ul>
                    <li>
                        Please provide <strong>two different people</strong> who know you well.
                    </li>
                    <li>
                        Ideally, each referee should have known you for
                        <strong>at least 5 years</strong>.
                    </li>
                    <li>
                        Your referees must <strong>not be relatives or romantic partners</strong>.
                    </li>
                    <li>
                        Make sure the contact details are accurate because we may contact them
                        as part of your screening.
                    </li>
                </ul>

                <div class="reference-note">
                    <i class="fa fa-circle-info" aria-hidden="true"></i>
                    <strong>Don't have anyone who has known you for 5 years?</strong>
                    That's okay. Please provide the most suitable referees you have available.
                    You can still submit this page and the shorter relationship will be reviewed.
                </div>
            </div>
        </div>

        @foreach ($refs as $i => $ref)
            <div
                class="ref-card"
                id="ref-{{ $i }}"
                data-ref-index="{{ $i }}"
            >
                <div class="ref-card-head">
                    <div>
                        <div class="ref-title">
                            Referee {{ $i + 1 }}
                        </div>

                        <div
                            class="ref-subtitle"
                            id="ref_{{ $i }}_summary"
                        >
                            Add this person's details below.
                        </div>
                    </div>

                    <span
                        id="ref_{{ $i }}_status"
                        class="ref-status neutral"
                    >
                        <i class="fa fa-circle-dot" aria-hidden="true"></i>
                        Not checked yet
                    </span>
                </div>

                <div class="ref-card-body">
                    <div
                        id="ref_{{ $i }}_eligibility"
                        class="eligibility-box"
                        aria-live="polite"
                    >
                        <div class="eligibility-title">
                            Referee eligibility
                        </div>
                        <div class="eligibility-details">
                            Complete the name, relationship and dates to check this referee.
                        </div>
                    </div>

                    {{-- Name + relationship --}}
                    <div class="row field-row">
                        <div class="col-sm-7">
                            <div class="form-group {{ $errors->has("refs.$i.name") ? 'has-error' : '' }}">
                                <label
                                    class="control-label"
                                    for="refs_{{ $i }}_name"
                                >
                                    Referee's full name
                                    <span class="text-danger">*</span>
                                </label>

                                <input
                                    type="text"
                                    id="refs_{{ $i }}_name"
                                    name="refs[{{ $i }}][name]"
                                    class="form-control ref-name"
                                    value="{{ $ref['name'] }}"
                                    required
                                    autocomplete="name"
                                >

                                @if($errors->has("refs.$i.name"))
                                    <span class="help-block">
                                        {{ $errors->first("refs.$i.name") }}
                                    </span>
                                @else
                                    <span class="help-block"></span>
                                @endif
                            </div>
                        </div>

                        <div class="col-sm-5">
                            <div class="form-group {{ $errors->has("refs.$i.relationship") ? 'has-error' : '' }}">
                                <label
                                    class="control-label"
                                    for="refs_{{ $i }}_relationship"
                                >
                                    How do you know this person?
                                    <span class="text-danger">*</span>
                                </label>

                                <input
                                    type="text"
                                    id="refs_{{ $i }}_relationship"
                                    name="refs[{{ $i }}][relationship]"
                                    class="form-control ref-relationship"
                                    value="{{ $ref['relationship'] }}"
                                    required
                                    placeholder="e.g. Friend, Former colleague, Neighbour"
                                >

                                @if($errors->has("refs.$i.relationship"))
                                    <span class="help-block">
                                        {{ $errors->first("refs.$i.relationship") }}
                                    </span>
                                @else
                                    <span class="help-block">
                                        Relatives and romantic partners cannot be used.
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Known since / still known --}}
                    <div class="row field-row">
                        <div class="col-sm-6">
                            <div class="form-group {{ $errors->has("refs.$i.known_from") ? 'has-error' : '' }}">
                                <label
                                    class="control-label"
                                    for="refs_{{ $i }}_known_from"
                                >
                                    Known since
                                    <span class="text-danger">*</span>
                                </label>

                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa-regular fa-calendar" aria-hidden="true"></i>
                                    </span>

                                    <input
                                        type="date"
                                        id="refs_{{ $i }}_known_from"
                                        name="refs[{{ $i }}][known_from]"
                                        class="form-control ref-known-from"
                                        value="{{ $ref['known_from'] }}"
                                        max="{{ $today }}"
                                        required
                                    >
                                </div>

                                @if($errors->has("refs.$i.known_from"))
                                    <span class="help-block">
                                        {{ $errors->first("refs.$i.known_from") }}
                                    </span>
                                @else
                                    <span class="help-block"></span>
                                @endif
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group {{ $errors->has("refs.$i.known_to") ? 'has-error' : '' }}">
                                <label
                                    class="control-label"
                                    for="refs_{{ $i }}_known_to"
                                >
                                    Known until
                                </label>

                                <div
                                    id="refs_{{ $i }}_known_to_wrap"
                                    class="{{ (string)$ref['still_known'] === '1' ? 'hidden' : '' }}"
                                >
                                    <div class="input-group">
                                        <span class="input-group-addon">
                                            <i class="fa-regular fa-calendar" aria-hidden="true"></i>
                                        </span>

                                        <input
                                            type="date"
                                            id="refs_{{ $i }}_known_to"
                                            name="refs[{{ $i }}][known_to]"
                                            class="form-control ref-known-to"
                                            value="{{ $ref['known_to'] }}"
                                            max="{{ $today }}"
                                        >
                                    </div>
                                </div>

                                <div class="still-known-box">
                                    <label>
                                        <input
                                            type="checkbox"
                                            id="refs_{{ $i }}_still_known"
                                            name="refs[{{ $i }}][still_known]"
                                            class="ref-still-known"
                                            value="1"
                                            {{ (string)$ref['still_known'] === '1' ? 'checked' : '' }}
                                        >
                                        I still know this person
                                    </label>
                                </div>

                                @if($errors->has("refs.$i.known_to"))
                                    <span class="help-block">
                                        {{ $errors->first("refs.$i.known_to") }}
                                    </span>
                                @else
                                    <span class="help-block"></span>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Contact --}}
                    <div class="row field-row">
                        <div class="col-sm-6">
                            <div class="form-group {{ $errors->has("refs.$i.phone") || $errors->has("refs.$i.phone_country_code") ? 'has-error' : '' }}">
                                <label
                                    class="control-label"
                                    for="refs_{{ $i }}_phone"
                                >
                                    Phone number
                                    <span class="text-danger">*</span>
                                </label>

                                <div class="phone-row">
                                    <div class="phone-code">
                                        <select
                                            id="refs_{{ $i }}_phone_country_code"
                                            name="refs[{{ $i }}][phone_country_code]"
                                            class="form-control ref-phone-code"
                                            aria-label="Phone country code"
                                            required
                                        >
                                            <option value="">Code</option>

                                            @foreach($sortedPhoneCodes as $code)
                                                <option
                                                    value="{{ $code }}"
                                                    {{ (string)$ref['phone_country_code'] === (string)$code ? 'selected' : '' }}
                                                >
                                                    +{{ $code }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <input
                                        type="tel"
                                        id="refs_{{ $i }}_phone"
                                        name="refs[{{ $i }}][phone]"
                                        class="form-control phone-number ref-phone"
                                        value="{{ $ref['phone'] }}"
                                        required
                                        inputmode="tel"
                                        autocomplete="tel-national"
                                        maxlength="25"
                                        placeholder="e.g. 07123 456789"
                                    >
                                </div>

                                @if($errors->has("refs.$i.phone_country_code"))
                                    <span class="help-block">
                                        {{ $errors->first("refs.$i.phone_country_code") }}
                                    </span>
                                @elseif($errors->has("refs.$i.phone"))
                                    <span class="help-block">
                                        {{ $errors->first("refs.$i.phone") }}
                                    </span>
                                @else
                                    <span class="help-block">
                                        Spaces, brackets and hyphens are fine.
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group {{ $errors->has("refs.$i.email") ? 'has-error' : '' }}">
                                <label
                                    class="control-label"
                                    for="refs_{{ $i }}_email"
                                >
                                    Email address
                                    <span class="text-danger">*</span>
                                </label>

                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa-solid fa-envelope" aria-hidden="true"></i>
                                    </span>

                                    <input
                                        type="email"
                                        id="refs_{{ $i }}_email"
                                        name="refs[{{ $i }}][email]"
                                        class="form-control ref-email"
                                        value="{{ $ref['email'] }}"
                                        required
                                        autocomplete="email"
                                        placeholder="name@example.com"
                                    >
                                </div>

                                @if($errors->has("refs.$i.email"))
                                    <span class="help-block">
                                        {{ $errors->first("refs.$i.email") }}
                                    </span>
                                @else
                                    <span class="help-block"></span>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Address --}}
                    <div class="row field-row">
                        <div class="col-sm-6">
                            <div class="form-group {{ $errors->has("refs.$i.address") ? 'has-error' : '' }}">
                                <label
                                    class="control-label"
                                    for="refs_{{ $i }}_address"
                                >
                                    Address line 1
                                    <span class="text-danger">*</span>
                                </label>

                                <input
                                    type="text"
                                    id="refs_{{ $i }}_address"
                                    name="refs[{{ $i }}][address]"
                                    class="form-control ref-address"
                                    value="{{ $ref['address'] }}"
                                    required
                                    autocomplete="address-line1"
                                >

                                @if($errors->has("refs.$i.address"))
                                    <span class="help-block">
                                        {{ $errors->first("refs.$i.address") }}
                                    </span>
                                @else
                                    <span class="help-block">
                                        Start typing to search, or enter the address manually.
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group {{ $errors->has("refs.$i.address_line2") ? 'has-error' : '' }}">
                                <label
                                    class="control-label"
                                    for="refs_{{ $i }}_address_line2"
                                >
                                    Address line 2
                                </label>

                                <input
                                    type="text"
                                    id="refs_{{ $i }}_address_line2"
                                    name="refs[{{ $i }}][address_line2]"
                                    class="form-control ref-address-line2"
                                    value="{{ $ref['address_line2'] }}"
                                    autocomplete="address-line2"
                                >

                                @if($errors->has("refs.$i.address_line2"))
                                    <span class="help-block">
                                        {{ $errors->first("refs.$i.address_line2") }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="row field-row">
                        <div class="col-sm-4">
                            <div class="form-group {{ $errors->has("refs.$i.town") ? 'has-error' : '' }}">
                                <label
                                    class="control-label"
                                    for="refs_{{ $i }}_town"
                                >
                                    Town / City
                                    <span class="text-danger">*</span>
                                </label>

                                <input
                                    type="text"
                                    id="refs_{{ $i }}_town"
                                    name="refs[{{ $i }}][town]"
                                    class="form-control ref-town"
                                    value="{{ $ref['town'] }}"
                                    required
                                    autocomplete="address-level2"
                                >

                                @if($errors->has("refs.$i.town"))
                                    <span class="help-block">
                                        {{ $errors->first("refs.$i.town") }}
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="col-sm-4">
                            <div class="form-group {{ $errors->has("refs.$i.county") ? 'has-error' : '' }}">
                                <label
                                    class="control-label"
                                    for="refs_{{ $i }}_county"
                                >
                                    County / State / Region
                                </label>

                                <input
                                    type="text"
                                    id="refs_{{ $i }}_county"
                                    name="refs[{{ $i }}][county]"
                                    class="form-control ref-county"
                                    value="{{ $ref['county'] }}"
                                    autocomplete="address-level1"
                                >

                                @if($errors->has("refs.$i.county"))
                                    <span class="help-block">
                                        {{ $errors->first("refs.$i.county") }}
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="col-sm-4">
                            <div class="form-group {{ $errors->has("refs.$i.postcode") ? 'has-error' : '' }}">
                                <label
                                    class="control-label"
                                    for="refs_{{ $i }}_postcode"
                                >
                                    Postcode / ZIP / postal code
                                    <span class="text-danger">*</span>
                                </label>

                                <input
                                    type="text"
                                    id="refs_{{ $i }}_postcode"
                                    name="refs[{{ $i }}][postcode]"
                                    class="form-control ref-postcode"
                                    value="{{ $ref['postcode'] }}"
                                    required
                                    autocomplete="postal-code"
                                    style="text-transform:uppercase"
                                    maxlength="20"
                                >

                                @if($errors->has("refs.$i.postcode"))
                                    <span class="help-block">
                                        {{ $errors->first("refs.$i.postcode") }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="row field-row">
                        <div class="col-sm-6">
                            <div class="form-group {{ $errors->has("refs.$i.country") ? 'has-error' : '' }}">
                                <label
                                    class="control-label"
                                    for="refs_{{ $i }}_country"
                                >
                                    Country
                                    <span class="text-danger">*</span>
                                </label>

                                <select
                                    id="refs_{{ $i }}_country"
                                    name="refs[{{ $i }}][country]"
                                    class="form-control ref-country"
                                    required
                                >
                                    <option value="">Please select…</option>

                                    @if($uk)
                                        <option
                                            value="{{ $uk->iso3 }}"
                                            {{ (string)$ref['country'] === (string)$uk->iso3 ? 'selected' : '' }}
                                        >
                                            {{ $uk->nicename }}
                                        </option>
                                    @endif

                                    @foreach($countries as $country)
                                        @continue((int)$country->id === 225)

                                        <option
                                            value="{{ $country->iso3 }}"
                                            {{ (string)$ref['country'] === (string)$country->iso3 ? 'selected' : '' }}
                                        >
                                            {{ $country->nicename }}
                                        </option>
                                    @endforeach
                                </select>

                                @if($errors->has("refs.$i.country"))
                                    <span class="help-block">
                                        {{ $errors->first("refs.$i.country") }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach

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

    var TODAY = @json($today);

    var RELATIVE_WORDS = [
        'mother','father','parent','mum','mom','dad',
        'brother','sister','sibling','son','daughter','child',
        'wife','husband','spouse','cousin','aunt','uncle',
        'niece','nephew','grandmother','grandfather','grandparent',
        'grandma','grandpa','mother-in-law','father-in-law',
        'brother-in-law','sister-in-law','stepmother','stepfather',
        'stepsister','stepbrother','stepson','stepdaughter',
        'boyfriend','girlfriend','fiancé','fiance','fiancée','fiancee'
    ];

    function parseDate(value){
        if (!value) return null;

        var date = new Date(value + 'T12:00:00');
        return isNaN(date.getTime()) ? null : date;
    }

    function today(){
        return parseDate(TODAY);
    }

    function normalise(value){
        return String(value || '')
            .toLowerCase()
            .trim()
            .replace(/\s+/g,' ');
    }

    function cleanPhone(value){
        return String(value || '').replace(/\D+/g,'');
    }

    function formatDuration(fromValue,toValue){
        var from = parseDate(fromValue);
        var to = parseDate(toValue);

        if (!from || !to || to < from) {
            return null;
        }

        var years = to.getFullYear() - from.getFullYear();
        var months = to.getMonth() - from.getMonth();

        if (to.getDate() < from.getDate()) {
            months -= 1;
        }

        if (months < 0) {
            years -= 1;
            months += 12;
        }

        return {
            years:Math.max(0,years),
            months:Math.max(0,months)
        };
    }

    function yearsAsDecimal(duration){
        if (!duration) return 0;
        return duration.years + (duration.months / 12);
    }

    function durationText(duration){
        if (!duration) return '';

        var parts = [];

        if (duration.years) {
            parts.push(
                duration.years + ' ' +
                (duration.years === 1 ? 'year' : 'years')
            );
        }

        if (duration.months || !duration.years) {
            parts.push(
                duration.months + ' ' +
                (duration.months === 1 ? 'month' : 'months')
            );
        }

        return parts.join(' ');
    }

    function ensureHelpBlock(input){
        var group = input.closest('.form-group');
        if (!group) return null;

        var help = group.querySelector('.help-block');

        if (!help) {
            help = document.createElement('span');
            help.className = 'help-block';
            group.appendChild(help);
        }

        return help;
    }

    function setError(input,message){
        if (!input) return;

        var group = input.closest('.form-group');
        if (!group) return;

        var help = ensureHelpBlock(input);

        group.classList.toggle('has-error',!!message);

        if (message) {
            input.setAttribute('aria-invalid','true');

            if (help) {
                help.textContent = message;
                help.style.display = 'block';
            }
        } else {
            input.removeAttribute('aria-invalid');

            if (help && help.dataset.staticHelp !== '1') {
                help.textContent = '';
                help.style.display = '';
            }
        }
    }

    function isRelativeRelationship(value){
        var relationship = normalise(value);

        if (!relationship) return false;

        if (relationship.indexOf('business partner') !== -1) {
            relationship = relationship.replace(/business partner/g,'');
        }

        if (/(^|\b)partner(\b|$)/i.test(relationship)) {
            return true;
        }

        return RELATIVE_WORDS.some(function(word){
            var escaped = word.replace(/[.*+?^${}()|[\]\\]/g,'\\$&');
            return new RegExp('(?:^|\\b)' + escaped + '(?:\\b|$)','i')
                .test(relationship);
        });
    }

    function referenceValues(block){
        var index = block.dataset.refIndex;

        var stillKnown = document.getElementById(
            'refs_' + index + '_still_known'
        );

        var to = document.getElementById(
            'refs_' + index + '_known_to'
        );

        return {
            index:index,
            name:document.getElementById('refs_' + index + '_name'),
            relationship:document.getElementById(
                'refs_' + index + '_relationship'
            ),
            from:document.getElementById(
                'refs_' + index + '_known_from'
            ),
            to:to,
            stillKnown:stillKnown,
            email:document.getElementById(
                'refs_' + index + '_email'
            ),
            phoneCode:document.getElementById(
                'refs_' + index + '_phone_country_code'
            ),
            phone:document.getElementById(
                'refs_' + index + '_phone'
            ),
            address:document.getElementById(
                'refs_' + index + '_address'
            ),
            town:document.getElementById(
                'refs_' + index + '_town'
            ),
            postcode:document.getElementById(
                'refs_' + index + '_postcode'
            ),
            country:document.getElementById(
                'refs_' + index + '_country'
            )
        };
    }

    function requiredFieldsComplete(values){
        return [
            values.name,
            values.relationship,
            values.from,
            values.email,
            values.phoneCode,
            values.phone,
            values.address,
            values.town,
            values.postcode,
            values.country
        ].every(function(input){
            return input && String(input.value || '').trim();
        });
    }

    function validateReference(block,force){
        var values = referenceValues(block);
        var ok = true;

        function fail(input,message){
            ok = false;

            if (
                force ||
                input.dataset.touched === '1'
            ) {
                setError(input,message);
            }
        }

        function clear(input){
            if (input) setError(input,'');
        }

        [
            values.name,
            values.relationship,
            values.from,
            values.email,
            values.phoneCode,
            values.phone,
            values.address,
            values.town,
            values.postcode,
            values.country
        ].forEach(clear);

        if (values.to) clear(values.to);

        if (!String(values.name.value || '').trim()) {
            fail(values.name,'Referee name is required.');
        }

        if (!String(values.relationship.value || '').trim()) {
            fail(values.relationship,'Please say how you know this person.');
        } else if (isRelativeRelationship(values.relationship.value)) {
            fail(
                values.relationship,
                'Please use someone who is not a relative or romantic partner.'
            );
        }

        var fromDate = parseDate(values.from.value);

        if (!fromDate) {
            fail(values.from,'Enter when you first knew this person.');
        } else if (fromDate > today()) {
            fail(values.from,'Known-since date cannot be in the future.');
        }

        var stillKnown = !!values.stillKnown.checked;
        var toValue = stillKnown ? TODAY : values.to.value;
        var toDate = parseDate(toValue);

        if (!stillKnown && !toDate) {
            fail(
                values.to,
                'Enter when you stopped knowing this person, or select that you still know them.'
            );
        } else if (toDate && toDate > today()) {
            fail(values.to,'Known-until date cannot be in the future.');
        } else if (
            fromDate &&
            toDate &&
            toDate < fromDate
        ) {
            fail(
                values.to,
                'Known-until date cannot be before the known-since date.'
            );
        }

        if (!String(values.email.value || '').trim()) {
            fail(values.email,'Email address is required.');
        } else if (
            !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(
                values.email.value.trim()
            )
        ) {
            fail(values.email,'Enter a valid email address.');
        }

        if (!values.phoneCode.value) {
            fail(values.phoneCode,'Select a phone country code.');
        }

        var phoneDigits = cleanPhone(values.phone.value);

        if (!phoneDigits) {
            fail(values.phone,'Phone number is required.');
        } else if (phoneDigits.length < 5) {
            fail(values.phone,'Phone number looks too short.');
        } else if (phoneDigits.length > 18) {
            fail(values.phone,'Phone number looks too long.');
        }

        if (!String(values.address.value || '').trim()) {
            fail(values.address,'Address line 1 is required.');
        }

        if (!String(values.town.value || '').trim()) {
            fail(values.town,'Town / city is required.');
        }

        if (!String(values.postcode.value || '').trim()) {
            fail(values.postcode,'Postcode / postal code is required.');
        }

        if (!values.country.value) {
            fail(values.country,'Please select a country.');
        }

        return {
            ok:ok,
            duration:formatDuration(
                values.from.value,
                toValue
            ),
            relative:isRelativeRelationship(
                values.relationship.value
            ),
            complete:requiredFieldsComplete(values),
            values:values
        };
    }

    function duplicateProblems(){
        var blocks = Array.prototype.slice.call(
            document.querySelectorAll('.ref-card')
        );

        if (blocks.length !== 2) return [];

        var first = referenceValues(blocks[0]);
        var second = referenceValues(blocks[1]);

        var problems = [];

        var email1 = normalise(first.email.value);
        var email2 = normalise(second.email.value);

        if (email1 && email2 && email1 === email2) {
            problems.push({
                input:second.email,
                message:'Your two referees must be different people.'
            });
        }

        var phone1 = cleanPhone(first.phoneCode.value) +
            cleanPhone(first.phone.value);

        var phone2 = cleanPhone(second.phoneCode.value) +
            cleanPhone(second.phone.value);

        if (phone1 && phone2 && phone1 === phone2) {
            problems.push({
                input:second.phone,
                message:'Your two referees must be different people.'
            });
        }

        return problems;
    }

    function updateReferenceStatus(block){
        var result = validateReference(block,false);
        var index = block.dataset.refIndex;

        var status = document.getElementById(
            'ref_' + index + '_status'
        );

        var eligibility = document.getElementById(
            'ref_' + index + '_eligibility'
        );

        var summary = document.getElementById(
            'ref_' + index + '_summary'
        );

        var name = String(
            result.values.name.value || ''
        ).trim();

        var relationship = String(
            result.values.relationship.value || ''
        ).trim();

        if (summary) {
            if (name || relationship) {
                summary.textContent = [
                    name,
                    relationship
                ].filter(Boolean).join(' · ');
            } else {
                summary.textContent =
                    'Add this person\'s details below.';
            }
        }

        if (
            result.relative ||
            (!result.ok && result.complete)
        ) {
            status.className = 'ref-status error';
            status.innerHTML =
                '<i class="fa fa-circle-exclamation" aria-hidden="true"></i> Needs attention';

            eligibility.className =
                'eligibility-box error';

            eligibility.innerHTML =
                '<div class="eligibility-title">This referee needs attention</div>' +
                '<div class="eligibility-details">' +
                'Please correct the highlighted information before continuing.' +
                '</div>';

            return;
        }

        if (!result.duration) {
            status.className = 'ref-status neutral';
            status.innerHTML =
                '<i class="fa fa-circle-dot" aria-hidden="true"></i> Not checked yet';

            eligibility.className =
                'eligibility-box';

            eligibility.innerHTML =
                '<div class="eligibility-title">Referee eligibility</div>' +
                '<div class="eligibility-details">' +
                'Complete the relationship dates to check how long this person has known you.' +
                '</div>';

            return;
        }

        var duration = durationText(result.duration);
        var fiveYears = yearsAsDecimal(result.duration) >= 5;

        if (fiveYears) {
            status.className = 'ref-status success';
            status.innerHTML =
                '<i class="fa fa-circle-check" aria-hidden="true"></i> Suitable';

            eligibility.className =
                'eligibility-box success';

            eligibility.innerHTML =
                '<div class="eligibility-title">' +
                '<i class="fa fa-circle-check" aria-hidden="true"></i> ' +
                'Known for ' + duration +
                '</div>' +
                '<div class="eligibility-details">' +
                'This meets the preferred 5-year relationship length.' +
                '</div>';
        } else {
            status.className = 'ref-status warning';
            status.innerHTML =
                '<i class="fa fa-triangle-exclamation" aria-hidden="true"></i> Under 5 years';

            eligibility.className =
                'eligibility-box warning';

            eligibility.innerHTML =
                '<div class="eligibility-title">' +
                '<i class="fa fa-circle-info" aria-hidden="true"></i> ' +
                'Known for ' + duration +
                '</div>' +
                '<div class="eligibility-details">' +
                '<strong>You can still use this referee.</strong> ' +
                'If you do not have a suitable person who has known you for 5 years, ' +
                'provide the best available referee and continue.' +
                '</div>';
        }
    }

    function updateKnownTo(block){
        var values = referenceValues(block);
        var wrap = document.getElementById(
            'refs_' + values.index + '_known_to_wrap'
        );

        var stillKnown = values.stillKnown.checked;

        if (wrap) {
            wrap.classList.toggle('hidden',stillKnown);
        }

        if (values.to) {
            values.to.disabled = stillKnown;

            if (stillKnown) {
                values.to.removeAttribute('required');
            } else {
                values.to.setAttribute('required','required');
            }
        }

        updateReferenceStatus(block);
    }

    function validateWholeForm(){
        var blocks = Array.prototype.slice.call(
            document.querySelectorAll('.ref-card')
        );

        var valid = true;
        var firstBad = null;

        blocks.forEach(function(block){
            var result = validateReference(block,true);

            if (!result.ok) {
                valid = false;

                if (!firstBad) {
                    firstBad = block.querySelector(
                        '.has-error input, .has-error select'
                    );
                }
            }
        });

        duplicateProblems().forEach(function(problem){
            valid = false;
            setError(problem.input,problem.message);

            if (!firstBad) {
                firstBad = problem.input;
            }
        });

        if (!valid && firstBad) {
            firstBad.scrollIntoView({
                behavior:'smooth',
                block:'center'
            });

            setTimeout(function(){
                try {
                    firstBad.focus();
                } catch(e){}
            },250);
        }

        /*
         * IMPORTANT:
         * Being known for less than 5 years is NOT included in `valid`.
         * It is advisory only and never prevents submission.
         */
        return valid;
    }

    document.querySelectorAll('.ref-card').forEach(function(block){
        var values = referenceValues(block);

        block.addEventListener('input',function(event){
            var target = event.target;

            if (
                target.matches(
                    'input,select'
                )
            ) {
                target.dataset.touched = '1';
                validateReference(block,false);
                updateReferenceStatus(block);
            }
        });

        block.addEventListener('change',function(event){
            var target = event.target;

            if (
                target.matches(
                    'input,select'
                )
            ) {
                target.dataset.touched = '1';
            }

            if (target.classList.contains('ref-still-known')) {
                updateKnownTo(block);
            } else {
                validateReference(block,false);
                updateReferenceStatus(block);
            }

            duplicateProblems().forEach(function(problem){
                setError(problem.input,problem.message);
            });
        });

        block.addEventListener('blur',function(event){
            var target = event.target;

            if (
                target.matches(
                    'input,select'
                )
            ) {
                target.dataset.touched = '1';
                validateReference(block,false);
                updateReferenceStatus(block);
            }
        },true);

        updateKnownTo(block);
        updateReferenceStatus(block);
    });

    var form = document.getElementById(
        'personalReferencesForm'
    );

    if (form) {
        form.addEventListener('submit',function(event){
            if (!validateWholeForm()) {
                event.preventDefault();
                event.stopPropagation();
            }
        });
    }

    document.addEventListener('blur',function(event){
        if (
            event.target &&
            event.target.classList.contains('ref-postcode')
        ) {
            event.target.value = String(
                event.target.value || ''
            ).toUpperCase().trim();
        }

        if (
            event.target &&
            event.target.matches(
                'input[type="text"],input[type="email"],input[type="tel"]'
            )
        ) {
            event.target.value = String(
                event.target.value || ''
            ).trim();
        }
    },true);

    /* ---------------------------------------------------------------
     | Google Places autocomplete
     | --------------------------------------------------------------- */

    var COUNTRY_ROWS = @json(
        isset($countries)
            ? $countries->map(fn($c) => [
                'iso2'=>$c->iso,
                'iso3'=>$c->iso3,
                'name'=>$c->nicename
            ])->values()
            : collect()
    );

    var ISO2_TO_ISO3 = {};
    var ISO3_TO_ISO2 = {};

    COUNTRY_ROWS.forEach(function(country){
        if (country.iso2) {
            ISO2_TO_ISO3[country.iso2.toUpperCase()] =
                country.iso3
                    ? country.iso3.toUpperCase()
                    : null;
        }

        if (country.iso3) {
            ISO3_TO_ISO2[country.iso3.toUpperCase()] =
                country.iso2
                    ? country.iso2.toUpperCase()
                    : null;
        }
    });

    function component(components,type,shortName){
        var item = components.find(function(component){
            return component.types &&
                component.types.indexOf(type) !== -1;
        });

        if (!item) return '';

        return shortName
            ? item.short_name
            : item.long_name;
    }

    function buildLine1(components){
        var number = component(
            components,
            'street_number',
            false
        );

        var route = component(
            components,
            'route',
            false
        );

        return [number,route]
            .filter(Boolean)
            .join(' ');
    }

    function buildLine2(components){
        return [
            component(components,'subpremise',false),
            component(components,'premise',false),
            component(components,'neighborhood',false)
        ].filter(Boolean).join(', ');
    }

    function townFrom(components){
        return (
            component(components,'postal_town',false) ||
            component(components,'locality',false) ||
            component(components,'sublocality',false) ||
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

    function wireAutocomplete(block){
        if (
            !window.google ||
            !google.maps ||
            !google.maps.places
        ) {
            return;
        }

        var index = block.dataset.refIndex;

        var address = document.getElementById(
            'refs_' + index + '_address'
        );

        var addressLine2 = document.getElementById(
            'refs_' + index + '_address_line2'
        );

        var town = document.getElementById(
            'refs_' + index + '_town'
        );

        var county = document.getElementById(
            'refs_' + index + '_county'
        );

        var postcode = document.getElementById(
            'refs_' + index + '_postcode'
        );

        var country = document.getElementById(
            'refs_' + index + '_country'
        );

        if (
            !address ||
            address.dataset.placesWired === '1'
        ) {
            return;
        }

        address.dataset.placesWired = '1';

        var options = {
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

        var autocomplete =
            new google.maps.places.Autocomplete(
                address,
                options
            );

        if (country) {
            country.addEventListener(
                'change',
                function(){
                    try {
                        var iso2 = ISO3_TO_ISO2[
                            String(
                                country.value || ''
                            ).toUpperCase()
                        ];

                        autocomplete.setComponentRestrictions(
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
                var place = autocomplete.getPlace();

                if (
                    !place ||
                    !place.address_components
                ) {
                    return;
                }

                var components =
                    place.address_components;

                var line1 = buildLine1(components);
                var line2 = buildLine2(components);
                var newTown = townFrom(components);
                var newCounty = countyFrom(components);
                var newPostcode =
                    component(
                        components,
                        'postal_code',
                        false
                    );

                var iso2 =
                    component(
                        components,
                        'country',
                        true
                    );

                var iso3 = iso2
                    ? ISO2_TO_ISO3[
                        iso2.toUpperCase()
                    ]
                    : null;

                if (line1) address.value = line1;
                if (addressLine2) {
                    addressLine2.value = line2 || '';
                }
                if (town) town.value = newTown || '';
                if (county) county.value = newCounty || '';
                if (postcode) {
                    postcode.value =
                        String(newPostcode || '')
                            .toUpperCase();
                }
                if (country && iso3) {
                    country.value = iso3;
                }

                updateReferenceStatus(block);
            }
        );
    }

    window.__initReferencePlacesAutocomplete =
        function(){
            document
                .querySelectorAll('.ref-card')
                .forEach(wireAutocomplete);
        };

    if (
        window.google &&
        google.maps &&
        google.maps.places
    ) {
        window.__initReferencePlacesAutocomplete();
    }
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
            window.__initReferencePlacesAutocomplete
        ) {
            window.__initReferencePlacesAutocomplete();
        }

        return;
    }

    if (
        document.getElementById(
            'referencePlacesScript'
        )
    ) {
        return;
    }

    var script = document.createElement('script');

    script.id = 'referencePlacesScript';
    script.src =
        "https://maps.googleapis.com/maps/api/js?key={{ config('services.google.maps_places_key') }}" +
        "&libraries=places&callback=__initReferencePlacesAutocomplete";

    script.async = true;
    script.defer = true;

    document.head.appendChild(script);
})();
</script>
@endsection
