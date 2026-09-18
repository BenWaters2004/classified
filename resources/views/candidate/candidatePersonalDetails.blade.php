@extends('layout.candidate')

@section('form-content')
<style>
/* Section + card feel */
.form-section{padding:20px 20px 5px;margin-bottom:20px}
.section-title{color:#2C3C64;font-weight:700;margin:0 0 15px;display:flex;align-items:center;gap:10px}
.section-title:after{content:"";flex:1;height:1px;background:#e5e9f2}

/* Grid helpers for Bootstrap 3 form-horizontal */
.field-row{margin-left:-10px;margin-right:-10px}
.field-row .form-group{padding-left:10px;padding-right:10px;margin-bottom:15px}

/* Inputs */
.form-control{height:40px}
.form-control:focus{
  border-color: var(--brand) !important;
  -webkit-box-shadow: inset 0 1px 1px rgba(0,0,0,.075),
                      0 0 8px color-mix(in srgb, var(--brand) 60%, transparent) !important;
  box-shadow:         inset 0 1px 1px rgba(0,0,0,.075),
                      0 0 8px color-mix(in srgb, var(--brand) 60%, transparent) !important;
}
.input-group-addon i { font-size:16px; width:18px; text-align:center; line-height:1; }
.input-code{width:80px;}

/* Labels + help text */
.control-label{font-weight:600;color:#2C3C64}
.help-block{margin-top:6px;color:#6b7480}

/* Errors */
.has-error .control-label,.has-error .help-block{color:#b94a48}
.has-error .form-control{border-color:#b94a48;box-shadow:none}

/* Mobile: stack labels above inputs */
@media (max-width:768px){
    .form-horizontal .control-label{text-align:left;margin-bottom:6px}
}

.address-grid .form-group { margin-bottom:15px; }

.hidden{display:none!important;}
</style>

<form class="form-horizontal" method="POST" action="{{ route('candidate.personal.save') }}" novalidate>
    @csrf

    {{-- Error summary (accessible) --}}
    @if ($errors->any())
        <div class="alert alert-danger" role="alert" aria-live="polite" aria-atomic="true">
            <p class="sr-only">There were errors with your submission:</p>
            <ul class="m-0">
                @foreach ($errors->all() as $error)
                    <li><a href="#{{ \Illuminate\Support\Str::of($error)->snake()->replace(' ', '_') }}" class="text-danger">{{ $error }}</a></li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="p-5 bg-light rounded">
        <div class="form-section">

            <h3 class="section-title">About you</h3>
            <div class="alert alert-info" role="status">
                Please enter your personal details <strong>exactly as they appear on your official documents</strong> where applicable.
            </div>

            <div class="row field-row">
                {{-- Title --}}
                <div class="col-sm-4">
                    @php
                        $commonTitleOrder = [
                            'MR',
                            'MX',
                            'MRS',
                            'MISS',
                            'MS',
                            'DR',
                        ];

                        $sortedTitles = collect($titles)->sort(function ($a, $b) use ($commonTitleOrder) {
                            $aLabel = trim($a->userTitle);
                            $bLabel = trim($b->userTitle);

                            $aPos = array_search($aLabel, $commonTitleOrder, true);
                            $bPos = array_search($bLabel, $commonTitleOrder, true);

                            $aCommon = $aPos !== false;
                            $bCommon = $bPos !== false;

                            if ($aCommon && $bCommon) {
                                return $aPos <=> $bPos;
                            }

                            if ($aCommon) return -1;
                            if ($bCommon) return 1;

                            return strcasecmp($aLabel, $bLabel);
                        })->values();
                    @endphp
                    <div class="form-group {{ $errors->has('title') ? 'has-error' : '' }}">
                        <label for="title" class="control-label">Title <span class="text-danger">*</span></label>
                        <select id="title" name="title" class="form-control" required>
                        <option value="">Please select…</option>
                        @foreach($sortedTitles as $t)
                            <option value="{{ $t->id }}" {{ (string) old('title', $userDetails->title ?? '') === (string) $t->id ? 'selected' : '' }}>
                            {{ $t->userTitle }}
                            </option>
                        @endforeach
                        </select>
                        @if($errors->has('title'))<span class="help-block">{{ $errors->first('title') }}</span>@endif
                    </div>
                </div>

                {{-- Sex --}}
                <div class="col-sm-4">
                    <div class="form-group {{ $errors->has('sex') ? 'has-error' : '' }}">
                        <label for="sex" class="control-label">
                            Sex <span class="text-danger">*</span>
                        </label>
                        <select id="sex" name="sex" class="form-control" required>
                            <option value="">Please select…</option>
                            <option value="male" @if(($userDetails->gender ?? ' ') == "male") selected @endif>Male</option>
                            <option value="female" @if(($userDetails->gender ?? ' ') == "female") selected @endif>Female</option>
                        </select>
                        @if($errors->has('sex'))<span class="help-block">{{ $errors->first('sex') }}</span>@endif
                    </div>
                </div>

                {{-- DOB with icon --}}
                <div class="col-sm-4">
                    <div class="form-group {{ $errors->has('dob') ? 'has-error' : '' }}">
                        <label for="dob" class="control-label">Date of birth <span class="text-danger">*</span></label>
                        <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa-regular fa-calendar" aria-hidden="true"></i>
                        </span>
                        <input id="dob" name="dob" type="date" class="form-control"
                                value="{{ old('dob', isset($userDetails->dob) ? \Illuminate\Support\Str::of($userDetails->dob)->substr(0,10) : '') }}"
                                required max="{{ now()->format('Y-m-d') }}">
                        </div>
                        @if($errors->has('dob'))<span class="help-block">{{ $errors->first('dob') }}</span>@endif
                    </div>
                </div>
            </div>

            <div class="row field-row">
                <div class="col-sm-4">
                    <div class="form-group {{ $errors->has('birth_town') ? 'has-error' : '' }}">
                        <label for="birth_town" class="control-label">Birth town/city <span class="text-danger">*</span></label>
                        <input id="birth_town" name="birth_town" type="text" class="form-control"
                            value="{{ old('birth_town', $userDetails->birth_town ?? '') }}" required>
                        @if($errors->has('birth_town'))<span class="help-block">{{ $errors->first('birth_town') }}</span>@endif
                    </div>
                </div>

                <div class="col-sm-4">
                    <div class="form-group {{ $errors->has('birth_country') ? 'has-error' : '' }}">
                        <label for="birth_country" class="control-label">Birth country <span class="text-danger">*</span></label>
                        <select id="birth_country" name="birth_country" class="form-control" required>
                            <option value="">Please select…</option>

                            {{-- UK first (id 225) --}}
                            @php $uk = $countries->firstWhere('id', 225); @endphp
                            @if($uk)
                                <option value="{{ $uk->iso3 }}"
                                {{ old('birth_country', $userDetails->birth_country ?? '') === $uk->iso3 ? 'selected' : '' }}>
                                {{ $uk->nicename }}
                                </option>
                            @endif

                            {{-- Then the rest --}}
                            @foreach($countries as $c)
                                @continue($c->id == 225)
                                <option value="{{ $c->iso3 }}"
                                {{ old('birth_country', $userDetails->birth_country ?? '') === $c->iso3 ? 'selected' : '' }}>
                                {{ $c->nicename }}
                                </option>
                            @endforeach
                        </select>
                        @if($errors->has('birth_country'))<span class="help-block">{{ $errors->first('birth_country') }}</span>@endif
                    </div>
                </div>

                <div class="col-sm-4">
                    <div class="form-group {{ $errors->has('birth_nationality') ? 'has-error' : '' }}">
                        <label for="birth_nationality" class="control-label">Birth nationality <span class="text-danger">*</span></label>
                        <input id="birth_nationality" name="birth_nationality" type="text" class="form-control"
                            value="{{ old('birth_nationality', $userDetails->birth_nationality ?? '') }}" required>
                        @if($errors->has('birth_nationality'))<span class="help-block">{{ $errors->first('birth_nationality') }}</span>@endif
                    </div>
                </div>
            </div>

            <div class="row field-row">
                <div class="col-sm-4">
                    <div class="form-group {{ $errors->has('forename') ? 'has-error' : '' }}">
                        <label for="forename" class="control-label">First name <span class="text-danger">*</span></label>
                        <input id="forename" name="forename" type="text" class="form-control"
                            value="{{ old('forename', $userDetails->forename ?? '') }}" required autocomplete="given-name">
                        @if($errors->has('forename'))<span class="help-block">{{ $errors->first('forename') }}</span>@endif
                    </div>
                </div>

                <div class="col-sm-4">
                    <div class="form-group">
                        <label for="middlename" class="control-label">Middle name(s)</label>
                        <input id="middlename" name="middlename" type="text" class="form-control"
                            value="{{ old('middlename', $userDetails->middlename ?? '') }}" autocomplete="additional-name">
                    </div>
                </div>

                <div class="col-sm-4">
                    <div class="form-group {{ $errors->has('surname') ? 'has-error' : '' }}">
                        <label for="surname" class="control-label">Current surname / family name <span class="text-danger">*</span></label>
                        <input id="surname" name="surname" type="text" class="form-control"
                            value="{{ old('surname', $userDetails->presentSurname ?? '') }}" required autocomplete="family-name">
                        @if($errors->has('surname'))<span class="help-block">{{ $errors->first('surname') }}</span>@endif
                    </div>
                </div>
            </div>

            {{-- Toggle should be on if we had old() OR if DB already has rows --}}
            @php
                $oldPrev = old('previous_names', []);
                $hasPrevFromDb = isset($prevNames) && $prevNames->count() > 0;
                $toggleOn = old('has_prev_names') || $hasPrevFromDb;
            @endphp

            <div class="row field-row">
                <div class="col-sm-12">
                    <div class="form-group">
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" id="has_prev_names"
                                    {{ $toggleOn ? 'checked' : '' }}
                                    aria-controls="prev_names_panel"
                                    aria-expanded="{{ $toggleOn ? 'true' : 'false' }}">
                                Have you ever been known by another name?
                            </label>
                        </div>
                        <p class="help-block">Include previous surnames, maiden names, legally changed names or any other names you have previously used.</p>
                    </div>
                </div>
            </div>

            <div id="prev_names_panel" class="{{ $toggleOn ? '' : 'hidden' }}" aria-live="polite">
                <div class="well well-sm" style="margin-top:10px">
                    <p class="help-block" id="prev_names_counter">No previous names added yet.</p>

                    <div id="prev_names_list">
                        @php
                            // If we have old() values (after validation error), render those
                            if (!empty($oldPrev)) {
                                $prefill = collect($oldPrev)->map(function($pn){
                                    return [
                                        'forename'   => $pn['forename']   ?? '',
                                        'middlename' => $pn['middlename'] ?? '',
                                        'surname'    => $pn['surname']    ?? '',
                                        'from'       => $pn['from']       ?? '',
                                        'to'         => $pn['to']         ?? '',
                                    ];
                                })->values();
                            } elseif ($hasPrevFromDb) {
                                // otherwise use DB rows
                                $prefill = $prevNames->map(function($row){
                                    return [
                                        'forename'   => $row->other_forename ?? '',
                                        'middlename' => $row->other_middlename ?? '',
                                        'surname'    => $row->other_surname ?? '',
                                        // ensure yyyy-mm-dd for type="date"
                                        'from'       => $row->dateFrom ? \Illuminate\Support\Str::of($row->dateFrom)->substr(0,10) : '',
                                        'to'         => $row->dateTo   ? \Illuminate\Support\Str::of($row->dateTo)->substr(0,10)   : '',
                                    ];
                                })->values();
                            } else {
                                $prefill = collect();
                            }
                        @endphp

                        @if($prefill->count())
                            @foreach($prefill as $i => $pn)
                                <div class="well well-sm prev-name-item" data-index="{{ $i }}">
                                    <div class="row field-row">
                                        <div class="col-sm-4">
                                            <div class="form-group">
                                                <label class="control-label">Previous first name</label>
                                                <input type="text"
                                                    name="previous_names[{{ $i }}][forename]"
                                                    class="form-control pn-forename"
                                                    value="{{ $pn['forename'] }}">
                                                <span class="help-block pn-forename-err" style="display:none"></span>
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="form-group">
                                                <label class="control-label">Previous middle name(s)</label>
                                                <input type="text"
                                                    name="previous_names[{{ $i }}][middlename]"
                                                    class="form-control pn-middlename"
                                                    value="{{ $pn['middlename'] }}">
                                                <span class="help-block pn-middlename-err" style="display:none"></span>
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="form-group">
                                                <label class="control-label">Previous surname / family name</label>
                                                <input type="text"
                                                    name="previous_names[{{ $i }}][surname]"
                                                    class="form-control pn-surname"
                                                    value="{{ $pn['surname'] }}">
                                                <span class="help-block pn-surname-err" style="display:none"></span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row field-row">
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label class="control-label">Date from</label>
                                                <div class="input-group">
                                                    <span class="input-group-addon">
                                                        <i class="fa-regular fa-calendar" aria-hidden="true"></i>
                                                    </span>
                                                    <input type="date"
                                                        name="previous_names[{{ $i }}][from]"
                                                        class="form-control pn-from"
                                                        value="{{ $pn['from'] }}" max="{{ now()->format('Y-m-d') }}">
                                                </div>
                                                <span class="help-block pn-from-err" style="display:none"></span>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label class="control-label">Date to</label>
                                                <div class="input-group">
                                                    <span class="input-group-addon">
                                                        <i class="fa-regular fa-calendar" aria-hidden="true"></i>
                                                    </span>
                                                    <input type="date"
                                                        name="previous_names[{{ $i }}][to]"
                                                        class="form-control pn-to"
                                                        value="{{ $pn['to'] }}" max="{{ now()->format('Y-m-d') }}">
                                                </div>
                                                <span class="help-block pn-to-err" style="display:none"></span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="text-right">
                                        <button type="button" class="btn btn-link text-danger remove_prev_name">
                                            <i class="fa fa-trash"></i> Remove
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>

                    <div class="text-right">
                        <button type="button" class="btn btn-default" id="add_prev_name">
                            <i class="fa fa-plus"></i> Add another name
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-section">
            <h3 class="section-title">Your contact details</h3>
            @php
                $sortedPhoneCodes = collect($phoneCodes)
                    ->map(fn($code) => (string) $code)
                    ->unique()
                    ->sort(function ($a, $b) {
                        if ($a === '44') return -1;
                        if ($b === '44') return 1;

                        return (int) $a <=> (int) $b;
                    })
                    ->values();
            @endphp

            {{-- Email with icon --}}
            <div class="form-group {{ $errors->has('email') ? 'has-error' : '' }}">
                <label for="email" class="col-sm-4 control-label">Email address <span class="text-danger">*</span></label>
                <div class="col-sm-8">
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa-solid fa-envelope" aria-hidden="true"></i>
                        </span>
                        <input id="email" name="email" type="email" class="form-control"
                            value="{{ old('email', $userDetails->application_email ?? '') }}" required autocomplete="email" placeholder="name@example.com">
                    </div>
                    @if($errors->has('email'))<span class="help-block">{{ $errors->first('email') }}</span>@endif
                </div>
            </div>

            {{-- Contact number --}}
            <div class="form-group {{ $errors->has('contact_number') ? 'has-error' : '' }}">
                <label for="contact_number" class="col-sm-4 control-label">Contact number <span class="text-danger">*</span></label>
                <div class="col-sm-8">
                    <div class="input-group">
                        <span class="input-group-addon" aria-hidden="true">
                            <i class="fa-solid fa-phone"></i>
                        </span>
                        <div class="input-group-addon" style="background-color: white;">
                            <select style="border: 0; width:80px; font-size: 16px;" name="contact_country_code" aria-label="Country code">
                                <option value="">Code</option>
                                @foreach($sortedPhoneCodes as $code)
                                    <option value="{{ $code }}" {{ old('contact_country_code', $userDetails->contact_number_country_code ?? '') == $code ? 'selected' : '' }}>
                                        +{{ $code }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <input id="contact_number" name="contact_number" type="tel"
                            class="form-control phone-number"
                            value="{{ old('contact_number', $userDetails->contact_number ?? '') }}"
                            required inputmode="tel" autocomplete="tel" maxlength="16">
                    </div>
                    @if($errors->has('contact_number'))<span class="help-block">{{ $errors->first('contact_number') }}</span>@endif
                </div>
            </div>

            {{-- Mobile number --}}
            <div class="form-group">
                <label for="mobile_number" class="col-sm-4 control-label">Mobile number</label>
                <div class="col-sm-8">
                    <div class="input-group">
                        <span class="input-group-addon" aria-hidden="true">
                            <i class="fa-solid fa-phone"></i>
                        </span>

                        <div class="input-group-addon" style="background-color: white;">
                            <select name="mobile_country_code" style="border: 0; width:80px; font-size: 16px;" aria-label="Country code">
                                <option value="">Code</option>
                                @foreach($sortedPhoneCodes as $code)
                                    <option value="{{ $code }}" {{ old('mobile_country_code', $userDetails->mobile_number_country_code ?? '') == $code ? 'selected' : '' }}>
                                        +{{ $code }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <input id="mobile_number" name="mobile_number" type="tel"
                            class="form-control phone-number"
                            value="{{ old('mobile_number', $userDetails->mobile_number ?? '') }}"
                            inputmode="tel" autocomplete="tel-national" maxlength="16">
                    </div>
                </div>
            </div>
        </div>

        <div class="form-section">
            <h3 class="section-title">Current address</h3>

            <div class="row field-row address-grid">
                <div class="col-sm-4">
                    <div class="form-group {{ $errors->has('address_line1') ? 'has-error' : '' }}">
                        <label for="address_line1" class="control-label">Address line 1 <span class="text-danger">*</span></label>
                        <input id="address_line1" name="address_line1" type="text" class="form-control"
                            value="{{ old('address_line1', $userDetails->address_line_1 ?? '') }}"
                            required autocomplete="address-line1">
                        @if($errors->has('address_line1')) <span class="help-block">{{ $errors->first('address_line1') }}</span> @endif
                    </div>
                </div>

                <div class="col-sm-4">
                    <div class="form-group">
                        <label for="address_line2" class="control-label">Address line 2</label>
                        <input id="address_line2" name="address_line2" type="text" class="form-control"
                            value="{{ old('address_line2', $userDetails->address_line_2 ?? '') }}"
                            autocomplete="address-line2">
                    </div>
                </div>

                <div class="col-sm-4">
                    <div class="form-group {{ $errors->has('city') ? 'has-error' : '' }}">
                        <label for="city" class="control-label">Town/City <span class="text-danger">*</span></label>
                        <input id="city" name="city" type="text" class="form-control"
                            value="{{ old('city', $userDetails->address_town ?? '') }}"
                            required autocomplete="address-level2">
                        @if($errors->has('city')) <span class="help-block">{{ $errors->first('city') }}</span> @endif
                    </div>
                </div>
            </div>

            <div class="row field-row address-grid">
                <div class="col-sm-4">
                    <div class="form-group">
                        <label for="county" class="control-label">County</label>
                        <input id="county" name="county" type="text" class="form-control"
                            value="{{ old('county', $userDetails->address_county ?? '') }}"
                            autocomplete="address-level1">
                    </div>
                </div>

                <div class="col-sm-4">
                    <div class="form-group {{ $errors->has('postcode') ? 'has-error' : '' }}">
                        <label for="postcode" class="control-label">Postcode / Zip Code <span class="text-danger">*</span></label>
                        <input id="postcode" name="postcode" type="text" class="form-control"
                            value="{{ old('postcode', $userDetails->address_postcode ?? '') }}"
                            required autocomplete="postal-code" style="text-transform:uppercase" maxlength="11">
                        @if($errors->has('postcode')) <span class="help-block">{{ $errors->first('postcode') }}</span> @endif
                    </div>
                </div>

                <div class="col-sm-4">
                    <div class="form-group {{ $errors->has('country') ? 'has-error' : '' }}">
                        <label for="country" class="control-label">Country <span class="text-danger">*</span></label>
                        <select id="country" name="country" class="form-control" required>
                            <option value="">Please select…</option>

                            @php $uk = $countries->firstWhere('id', 225); @endphp
                            @if($uk)
                                <option value="{{ $uk->iso3 }}"
                                    {{ old('country', $userDetails->address_country ?? '') === $uk->iso3 ? 'selected' : '' }}>
                                    {{ $uk->nicename }}
                                </option>
                            @endif

                            @foreach($countries as $c)
                                @continue($c->id == 225)
                                <option value="{{ $c->iso3 }}"
                                    {{ old('country', $userDetails->address_country ?? '') === $c->iso3 ? 'selected' : '' }}>
                                    {{ $c->nicename }}
                                </option>
                            @endforeach
                        </select>
                        @if($errors->has('country'))<span class="help-block">{{ $errors->first('country') }}</span>@endif
                    </div>
                </div>
            </div>

            <div class="row field-row address-grid">
                <div class="col-sm-4">
                    <div class="form-group {{ $errors->has('moved_in') ? 'has-error' : '' }}">
                        <label for="moved_in" class="control-label">Living here since <span class="text-danger">*</span></label>
                        <div class="">
                            <div class="input-group">
                                <span class="input-group-addon"><i class="fa-regular fa-calendar" aria-hidden="true"></i></span>
                                <input id="moved_in" name="moved_in" type="date" class="form-control"
                                    value="{{ old('moved_in', isset($userDetails->current_address_from) ? substr($userDetails->current_address_from,0,10) : '') }}"
                                    placeholder="dd/mm/yyyy" required max="{{ now()->format('Y-m-d') }}">
                            </div>
                        </div>
                        @if($errors->has('moved_in')) <span class="help-block">{{ $errors->first('moved_in') }}</span> @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="form-group m-t-20">
            <div class="col-sm-offset-4 col-sm-8">
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
    var hasToggle = document.getElementById('has_prev_names');
    var panel = document.getElementById('prev_names_panel');
    var list = document.getElementById('prev_names_list');
    var addBtn = document.getElementById('add_prev_name');
    var counter = document.getElementById('prev_names_counter');

    // current number of rendered rows (from old() or zero)
    var idx = list ? list.querySelectorAll('.prev-name-item').length : 0;

    function updateCounter(){
        var count = list.querySelectorAll('.prev-name-item').length;
        counter.textContent = count === 0
            ? 'No previous names added yet.'
            : count + ' previous ' + (count===1?'name':'names') + ' added.';
    }

    function setRequired(on){
        // Only mark fields as required when the section is on and a row exists
        var rows = list.querySelectorAll('.prev-name-item');
        rows.forEach(function(row){
            ['forename','surname','from','to'].forEach(function(key){
                var el = row.querySelector('[name^="previous_names"][name$="['+key+']"]');
                if (el) {
                    if (on) el.setAttribute('required','required');
                    else el.removeAttribute('required');
                }
            });
        });
    }

    function ensureAtLeastOneRow(){
        if (list.querySelectorAll('.prev-name-item').length === 0) {
            list.insertAdjacentHTML('beforeend', prevNameTemplate(idx++));
        }
    }

    function prevNameTemplate(i){
        return ''+
        '<div class="well well-sm prev-name-item" data-index="'+i+'">'+
            '<div class="row field-row">'+
                '<div class="col-sm-4">'+
                    '<div class="form-group">'+
                        '<label class="control-label">Previous first name</label>'+
                        '<input type="text" name="previous_names['+i+'][forename]" class="form-control pn-forename">'+
                        '<span class="help-block pn-forename-err" style="display:none"></span>'+
                    '</div>'+
                '</div>'+
                '<div class="col-sm-4">'+
                    '<div class="form-group">'+
                        '<label class="control-label">Previous middle name(s)</label>'+
                        '<input type="text" name="previous_names['+i+'][middlename]" class="form-control pn-middlename">'+
                        '<span class="help-block pn-middlename-err" style="display:none"></span>'+
                    '</div>'+
                '</div>'+
                '<div class="col-sm-4">'+
                    '<div class="form-group">'+
                        '<label class="control-label">Previous surname / family name</label>'+
                        '<input type="text" name="previous_names['+i+'][surname]" class="form-control pn-surname">'+
                        '<span class="help-block pn-surname-err" style="display:none"></span>'+
                    '</div>'+
                '</div>'+
            '</div>'+
            '<div class="row field-row">'+
                '<div class="col-sm-6">'+
                    '<div class="form-group">'+
                        '<label class="control-label">Date from</label>'+
                        '<div class="input-group">'+
                            '<span class="input-group-addon">'+
                                '<i class="fa-regular fa-calendar" aria-hidden="true"></i>'+
                            '</span>'+
                            '<input type="date" name="previous_names['+i+'][from]" class="form-control pn-from">'+
                        '</div>'+
                        '<span class="help-block pn-from-err" style="display:none"></span>'+
                    '</div>'+
                '</div>'+
                '<div class="col-sm-6">'+
                    '<div class="form-group">'+
                        '<label class="control-label">Date to</label>'+
                        '<div class="input-group">'+
                            '<span class="input-group-addon">'+
                                '<i class="fa-regular fa-calendar" aria-hidden="true"></i>'+
                            '</span>'+
                            '<input type="date" name="previous_names['+i+'][to]" class="form-control pn-to">'+
                        '</div>'+
                        '<span class="help-block pn-to-err" style="display:none"></span>'+
                    '</div>'+
                '</div>'+
            '</div>'+
            '<div class="text-right">'+
                '<button type="button" class="btn btn-link text-danger remove_prev_name"><i class="fa fa-trash"></i> Remove</button>'+
            '</div>'+
        '</div>';
    }

    if (!hasToggle || !panel || !list || !addBtn || !counter) return;

    // Toggle show/hide
    hasToggle.addEventListener('change', function(){
        var expanded = this.checked;
        this.setAttribute('aria-expanded', expanded ? 'true' : 'false');
        panel.classList.toggle('hidden', !expanded);

        if (expanded) {
            ensureAtLeastOneRow();
        }
        setRequired(expanded);
        updateCounter();
    });

    // Add new row
    addBtn.addEventListener('click', function(){
        list.insertAdjacentHTML('beforeend', prevNameTemplate(idx++));
        setRequired(hasToggle.checked);
        updateCounter();
    });

    // Remove row (event delegation)
    list.addEventListener('click', function(e){
        var btn = e.target.closest('.remove_prev_name');
        if (!btn) return;
        e.preventDefault();
        var block = btn.closest('.prev-name-item');
        if (block) block.parentNode.removeChild(block);
        // If user removes all, keep panel on but no required fields
        setRequired(hasToggle.checked);
        updateCounter();
    });

    // Initial state on page load
    if (hasToggle.checked) {
        panel.classList.remove('hidden');
        ensureAtLeastOneRow();
        setRequired(true);
    } else {
        panel.classList.add('hidden');
        setRequired(false);
    }
    updateCounter();

    // Revalidate rows whenever DOB changes (DOB affects min date checks)
    var dobEl = document.getElementById('dob');
    if (dobEl) {
        dobEl.addEventListener('change', validateAllPrevRows);
        dobEl.addEventListener('blur', validateAllPrevRows);
    }

    // Delegate input/change events for dynamic rows
    list.addEventListener('input', function(e){
        var row = e.target.closest('.prev-name-item');
        if (!row) return;
        validatePrevRow(row, document.getElementById('has_prev_names').checked);
    });
    list.addEventListener('change', function(e){
        var row = e.target.closest('.prev-name-item');
        if (!row) return;
        validatePrevRow(row, document.getElementById('has_prev_names').checked);
    });

    // Run validation after adding a row
    addBtn.addEventListener('click', function(){
        // your existing insertion code already runs
        setTimeout(validateAllPrevRows, 0);
    });

    // Guard form submit
    var form = document.querySelector('form.form-horizontal');
    if (form) {
        form.addEventListener('submit', function(e){
            if (!validateAllPrevRows()) {
                e.preventDefault();
                e.stopPropagation();
            }
        });
    }
})();

// ========== Validation helpers ==========
function getDOB() {
    var dobEl = document.getElementById('dob');
    if (!dobEl || !dobEl.value) return null;       // type="date" => yyyy-mm-dd
    var d = new Date(dobEl.value);
    return isNaN(d) ? null : d;
}
function parseDate(val) {
    if (!val) return null;
    var d = new Date(val);                          // yyyy-mm-dd supported by all modern browsers
    return isNaN(d) ? null : d;
}
function todayEnd() {
    var d = new Date();
    d.setHours(23,59,59,999);
    return d;
}
function setErr(inputEl, msg) {
    var fg = inputEl.closest('.form-group');
    var help = fg.querySelector('.help-block');
    if (msg) {
        fg.classList.add('has-error');
        inputEl.setAttribute('aria-invalid','true');
        if (help){ help.textContent = msg; help.style.display='block'; }
    } else {
        fg.classList.remove('has-error');
        inputEl.removeAttribute('aria-invalid');
        if (help){ help.textContent = ''; help.style.display='none'; }
    }
}
function nameOK(str, allowEmpty){
    if (!str) return !!allowEmpty;
    // letters, spaces, hyphen, apostrophe, dot, curly apostrophe
    return /^[A-Za-z .'\-’]+$/.test(str);
}

// Validate a single previous-name row
function validatePrevRow(row, requiredOn) {
    var hasError = false;
    var dob = getDOB();
    var now = todayEnd();

    var forename = row.querySelector('.pn-forename');
    var middlename = row.querySelector('.pn-middlename');
    var surname  = row.querySelector('.pn-surname');
    var fromEl   = row.querySelector('.pn-from');
    var toEl     = row.querySelector('.pn-to');

    // Names
    if (requiredOn && (!forename.value || !nameOK(forename.value, false))) {
        setErr(forename, forename.value ? 'Only letters, spaces, hyphen, apostrophe, dot.' : 'First name is required.');
        hasError = true;
    } else { setErr(forename, null); }

    if (middlename && !nameOK(middlename.value, true)) {
        setErr(middlename, 'Only letters, spaces, hyphen, apostrophe, dot.');
        hasError = true;
    } else { setErr(middlename, null); }

    if (requiredOn && (!surname.value || !nameOK(surname.value, false))) {
        setErr(surname, surname.value ? 'Only letters, spaces, hyphen, apostrophe, dot.' : 'Surname is required.');
        hasError = true;
    } else { setErr(surname, null); }

    // Dates
    var fromDate = parseDate(fromEl.value);
    var toDate   = parseDate(toEl.value);

    // from required
    if (requiredOn && !fromDate) { setErr(fromEl, 'Please enter a valid start date.'); hasError = true; }
    else { setErr(fromEl, null); }

    // to required
    if (requiredOn && !toDate) { setErr(toEl, 'Please enter a valid end date.'); hasError = true; }
    else { setErr(toEl, null); }

    // If we have dates, run comparisons
    if (fromDate && dob && fromDate < dob) { setErr(fromEl, 'Start date cannot be before your date of birth.'); hasError = true; }
    if (toDate   && dob && toDate   < dob) { setErr(toEl,   'End date cannot be before your date of birth.');   hasError = true; }

    if (fromDate && fromDate > now) { setErr(fromEl, 'Start date cannot be in the future.'); hasError = true; }
    if (toDate   && toDate   > now) { setErr(toEl,   'End date cannot be in the future.');   hasError = true; }

    if (fromDate && toDate && toDate < fromDate) { setErr(toEl, 'End date cannot be before start date.'); hasError = true; }

    // Optional: prevent micro ranges that end on the same day? (Usually acceptable; we allow equal)
    // If you want strictly after: replace "<" with "<=" above.

    return !hasError;
}

// Validate all rows; returns true/false
function validateAllPrevRows() {
    var hasToggle = document.getElementById('has_prev_names');
    var list = document.getElementById('prev_names_list');
    if (!hasToggle || !list) return true;

    var requiredOn = !!hasToggle.checked;
    var rows = list.querySelectorAll('.prev-name-item');
    var allOK = true, firstBad = null;

    rows.forEach(function(row){
        var ok = validatePrevRow(row, requiredOn);
        if (!ok) {
            allOK = false;
            if (!firstBad) firstBad = row;
        }
    });

    if (!allOK && firstBad) {
        // Scroll into view on first error row
        firstBad.scrollIntoView({ behavior:'smooth', block:'center' });
    }
    return allOK;
}
</script>

<script>
/**
 * Autocomplete for Current Address
 * - Uses #address_line1 as the autocomplete target
 * - Fills: address_line1, address_line2, city, county, postcode, country
 * - Keeps country <select> (ISO3) updated using a map built from $countries
 */
(function(){
    // Build ISO2 <-> ISO3 maps from your DB countries (already fetched in controller)
    const COUNTRY_ROWS = @json(
        $countries->map(fn($c) => ['iso2'=>$c->iso, 'iso3'=>$c->iso3, 'name'=>$c->nicename])->values()
    );

    const ISO2_TO_ISO3 = {};
    const ISO3_TO_ISO2 = {};
    COUNTRY_ROWS.forEach(c => {
        if (c.iso2) ISO2_TO_ISO3[c.iso2.toUpperCase()] = c.iso3?.toUpperCase() || null;
        if (c.iso3) ISO3_TO_ISO2[c.iso3.toUpperCase()] = c.iso2?.toUpperCase() || null;
    });

    function setSelectCountryByIso3(selectEl, iso3){
        if (!selectEl || !iso3) return;
        // Only change if different to avoid user surprise
        if (selectEl.value !== iso3) {
            selectEl.value = iso3;
            // in case you have custom UI refresh, trigger change
            const ev = new Event('change', {bubbles:true});
            selectEl.dispatchEvent(ev);
        }
    }

    function component(components, type){
        const c = components.find(x => x.types.includes(type));
        return c ? c.long_name : '';
    }
    function componentShort(components, type){
        const c = components.find(x => x.types.includes(type));
        return c ? c.short_name : '';
    }

    function preferTown(components){
        // UK can return postal_town instead of locality
        return component(components, 'locality')
            || component(components, 'postal_town')
            || component(components, 'sublocality')
            || component(components, 'sublocality_level_1')
            || '';
    }

    function preferCounty(components){
        // Often: admin_area_level_2 (county) or sometimes level_1
        return component(components, 'administrative_area_level_2')
            || component(components, 'administrative_area_level_1')
            || '';
    }

    function buildLine1(components){
        const num   = component(components, 'street_number');
        const route = component(components, 'route');
        if (num && route) return `${num} ${route}`;
        return route || num || '';
    }

    function buildLine2(components){
        // subpremise/premise/neighborhood can be useful as an extra line
        const subprem = component(components, 'subpremise');
        const prem    = component(components, 'premise');
        const neigh   = component(components, 'neighborhood');
        const parts = [subprem, prem, neigh].filter(Boolean);
        return parts.join(', ');
    }

    // Expose init for Google callback
    window.__initPlacesAutocomplete = function(){
        if (!(window.google && google.maps && google.maps.places)) return;

        const line1   = document.getElementById('address_line1');
        const line2   = document.getElementById('address_line2');
        const city    = document.getElementById('city');
        const county  = document.getElementById('county');
        const post    = document.getElementById('postcode');
        const country = document.getElementById('country');

        if (!line1) return; // no-op if the field isn't on the page

        // Determine initial country restriction from current select value (ISO3 -> ISO2), fallback GB
        let restrictIso2 = 'GB';
        if (country && country.value && ISO3_TO_ISO2[country.value.toUpperCase()]) {
            restrictIso2 = ISO3_TO_ISO2[country.value.toUpperCase()];
        }

        const ac = new google.maps.places.Autocomplete(line1, {
            types: ['address'],
            fields: ['address_components', 'geometry'],
            componentRestrictions: { country: [restrictIso2.toLowerCase()] }
        });

        // If the user changes country select, update restriction dynamically
        if (country) {
            country.addEventListener('change', function(){
                try {
                    const iso3 = country.value?.toUpperCase();
                    const iso2 = ISO3_TO_ISO2[iso3] || null;
                    if (iso2 && ac && ac.setComponentRestrictions) {
                        ac.setComponentRestrictions({ country: [iso2.toLowerCase()] });
                    } else {
                        // remove restriction if unknown
                        ac.setComponentRestrictions({ country: [] });
                    }
                } catch(e){}
            });
        }

        ac.addListener('place_changed', function(){
            const place = ac.getPlace();
            if (!place || !place.address_components) return;

            const comps = place.address_components;

            // Fill fields
            const newLine1 = buildLine1(comps);
            if (newLine1) line1.value = newLine1;

            const newLine2 = buildLine2(comps);
            if (line2) line2.value = newLine2;

            const newCity = preferTown(comps);
            if (city) city.value = newCity;

            const newCounty = preferCounty(comps);
            if (county) county.value = newCounty;

            const newPost = componentShort(comps, 'postal_code') || component(comps, 'postal_code');
            if (post) {
                post.value = (newPost || '').toUpperCase();
                // trigger your existing max-length / formatting if any
                const ev = new Event('input', {bubbles:true});
                post.dispatchEvent(ev);
            }

            // Country: map Google ISO2 -> your ISO3 select value
            const iso2 = componentShort(comps, 'country'); // e.g. 'GB'
            const iso3 = iso2 ? (ISO2_TO_ISO3[iso2.toUpperCase()] || null) : null;
            if (country && iso3) setSelectCountryByIso3(country, iso3);
        });

        // Progressive enhancement: if user edits line1 manually, don't wipe other fields
        // but give them an easy way to clear autofill if needed:
        line1.addEventListener('input', function(){
            // noop (kept for future – e.g., you could add a "Clear" link next to the field)
        });
    };
})();
</script>

{{-- Load Google Maps JS (Places) asynchronously --}}
<script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google.maps_places_key') }}&libraries=places&callback=__initPlacesAutocomplete" async defer></script>

@endsection
