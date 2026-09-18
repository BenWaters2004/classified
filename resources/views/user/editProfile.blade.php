@extends('layout.admin')

@section('title', 'Edit User Details')

@section('content')

<div class="container-fluid">
    <section class="content">
        <div class="row">
            <div class="col-md-10 col-md-offset-1">

                <div class="custom-box">

                    {{-- Header --}}
                    <div class="box-header" style="display:inline-flex;">
                        <div>
                            <h3 class="box-title">
                                Edit
                                @if ($userDetails->userType == 'admin')
                                    User
                                @else
                                    Applicant
                                @endif
                                Details
                            </h3>

                            @if ($userDetails->userType != 'admin')
                                <p class="header-subtitle">
                                    Update applicant information, screening checks and account details.
                                </p>
                            @else
                                <p class="header-subtitle">
                                    Update user account details, permissions and organisations.
                                </p>
                            @endif
                        </div>

                        <a href="{{ url()->previous() }}" class="back-button">
                            <i class="fa fa-arrow-left"></i> Back
                        </a>
                    </div>

                    <div class="box-body">

                        {{-- Success message --}}
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible">
                                <button type="button"
                                        class="close"
                                        data-dismiss="alert"
                                        aria-hidden="true">
                                    ×
                                </button>

                                <i class="fa fa-check"></i>
                                {{ session('success') }}
                            </div>
                        @endif


                        {{-- Validation errors --}}
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <strong>
                                    <i class="fa fa-exclamation-circle"></i>
                                    There were some problems with the information entered:
                                </strong>

                                <ul style="margin-top:10px; margin-bottom:0;">
                                    @foreach ($errors->all() as $error)
                                        <li>
                                            @if($error == 'email_exists')
                                                The email address is already registered in the database.
                                            @else
                                                {{ $error }}
                                            @endif
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif


                        <form method="post"
                              action="{{ env('APP_URL') }}users/updateUserDetails"
                              enctype="multipart/form-data"
                              id="editUserForm">

                            {{ csrf_field() }}

                            {{-- ====================================================== --}}
                            {{-- PERSONAL DETAILS --}}
                            {{-- ====================================================== --}}

                            <div class="section-heading">
                                <div class="section-icon">
                                    <i class="fa fa-user"></i>
                                </div>

                                <div>
                                    <h4>Personal Details</h4>
                                    <p>Basic information associated with this account.</p>
                                </div>
                            </div>


                            <div class="form-row">

                                <div class="form-group @if($errors->has('userTitle')) has-error @endif">
                                    <label for="userTitle">Title</label>

                                    <input
                                        type="text"
                                        class="form-control"
                                        name="userTitle"
                                        id="userTitle"
                                        maxlength="50"
                                        value="{{ old(
                                            'userTitle',
                                            $userDetails->DBSApplicationID > 0
                                                ? $userDetails->applicationTitle
                                                : $userDetails->title
                                        ) }}"
                                    >

                                    @if($errors->has('userTitle'))
                                        <p class="help-block">
                                            {{ $errors->first('userTitle') }}
                                        </p>
                                    @endif
                                </div>


                                <div class="form-group @if($errors->has('firstName')) has-error @endif">
                                    <label for="firstName">
                                        First Name <span class="required">*</span>
                                    </label>

                                    <input
                                        type="text"
                                        class="form-control"
                                        name="firstName"
                                        id="firstName"
                                        maxlength="50"
                                        value="{{ old(
                                            'firstName',
                                            $userDetails->DBSApplicationID > 0
                                                ? $userDetails->applicationForename
                                                : $userDetails->firstName
                                        ) }}"
                                        required
                                    >

                                    @if($errors->has('firstName'))
                                        <p class="help-block">
                                            {{ $errors->first('firstName') }}
                                        </p>
                                    @endif
                                </div>


                                <div class="form-group @if($errors->has('lastName')) has-error @endif">
                                    <label for="lastName">
                                        Last Name <span class="required">*</span>
                                    </label>

                                    <input
                                        type="text"
                                        class="form-control"
                                        name="lastName"
                                        id="lastName"
                                        maxlength="50"
                                        value="{{ old(
                                            'lastName',
                                            $userDetails->DBSApplicationID > 0
                                                ? $userDetails->applicationPresentSurname
                                                : $userDetails->lastName
                                        ) }}"
                                        required
                                    >

                                    @if($errors->has('lastName'))
                                        <p class="help-block">
                                            {{ $errors->first('lastName') }}
                                        </p>
                                    @endif
                                </div>

                            </div>


                            {{-- ====================================================== --}}
                            {{-- ORGANISATION --}}
                            {{-- ====================================================== --}}

                            <div class="section-heading">
                                <div class="section-icon">
                                    <i class="fa fa-building"></i>
                                </div>

                                <div>
                                    <h4>Organisation</h4>
                                    <p>The organisation this account belongs to.</p>
                                </div>
                            </div>


                            <div class="form-group">
                                <label>Base Organisation</label>

                                <div class="read-only-field">
                                    <i class="fa fa-building-o"></i>

                                    <span>
                                        @if(isset($userDetails->organisationName))
                                            {{ $userDetails->organisationName }}
                                        @else
                                            Organisation ID: {{ $userDetails->organisationID }}
                                        @endif
                                    </span>
                                </div>
                            </div>


                            {{-- ====================================================== --}}
                            {{-- CONTACT / APPLICATION --}}
                            {{-- ====================================================== --}}

                            <div class="section-heading">
                                <div class="section-icon">
                                    <i class="fa fa-envelope"></i>
                                </div>

                                <div>
                                    <h4>
                                        @if($userDetails->userType == 'admin')
                                            Contact Details
                                        @else
                                            Contact & Application Details
                                        @endif
                                    </h4>

                                    <p>
                                        @if($userDetails->userType == 'admin')
                                            Contact information for this user.
                                        @else
                                            Applicant contact information and application reference.
                                        @endif
                                    </p>
                                </div>
                            </div>


                            @if($userDetails->userType != 'admin')

                                <div class="form-row">

                                    <div class="form-group @if($errors->has('userEmail')) has-error @endif">
                                        <label for="userEmail">
                                            Email Address <span class="required">*</span>
                                        </label>

                                        <input
                                            type="email"
                                            class="form-control"
                                            name="userEmail"
                                            id="userEmail"
                                            maxlength="255"
                                            value="{{ old('userEmail', $userDetails->email) }}"
                                            required
                                        >

                                        @if($errors->has('userEmail'))
                                            <p class="help-block">
                                                @if($errors->first('userEmail') == 'email_exists')
                                                    The email address is already registered in the database.
                                                @else
                                                    {{ $errors->first('userEmail') }}
                                                @endif
                                            </p>
                                        @endif
                                    </div>


                                    <div class="form-group @if($errors->has('applicationCode')) has-error @endif">
                                        <label for="applicationCode">
                                            Approved Access Number (AAN) / Application Code
                                        </label>

                                        <input
                                            type="text"
                                            class="form-control"
                                            name="applicationCode"
                                            id="applicationCode"
                                            maxlength="100"
                                            value="{{ old(
                                                'applicationCode',
                                                $userDetails->applicationCode ?? ''
                                            ) }}"
                                        >

                                        @if($errors->has('applicationCode'))
                                            <p class="help-block">
                                                {{ $errors->first('applicationCode') }}
                                            </p>
                                        @endif
                                    </div>

                                </div>

                            @else

                                <div class="form-group @if($errors->has('userEmail')) has-error @endif">
                                    <label for="userEmail">
                                        Email Address <span class="required">*</span>
                                    </label>

                                    <input
                                        type="email"
                                        class="form-control"
                                        name="userEmail"
                                        id="userEmail"
                                        maxlength="255"
                                        value="{{ old('userEmail', $userDetails->email) }}"
                                        required
                                    >

                                    @if($errors->has('userEmail'))
                                        <p class="help-block">
                                            @if($errors->first('userEmail') == 'email_exists')
                                                The email address is already registered in the database.
                                            @else
                                                {{ $errors->first('userEmail') }}
                                            @endif
                                        </p>
                                    @endif
                                </div>

                            @endif


                            {{-- ====================================================== --}}
                            {{-- PASSWORD --}}
                            {{-- ====================================================== --}}

                            <div class="section-heading">
                                <div class="section-icon">
                                    <i class="fa fa-lock"></i>
                                </div>

                                <div>
                                    <h4>Password</h4>
                                    <p>Leave both fields blank to keep the existing password.</p>
                                </div>
                            </div>


                            <div class="form-row">

                                <div class="form-group @if($errors->has('password')) has-error @endif">
                                    <label for="password">New Password</label>

                                    <input
                                        type="password"
                                        class="form-control"
                                        name="password"
                                        id="password"
                                        autocomplete="new-password"
                                    >

                                    @if($errors->has('password'))
                                        <p class="help-block">
                                            {{ $errors->first('password') }}
                                        </p>
                                    @endif
                                </div>


                                <div class="form-group">
                                    <label for="password_confirmation">
                                        Confirm New Password
                                    </label>

                                    <input
                                        type="password"
                                        class="form-control"
                                        name="password_confirmation"
                                        id="password_confirmation"
                                        autocomplete="new-password"
                                    >
                                </div>

                            </div>


                            <div id="passwordRequirements"
                                 class="password-requirements"
                                 style="display:none;">

                                <strong>Password requirements</strong>

                                <div class="password-grid">

                                    <div id="req-length" class="password-requirement">
                                        <i class="fa fa-times text-danger"></i>
                                        At least 14 characters
                                    </div>

                                    <div id="req-uppercase" class="password-requirement">
                                        <i class="fa fa-times text-danger"></i>
                                        One uppercase letter
                                    </div>

                                    <div id="req-lowercase" class="password-requirement">
                                        <i class="fa fa-times text-danger"></i>
                                        One lowercase letter
                                    </div>

                                    <div id="req-number" class="password-requirement">
                                        <i class="fa fa-times text-danger"></i>
                                        One number
                                    </div>

                                    <div id="req-special" class="password-requirement">
                                        <i class="fa fa-times text-danger"></i>
                                        One special character
                                    </div>

                                    <div id="req-match" class="password-requirement">
                                        <i class="fa fa-times text-danger"></i>
                                        Passwords match
                                    </div>

                                </div>

                            </div>


                            {{-- ====================================================== --}}
                            {{-- SCREENING CHECKS --}}
                            {{-- ====================================================== --}}

                            @inject('checkAccess', 'App\Http\Controllers\Controller')

                            @if ($checkAccess->checkAccess('superuser') && $userDetails->userType != 'admin')

                                <div class="section-heading">
                                    <div class="section-icon">
                                        <i class="fa fa-check-square-o"></i>
                                    </div>

                                    <div>
                                        <h4>Checks Requested</h4>
                                        <p>
                                            Select the screening checks required for this applicant.
                                        </p>
                                    </div>
                                </div>


                                <div class="checks-box">

                                    <div class="multi-col-checklist">

                                        {{-- DBS --}}
                                        <label class="check-card" for="dbsApplication">

                                            <input type="hidden"
                                                   name="dbsApplication"
                                                   value="0">

                                            <input
                                                type="checkbox"
                                                name="dbsApplication"
                                                id="dbsApplication"
                                                value="1"
                                                {{ (int)old(
                                                    'dbsApplication',
                                                    !empty($userDetails->dbsApplication) ? 1 : 0
                                                ) === 1 ? 'checked' : '' }}
                                            >

                                            <span class="check-content">
                                                <span class="check-title">
                                                    DBS Application
                                                </span>

                                                <span class="check-description">
                                                    Criminal record screening.
                                                </span>
                                            </span>

                                        </label>


                                        {{-- BPSS --}}
                                        <label class="check-card" for="bpssApplication">

                                            <input type="hidden"
                                                   name="bpssApplication"
                                                   value="0">

                                            <input
                                                type="checkbox"
                                                name="bpssApplication"
                                                id="bpssApplication"
                                                value="1"
                                                {{ (int)old(
                                                    'bpssApplication',
                                                    !empty($userDetails->bpssApplication) ? 1 : 0
                                                ) === 1 ? 'checked' : '' }}
                                            >

                                            <span class="check-content">
                                                <span class="check-title">
                                                    BPSS Application
                                                </span>

                                                <span class="check-description">
                                                    Baseline Personnel Security Standard.
                                                </span>
                                            </span>

                                        </label>


                                        {{-- REVAL ONSITE --}}
                                        <label class="check-card" for="REVALonsite">

                                            <input type="hidden"
                                                   name="REVALonsite"
                                                   value="0">

                                            <input
                                                type="checkbox"
                                                name="REVALonsite"
                                                id="REVALonsite"
                                                value="1"
                                                {{ (int)old(
                                                    'REVALonsite',
                                                    !empty($userDetails->REVALonsite) ? 1 : 0
                                                ) === 1 ? 'checked' : '' }}
                                            >

                                            <span class="check-content">
                                                <span class="check-title">
                                                    REVAL Onsite
                                                </span>

                                                <span class="check-description">
                                                    Onsite BPSS revalidation.
                                                </span>
                                            </span>

                                        </label>


                                        {{-- REVAL OFFSITE --}}
                                        <label class="check-card" for="REVALoffsite">

                                            <input type="hidden"
                                                   name="REVALoffsite"
                                                   value="0">

                                            <input
                                                type="checkbox"
                                                name="REVALoffsite"
                                                id="REVALoffsite"
                                                value="1"
                                                {{ (int)old(
                                                    'REVALoffsite',
                                                    !empty($userDetails->REVALoffsite) ? 1 : 0
                                                ) === 1 ? 'checked' : '' }}
                                            >

                                            <span class="check-content">
                                                <span class="check-title">
                                                    REVAL Offsite
                                                </span>

                                                <span class="check-description">
                                                    Offsite BPSS revalidation.
                                                </span>
                                            </span>

                                        </label>


                                        {{-- YOTI --}}
                                        <label class="check-card" for="useYoti">

                                            <input type="hidden"
                                                   name="useYoti"
                                                   value="0">

                                            <input
                                                type="checkbox"
                                                name="useYoti"
                                                id="useYoti"
                                                value="1"
                                                {{ (int)old(
                                                    'useYoti',
                                                    !empty($userDetails->useYoti) ? 1 : 0
                                                ) === 1 ? 'checked' : '' }}
                                            >

                                            <span class="check-content">
                                                <span class="check-title">
                                                    Digital Identity Verification
                                                </span>

                                                <span class="check-description">
                                                    Identity verification through Yoti.
                                                </span>
                                            </span>

                                        </label>

                                    </div>

                                </div>

                            @endif


                            {{-- ====================================================== --}}
                            {{-- ADMIN DETAILS --}}
                            {{-- ====================================================== --}}

                            @if ($userDetails->userType == 'admin')

                                <div class="section-heading">
                                    <div class="section-icon">
                                        <i class="fa fa-briefcase"></i>
                                    </div>

                                    <div>
                                        <h4>Work Details</h4>
                                        <p>Additional information for this administrative user.</p>
                                    </div>
                                </div>


                                <div class="form-row">

                                    <div class="form-group @if($errors->has('position')) has-error @endif">
                                        <label for="position">Post</label>

                                        <input
                                            type="text"
                                            class="form-control"
                                            name="position"
                                            id="position"
                                            maxlength="50"
                                            value="{{ old('position', $userDetails->position) }}"
                                        >

                                        @if($errors->has('position'))
                                            <p class="help-block">
                                                {{ $errors->first('position') }}
                                            </p>
                                        @endif
                                    </div>


                                    <div class="form-group @if($errors->has('phoneNumber')) has-error @endif">
                                        <label for="phoneNumber">Phone Number</label>

                                        <input
                                            type="text"
                                            class="form-control"
                                            name="phoneNumber"
                                            id="phoneNumber"
                                            maxlength="30"
                                            value="{{ old('phoneNumber', $userDetails->phoneNumber) }}"
                                        >

                                        @if($errors->has('phoneNumber'))
                                            <p class="help-block">
                                                {{ $errors->first('phoneNumber') }}
                                            </p>
                                        @endif
                                    </div>

                                </div>


                                <div class="form-group @if($errors->has('signature')) has-error @endif">

                                    <label for="signature">Signature</label>

                                    <div class="file-upload-box">

                                        @if(strlen($signaturePath) > 0)
                                            <div class="current-signature">

                                                <span>Current signature:</span>

                                                <img
                                                    src="/uploads/user_signatures/{{ str_replace(' ', '', strtolower($signaturePath)) }}?time={{ $currentTime }}"
                                                    alt="Current signature"
                                                >

                                            </div>
                                        @endif

                                        <input
                                            type="file"
                                            name="signature"
                                            id="signature"
                                            accept="image/png"
                                        >

                                        <p class="file-help">
                                            PNG files only. Leave blank to keep the existing signature.
                                        </p>

                                    </div>

                                    @if($errors->has('signature'))
                                        <p class="help-block">
                                            {{ $errors->first('signature') }}
                                        </p>
                                    @endif

                                </div>

                            @endif


                            {{-- ====================================================== --}}
                            {{-- SUPERUSER ADMIN ACCESS --}}
                            {{-- ====================================================== --}}

                            @if ($checkAccess->checkAccess('superuser') && $userDetails->userType == 'admin')

                                <div class="section-heading">
                                    <div class="section-icon">
                                        <i class="fa fa-shield"></i>
                                    </div>

                                    <div>
                                        <h4>Access & Permissions</h4>
                                        <p>
                                            Manage additional organisations and administrative roles.
                                        </p>
                                    </div>
                                </div>


                                <div class="access-grid">

                                    {{-- Additional Organisations --}}
                                    <div class="access-card">

                                        <h5>
                                            <i class="fa fa-building"></i>
                                            Additional Organisations
                                        </h5>

                                        <div class="organisation-list">

                                            @foreach ($availableOrganisations as $availableOrganisation)

                                                @if ($availableOrganisation->id != $userDetails->organisationID)

                                                    <label class="simple-checkbox">

                                                        <input
                                                            name="userExtraOrganisation_{{ $availableOrganisation->id }}"
                                                            id="userExtraOrganisation_{{ $availableOrganisation->id }}"
                                                            value="{{ $availableOrganisation->id }}"
                                                            type="checkbox"
                                                            organisation-id="{{ $availableOrganisation->id }}"
                                                            @if(in_array($availableOrganisation->id, $userExtraOrganisations))
                                                                checked
                                                            @endif
                                                        >

                                                        <span>
                                                            {{ $availableOrganisation->organisationName }}
                                                        </span>

                                                    </label>

                                                    <small
                                                        id="label_extraOrganisation_{{ $availableOrganisation->id }}"
                                                        class="label bg-green"
                                                        style="display:none;">
                                                    </small>

                                                @endif

                                            @endforeach

                                        </div>

                                    </div>


                                    {{-- Roles --}}
                                    <div class="access-card">

                                        <h5>
                                            <i class="fa fa-key"></i>
                                            User Roles
                                        </h5>


                                        <label class="simple-checkbox">

                                            <input
                                                name="userRole_siteuser"
                                                id="userRole_siteuser"
                                                value="siteuser"
                                                type="checkbox"
                                                role-id="siteuser"
                                                @if(in_array('siteuser', $userDetails->userRoles))
                                                    checked
                                                @endif
                                            >

                                            <span>Site User</span>

                                        </label>

                                        <small
                                            id="label_userRole_{{ $userDetails->id }}_siteuser"
                                            class="label bg-green"
                                            style="display:none;">
                                        </small>


                                        <label class="simple-checkbox">

                                            <input
                                                name="userRole_superuser"
                                                id="userRole_superuser"
                                                value="superuser"
                                                type="checkbox"
                                                role-id="superuser"
                                                @if(in_array('superuser', $userDetails->userRoles))
                                                    checked
                                                @endif
                                            >

                                            <span>Super User</span>

                                        </label>

                                        <small
                                            id="label_userRole_{{ $userDetails->id }}_superuser"
                                            class="label bg-green"
                                            style="display:none;">
                                        </small>

                                    </div>

                                </div>

                            @endif


                            {{-- ====================================================== --}}
                            {{-- ACTIONS --}}
                            {{-- ====================================================== --}}

                            <div class="form-actions">

                                <input
                                    type="hidden"
                                    name="userID"
                                    id="userID"
                                    value="{{ $userDetails->id }}"
                                >

                                <button
                                    type="submit"
                                    class="btn save-button"
                                    id="saveProfile"
                                >
                                    <i class="fa fa-save"></i>
                                    Save Changes
                                </button>

                                <a
                                    href="{{ url()->previous() }}"
                                    class="btn cancel-button"
                                >
                                    Cancel
                                </a>

                            </div>

                        </form>

                    </div>
                </div>

            </div>
        </div>
    </section>
