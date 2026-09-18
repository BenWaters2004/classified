@if (\Auth::check()) 
    @inject('checkAccess', 'App\Http\Controllers\Controller')
@endif

@extends('layout.admin')
@section('title', 'Review Applicant Details')

@php
function getStatusClass($status) {
    return [
        'complete' => 'success',
        'Certificate contains no information' => 'success',
        'pending' => 'warning',
        'pending review' => 'warning',
        'pending submission' => 'warning',
        'submitted to DBS' => 'warning',
        'DBS processing' => 'warning',
        'failed' => 'danger',
        'review fail' => 'danger',
        'DBS fail' => 'danger',
        'incomplete' => 'primary',
        'not started' => 'primary'
    ][$status] ?? 'secondary';
}

$tickImg = asset('/images/checkbox_checked.png'); // Path to tick image
$emptyBoxImg = asset('/images/checkbox.png'); // Path to empty box image

@endphp


@section('content')
<div class="container-fluid">
  <section class="content">
  	@if(session()->has('successMessage'))
	    <div class="alert alert-success">
	        {{ session()->get('successMessage') }}
	    </div>
	@endif

	@if(session()->has('errorMessage'))
	    <div class="alert alert-danger">
	        @if (is_array(session()->get('errorMessage')))
	        	@php $errorArray = session()->get('errorMessage');@endphp
	        	@foreach ($errorArray as $key =>$errorMessage)
	        		@if($key != 'message' || $key == 0)
	        			{{$errorMessage}}<br />
	        		@endif
	        	@endforeach
	        @else
	        {{ session()->get('errorMessage') }}
	        @endif
	    </div>
	@endif

    <div class="modal" id="emailModal" reference-email="-" application-id="0">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span></button>
                    <h4 class="modal-title">CONFIRM</h4>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to email this reference?</p>
                    <p class="reference-email-addr">&nbsp;</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn forceBgClassified pull-left" id="confirmEmail">YES</button>
                    <button type="button" class="btn btn-secondary pull-left" data-dismiss="modal">No</button>
                </div>
            </div>
        </div>
    </div>




    <div class="box box-default">
      <div class="box-header with-border">
        <div class="form-group">
            <a href="{{ env('APP_URL') }}users/consolidatedSearch"><button type="button" class="btn btn-default pull-left" style="margin-bottom: 10px;">Back</button></a>
            <div style="float: left; margin:0 0 0 25px; font-size: 24px; font-weight: bold;">Final Report</div> 
        </div>
        </div><!-- /.box-header -->
        <div class="box-body" style="color: #2C3C64; font-size: 16px;">
            <p><span><strong>Approved Access No:</strong> {{$userDetails->applicationCode}}</span></p>
            <p><span><strong>Candidate Name:</strong> {{$userDetails->firstName}} {{$userDetails->lastName}}</span></p>
            <p><span><strong>Client Name:</strong> {{$userDetails->organisationName}}</span></p>
        </div>
        <div class="box-header with-border bg-gray disabled">		        	
            <h3 class="box-title">Checks Ordered</h3>
        </div>
        <div class="box-body">
            @if (!empty($orderedChecks) && count($orderedChecks) > 0)
                <table class="table table-responsive">
                    <thead>
                    <tr>
                        <th>Check Type</th>
                        <th>Status</th>
                        <th>Completed</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($orderedChecks as $check)
                        <tr>
                        <td>{{ $check['name'] }}</td>
                        <td>
                            <span class="badge label-{{ getStatusClass($check['status']) }}">
                            {{ ucfirst($check['status']) }}
                            </span>
                        </td>
                        <td>
                            @if ($check['updated_at'] === 'N/A')
                                N/A
                            @else
                                {{ \Carbon\Carbon::parse($check['updated_at'])->format('d M Y') }}
                            @endif
                        </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            @else
                <p class="text-muted">No checks ordered yet.</p>
            @endif
        </div>
        <div class="box-header with-border bg-gray disabled">		        	
            <h3 class="box-title">Candidate Details</h3>
        </div>
        <div class="box-body">
            <div class="table-container">
                <table class="table table-bordered table-custom">
                    <tbody>
                        <tr>
                            <td><strong>Present Surname:</strong></td>
                            <td>{{ $userDetails->presentSurname ?? ' '}}</td>
                            <td><strong>Present Forename:</strong></td>
                            <td>{{ $userDetails->forename ?? ' '}}</td>
                        </tr>
                        <tr>
                            <td><strong>Previous Surname:</strong></br><span>If Applicable</span></td>
                            <td>{{ $userDetails->other_surname ?? ' ' }}</td>
                            <td><strong>Previous Forename:</strong></br><span>If Applicable</span></td>
                            <td>{{ $userDetails->other_forename ?? ' ' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Contact Number:</strong></td>
                            <td>{{ $userDetails->contact_number ?? ' '}}</td>
                            <td><strong>AA-Number:</strong></td>
                            <td>{{$userDetails->applicationCode ?? ' '}}</td>
                        </tr>
                        <tr>
                            <td><strong>Address Line 1:</strong></td>
                            <td>{{ $userDetails->address_line_1 ?? ' '}}</td>
                            <td><strong>Date Added:</strong></td>
                            <td>{{ !empty($createdOn) ? \Carbon\Carbon::parse($createdOn)->format('d/m/Y') : '' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Line 2:</strong></td>
                            <td>{{ $userDetails->address_line_2 ?? '' }}</td>
                            <td><strong>Status:</strong></td>
                            <td> </td>
                        </tr>
                        <tr>
                            <td><strong>Line 3:</strong></td>
                            <td>{{ $userDetails->address_town ?? ' '}}, {{ $userDetails->address_county ?? ' '}}, {{ $userDetails->address_country ?? ' '}}</td>
                            <td><strong>Final Report Issued:</strong></td>
                            <td>{{ \Carbon\Carbon::parse($userDetails->completedDate ?? '')->format('d M Y') ?? ' ' }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        @if ($userDetails->bpssApplication > 0)
            <div class="box-header with-border bg-gray disabled">		        	
                <h3 class="box-title">Nationality and Immigration Status</h3>
            </div>
            <div class="box-body">
                <div class="table-container">
                    <table class="table table-bordered table-custom">
                        <tbody>
                            <tr>
                                <td><strong>Date of Birth:</strong></td>
                                <td>{{ \Carbon\Carbon::parse($userDetails->dob ?? '')->format('d M Y') ?? ' ' }}</td>
                                <td><strong>Town of Birth:</strong></td>
                                <td>{{$userDetails->birth_town ?? ' '}}</td>
                            </tr>
                            <tr>
                                <td><strong>Country of Birth:</strong></td>
                                <td>{{$userDetails->birth_country_fullName ?? ' '}}</td>
                                <td><strong>Nationality:</strong></td>
                                <td>{{$BPSSApplication->present_nationality ?? ' '}}</td>
                            </tr>
                            <tr>
                                <td><strong>Dual Nationality:</strong></td>
                                <td>
                                    @if($BPSSApplication->dual_citizenship == 1)
                                        @foreach ($dualNationality as $extraNationality)
                                            {{$extraNationality->country}}, 
                                        @endforeach
                                    @endif
                                </td>
                                <td><strong>Former Nationality:</strong></td>
                                <td>{{$BPSSApplication->former_nationality_details ?? ' '}}</td>
                            </tr>
                            <tr>
                                <td><strong>British Nationalisation Certificate No:</strong></td>
                                <td>{{$BPSSApplication->naturalisation_certificate_number ?? ' '}}</td>
                                <td><strong>Date Issued:</strong></td>
                                <td>
                                @if(date("Y-m-d", strtotime($BPSSApplication->naturalisation_certificate_date)) !='1970-01-01')
                                    {{date("d/m/Y", strtotime($BPSSApplication->naturalisation_certificate_date))}}
                                @endif
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Dual Citizenship:</strong></td>
                                <td>
                                    <div class="checkbox">
                                        <label style="font-size: 12px; color: #595959; vertical-align: middle; margin-left: 5px;">
                                            <input type="checkbox" style="cursor: not-allowed; accent-color: #C55359; pointer-events: none;" disabled="disabled" @if($BPSSApplication->dual_citizenship) checked="checked" @endif>Yes &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                            <input type="checkbox" style="cursor: not-allowed; accent-color: #C55359; pointer-events: none;" disabled="disabled" @if(isset($BPSSApplication->dual_citizenship) && empty($BPSSApplication->dual_citizenship)) checked="checked" @endif>No
                                        </label>
                                    </div>
                                </td>
                                <td><strong>Lawfully in the UK:</strong></td>
                                <td>
                                    <div class="checkbox">
                                        <label style="font-size: 12px; color: #595959; vertical-align: middle; margin-left: 5px;">
                                            <input type="checkbox" style="cursor: not-allowed; accent-color: #C55359; pointer-events: none;" disabled="disabled" @if($BPSSApplication->lawfully_resident_in_uk) checked="checked" @endif>Yes &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                            <input type="checkbox" style="cursor: not-allowed; accent-color: #C55359; pointer-events: none;" disabled="disabled" @if(isset($BPSSApplication->lawfully_resident_in_uk) && empty($BPSSApplication->lawfully_resident_in_uk)) checked="checked" @endif>No
                                        </label>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Restrictions on continued residence in the UK:</strong><br>
                                    <div class="checkbox">
                                        <label style="font-size: 12px; color: #595959; vertical-align: middle; margin-left: 5px;">
                                            <input type="checkbox" style="cursor: not-allowed; accent-color: #C55359; pointer-events: none; margin-top: 5px;" disabled="disabled" @if($BPSSApplication->continued_residence_restrictions) checked="checked" @endif>Yes &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                            <input type="checkbox" style="cursor: not-allowed; accent-color: #C55359; pointer-events: none;" disabled="disabled" @if(isset($BPSSApplication->continued_residence_restrictions) && empty($BPSSApplication->continued_residence_restrictions)) checked="checked" @endif>No
                                        </label>
                                    </div>
                                </td>
                                <td>{{$BPSSApplication->continued_residence_restrictions_details ?? ' '}}</td>
                                <td><strong>Subject to immigration Control:</strong><br>
                                    <div class="checkbox">
                                        <label style="font-size: 12px; color: #595959; vertical-align: middle; margin-left: 5px;">
                                            <input type="checkbox" style="cursor: not-allowed; accent-color: #C55359; pointer-events: none; margin-top: 5px;" disabled="disabled" @if($BPSSApplication->subject_to_immigration_control) checked="checked" @endif>Yes &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                            <input type="checkbox" style="cursor: not-allowed; accent-color: #C55359; pointer-events: none;" disabled="disabled" @if(isset($BPSSApplication->subject_to_immigration_control) && empty($BPSSApplication->subject_to_immigration_control)) checked="checked" @endif>No
                                        </label>
                                    </div>
                                </td>
                                <td>{{$BPSSApplication->subject_to_immigration_control_details ?? ' '}}</td>
                            </tr>
                            <tr>
                                <td><strong>Restrictions on continued freedom to take employment in the UK:</strong><br>
                                    <div class="checkbox">
                                        <label style="font-size: 12px; color: #595959; vertical-align: middle; margin-left: 5px;">
                                            <input type="checkbox" style="cursor: not-allowed; accent-color: #C55359; pointer-events: none; margin-top: 5px;" disabled="disabled" @if($BPSSApplication->freedom_to_take_employment) checked="checked" @endif>Yes &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                            <input type="checkbox" style="cursor: not-allowed; accent-color: #C55359; pointer-events: none;" disabled="disabled" @if(isset($BPSSApplication->freedom_to_take_employment) && empty($BPSSApplication->freedom_to_take_employment)) checked="checked" @endif>No
                                        </label>
                                    </div>
                                </td>
                                <td>{{$BPSSApplication->freedom_to_take_employment_details ?? ' '}}</td>
                                <td><strong>Home Office / Port Reference Number:</strong></td>
                                <td>{{$BPSSApplication->ho_port_reference_number ?? ' '}}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
        <div class="box-header with-border bg-gray disabled">		        	
            <h3 class="box-title">Added By</h3>
        </div>
        <div class="box-body">
            <div class="table-container">
                <table class="table table-bordered table-custom">
                    <tbody>
                        <tr>
                            <td><strong>Site Admin:</strong></td>
                            <td>{{ $siteAdmin->firstName ?? ' '}} {{ $siteAdmin->lastName ?? ' '}}</td>
                            <td><strong>Client Name:</strong></td>
                            <td>{{$userDetails->organisationName}}</td>
                        </tr>
                        <tr>
                            <td><strong>Admin Email:</strong></td>
                            <td>{{ $siteAdmin->email ?? ' ' }}</td>
                            <td><strong>Admin tel:</strong></td>
                            <td>{{ $siteAdmin->phoneNumber ?? ' ' }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        @if (!empty($orderedChecks))
            @php
                $identityCheck = collect($orderedChecks)->firstWhere('name', 'Digital Identity Verification');
            @endphp
            @if ($identityCheck)
                <div class="box-header with-border bg-gray disabled">
                    <h3 class="box-title">Digital Identity Verification</h3>
                </div>
                <div class="box-body">
                    <div class="table-container">
                        <table class="table table-bordered table-custom">
                            <tbody>
                                <tr>
                                    <td><strong>Check status</strong></td>
                                    <td>
                                        <span class="badge label-{{ getStatusClass($identityCheck['status']) }}">
                                            {{ ucfirst($identityCheck['status']) ?? ' '}}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Check completed</strong></td>
                                    <td>
                                        @if ($identityCheck['updated_at'] === 'N/A')
                                            N/A
                                        @else
                                            {{ \Carbon\Carbon::parse($identityCheck['updated_at'])->format('d M Y') ?? ' '}}
                                        @endif
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <br>
                    <div class="table-container">
                        <table class="table table-bordered table-custom">
                            <tbody>
                            @if (!empty($yotiChecks))
                                @foreach ($yotiChecks as $check)
                                    <tr>
                                        <td><strong>Check Type:</strong> {{ $check->getType() }}</td>
                                        <td><strong>Check Status:</strong> {{ $check->getState() }}</td>
                                        @php
                                            $report = $check->getReport();
                                            $recommendation = $report ? $report->getRecommendation()->getValue() : null;
                                        @endphp

                                        @if ($recommendation)
                                            <td><strong>Recommendation:</strong> <span style="color: #00B050;">{{ $recommendation }}</span></td>
                                        @endif
                                    </tr>
                                @endforeach
                            @else
                                <p>No checks found.</p>
                            @endif
                            </tbody>
                        </table>
                    </div>
                    <p style="color: #595959; font-size: 10px;">*Any failed automated check was then verified by a qualified ID Checker.</p>
                </div>
            @endif
        @endif
        
        <div class="box-header with-border bg-gray disabled">
            <h3 class="box-title">UK Criminal Record (Basic, England & Wales)</h3>
        </div>
        <div class="box-body">
            <p style="color: #808080; font-size: 14px;"><strong>* This is not a certificate issued by DBS</strong></p>
            <div class="table-container">
                <table class="table table-bordered table-custom">
                    <tbody>
                        <tr>
                            <td><strong>Check status</strong></td>
                            <td>{{ $userDetails->dbsResponse_int023_DisclosureIssueDate ? 'Complete' : ' ' }}</td>
                            <td>{{$userDetails->dbsResponse_int023_DisclosureStatus  ?? ' '}}</td>
                        </tr>
                        <tr>
                            <td><strong>Check Completed</strong></td>
                            <td>{{ $userDetails->dbsResponse_int023_DisclosureIssueDate ? \Carbon\Carbon::parse($userDetails->dbsResponse_int023_DisclosureIssueDate)->format('d/m/Y') : '' }}</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td><strong>Application Reference</strong></td>
                            <td>{{$userDetails->dbsResponse_int022_DBSApplicationFormReference  ?? ' '}}</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td><strong>Cert No</strong></td>
                            <td>{{$userDetails->dbsResponse_int023_DisclosureNumber  ?? ' '}}</td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            @if ($userDetails->bpssApplication > 0)
                @if ($BPSSApplication->convicted_by_court == 1 || $BPSSApplication->convicted_by_court_martial == 1 || $BPSSApplication->background_reliability == 1)
                    <div class="table-container">
                        <table style="border-collapse: collapse; table-layout: fixed; max-width: 100%; font-size: 11px; max-width: 1200px;">
                            <tbody>
                                <tr>
                                    <td style="width: 100%; padding-left: 10px; padding-right: 10px; word-break: break-word; white-space: normal; color: #2C3C64; padding-top: 10px; padding-bottom: 10px; border: 1px solid #555555; background-color: #ccc; font-size: 14px;">Criminal Record Declaration</td>
                                </tr>
                                <tr>
                                    <td style="width: 100%; padding-left: 10px; padding-right: 10px; word-break: break-word; white-space: normal; color: black; padding-top: 10px; padding-bottom: 10px; border: 1px solid #555555;">
                                        <strong>Guidance Note: </strong>
                                        The Company has Government contracts, some or all of which require the company to hold material or information, which is the property of the Government. The company has a duty to protect these assets while in its possession and this obligation extends to its employees and agents. Since you are or may become such a person please complete the following sections.<br><br>
                                        Please answer the following questions honestly. In addition to your self-declaration below, a check against the National Collection of Criminal Records will be undertaken and documentary evidence sought to confirm your answers in the form of Police Act Disclosure which will be paid for by Bluescreen IT. By signing the declaration in part 5 you are giving us permission to do so.<br><br>
                                        The information you give will be treated in strict confidence.<br><br>
                                        Have you ever been convicted or found guilty by a court of any offence in any country (excluding parking but including all motoring offences even where a spot fine has been administrated by the police) or have you ever been put on probation (probation orders are now called community rehabilitation orders) or absolutely/conditionally discharged or bound over after being charged with any offence or is there any action pending against you? You need not declare convictions which are "spent" under the Rehabilitation of Offenders Act (1974).<br><br>

                                        <table width="100%" style="border-collapse: collapse; table-layout: fixed; max-width: 100%; font-size: 11px;">
                                            <tbody>
                                                <tr>
                                                    <td style="width: 20%; border: 1px solid #555555; padding-left: 5px; padding-right: 5px; padding-top: 7px; padding-bottom: 7px;">
                                                        <img src="{{ $BPSSApplication->convicted_by_court == 1 ? $tickImg : $emptyBoxImg }}" style="width: 15px;" alt="Checkbox"> 
                                                        Yes
                                                    </td>
                                                    <td style="width: 20%; border: 1px solid #555555; padding-left: 5px; padding-right: 5px; padding-top: 7px; padding-bottom: 7px;">
                                                        <img src="{{ $BPSSApplication->convicted_by_court == 0 ? $tickImg : $emptyBoxImg }}" style="width: 15px;" alt="Checkbox"> 
                                                        No
                                                    </td>
                                                    <td style="width: 60%; color: #C55359; border: 1px solid #555555; padding-left: 5px; padding-right: 5px; padding-top: 7px; padding-bottom: 7px;">
                                                        <img src="{{$tickImg}}" style="width: 15px;" alt="Checkbox"> (as applicable - if yes, please give details on the following page)
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                        <br><br>
                                            Have you ever been convicted by a Court Martial or sentenced to detention or dismissal whilst serving in the Armed Forces of the UK or any Commonwealth or foreign country? You need not declare convictions which are "spent" under the Rehabilitation of Offenders Act (1974).
                                        <br><br>
                                        <table width="100%" style="border-collapse: collapse; table-layout: fixed; max-width: 100%; font-size: 11px;">
                                            <tbody>
                                                <tr>
                                                    <td style="width: 20%; border: 1px solid #555555; padding-left: 5px; padding-right: 5px; padding-top: 7px; padding-bottom: 7px;">
                                                        <img src="{{ $BPSSApplication->convicted_by_court_martial == 1 ? $tickImg : $emptyBoxImg }}" style="width: 15px;" alt="Checkbox"> 
                                                        Yes
                                                    </td>
                                                    <td style="width: 20%; border: 1px solid #555555; padding-left: 5px; padding-right: 5px; padding-top: 7px; padding-bottom: 7px;">
                                                        <img src="{{ $BPSSApplication->convicted_by_court_martial == 0 ? $tickImg : $emptyBoxImg }}" style="width: 15px;" alt="Checkbox"> 
                                                        No
                                                    </td>
                                                    <td style="width: 60%; color: #C55359; border: 1px solid #555555; padding-left: 5px; padding-right: 5px; padding-top: 7px; padding-bottom: 7px;">
                                                        <img src="{{$tickImg}}" style="width: 15px;" alt="Checkbox"> (as applicable - if yes, please give details on the following page)
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                        <br><br>
                                        Do you know of any other matters in your background which might cause your reliability or suitability to have access to government assets to be called into question?
                                        <br><br>
                                        <table width="100%" style="border-collapse: collapse; table-layout: fixed; max-width: 100%; font-size: 11px;">
                                            <tbody>
                                                <tr>
                                                    <td style="width: 20%; border: 1px solid #555555; padding-left: 5px; padding-right: 5px; padding-top: 7px; padding-bottom: 7px;">
                                                        <img src="{{ $BPSSApplication->background_reliability == 1 ? $tickImg : $emptyBoxImg }}" style="width: 15px;" alt="Checkbox"> 
                                                        Yes
                                                    </td>
                                                    <td style="width: 20%; border: 1px solid #555555; padding-left: 5px; padding-right: 5px; padding-top: 7px; padding-bottom: 7px;">
                                                        <img src="{{ $BPSSApplication->background_reliability == 0 ? $tickImg : $emptyBoxImg }}" style="width: 15px;" alt="Checkbox"> 
                                                        No
                                                    </td>
                                                    <td style="width: 60%; color: #C55359; border: 1px solid #555555; padding-left: 5px; padding-right: 5px; padding-top: 7px; padding-bottom: 7px;">
                                                    <img src="{{$tickImg}}" style="width: 15px;" alt="Checkbox"> (as applicable - if yes, please give details on the following page)
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                        <br><br>
                                        If you answered YES to any of the questions on this form, please give details bellow:
                                        <br><br>
                                        <table width="100%" style="border-collapse: collapse; table-layout: fixed; max-width: 100%; font-size: 11px;">
                                            <tbody>
                                                <tr>
                                                    <td style="width: 100%; border: 1px solid #555555; padding-left: 5px; padding-right: 5px; padding-top: 7px; padding-bottom: 7px;">
                                                        <strong>Details:</strong> <span style="color: #C55359;">please list bellow if applicable</span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td style="width: 100%; border: 1px solid #555555; padding-left: 5px; padding-right: 5px; padding-top: 7px; padding-bottom: 7px;">
                                                        {{ $BPSSApplication->convictions_details ?? ' ' }}
                                                        <br>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                        <br><br>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <br>
                @endif
            @endif
        </div>

        @if ($userDetails->bpssApplication > 0)
            <div class="box-header with-border bg-gray disabled">
                <h3 class="box-title">Academic History (5 years)</h3>
            </div>
            <div class="box-body">
                <div class="table-container">
                    <table class="table table-bordered table-academic">
                        <tbody>
                            <tr>
                                <td>School <img src="{{ $BPSSApplication->school_data == 1 ? $tickImg : $emptyBoxImg }}" style="width: 15px;" alt="Checkbox"></td>
                                <td>College <img src="{{ $BPSSApplication->college_data == 1 ? $tickImg : $emptyBoxImg }}" style="width: 15px;" alt="Checkbox"></td>
                                <td>University <img src="{{ $BPSSApplication->university_data == 1 ? $tickImg : $emptyBoxImg }}" style="width: 15px;" alt="Checkbox"></td>
                            </tr>
                            <tr>
                                <td>Name: {{ $BPSSApplication->school_name ?? ' ' }}</td>
                                <td>Name: {{ $BPSSApplication->college_name ?? ' ' }}</td>
                                <td>Name: {{ $BPSSApplication->university_name ?? ' ' }}</td>
                            </tr>
                            <tr>
                                <td>Date From: {{ $BPSSApplication->school_date_from ? \Carbon\Carbon::parse($BPSSApplication->school_date_from)->format('d M Y') : '' }}</td>
                                <td>Date From: {{ $BPSSApplication->college_date_from ? \Carbon\Carbon::parse($BPSSApplication->college_date_from)->format('d M Y') : '' }}</td>
                                <td>Date From: {{ $BPSSApplication->university_date_from ? \Carbon\Carbon::parse($BPSSApplication->university_date_from)->format('d M Y') : '' }}</td>
                            </tr>
                            <tr>
                                <td>Date To: {{ $BPSSApplication->school_date_to ? \Carbon\Carbon::parse($BPSSApplication->school_date_to)->format('d M Y') : '' }}</td>
                                <td>Date To: {{ $BPSSApplication->college_date_to ? \Carbon\Carbon::parse($BPSSApplication->college_date_to)->format('d M Y') : '' }}</td>
                                <td>Date To: {{ $BPSSApplication->university_date_to ? \Carbon\Carbon::parse($BPSSApplication->university_date_to)->format('d M Y') : '' }}</td>
                            </tr>
                            <tr>
                                <td>Contact Name: {{ $BPSSApplication->school_contact_name ?? ' ' }}</td>
                                <td>Contact Name: {{ $BPSSApplication->college_contact_name ?? ' ' }}</td>
                                <td>Contact Email: {{ $BPSSApplication->university_contact_email ?? ' ' }}</td>
                            </tr>
                            <tr>
                                <td>Contact Address: {{ $BPSSApplication->school_contact_address ?? ' ' }}</td>
                                <td>Contact Address: {{ $BPSSApplication->college_contact_address ?? ' ' }}</td>
                                <td>Contact Address: {{ $BPSSApplication->university_contact_number ?? ' ' }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="box-header with-border bg-gray disabled">
                <h3 class="box-title">Employment History (5 years)</h3>
            </div>
            <div class="box-body">
                @php
                    $counter = 0;
                @endphp
                @if (!empty($employment) && count($employment) > 0)
                    @foreach ($employment as $job)
                        @php
                            $counter += 1;
                        @endphp
                        <div class="table-container">
                            <table class="table table-bordered table-custom">
                                <tbody>
                                
                                    <tr>
                                        <td><span style="color: #2C3C64;">Period Covered [{{$counter}}]</span></td>
                                        <td><span style="color: #2C3C64;">Date From: </span>{{ !empty($job->date_from) ? \Carbon\Carbon::parse($job->date_from)->format('d/m/Y') : '' }}</td>
                                        <td><span style="color: #2C3C64;">Date To: </span>{{ !empty($job->date_to) ? \Carbon\Carbon::parse($job->date_to)->format('d/m/Y') : '' }}</td>
                                    </tr>
                                    <tr>
                                        <td><span style="color: #2C3C64;">Company Name:</span></td>
                                        <td>{{ $job->company_name ?? '' }}</td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td><span style="color: #2C3C64;">Email Address:</span></td>
                                        <td>{{ $job->email_address ?? '' }}</td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td><span style="color: #2C3C64;">Company Address:</span></td>
                                        <td>{{ $job->company_address_line ?? '' }}</td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td><span style="color: #2C3C64;">Town:</span></td>
                                        <td>{{ $job->company_address_town ?? '' }}</td>
                                        <td><span style="color: #2C3C64;">Postcode: </span>{{ $job->company_address_postcode ?? '' }}</td>
                                    </tr>
                                    <br>
                                </tbody>
                            </table>
                            <br>
                        </div>
                    @endforeach
                @else
                    <p class="text-muted">No employment listed.</p>
                @endif

                <h3 style="font-size: 14px; color: #2C3C64; padding-bottom: 5px;">Unemployment period:</h3>
                @if (!empty($unemployment) && count($unemployment) > 0)
                    @foreach ($unemployment as $job)
                        <div style="color: #595959;"><span style="color: #C55359;">Date From: </span>{{ !empty($job->date_from) ? \Carbon\Carbon::parse($job->date_from)->format('d/m/Y') : '' }}, <span style="color: #C55359;">Date To: </span>{{ !empty($job->date_to) ? \Carbon\Carbon::parse($job->date_to)->format('d/m/Y') : '' }}</div><br>
                    @endforeach
                @else
                    <p>n/a</p>
                @endif
            </div>

            <div class="box-header with-border bg-gray disabled">		        	
                <h3 class="box-title">Personal References (5 years)</h3>
            </div>


            <div class="box-body">			        	
                <div class="row">

                    <div class="col-md-3">
                        <h5 class="strong">Referee Name</h5>
                    </div>
                    <div class="col-md-1">
                        <h5 class="strong">Relationship</h5>
                    </div>
                    <div class="col-md-2">
                        <h5 class="strong">Email</h5>
                    </div>
                    <div class="col-md-2">
                        <h5 class="strong">Address</h5>
                    </div>
                    <div class="col-md-2">
                        <h5 class="strong">Length of Association</h5>
                    </div>
                    <div class="col-md-1">
                        <h5 class="strong">&nbsp;</h5>
                    </div>
                    <div class="col-md-1">
                        <h5 class="strong">&nbsp;</h5>
                    </div>
                </div>

                <div class="row">
                    @if(isset($persRef1->referee_name) && strlen($persRef1->referee_name)>0)
                        <div class="col-md-3"><div class="with-border bg-gray disabled" style="width:10%; float:left; padding: 5px; text-align: center;">1.</div>
                            <input style="width:88%; float:right;" class="form-control" id="reference1_referee_name" name="reference1_referee_name" type="text" value="@if(isset($persRef1->referee_name)){{$persRef1->referee_name}}@endif">
                        </div>
                        <div class="col-md-1">
                            <input class="form-control" id="reference1_referee_relationship" name="reference1_referee_relationship" type="text" value="@if(isset($persRef1->relationship)){{$persRef1->relationship}}@endif">
                        </div>
                        <div class="col-md-2">
                            <input class="form-control" id="reference1_referee_email" name="reference1_referee_email" type="text" value="@if(isset($persRef1->referee_email)){{$persRef1->referee_email}}@endif">
                        </div>
                        <div class="col-md-2">
                            <input class="form-control" id="reference1_referee_address" name="reference1_referee_address" type="text" value="@if(isset($persRef1->referee_address_line)){{$persRef1->referee_address_line}}, {{$persRef1->referee_address_town ?? ' '}}, {{$persRef1->referee_address_postcode ?? ' '}}@endif">
                        </div>
                        <div class="col-md-2">
                            <input class="form-control" id="reference1_referee_length_of_association" name="reference1_referee_length_of_association" type="text" value="@if(isset($persRef1->date_from)){{$persRef1->date_from}} to {{$persRef1->date_to}}@endif">
                        </div>
                        <div class="col-md-1">
                            @if(isset($persRef1->referee_email))
                            <button type="button" class="btn btn-success btn-flat emailReference1" style="padding: 5px 10px" data-toggle="modal" data-target="#emailModal" data-application-id="{{ $BPSSApplication->id }}" data-button-id="emailReference1" id="emailReference1" data-reference-email="@if(isset($persRef1->referee_email)){{$persRef1->referee_email}}@endif"><i class="fa fa-envelope"></i> Email</button>
                            @endif
                        </div>
                        <div class="col-md-1">
                            <!--
                            <button type="button" class="btn btn-warning btn-flat uploadReference1PDF"><i class="fa fa-file-pdf-o"></i> Upload</button>
                            -->

                        </div>
                        <input name="reference1_id" type="hidden" value="{{$persRef1->id}}">
                    @elseif(isset($reference1->referee_name) && strlen($reference1->referee_name)>0)
                        <div class="col-md-3"><div class="with-border bg-gray disabled" style="width:10%; float:left; padding: 5px; text-align: center;">1.</div>
                            <input style="width:88%; float:right;" class="form-control" id="reference1_referee_name" name="reference1_referee_name" type="text" value="{{$reference1->referee_name}}">
                        </div>
                        <div class="col-md-1">
                            <input class="form-control" id="reference1_referee_relationship" name="reference1_referee_relationship" type="text" value="">
                        </div>
                        <div class="col-md-2">
                            <input class="form-control" id="reference1_referee_email" name="reference1_referee_email" type="text" value="@if(isset($reference1->referee_referee_email)){{$reference1->referee_referee_email}}@endif">
                        </div>
                        <div class="col-md-2">
                            <input class="form-control" id="reference1_referee_address" name="reference1_referee_address" type="text" value="{{$reference1->referee_address_line}}, {{$reference1->referee_address_town}} {{$reference1->referee_address_postcode}}">
                        </div>
                        <div class="col-md-2">
                            <input class="form-control" id="reference1_referee_length_of_association" name="reference1_referee_length_of_association" type="text" value="@if(isset($reference1->referee_length_of_association)){{$reference1->referee_length_of_association}}@endif">
                        </div>
                        <div class="col-md-1">
                            @if(isset($reference1->referee_referee_email))
                            <!--
                            <button type="button" class="btn btn-success btn-flat emailReference1" style="padding: 5px 10px" data-toggle="modal" data-target="#emailModal" data-button-id="emailReference1" id="emailReference1" data-reference-email="" data-application-id="{{ $BPSSApplication->id }}" ><i class="fa fa-envelope"></i> Email</button>
                            -->
                            @endif
                        </div>
                        <div class="col-md-1">
                            <!--
                            <button type="button" class="btn btn-warning btn-flat uploadReference1PDF"><i class="fa fa-file-pdf-o"></i> Upload</button>
                        -->
                        </div>
                        <input name="reference1_id" type="hidden" value="0">
                    @else
                        <div class="col-md-3"><div class="with-border bg-gray disabled" style="width:10%; float:left; padding: 5px; text-align: center;">1.</div>
                            <input style="width:88%; float:right;" class="form-control" id="reference1_referee_name" name="reference1_referee_name" type="text" value="">
                        </div>
                        <div class="col-md-1">
                            <input class="form-control" id="reference1_referee_relationship" name="reference1_referee_relationship" type="text" value="">
                        </div>
                        <div class="col-md-2">
                            <input class="form-control" id="reference1_referee_email" name="reference1_referee_email" type="text" value="">
                        </div>
                        <div class="col-md-2">
                            <input class="form-control" id="reference1_referee_address" name="reference1_referee_address" type="text" value="">
                        </div>
                        <div class="col-md-2">
                            <input class="form-control" id="reference1_referee_length_of_association" name="reference1_referee_length_of_association" type="text" value="">
                        </div>
                        <div class="col-md-1">
                            &nbsp;
                        </div>
                        <div class="col-md-1">
                            &nbsp;
                        </div>
                        <input name="reference1_id" type="hidden" value="0">
                    @endif
                </div>
                <div class="row">
                    @if(isset($persRef2->referee_name) && strlen($persRef2->referee_name)>0)
                        <div class="col-md-3"><div class="with-border bg-gray disabled" style="width:10%; float:left; padding: 5px; text-align: center;">2.</div>
                            <input style="width:88%; float:right;" class="form-control" id="reference2_referee_name" name="reference2_referee_name" type="text" value="@if(isset($persRef2->referee_name)){{$persRef2->referee_name}}@endif">
                        </div>
                        <div class="col-md-1">
                            <input class="form-control" id="reference2_referee_relationship" name="reference2_referee_relationship" type="text" value="@if(isset($persRef2->relationship)){{$persRef2->relationship}}@endif">
                        </div>
                        <div class="col-md-2">
                            <input class="form-control" id="reference2_referee_email" name="reference2_referee_email" type="text" value="@if(isset($persRef2->referee_email)){{$persRef2->referee_email}}@endif">
                        </div>
                        <div class="col-md-2">
                            <input class="form-control" id="reference2_referee_address" name="reference2_referee_address" type="text" value="@if(isset($persRef2->referee_address_line)){{$persRef2->referee_address_line}}, {{$persRef2->referee_address_town ?? ' '}}, {{$persRef2->referee_address_postcode ?? ' '}}@endif">
                        </div>
                        <div class="col-md-2">
                            <input class="form-control" id="reference2_referee_length_of_association" name="reference2_referee_length_of_association" type="text" value="@if(isset($persRef2->date_from)){{$persRef2->date_from}} to {{$persRef2->date_to}}@endif">
                        </div>
                        <div class="col-md-1">
                            @if(isset($persRef2->referee_email))
                            <button type="button" class="btn btn-success btn-flat emailReference2" style="padding: 5px 10px" data-toggle="modal" data-target="#emailModal" data-button-id="emailReference2" id="emailReference2" data-reference-email="@if(isset($persRef2->referee_email)){{$persRef2->referee_email}}@endif" data-application-id="{{ $BPSSApplication->id }}" ><i class="fa fa-envelope"></i> Email</button>
                            @endif
                        </div>
                        <div class="col-md-1">
                            <!--
                            <button type="button" class="btn btn-warning btn-flat uploadReference2PDF"><i class="fa fa-file-pdf-o"></i> Upload</button>
                        -->
                        </div>
                        <input name="reference2_id" type="hidden" value="{{$persRef2->id}}">
                    @elseif(isset($reference2->referee_name) && strlen($reference2->referee_name)>0)
                        <div class="col-md-3"><div class="with-border bg-gray disabled" style="width:10%; float:left; padding: 5px; text-align: center;">2.</div>
                            <input style="width:88%; float:right;" class="form-control" id="reference2_referee_name" name="reference2_referee_name" type="text" value="{{$reference2->referee_name}}">
                        </div>
                        <div class="col-md-1">
                            <input class="form-control" id="reference2_referee_relationship" name="reference2_referee_relationship" type="text" value="">
                        </div>
                        <div class="col-md-2">
                            <input class="form-control" id="reference2_referee_email" name="reference2_referee_email" type="text" value="@if(isset($reference2->referee_referee_email)){{$reference2->referee_referee_email}}@endif">
                        </div>
                        <div class="col-md-2">
                            <input class="form-control" id="reference2_referee_address" name="reference2_referee_address" type="text" value="{{$reference2->referee_address_line}}, {{$reference2->referee_address_town}} {{$reference2->referee_address_postcode}}">
                        </div>
                        <div class="col-md-2">
                            <input class="form-control" id="reference2_referee_length_of_association" name="reference2_referee_length_of_association" type="text" value="@if(isset($reference2->referee_length_of_association)){{$reference2->referee_length_of_association}}@endif">
                        </div>
                        <div class="col-md-1">
                            @if(isset($reference2->referee_referee_email))
                            <!--
                            <button type="button" class="btn btn-success btn-flat emailReference2" style="padding: 5px 10px" data-toggle="modal" data-target="#emailModal" data-button-id="emailReference2" id="emailReference2" data-reference-email="" data-application-id="{{ $BPSSApplication->id }}" ><i class="fa fa-envelope"></i> Email</button>
                            -->
                            @endif
                        </div>
                        <div class="col-md-1">
                            <!--
                            <button type="button" class="btn btn-warning btn-flat uploadReference2PDF"><i class="fa fa-file-pdf-o"></i> Upload</button>
                        -->
                        </div>
                        <input name="reference2_id" type="hidden" value="0">
                    @else
                        <div class="col-md-3"><div class="with-border bg-gray disabled" style="width:10%; float:left; padding: 5px; text-align: center;">2.</div>
                            <input style="width:88%; float:right;" class="form-control" id="reference2_referee_name" name="reference2_referee_name" type="text" value="">
                        </div>
                        <div class="col-md-1">
                            <input class="form-control" id="reference2_referee_relationship" name="reference2_referee_relationship" type="text" value="">
                        </div>
                        <div class="col-md-2">
                            <input class="form-control" id="reference2_referee_email" name="reference2_referee_email" type="text" value="">
                        </div>
                        <div class="col-md-2">
                            <input class="form-control" id="reference2_referee_address" name="reference2_referee_address" type="text" value="">
                        </div>
                        <div class="col-md-2">
                            <input class="form-control" id="reference2_referee_length_of_association" name="reference2_referee_length_of_association" type="text" value="">
                        </div>
                        <div class="col-md-1">
                            &nbsp;
                        </div>
                        <div class="col-md-1">
                            &nbsp;
                        </div>
                        <input name="reference2_id" type="hidden" value="0">
                    @endif
                </div>
                
                <div class="row">
                    <div class="col-md-12">&nbsp</div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        @if(isset($referenceRecords) && intval($referenceRecords)>0)	
                            <a href="{{ env('APP_URL') }}applications/loadReferenceLog/{{$applicationID}}" target="_blank">            				
                                <button type="button" class="btn btn-warning btn-flat viewReferences"><i class="fa fa-search"></i> View Reference Details</button>
                            </a>
                        @else 
                            <button type="button" class="btn btn-danger btn-flat" disabled="disabled" style="width:100% !important;"><i class="fa fa-exclamation-triangle"></i> No References Requested</button>
                        @endif
                    </div>
                </div>

                
                <hr style="padding: 0; margin: 15px -15px !important;" />
            </div>
        @endif
        

        <div class="box-header with-border bg-gray disabled">		        	
            <h3 class="box-title">Documents Uploaded by Candidate</h3>
        </div>
        <div class="box-body">
            <div class="row">
                <div class="col-md-6">
                    @if(isset($IDdocs->supporting_documents) && count($IDdocs->supporting_documents)>0)
                    <table style="width: 100%;">
                        <thead>
                            <tr>
                                <th style="width:70%">Document Name</th>
                                <th style="width: 20%">Document Type</th>
                                <th style="width:10%">Download</th>
                            </tr>
                        </thead>
                        <tbody>		              			
                            @foreach ($IDdocs->supporting_documents as $document)
                                <tr>
                                    <td>
                                        @if(isset($document->document_type) && $document->document_type == 'pdf')
                                            <i class="fa fa-file-pdf-o" style="color: #FF0000; margin-right: 10px;" aria-hidden="true"></i>
                                        @elseif(isset($document->document_type) && $document->document_type == 'img')
                                            <i class="fa fa-file-image-o" style="color: #0000FF; margin-right: 10px;" aria-hidden="true"></i>
                                        @else
                                            <i class="fa fa-file-o" style="color: #FF00FF; margin-right: 10px;" aria-hidden="true"></i>
                                        @endif
                                            <a href="{{ env('APP_URL') }}downloadfile/supporting_dbs/{{$document->id}}/1" target="_blank">
                                            @if(isset($document->document_name) && !empty($document->document_name))
                                                {{$document->document_name}}
                                            @else
                                                Unknown name
                                            @endif
                                        </a>
                                    </td>
                                    <td>
                                        @if(isset($document->document_name) && !empty($document->document_name))
                                            {{$document->document_category}}
                                        @else
                                            Unselected
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ env('APP_URL') }}downloadfile/supporting_dbs/{{$document->id}}/1" target="_blank"><button type="button"  class="btn btn-danger pull-right" style="margin-bottom:5px;"><i class="icon fa fa-download"></i></button></a>
                                    </td>
                                </tr>
                            @endforeach				                
                        </tbody>
                    </table>
                    @else
                        <span style="color:#FF0000;">There are no supporting documents uploaded for this application!</span>
                    @endif
                </div>
            </div>
        </div>

        <div class="box-header with-border bg-gray disabled">		        	
            <h3 class="box-title">Documents Uploaded by Admins</h3>
        </div>
        <form action="{{ route('addUserFiles') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="box-body">
                <div class="row">
                    <div class="col-md-10">
                    @if(isset($userFiles) && count($userFiles)>0)
                        <table style="width: 100%;">
                        <thead>
                            <tr>
                            <th style="width:50%">Document Name</th>
                            <th style="width:20%">Document Type</th>
                            <th style="width:30%">Actions</th>
                            </tr>
                        </thead>
                        <tbody>                       
                            @foreach ($userFiles as $userfile)
                                <tr id="block_file_{{$userfile->id}}">
                                <td>{{$userfile->document_name}}</td>
                                <td>{{$userfile->file_type}}</td>
                                <td>
                                <a href="{{ env('APP_URL') }}downloadfile/new_applicant/{{$userfile->id}}/1" target="_blank"><button type="button"  class="btn btn-success pull-left" style="margin-bottom:5px;"><i class="icon fa fa-download"></i> Download</button></a>&nbsp;&nbsp;
                                <button type="button" id="remove_document_{{$userfile->id}}" document-id="{{$userfile->id}}" class="btn btn-danger pull-right" style="margin-bottom:5px;"><i class="icon fa fa-times"></i> Remove</button>
                                </td>
                            </tr>
                            @endforeach                       
                        </tbody>
                        </table>
                        <hr style="width:100%;">
                    @else
                    There are no files uploaded for this user<br /><hr /><br />
                    @endif
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group @if ($errors->any() && $errors->has('newfile')) has-error @endif">
                            <label for="newfile">Add File</label>
                            <input type="file" name="newfile" id="newfile" paceholder="Select PDF Document" accept="application/pdf">
                            <input type="hidden" name="userid" value="{{$userID}}">
                            <p class="help-block"  id="error_newfile" @if ($errors->any() && $errors->has('newfile')) @else style="display:none;" @endif>Please upload a PDF file!</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="newfileType">File Type</label>
                            <select  name="newfileType" class="form-control">
                            <option value="" selected="selected">Select File Type</option>
                            <option value="misc">Misc</option>
                            <option value="secmx">Security Matrix</option>
                            <option value="mkden">MK Denial</option>
                            </select>
                            <p class="help-block text-red"  id="error_newfileType" @if ($errors->any() && $errors->has('newfileType')) @else style="display:none;" @endif>Please select a file type!</p>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="submit">&nbsp;</label><br />
                            <button type="submit" class="btn forceBgClassified" id="uploadFile">Upload</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        <form id="checklistForm">
            @csrf
            <div class="box-header with-border bg-gray disabled">		        	
                <h3 class="box-title">Verified Identity Documents</h3>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-md-12" id="documentVerifiedListBlock" show-flag="true">
                        <div class="row">
                            <div class="col-md-6">
                                <label>Document</label>
                            </div>
                            <div class="col-md-3">
                                <label>Date of Issue</label>
                            </div>
                            <div class="col-md-3">
                                &nbsp;
                            </div>
                        </div>

                        @if(isset($BPSSVR_identity_documents) && count($BPSSVR_identity_documents)>0)
                            @foreach ($BPSSVR_identity_documents as $key => $documentVerified)
                                <div class="row" id="documentVerified_block_{{$documentVerified->id}}">
                                    <div class="col-md-6">{{$documentVerified->document_name}}</div>
                                    <div class="col-md-3">@if(isset($documentVerified->document_date_of_issue_name) && date('Y-m-d',strtotime($documentVerified->document_date_of_issue_name)) != '1970-01-01'){{\Carbon\Carbon::parse($documentVerified->document_date_of_issue_name)->format('d/m/Y')}}@endif</div>
                                    <div class="col-md-3" style=" padding-left:0;">
                                        <button id="remove_documentVerified_block_{{$documentVerified->id}}" document-id="{{$documentVerified->id}}" type="button"  class="btn btn-danger pull-left" style="margin-bottom:5px;"><i class="icon fa fa-remove"></i> Delete</button>
                                    </div>
                                    <hr />
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>

                <div class="row">
                    <div id="addNewVerifiedDocumentBlock">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="document_name">Document</label>
                                <input class="form-control" id="document_name" name="document_name" type="text" value="" maxlength="100">
                                <span class="help-block block-hidden">Please enter a value</span>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="document_date_of_issue_name">Date of Issue</label>
                                <input class="form-control" id="document_date_of_issue_name" name="document_date_of_issue_name" type="text" value="" placeholder="dd/mm/yyyy" >
                                <span class="help-block block-hidden">You must select a date of issue.</span>
                            </div>
                        </div>

                        <div class="col-md-3" style=" padding-left:0;">
                            <div class="form-group">
                                <br />
                                <button id="save_documentVerified_details"  type="button" class="btn btn-danger pull-left" style="width: 200px;"><i class="fa fa-save" style="margin-right: 10px;"></i>Save Document Details</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div> 	
            @php
                $organisationName = $userDetails->organisationName ?? '';
                $isCollins = stripos($organisationName, 'Collins') === 0 || stripos($organisationName, 'Safran') === 0;
            @endphp
            @if ($isCollins)
                <div class="box-header with-border bg-gray disabled">
                    <h3 class="box-title" style="color: #FF0000;">APPROVAL FOR ACCESS (To be completed by the Site Security Controller)</h3>
                </div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-12">
                            <p style="color: #FF0000;">
                                I certify that in accordance with the requirements of Baseline Security Personnel Standard that I, the undersigned, have personally examined, or seen signed confirmation of, the candidate’s documents provided as evidence and am satisfied they meet the BPSS requirements as specified by the cabinet office.
                            </p>
                        </div>
                    </div>
                    
                    <!-- Status Selection -->
                    <div class="row">
                        <div class="col-md-12" style="font-size: 30px;">
                            <label>
                                <input type="radio" name="approval" id="status_approved" value="approved" 
                                @if(($collinsApproval->approval ?? ' ') == 1) checked="checked" @endif> Approved
                            </label>
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <label>
                                <input type="radio" name="approval" id="status_denied" value="denied" 
                                @if(($collinsApproval->approval ?? ' ') == 0) checked="checked" @endif> Denied
                            </label>
                        </div>
                    </div>

                    <!-- Approval Table -->
                    <div class="row">
                        <div class="col-md-12">
                            <table class="table">
                                <tbody>
                                    <tr>
                                        <th style="width: 15%; border: 1px solid #000000;">Name:</th>
                                        <td style="width: 35%; border: 1px solid #000000;">
                                            <input class="form-control" id="admin_approval_name" name="admin_approval_name" type="text" value="{{ $collinsApproval->adminName ?? '' }}" required>
                                        </td>
                                        <th style="width: 15%; border: 1px solid #000000;">Title:</th>
                                        <td style="width: 35%; border: 1px solid #000000;">
                                            <input class="form-control" id="admin_approval_title" name="admin_approval_title" type="text" value="{{ $collinsApproval->adminTitle ?? '' }}" required>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th style="width: 15%; border: 1px solid #000000;">Date:</th>
                                        <td style="width: 35%; border: 1px solid #000000;">{{ $collinsApproval->updated_at ?? '' }}</td>
                                        <th style="width: 15%; border: 1px solid #000000;">Signature:</th>
                                        <td style="width: 35%; border: 1px solid #000000;"></td>
                                    </tr>
                                    
                                    <!-- Employment Type Selection -->
                                    <tr>
                                        <td colspan="2" style="border: 1px solid #000000;">
                                            <label>
                                                <input type="radio" name="employment_type" id="employment_type_employee" value="employee"  
                                                @if(($BPSSApplication->employement_type ?? ($BPSSApplication->employement_type ?? '')) == 'employee') checked="checked" @endif> 
                                                Employee (10 years)
                                            </label>
                                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                            <label>
                                                <input type="radio" name="employment_type" id="employment_type_contractor" value="contractor"  
                                                @if(($BPSSApplication->employement_type ?? ($BPSSApplication->employement_type ?? '')) == 'contractor') checked="checked" @endif> 
                                                Contractor (3 years)
                                            </label>
                                        </td>
                                        <td colspan="2" style="border: 1px solid #000000;">
                                            <label>
                                                <input type="radio" name="application_type" id="application_type_initial_clearance" value="initial_clearance" 
                                                @if(($BPSSApplication->form_type ?? ($BPSSApplication->form_type ?? '')) == 'initial_clearance') checked="checked" @endif> 
                                                Initial Clearance
                                            </label>
                                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                            <label>
                                                <input type="radio" name="application_type" id="application_type_revalidation" value="revalidation" 
                                                @if(($BPSSApplication->form_type ?? ($BPSSApplication->form_type ?? '')) == 'revalidation') checked="checked" @endif> 
                                                Revalidation
                                            </label>
                                        </td>
                                    </tr>

                                    <!-- Additional Info -->
                                    <tr>
                                        <th style="border: 1px solid #000000;">Site:</th>
                                        <td style="border: 1px solid #000000;">{{ $userDetails->organisationName ?? '' }}</td>
                                        <th style="border: 1px solid #000000;">Known Cargo:</th>
                                        <td style="border: 1px solid #000000;">
                                            <label>
                                                <input type="radio" name="cargo_type" id="cargo" value="1" @if(($collinsApproval->cargo ?? ' ') == 1) checked="checked" @endif>
                                                Cargo
                                            </label>
                                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                            <label>
                                                <input type="radio" name="cargo_type" id="noncargo" value="0" @if(($collinsApproval->cargo ?? ' ') == 0) checked="checked" @endif>
                                                Non-Cargo
                                            </label>
                                        </td>
                                    </tr>

                                    <!-- Contractor Info -->
                                    <tr>
                                        <th style="border: 1px solid #000000;">Contractor Company:</th>
                                        <td style="border: 1px solid #000000;">
                                            <input class="form-control" id="contractor_company" name="contractor_company" type="text" 
                                            value="{{ $BPSSApplication->contractor_name_of_company ?? '' }}">
                                        </td>
                                        <th style="border: 1px solid #000000;">Position:</th>
                                        <td style="border: 1px solid #000000;">
                                            <input class="form-control" id="contractor_position" name="contractor_position" type="text" 
                                            value="{{ $userDetails->position_applied_for ?? '' }}">
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
        
            <div class="box-header with-border bg-gray disabled">
                <h3 class="box-title">Checklist</h3>
            </div>
            <div class="box-body">
                <div class="row">
                    <!-- Left Column -->
                    <div class="col-md-6 col-sm-6">
                        <div class="checkbox">
                            <label><input type="checkbox" class="checklist-item" name="admin_x3_ids_uploaded" {{ $userDetails->admin_x3_ids_uploaded ? 'checked' : '' }}> Identity has been verified</label>
                        </div>
                        <div class="checkbox">
                            <label class="{{ $userDetails->bpssApplication < 1 ? 'disabled-label' : '' }}"><input type="checkbox" class="checklist-item" name="admin_personal_references_received" {{ $userDetails->bpssApplication < 1 ? 'disabled' : '' }} {{ ($userDetails->admin_personal_ref1_returned && $userDetails->admin_personal_ref2_returned) ? 'checked' : '' }}> Personal references received</label>
                        </div>
                        <div class="checkbox">
                            <label class="{{ $userDetails->bpssApplication < 1 ? 'disabled-label' : '' }}"><input type="checkbox" class="checklist-item" name="admin_employment_ref_returned" {{ $userDetails->bpssApplication < 1 ? 'disabled' : '' }} {{ $userDetails->admin_employment_ref_returned ? 'checked' : '' }}> Employment History confirmed</label>
                        </div>
                        <div class="checkbox">
                            <label class="{{ $userDetails->bpssApplication < 1 ? 'disabled-label' : '' }}"><input type="checkbox" class="checklist-item" name="admin_academic_history_confirmed" {{ $userDetails->bpssApplication < 1 ? 'disabled' : '' }} {{ $userDetails->admin_academic_history_confirmed ? 'checked' : '' }}> Academic History confirmed</label>
                        </div>
                    </div>

                    <!-- Right Column -->
                    <div class="col-md-6 col-sm-6">
                        <div class="checkbox">
                            <label><input type="checkbox" class="checklist-item" name="admin_all_evidence_attached" {{ $userDetails->admin_all_evidence_attached ? 'checked' : '' }}> All evidence attached (at least 3 forms of ID)</label>
                        </div>
                        <div class="checkbox">
                            <label><input type="checkbox" class="checklist-item" name="admin_right_to_work" {{ $userDetails->admin_right_to_work ? 'checked' : '' }}> Right to Work</label>
                        </div>
                        <div class="checkbox">
                            <label class="{{ $userDetails->bpssApplication < 1 ? 'disabled-label' : '' }}"><input type="checkbox" class="checklist-item" name="denied_party_screening" {{ collect($orderedChecks)->contains('name', 'MK Screening report') ? 'checked' : '' }}> Denied Party Screening uploaded</label>
                        </div>
                        <div class="checkbox">
                            <label class="{{ $userDetails->bpssApplication < 1 ? 'disabled-label' : '' }}"><input type="checkbox" class="checklist-item" name="security_matrix" {{ collect($orderedChecks)->contains('name', 'Security Clearance Matrix') ? 'checked' : '' }}> Security Matrix uploaded</label>
                        </div>
                    </div>
                </div>

                <!-- Status & Date Fields -->
                <div id="checklistStatusContainer" style="display: none; margin-top: 10px;">
                    <label>Status:</label>
                    <input type="text" id="checklistStatus" class="form-control" readonly>
                    <label>Date Completed:</label>
                    <input type="text" id="checklistDateCompleted" class="form-control" readonly>
                </div>

                <br>
                <label for="admin_approval_notes">Aditional Information</label>
                <textarea class="form-control" rows="5"  id="admin_approval_notes"  name="admin_approval_notes">{{ $userDetails->admin_approval_notes ?? '' }}</textarea>
            </div>
        

            <!-- Buttons -->
            <div class="box-footer">
                @if ($userDetails->completed == 1)
                    <a href="{{ env('APP_URL') }}finalReport/generate/{{ $userID }}" class="btn btn-success">Download Report</a>
                @else
                    <button type="button" id="saveChecklist" class="btn btn-warning">Save Changes</button>
                    <button type="button" id="completeBtn" class="btn btn-success" onclick="markCandidateComplete()">Mark candidate as complete</button>
                @endif
            </div>
        </form>
    </div>
  </section>
</div>

<style>
    .label-warning {
        background-color: #f39c12 !important;
    }
    .table-container {
        display: flex;
        justify-content: center;
        width: 100%;
        margin-top: 20px;
    }
    .table-custom {
        border-collapse: collapse;
        max-width: 1200px;
    }
    .table-custom td {
        border-left: none !important;
        border-right: none !important;
        border-top: 1px solid black !important;
        border-bottom: 1px solid black !important;
    }
    .table-custom span {
        color: #2C3C64;
    }
    .table-custom td {
        color: #595959;
    }
    .table-custom td strong {
        color: #C55359;
    }
    #rightToWorkForm {
        max-width: 500px;
        font-size: 18px;
        color: #2C3C64;
    }
    .table-academic {
        color: #2C3C64; 
        max-width: 1200px;
    }
    .table-academic td {
        border: 1px solid black !important;
        width: 33.33%;
    }
    /* Greys out the label when checkboxes are disabled */
    .disabled-label {
        color: #b0b0b0 !important;
    }

    /* Reduce opacity of the disabled checkbox */
    input[type="checkbox"]:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }
    @media (max-width: 768px) {
        .container-fluid {
            padding: 0;
        }
        .content {
            padding: 0;
        }
    }
</style>

@endsection

@section('pageJavascript')
<script type="text/javascript">
    function markCandidateComplete() {
        window.open("{{ env('APP_URL') }}finalReport/generate/{{ $userID }}", '_blank');
    }

    $(document).ready(function() {
        $('#saveChecklist').click(function () {
            let form = $('#checklistForm')[0];
            let formData = new FormData(form);
            formData.append('userID', '{{ $userID }}');

            $.ajax({
                url: "{{ route('saveChecklist') }}",
                type: "POST",
                data: formData,
                contentType: false,
                processData: false,
                headers: {
                    'X-CSRF-TOKEN': $('input[name="_token"]').val()
                },
                success: function (response) {
                    if (response.status === "success") {
                        alert("Checklist and approval details updated successfully!");
                    } else {
                        alert("Error: " + response.message);
                    }
                },
                error: function (xhr) {
                    alert("An error occurred while saving the checklist.");
                }
            });
        });
    });


    $(document).on("click", '[id^="remove_document_"]', function() {
        var documentID = $(this).attr('document-id');
        $.ajax({
        url: "<?php echo env('APP_URL'); ?>" + "adminoperator/removeFile", 
            method: "POST",
            data: {"_token":"{{ csrf_token() }}", "documentID":documentID},
            success: function(result){
                var deleteResult = JSON.parse(result);
                if(deleteResult.status == 1){
                $("#block_file_"+documentID).remove();
                } else {
                alert('Error: Please try again!');
                }
            },
            error: function(result){
                alert('Error: Please try again!');
            },
        });
    });

    document.addEventListener("DOMContentLoaded", function () {
        const checklistItems = document.querySelectorAll(".checklist-item:not(:disabled)");
        const optionalItems = ["admin_right_to_work", "denied_party_screening", "security_matrix", "admin_personal_references_received"];
        const statusField = document.getElementById("checklistStatus");
        const dateField = document.getElementById("checklistDateCompleted");
        const statusContainer = document.getElementById("checklistStatusContainer");
        const completeBtn = document.getElementById("completeBtn");

        function updateChecklistStatus() {
            let allRequiredChecked = true;

            checklistItems.forEach(item => {
                if (!item.checked && !optionalItems.includes(item.name)) {
                    allRequiredChecked = false;
                }
            });

            if (allRequiredChecked) {
                let currentDate = new Date().toLocaleString();
                statusField.value = "Completed";
                dateField.value = currentDate;
                statusContainer.style.display = "block";
                completeBtn.removeAttribute("disabled"); // Enable button
            } else {
                statusField.value = "";
                dateField.value = "";
                statusContainer.style.display = "none";
                completeBtn.setAttribute("disabled", "disabled"); // Disable button
            }
        }

        checklistItems.forEach(item => {
            item.addEventListener("change", updateChecklistStatus);
        });

        updateChecklistStatus(); // Check status on page load
    });


    $('#emailModal').on('show.bs.modal', function(e) {
        $('.reference-email-addr').empty();
        var referenceEmail = e.relatedTarget.dataset.referenceEmail;
        $('#emailModal').attr("reference-email", referenceEmail);
        var applicationId = e.relatedTarget.dataset.applicationId;
        $('#emailModal').attr("application-id", applicationId);
        var buttonId = e.relatedTarget.dataset.buttonId;
        $('#emailModal').attr("button-id", buttonId);
        $('.reference-email-addr').html('<strong>Reference email address: '+referenceEmail+'</strong>');
    });


    $(document).on("click", "#confirmEmail", function() {
        var referenceEmail = $("#emailModal").attr("reference-email");
        var applicationId = $("#emailModal").attr("application-id");
        var buttonId = $("#emailModal").attr("button-id");

        emailReference(referenceEmail,applicationId,buttonId);
    });


    function emailReference(referenceEmail,applicationId,buttonId,templateName = ""){
        $('#emailModal').modal('toggle');//remove this line
        
        $.ajax({
            url: "<?php echo env('APP_URL'); ?>" + "applicant/sendReferenceEmail", 
            method: "POST",
            data: {"_token":"{{ csrf_token() }}", "referenceEmail":referenceEmail, "applicationId":applicationId, "templateName":templateName},
            success: function(result){
            if (result == 1){
                 $("#"+buttonId).removeClass("btn-success").addClass("btn-danger").html('Reference Email Sent!');
                setTimeout(function() {
                $("#"+buttonId).removeClass("btn-danger").addClass("btn-success").html('<i class="fa fa-envelope"></i> Email')}, 2000);            
            } else {
                $("#"+buttonId).removeClass("btn-success").addClass("btn-warning").html('Email NOT Sent!');
                setTimeout(function() {
                $("#"+buttonId).removeClass("btn-warning").addClass("btn-success").html('<i class="fa fa-envelope"></i> Email')}, 2000);
            }
            $('#emailModal').modal('toggle');
            },
            error: function(result){
                $("#"+buttonId).removeClass("btn-success").addClass("btn-warning").html('Email NOT Sent!');
                setTimeout(function() {
            $("#"+buttonId).removeClass("btn-warning").addClass("btn-success").html('<i class="fa fa-envelope"></i> Email')}, 2000);
            $('#emailModal').modal('toggle');
            },
        });   
    }

    $(document).on("click", '#save_documentVerified_details', function() {
        var BPSSVR_ApplicationID = "<?php if(isset($userID) && $userID > 0 ) echo $userID; else echo'0'; ?>";
        save_documentVerified_details(BPSSVR_ApplicationID);
    });
    function save_documentVerified_details(BPSSVR_ApplicationID){
        var document_name = $( "#document_name" ).val();
        var document_date_of_issue_name = $( "#document_date_of_issue_name" ).val();
        $.ajax({
            url: "<?php echo env('APP_URL'); ?>" + "applications/addDocumentVerifiedDetails", 
            method: "POST",
            data: {"_token":"{{ csrf_token() }}", "applicationID":BPSSVR_ApplicationID, "document_name":document_name, "document_date_of_issue_name":document_date_of_issue_name},
            success: function(result){
                var saveResult = JSON.parse(result);
                if(saveResult.status == 1){
                    var documentLine = '<div class="row" id="documentVerified_block_'+saveResult.documentID+'"><div class="col-md-6">';
                    documentLine += document_name;
                    documentLine += '</div>';
                    documentLine += '<div class="col-md-3">';
                    documentLine += document_date_of_issue_name;
                    documentLine += '</div>';
                    documentLine += '<div class="col-md-3" style=" padding-left:0;">';
                    documentLine += '<button id="remove_documentVerified_block_'+saveResult.documentID+'" type="button" document-id="'+saveResult.documentID+'" class="btn btn-danger pull-left" style="margin-bottom:5px;"><i class="icon fa fa-remove"></i> Delete</button>';
                    documentLine += '</div>';
                    documentLine += '<hr />';
                    documentLine += '</div>';
                    $('#documentVerifiedListBlock').append(documentLine);
                } else {
                    alert('Error: Please try again!');
                }
            },
            error: function(result){
            alert('Error: Please try again!');
            },
        });
    }
    $(document).on("click", '[id^="remove_documentVerified_block_"]', function() {
        var documentID = $(this).attr('document-id');
        var BPSSVR_ApplicationID = "<?php if(isset($userID) && $userID > 0 ) echo $userID; else echo'0'; ?>";
        remove_documentVerified_details(BPSSVR_ApplicationID, documentID);
    });
    function remove_documentVerified_details(BPSSVR_ApplicationID, documentID){
        $.ajax({
            url: "<?php echo env('APP_URL'); ?>" + "applications/removeDocumentVerifiedDetails", 
            method: "POST",
            data: {"_token":"{{ csrf_token() }}", "applicationID":BPSSVR_ApplicationID, "documentID":documentID},
            success: function(result){
                var deleteResult = JSON.parse(result);
                if(deleteResult.status == 1){
                    $("#documentVerified_block_"+documentID).remove();
                } else {
                    alert('Error: Please try again!');
                }
            },
            error: function(result){
            alert('Error: Please try again!');
            },
        });
    }
</script>
@endsection