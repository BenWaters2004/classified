@extends('layout.admin')

@section('title', 'Edit Pending Applicant')

@section('content')

<div class="container-fluid">
    <section class="content">
        <div class="row justify-content-center">
            <div class="col-md-8">

                <div class="custom-box">

                    <div class="box-header">
                        <h3 class="box-title">Edit Pending Applicant</h3>

                        <a href="{{ env('APP_URL') }}applications/pendingRequests"
                           class="back-button">
                            <i class="fa fa-arrow-left"></i> Back
                        </a>
                    </div>

                    <div class="box-body">

                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <strong>There were some problems with the information entered:</strong>

                                <ul style="margin-top:10px; margin-bottom:0;">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form method="POST"
                              action="{{ route('applications.pending.update', $applicant->id) }}">

                            {{ csrf_field() }}

                            {{-- Name --}}
                            <div class="form-row">

                                <div class="form-group @if($errors->has('forename')) has-error @endif">
                                    <label for="forename">
                                        Forename <span class="required">*</span>
                                    </label>

                                    <input
                                        type="text"
                                        class="form-control"
                                        name="forename"
                                        id="forename"
                                        maxlength="100"
                                        value="{{ old('forename', $applicant->forename) }}"
                                        required
                                    >

                                    @if($errors->has('forename'))
                                        <p class="help-block">
                                            {{ $errors->first('forename') }}
                                        </p>
                                    @endif
                                </div>


                                <div class="form-group @if($errors->has('surname')) has-error @endif">
                                    <label for="surname">
                                        Surname <span class="required">*</span>
                                    </label>

                                    <input
                                        type="text"
                                        class="form-control"
                                        name="surname"
                                        id="surname"
                                        maxlength="100"
                                        value="{{ old('surname', $applicant->surname) }}"
                                        required
                                    >

                                    @if($errors->has('surname'))
                                        <p class="help-block">
                                            {{ $errors->first('surname') }}
                                        </p>
                                    @endif
                                </div>

                            </div>


                            {{-- Contact Details --}}
                            <div class="section-heading">
                                <h4>Contact Details</h4>
                            </div>

                            <div class="form-row">

                                <div class="form-group @if($errors->has('email')) has-error @endif">
                                    <label for="email">
                                        Email Address <span class="required">*</span>
                                    </label>

                                    <input
                                        type="email"
                                        class="form-control"
                                        name="email"
                                        id="email"
                                        maxlength="255"
                                        value="{{ old('email', $applicant->email) }}"
                                        required
                                    >

                                    @if($errors->has('email'))
                                        <p class="help-block">
                                            {{ $errors->first('email') }}
                                        </p>
                                    @endif
                                </div>


                                <div class="form-group @if($errors->has('mobileNumber')) has-error @endif">
                                    <label for="mobileNumber">
                                        Mobile Number
                                    </label>

                                    <input
                                        type="text"
                                        class="form-control"
                                        name="mobileNumber"
                                        id="mobileNumber"
                                        maxlength="30"
                                        value="{{ old('mobileNumber', $applicant->mobileNumberMain) }}"
                                    >

                                    @if($errors->has('mobileNumber'))
                                        <p class="help-block">
                                            {{ $errors->first('mobileNumber') }}
                                        </p>
                                    @endif
                                </div>

                            </div>


                            {{-- Application Details --}}
                            <div class="section-heading">
                                <h4>Application Details</h4>
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
                                    value="{{ old('applicationCode', $applicant->applicationCode) }}"
                                >

                                @if($errors->has('applicationCode'))
                                    <p class="help-block">
                                        {{ $errors->first('applicationCode') }}
                                    </p>
                                @endif

                            </div>


                            {{-- Requested Checks --}}
                            <div class="section-heading">
                                <h4>Checks Requested</h4>
                            </div>

                            <div class="checks-box">

                                <div class="multi-col-checklist">

                                    <div class="check-option">
                                        <input
                                            type="checkbox"
                                            name="dbsApplication"
                                            id="dbsApplication"
                                            value="1"
                                            {{ old(
                                                'dbsApplication',
                                                !empty($applicant->dbsApplication)
                                                    ? 1
                                                    : null
                                            ) ? 'checked' : '' }}
                                        >

                                        <label class="NotHeading" for="dbsApplication">
                                            DBS Application
                                        </label>
                                    </div>


                                    <div class="check-option">
                                        <input
                                            type="checkbox"
                                            name="bpssApplication"
                                            id="bpssApplication"
                                            value="1"
                                            {{ old(
                                                'bpssApplication',
                                                !empty($applicant->bpssApplication)
                                                    ? 1
                                                    : null
                                            ) ? 'checked' : '' }}
                                        >

                                        <label class="NotHeading" for="bpssApplication">
                                            BPSS Application
                                        </label>
                                    </div>


                                    <div class="check-option">
                                        <input
                                            type="checkbox"
                                            name="REVALonsite"
                                            id="REVALonsite"
                                            value="1"
                                            {{ old(
                                                'REVALonsite',
                                                !empty($applicant->REVALonsite)
                                                    ? 1
                                                    : null
                                            ) ? 'checked' : '' }}
                                        >

                                        <label class="NotHeading" for="REVALonsite">
                                            REVAL Onsite
                                        </label>
                                    </div>


                                    <div class="check-option">
                                        <input
                                            type="checkbox"
                                            name="REVALoffsite"
                                            id="REVALoffsite"
                                            value="1"
                                            {{ old(
                                                'REVALoffsite',
                                                !empty($applicant->REVALoffsite)
                                                    ? 1
                                                    : null
                                            ) ? 'checked' : '' }}
                                        >

                                        <label class="NotHeading" for="REVALoffsite">
                                            REVAL Offsite
                                        </label>
                                    </div>


                                    <div class="check-option">
                                        <input
                                            type="checkbox"
                                            name="useYoti"
                                            id="useYoti"
                                            value="1"
                                            {{ old(
                                                'useYoti',
                                                !empty($applicant->useYoti)
                                                    ? 1
                                                    : null
                                            ) ? 'checked' : '' }}
                                        >

                                        <label class="NotHeading" for="useYoti">
                                            Digital Identity Verification (Yoti)
                                        </label>
                                    </div>

                                </div>

                            </div>


                            <div class="form-actions">

                                <button
                                    type="submit"
                                    class="btn save-button"
                                >
                                    <i class="fa fa-save"></i>
                                    Save Changes
                                </button>

                                <a
                                    href="{{ env('APP_URL') }}applications/pendingRequests"
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
        border-radius: 8px;
        padding: 30px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.05);
    }


    .custom-box .box-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 2px solid #C55359;
        padding-bottom: 10px;
        margin-bottom: 30px;
    }


    .custom-box h3 {
        color: #2C3C64;
        padding: 0;
        margin: 0;
        border: none;
    }


    .custom-box .back-button {
        font-size: 14px;
        background-color: #2C3C64;
        color: #fff;
        padding: 7px 12px;
        border-radius: 4px;
        text-decoration: none;
        margin-left: auto;
    }


    .custom-box .back-button:hover {
        background-color: #1f2c4c;
        color: white;
    }


    .custom-box label:not(.NotHeading) {
        color: #2C3C64;
        font-weight: 600;
    }


    .NotHeading {
        font-weight: 400;
        cursor: pointer;
        margin-bottom: 0;
    }


    .form-control {
        border: 1px solid #ced4da;
        border-radius: 4px;
        padding: 10px;
        height: auto;
    }


    .form-control:focus {
        border-color: #C55359;
        box-shadow: none;
    }


    .form-row {
        display: flex;
        gap: 15px;
        flex-wrap: wrap;
    }


    .form-row .form-group {
        flex: 1;
        min-width: 250px;
    }


    .section-heading {
        border-bottom: 1px solid #e7e7e7;
        margin-top: 25px;
        margin-bottom: 20px;
    }


    .section-heading h4 {
        color: #2C3C64;
        font-weight: 600;
        margin-bottom: 8px;
    }


    .checks-box {
        border: 1px solid #dee2e6;
        background: #f9f9f9;
        border-radius: 6px;
        padding: 20px;
    }


    .multi-col-checklist {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 12px 25px;
    }


    .check-option {
        display: flex;
        align-items: center;
    }


    .check-option input[type="checkbox"] {
        margin: 0 8px 0 0;
    }


    .form-actions {
        border-top: 1px solid #e7e7e7;
        margin-top: 30px;
        padding-top: 20px;
    }


    .save-button {
        background-color: #C55359;
        color: white;
        border: none;
        font-weight: bold;
    }


    .save-button:hover,
    .save-button:focus {
        background-color: #a94247;
        color: white;
    }


    .cancel-button {
        background-color: #6c757d;
        color: white;
        margin-left: 5px;
    }


    .cancel-button:hover,
    .cancel-button:focus {
        background-color: #545b62;
        color: white;
    }


    .required {
        color: #C55359;
    }


    .help-block {
        font-size: 13px;
    }


    @media (max-width: 768px) {

        .container-fluid {
            padding: 0;
        }

        .content {
            padding: 0;
        }

        .custom-box {
            padding: 20px;
            border-radius: 0;
        }

        .multi-col-checklist {
            grid-template-columns: 1fr;
        }

        .custom-box .box-header {
            align-items: flex-start;
        }

    }

</style>

@endsection