</div>

@endsection


@section('pageCSS')

<style>

    .custom-box {
        background-color: #fff;
        border: 1px solid #dee2e6;
        border-radius: 10px;
        padding: 30px;
        box-shadow: 0 3px 15px rgba(0, 0, 0, 0.06);
        margin-bottom: 30px;
    }


    /* =========================================================
       HEADER
       ========================================================= */

    .custom-box .box-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 20px;
        border-bottom: 2px solid #C55359;
        padding-bottom: 18px;
        margin-bottom: 30px;
    }


    .custom-box .box-title {
        color: #2C3C64;
        margin: 0 0 5px 0;
        font-size: 24px;
        font-weight: 600;
    }


    .header-subtitle {
        color: #777;
        margin: 0;
        font-size: 14px;
    }


    .back-button {
        background-color: #2C3C64;
        color: white;
        padding: 8px 13px;
        border-radius: 5px;
        text-decoration: none;
        font-size: 14px;
        white-space: nowrap;
    }


    .back-button:hover,
    .back-button:focus {
        background-color: #1f2c4c;
        color: white;
        text-decoration: none;
    }


    /* =========================================================
       SECTION HEADINGS
       ========================================================= */

    .section-heading {
        display: flex;
        align-items: center;
        gap: 12px;
        border-bottom: 1px solid #e6e6e6;
        padding-bottom: 12px;
        margin-top: 32px;
        margin-bottom: 20px;
    }


    .section-heading:first-of-type {
        margin-top: 0;
    }


    .section-icon {
        width: 38px;
        height: 38px;
        min-width: 38px;
        border-radius: 50%;
        background-color: rgba(197, 83, 89, 0.10);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #C55359;
        font-size: 16px;
    }


    .section-heading h4 {
        color: #2C3C64;
        font-weight: 600;
        font-size: 17px;
        margin: 0 0 2px 0;
    }


    .section-heading p {
        margin: 0;
        color: #777;
        font-size: 13px;
    }


    /* =========================================================
       FORMS
       ========================================================= */

    .form-row {
        display: flex;
        gap: 18px;
        flex-wrap: wrap;
    }


    .form-row .form-group {
        flex: 1;
        min-width: 220px;
    }


    .custom-box label:not(.check-card):not(.simple-checkbox) {
        color: #2C3C64;
        font-weight: 600;
    }


    .form-control {
        border: 1px solid #ced4da;
        border-radius: 5px;
        padding: 10px 12px;
        height: 40px;
    }


    .form-control:focus {
        border-color: #C55359;
        box-shadow: 0 0 0 2px rgba(197, 83, 89, 0.08);
    }


    .required {
        color: #C55359;
    }


    .help-block {
        font-size: 13px;
    }


    .has-error .form-control {
        border-color: #C55359;
    }


    /* =========================================================
       READ ONLY
       ========================================================= */

    .read-only-field {
        border: 1px solid #e0e0e0;
        background: #f8f9fa;
        border-radius: 5px;
        padding: 11px 14px;
        color: #555;
        display: flex;
        align-items: center;
        gap: 9px;
    }


    .read-only-field i {
        color: #2C3C64;
    }


    /* =========================================================
       PASSWORD
       ========================================================= */

    .password-requirements {
        background: #f8f9fa;
        border: 1px solid #e3e3e3;
        border-left: 4px solid #C55359;
        border-radius: 5px;
        padding: 16px 18px;
    }


    .password-requirements strong {
        display: block;
        color: #2C3C64;
        margin-bottom: 12px;
    }


    .password-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 8px 20px;
    }


    .password-requirement {
        font-size: 13px;
        color: #555;
    }


    .password-requirement i {
        width: 16px;
        margin-right: 4px;
    }


    /* =========================================================
       SCREENING CHECKS
       ========================================================= */

    .checks-box {
        background: #f8f9fa;
        border: 1px solid #e2e2e2;
        border-radius: 7px;
        padding: 15px;
    }


    .multi-col-checklist {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 12px;
    }


    .check-card {
        display: flex;
        align-items: flex-start;
        gap: 11px;
        margin: 0;
        padding: 14px;
        cursor: pointer;
        background: white;
        border: 1px solid #dedede;
        border-radius: 6px;
        transition: border-color .15s ease, box-shadow .15s ease;
        font-weight: normal;
    }


    .check-card:hover {
        border-color: #C55359;
        box-shadow: 0 2px 8px rgba(0, 0, 0, .05);
    }


    .check-card input[type="checkbox"] {
        margin-top: 4px;
        flex-shrink: 0;
    }


    .check-content {
        display: flex;
        flex-direction: column;
    }


    .check-title {
        color: #2C3C64;
        font-weight: 600;
    }


    .check-description {
        color: #777;
        font-size: 12px;
        margin-top: 2px;
    }


    /* =========================================================
       FILE UPLOAD
       ========================================================= */

    .file-upload-box {
        border: 1px dashed #cfcfcf;
        border-radius: 6px;
        padding: 16px;
        background: #fafafa;
    }


    .current-signature {
        margin-bottom: 15px;
        padding-bottom: 15px;
        border-bottom: 1px solid #e6e6e6;
        display: flex;
        align-items: center;
        gap: 15px;
    }


    .current-signature span {
        color: #777;
        font-size: 13px;
    }


    .current-signature img {
        max-height: 45px;
        max-width: 200px;
    }


    .file-help {
        color: #777;
        font-size: 12px;
        margin: 8px 0 0;
    }


    /* =========================================================
       ACCESS
       ========================================================= */

    .access-grid {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 20px;
    }


    .access-card {
        border: 1px solid #e0e0e0;
        border-radius: 6px;
        background: #f9f9f9;
        padding: 18px;
    }


    .access-card h5 {
        color: #2C3C64;
        font-size: 15px;
        font-weight: 600;
        margin: 0 0 15px;
    }


    .access-card h5 i {
        color: #C55359;
        margin-right: 6px;
    }


    .organisation-list {
        columns: 2;
        -webkit-columns: 2;
        -moz-columns: 2;
    }


    .simple-checkbox {
        display: flex;
        align-items: center;
        gap: 7px;
        font-weight: normal;
        color: #444;
        cursor: pointer;
        margin-bottom: 9px;
    }


    .simple-checkbox input {
        margin: 0;
    }


    /* =========================================================
       ACTIONS
       ========================================================= */

    .form-actions {
        border-top: 1px solid #e4e4e4;
        padding-top: 22px;
        margin-top: 35px;
    }


    .save-button {
        background-color: #C55359;
        color: white;
        border: none;
        font-weight: 600;
        padding: 9px 16px;
    }


    .save-button:hover,
    .save-button:focus {
        background-color: #a94247;
        color: white;
    }


    .cancel-button {
        background-color: #6c757d;
        color: white;
        border: none;
        margin-left: 5px;
        padding: 9px 16px;
    }


    .cancel-button:hover,
    .cancel-button:focus {
        background-color: #545b62;
        color: white;
    }


    /* =========================================================
       MOBILE
       ========================================================= */

    @media(max-width: 768px) {

        .container-fluid {
            padding: 0;
        }

        .content {
            padding: 0;
        }

        .col-md-10 {
            padding: 0;
        }

        .custom-box {
            padding: 20px;
            border-radius: 0;
            margin: 0;
        }

        .custom-box .box-header {
            align-items: flex-start;
        }

        .multi-col-checklist,
        .password-grid,
        .access-grid {
            grid-template-columns: 1fr;
        }

        .organisation-list {
            columns: 1;
            -webkit-columns: 1;
            -moz-columns: 1;
        }

    }

</style>

@endsection


@section('pageJavascript')

<script type="text/javascript">

    /*
    |--------------------------------------------------------------------------
    | User Roles
    |--------------------------------------------------------------------------
    */

    $(document).on("click", "[id^='userRole_']", function() {

        var roleID = $(this).attr("role-id");
        var userID = $("#userID").val();

        if ($(this).is(':checked')) {
            updateUserRole(userID, roleID, 'addRole');
        } else {
            updateUserRole(userID, roleID, 'removeRole');
        }

    });


    function updateUserRole(userID, roleID, updateType) {

        var label = $("#label_userRole_" + userID + "_" + roleID);

        $.ajax({
            url: "{{ env('APP_URL') }}users/updateUserRole",
            method: "POST",

            data: {
                "_token": "{{ csrf_token() }}",
                "userID": userID,
                "roleID": roleID,
                "updateType": updateType
            },

            success: function() {

                label
                    .removeClass('bg-red')
                    .addClass('bg-green')
                    .html('Updated')
                    .show();

                setTimeout(function() {
                    label.fadeOut();
                }, 2000);
            },

            error: function() {

                label
                    .removeClass('bg-green')
                    .addClass('bg-red')
                    .html('Error')
                    .show();

                setTimeout(function() {
                    label.fadeOut();
                }, 2000);
            }
        });
    }


    /*
    |--------------------------------------------------------------------------
    | Additional Organisations
    |--------------------------------------------------------------------------
    */

    $(document).on("click", "[id^='userExtraOrganisation_']", function() {

        var organisationID = $(this).attr("organisation-id");
        var userID = $("#userID").val();

        if ($(this).is(':checked')) {
            updateUserOrganisation(userID, organisationID, 'addOrganisation');
        } else {
            updateUserOrganisation(userID, organisationID, 'removeOrganisation');
        }

    });


    function updateUserOrganisation(userID, organisationID, updateType) {

        var label = $("#label_extraOrganisation_" + organisationID);

        $.ajax({
            url: "{{ env('APP_URL') }}users/updateUserOrganisation",
            method: "POST",

            data: {
                "_token": "{{ csrf_token() }}",
                "userID": userID,
                "organisationID": organisationID,
                "updateType": updateType
            },

            success: function() {

                label
                    .removeClass('bg-red')
                    .addClass('bg-green')
                    .html('Updated')
                    .show();

                setTimeout(function() {
                    label.fadeOut();
                }, 2000);
            },

            error: function() {

                label
                    .removeClass('bg-green')
                    .addClass('bg-red')
                    .html('Error')
                    .show();

                setTimeout(function() {
                    label.fadeOut();
                }, 2000);
            }
        });
    }


    /*
    |--------------------------------------------------------------------------
    | Password Requirements
    |--------------------------------------------------------------------------
    */

    function validatePasswordDisplay() {

        var password = $("#password").val();
        var confirmation = $("#password_confirmation").val();

        if (password.length === 0 && confirmation.length === 0) {
            $("#passwordRequirements").hide();
            return;
        }

        $("#passwordRequirements").show();

        var requirements = {
            length: password.length >= 14,
            uppercase: /[A-Z]/.test(password),
            lowercase: /[a-z]/.test(password),
            number: /[0-9]/.test(password),
            special: /[@$!%*#?&]/.test(password),
            match: password.length > 0 && password === confirmation
        };


        $.each(requirements, function(key, met) {

            var icon = $("#req-" + key).find("i");

            icon.removeClass(
                "fa-check text-success fa-times text-danger"
            );

            if (met) {
                icon.addClass("fa-check text-success");
            } else {
                icon.addClass("fa-times text-danger");
            }

        });
    }


    $("#password, #password_confirmation").on(
        "input",
        validatePasswordDisplay
    );


    /*
    |--------------------------------------------------------------------------
    | Prevent weak password submission
    |--------------------------------------------------------------------------
    */

    $("#editUserForm").on("submit", function(e) {

        var password = $("#password").val();
        var confirmation = $("#password_confirmation").val();

        /*
         * Blank password = don't update it.
         */
        if (password.length === 0 && confirmation.length === 0) {
            return true;
        }


        var valid =
            password.length >= 14 &&
            /[A-Z]/.test(password) &&
            /[a-z]/.test(password) &&
            /[0-9]/.test(password) &&
            /[@$!%*#?&]/.test(password) &&
            password === confirmation;


        if (!valid) {

            e.preventDefault();

            validatePasswordDisplay();

            $("html, body").animate({
                scrollTop:
                    $("#passwordRequirements").offset().top - 100
            }, 300);

            return false;
        }

        return true;
    });

</script>

@endsection