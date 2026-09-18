@if (\Auth::check()) 
    @inject('checkAccess', 'App\Http\Controllers\Controller')
@endif

@extends('layout.admin')
@section('title', "Review DBS application")



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

		<div class="row">
		    <!-- left column -->
		    <div class="col-md-12">
		      <!-- general form elements -->
		      <div class="box box-default">
		        <div class="box-header with-border">

		        	<div class="form-group">
			            <a href="{{ env('APP_URL') }}users/consolidatedSearch"><button type="button" class="btn btn-default pull-left" style="margin-bottom: 10px;">Back</button></a>
			            <div style="float: left; margin:0 0 0 25px; font-size: 24px; font-weight: bold;">DBS details</div> 
			            @if ($DBSApplication->applicationStatus == 1)
			            	<a href="{{ env('APP_URL') }}applicant/modifyDBSApplication/{{$DBSApplication->id}}"><button type="button" class="btn btn-danger pull-right" style="margin-left: 20px;"><i class="fa fa-edit"></i> Send back to user</button></a>
			            @endif
			        </div>
			        <div style="clear: both;"></div>
		            <hr style="padding: 0; margin: 15px -15px !important;" />

		          <h3 class="box-title">Application Details</h3>
		        </div>
		        <!-- /.box-header -->
			
				<div class="box-body">
					<div class="row">
						<div class="col-md-6">
		            		<div class="row">
					            	<div class="col-md-4">
									    <h5 class="strong">Application Owner: </h5>
						            </div>
						            <div class="col-md-8">
						            	{{ucfirst(strtolower($DBSApplication->forename))}} {{ucfirst(strtolower($DBSApplication->middlename))}} {{ucfirst($DBSApplication->presentSurname)}}
						            </div>
					    	</div>
						 </div>
						 <div class="col-md-6">&nbsp;</div>
					</div>

			        <div class="row">
		            	<div class="col-md-6">
		            		<div class="row">
		            			<div class="col-md-4">
				            		<h5 class="strong">Application Status:</h5>
				            	</div>
				            	<div class="col-md-8">
				            		<div class="input-group">
				            			@if ($DBSApplication->applicationStatus == 0)
				                        	<small class="label label-primary"><i class="fa fa-clock-o"></i> Incomplete</small>
					                    @elseif ($DBSApplication->applicationStatus == 1)
					                        <small class="label label-warning">Pending Review</small>
					                    @elseif ($DBSApplication->applicationStatus == 2)
					                        <small class="label label-danger">Review Fail</small>
					                    @elseif ($DBSApplication->applicationStatus == 3)
					                        <small class="label label-warning">Pending Submission</small>
					                    @elseif ($DBSApplication->applicationStatus == 4)
					                        Submitted to DBS
					                    @elseif ($DBSApplication->applicationStatus == 5)
					                        DBS Submission Success
					                    @elseif ($DBSApplication->applicationStatus == 6)
					                        <small class="label label-danger">DBS Fail</small>
					                    @elseif ($DBSApplication->applicationStatus == 8)
					                        <small class="label label-success">{{$DBSApplication->dbsResponse_int023_DisclosureStatus}}</small>
					                    
					                    @endif

				            			@if (\Auth::check()) 
								            @inject('checkAccess', 'App\Http\Controllers\Controller')
								            @if ($checkAccess->checkAccess('siteuser') && !empty($DBSApplication->applicationStatus))
								                @if ($DBSApplication->applicationStatus == 4)
					                    			<span class="input-group-bt" style="margin-left: 10px;"><a href="{{ env('APP_URL') }}applications/checkApplicationsStatus/{{$DBSApplication->id}}"><button type="button" class="btn btn-primary btn-sm">Check Status</button></a></span>
					                    		@elseif ($DBSApplication->applicationStatus == 5)
					                    			<span class="input-group-bt" style="margin-left: 10px;"><a href="{{ env('APP_URL') }}applications/getApplicationResult/{{$DBSApplication->id}}"><button type="button" class="btn btn-success btn-sm">Get Application Result Details</button></a></span>
					                    		@endif
					                    	@endif
					                    @endif
						            </div>
						        </div>
						    </div>

						    <div class="row">
		            			<div class="col-md-4">
				            		<h5 class="strong">Consent provided to RO?</h5>
				            	</div>
				            	<div class="col-md-8">
				            		@if($DBSApplication->dbs_consent) Yes @else<small class="label label-danger">No</small>@endif
						        </div>
						    </div>

						    @if ($DBSApplication->applicationStatus == 4 OR $DBSApplication->applicationStatus == 5)
						    <div class="row">
		            			<div class="col-md-4">
				            		<h5 class="strong">DBS application status:</h5>
				            	</div>
				            	<div class="col-md-8">
				            		<small class="label label-success">{{$DBSApplication->dbsResponse_int025_ApplicationStatus}}</small>
						        </div>
						    </div>
						    @endif

						    <div class="row">
		            			<div class="col-md-4">
				            		<h5 class="strong">Application Reference:</h5>
				            	</div>
				            	<div class="col-md-8">
				            		@if(strlen($DBSApplication->dbsResponse_int022_DBSApplicationFormReference) > 1){{$DBSApplication->dbsResponse_int022_DBSApplicationFormReference}}@else<small class="label label-danger">N/A</small>@endif
						        </div>
						    </div>

						    <div class="row">
		            			<div class="col-md-4">
				            		<h5 class="strong">Cert No and Date:</h5>
				            	</div>
				            	<div class="col-md-8">
				            		@if(strlen($DBSApplication->dbsResponse_int023_DisclosureNumber) > 1)
				            			{{$DBSApplication->dbsResponse_int023_DisclosureNumber}} 
				            			@if(date("Y-m-d", strtotime($DBSApplication->dbsResponse_int023_DisclosureIssueDate)) != '1970-01-01')
				            				{{date("D jS M Y", strtotime($DBSApplication->dbsResponse_int023_DisclosureIssueDate))}}
				            			@endif
				            		@else
				            			<small class="label label-danger">N/A</small>
				            		@endif
						        </div>
						    </div>

						    <div class="row">
		            			<div class="col-md-4">
				            		<h5 class="strong">Purpose for application:</h5>
				            	</div>
				            	<div class="col-md-8">
				            		{{ucfirst($DBSApplication->purpose_of_check)}}
						        </div>
						    </div>

						    <div class="row">
		            			<div class="col-md-4">
				            		<h5 class="strong">Position applied for:</h5>
				            	</div>
				            	<div class="col-md-8">
				            		{{ucfirst($DBSApplication->position_applied_for)}}
						        </div>
						    </div>
		            	</div>


		            	<div class="col-md-6">
						    <div class="row">
		            			<div class="col-md-12">
				            		<h5 class="strong">Employment Sector:</h5>
				            		{{strtoupper($DBSApplication->employment_sector_name)}}
						        </div>
						    </div>

						    <div class="row">
		            			<div class="col-md-12">
				            		<h5 class="strong">Name of Employer:</h5>
				            		{{ucfirst($DBSApplication->name_of_employer)}}
						        </div>
						    </div>

		            	</div>	
					</div>
					<div class="row">
		            	<div class="col-md-12">
		            		<h5 class="strong" id="declaration_report" style="cursor:pointer;">Declaration Details <i class="fa fa-arrow-down"></i></h5>
		            		@if($DBSApplication->terms_accepted)
		            			Terms accepted on <span class="strong">{{date("D jS M Y @ H:i:s", strtotime($DBSApplication->terms_accepted_date))}}
		            		@else
		            			<span class="text-red">Terms not accepted yet!</span>
		            		@endif
		            	</div>
		            </div>
		            
		            <div id="reviewDeclarationBlock" style="display: none;">
			            <div class="row">
			            	<div class="col-md-6">
			            		<p>&nbsp;</p>
					            <h3>Privacy Policy - basics check declaration</h3>
					                I have read the Basic DBS Check Processing Privacy Policy <a href="https://www.gov.uk/government/publications/dbs-privacy-policies" target="_blank">https://www.gov.uk/government/publications/dbs-privacy-policies</a> and I understand how DBS will process my personal data.<br />
								<p>&nbsp;</p>
					            <table style="width: 100%; text-align: left;" class="table table-striped">
			                      	<tr>
			                      		<td style="border: 1px solid black;" class="col-md-9">Applicant must consent by typing I CONFIRM in box A</td>
			                      		<td style="border: 1px solid black;" class="col-md-3">A: <input style="width:120px; float:right;" class="form-control" name="user_privacy_policy_accepted" id="user_privacy_policy_accepted" type="text" value="@if(isset($DBSApplication->privacy_policy) && $DBSApplication->privacy_policy){{'I confirm'}}@endif" ></td>
			                      	</tr>
			                    </table>
			                    <span id="user_privacy_policy_accepted_error_block" class="help-block block-hidden text-red">Can you please confirm by typing "I CONFIRM" or "I confirm" in the above box</span>

			            	</div>
			            </div>

			            <div class="row">
			            	<div class="col-md-6">

					                By confirming your acceptance of the below declaration, you are giving us consent to receive an e-result regarding your basic DBS application. If consent is not given you have the option to complete a basic check via <a href="https://www.gov.uk/DBS" target="_blank">www.gov.uk/DBS</a> and I understand how DBS will process my personal data.<br />
					            <p>&nbsp;</p>
					            <h3>Consent to obtain basic check electronic result</h3>

					                I consent to the DBS providing an electronic result directly to the responsible organisation that has submitted my application. I understand that an electronic result contains a message that indicates either the certificate does not contain criminal record information or to await certificate which will indicate that my certificate contains criminal record information. In some cases the responsible organisation may provide this information directly to my employer prior to me receiving my certificate.<br />

					                I understand if I do not consent to an electronic result being issued to the responsible organisation submitting my application that I must not proceed with this application and I should apply directly to DBS <a href="https://www.gov.uk/request-copy-criminal-record" target="_blank">Request a basic DBS check - GOV.UK (www.gov.uk)</a> I understand that to withdraw my consent whilst my application is in progress I must contact the DBS helpline 03000 200 190. My application will then be withdrawn.<br />

					            <table style="width: 100%; text-align: left;" class="table table-striped">
			                      	<tr>
			                      		<td style="border: 1px solid black;" class="col-md-9">Applicant must consent by typing I AGREE in box B</td>
			                      		<td style="border: 1px solid black;" class="col-md-3">B: <input style="width:120px; float:right;" class="form-control" name="user_consent_basic_check" id="user_consent_basic_check" type="text" value="@if(isset($DBSApplication->consent_basic_check) && $DBSApplication->consent_basic_check){{'I agree'}}@endif" ></td>
			                      	</tr>
			                    </table>
			                    <span id="user_consent_basic_check_error_block" class="help-block block-hidden text-red">Can you please consent by typing "I AGREE" or "I agree" in the above box</span>
			            	</div>
			            </div>

			            <div class="row">
			            	<div class="col-md-6">

					                As the applicant you must explicitly confirm that you have provided complete and true information in support of this application.
					            <p>&nbsp;</p>
					            <h3>Declaration By Applicant</h3>
	
					                I have provided complete and true information in support of the application, and I understand that knowingly making a false statement for this purpose is a criminal offence.<br />

					            <table style="width: 100%; text-align: left;" class="table table-striped">
			                      	<tr>
			                      		<td style="border: 1px solid black;" class="col-md-9">Applicant must consent by typing I CONFIRM in box C</td>
			                      		<td style="border: 1px solid black;" class="col-md-3">C: <input style="width:120px; float:right;" class="form-control" id="user_declaration_by_applicant_accepted" name="user_declaration_by_applicant_accepted" type="text" value="@if(isset($DBSApplication->declaration_by_applicant) && $DBSApplication->declaration_by_applicant){{'I confirm'}}@endif" ></td>
			                      	</tr>
			                    </table>
			                    <span id="user_declaration_by_applicant_accepted_error_block" class="help-block block-hidden text-red">Can you please confirm by typing "I CONFIRM" or "I confirm" in the above box</span>
			            	</div>
			            </div>

			        </div>

					@if(!empty($DBSApplication->dbsResponse_int025_ReceivedDate) || !empty($DBSApplication->dbsResponse_int025_SubmittedForSponsorshipDate) || !empty($DBSApplication->dbsResponse_int025_PoliceNationalComputerSearchDate) || !empty($DBSApplication->dbsResponse_int025_AssembleCertificateDate) || !empty($DBSApplication->dbsResponse_int025_CertificateDespatchedDate) || !empty($DBSApplication->dbsResponse_int025_LocalPoliceSearchDate))
						<hr style="padding: 0; margin: 15px -15px !important;" />
						<div class="row">
			            	<div class="col-md-12">
			            		<h3 class="box-title">Submission Details</h3>
			            	</div>
			            </div>

			            <div class="row">
			            	@if(!empty($DBSApplication->dbsResponse_int025_ReceivedDate))
				            	<div class="col-md-4">
								    <h5 class="strong">Received Date:</h5>
					            	{{date("d/m/Y", strtotime($DBSApplication->dbsResponse_int025_ReceivedDate))}}
					            </div>
				            @endif
				            @if(!empty($DBSApplication->dbsResponse_int025_SubmittedForSponsorshipDate))
				            	<div class="col-md-4">
								    <h5 class="strong">Submitted For Sponsorship Date:</h5>
					            	{{date("d/m/Y", strtotime($DBSApplication->dbsResponse_int025_SubmittedForSponsorshipDate))}}
					            </div>
				            @endif
				            @if(!empty($DBSApplication->dbsResponse_int025_PoliceNationalComputerSearchDate))
				            	<div class="col-md-4">
								    <h5 class="strong">Police National Computer Search Date:</h5>
					            	{{date("d/m/Y", strtotime($DBSApplication->dbsResponse_int025_PoliceNationalComputerSearchDate))}}
					            </div>
				            @endif
				            @if(!empty($DBSApplication->dbsResponse_int025_AssembleCertificateDate))
				            	<div class="col-md-4">
								    <h5 class="strong">Assemble Certificate Date:</h5>
					            	{{date("d/m/Y", strtotime($DBSApplication->dbsResponse_int025_AssembleCertificateDate))}}
					            </div>
				            @endif
				            @if(!empty($DBSApplication->dbsResponse_int025_CertificateDespatchedDate))
				            	<div class="col-md-4">
								    <h5 class="strong">Certificate Dispatched Date:</h5>
					            	{{date("d/m/Y", strtotime($DBSApplication->dbsResponse_int025_CertificateDespatchedDate))}}
					            </div>
				            @endif
				            @if(!empty($DBSApplication->dbsResponse_int025_LocalPoliceSearchDate))
				            	<div class="col-md-4">
								    <h5 class="strong">Local Police Search Date:</h5>
					            	{{date("d/m/Y", strtotime($DBSApplication->dbsResponse_int025_LocalPoliceSearchDate))}}
					            </div>
				            @endif
				            
			            </div>
			        @endif

					<hr style="padding: 0; margin: 15px -15px !important;" />
					
					<div class="row">
		            	<div class="col-md-12">
		            		<h3 class="box-title">Application checklist</h3>
		            	</div>
		            </div>

		            <div class="row" style="font-size:12px; text-align: center;">
		            	<div class="col-md-2">
						    <div class="checkbox">
				              <label>
				                <input type="checkbox" id="admin_x3_ids_uploaded" name="admin_x3_ids_uploaded" value="1" user-id="{{$DBSApplication->userID}}" @if (isset($DBSApplication->admin_x3_ids_uploaded ) && $DBSApplication->admin_x3_ids_uploaded ) checked="checked" @endif>

				              </label>
				            </div>
				            x3 ID's Uploaded
				            <small id="admin_x3_ids_uploaded_confirm" class="label bg-green" style="display:none;">Updated</small>
				            <small id="admin_x3_ids_uploaded_error" class="label bg-red" style="display:none;">Error</small>
				            @if(date("Y-m-d", strtotime($DBSApplication->admin_x3_ids_uploaded_date)) != '1970-01-01')
				            	<small><br />{{date('d/F/Y H:i:s', strtotime($DBSApplication->admin_x3_ids_uploaded_date))}}</small>
				            @endif
			            </div>
			            <div class="col-md-2">
						    <div class="checkbox">
				              <label>
				                <input type="checkbox" id="admin_ids_verified" name="admin_ids_verified" value="1" user-id="{{$DBSApplication->userID}}" @if (isset($DBSApplication->admin_ids_verified ) && $DBSApplication->admin_ids_verified ) checked="checked" @endif>
				              </label>
				            </div>
				            Video ID Verification
				            <small id="admin_ids_verified_confirm" class="label bg-green" style="display:none;">Updated</small>
				            <small id="admin_ids_verified_error" class="label bg-red" style="display:none;">Error</small>
				            @if(date("Y-m-d", strtotime($DBSApplication->admin_ids_verified_date)) != '1970-01-01')
				            	<small><br />{{date('d/F/Y H:i:s', strtotime($DBSApplication->admin_ids_verified_date))}}</small>
				            @endif
			            </div>
		            	<div class="col-md-2">
						    <div class="checkbox">
				              <label>
				                <input type="checkbox" id="admin_personal_ref_sent" name="admin_personal_ref_sent" value="1" user-id="{{$DBSApplication->userID}}" @if (isset($DBSApplication->admin_personal_ref_sent ) && $DBSApplication->admin_personal_ref_sent ) checked="checked" @endif>
				              </label>
				            </div>
				            Emailed Personal References
				            <small id="admin_personal_ref_sent_confirm" class="label bg-green" style="display:none;">Updated</small>
				            <small id="admin_personal_ref_sent_error" class="label bg-red" style="display:none;">Error</small>
				            @if(date("Y-m-d", strtotime($DBSApplication->admin_personal_ref_sent_date)) != '1970-01-01')
				            	<small><br />{{date('d/F/Y H:i:s', strtotime($DBSApplication->admin_personal_ref_sent_date))}}</small>
				            @endif
			            </div>
			            <div class="col-md-2">
						    <div class="checkbox">
				              <label>
				                <input type="checkbox" id="admin_employment_ref_sent" name="admin_employment_ref_sent" value="1" user-id="{{$DBSApplication->userID}}" @if (isset($DBSApplication->admin_employment_ref_sent ) && $DBSApplication->admin_employment_ref_sent ) checked="checked" @endif>
				              </label>
				            </div>
				            Employer Reference Emailed
				            <small id="admin_employment_ref_sent_confirm" class="label bg-green" style="display:none;">Updated</small>
				            <small id="admin_employment_ref_sent_error" class="label bg-red" style="display:none;">Error</small>
				            @if(date("Y-m-d", strtotime($DBSApplication->admin_employment_ref_sent_date)) != '1970-01-01')
				            	<small><br />{{date('d/F/Y H:i:s', strtotime($DBSApplication->admin_employment_ref_sent_date))}}</small>
				            @endif
			            </div>
			            <div class="col-md-1">
						    <div class="checkbox">
				              <label>
				                <input type="checkbox" id="admin_personal_ref1_returned" name="admin_personal_ref1_returned" value="1" user-id="{{$DBSApplication->userID}}" @if (isset($DBSApplication->admin_personal_ref1_returned ) && $DBSApplication->admin_personal_ref1_returned ) checked="checked" @endif>
				              </label>
				            </div>
				            PR #1 Received
				            <small id="admin_personal_ref1_returned_confirm" class="label bg-green" style="display:none;">Updated</small>
				            <small id="admin_personal_ref1_returned_error" class="label bg-red" style="display:none;">Error</small>
				            @if(date("Y-m-d", strtotime($DBSApplication->admin_personal_ref1_returned_date)) != '1970-01-01')
				            	<small><br />{{date('d/F/Y H:i:s', strtotime($DBSApplication->admin_personal_ref1_returned_date))}}</small>
				            @endif
			            </div>
		            	<div class="col-md-1">
						    <div class="checkbox">
				              <label>
				                <input type="checkbox" id="admin_personal_ref2_returned" name="admin_personal_ref2_returned" value="1" user-id="{{$DBSApplication->userID}}" @if (isset($DBSApplication->admin_personal_ref2_returned ) && $DBSApplication->admin_personal_ref2_returned ) checked="checked" @endif> 
				              </label>
				            </div>
				            PR #2 Received
				            <small id="admin_personal_ref2_returned_confirm" class="label bg-green" style="display:none;">Updated</small>
				            <small id="admin_personal_ref2_returned_error" class="label bg-red" style="display:none;">Error</small>
				            @if(date("Y-m-d", strtotime($DBSApplication->admin_personal_ref2_returned_date)) != '1970-01-01')
				            	<small><br />{{date('d/F/Y H:i:s', strtotime($DBSApplication->admin_personal_ref2_returned_date))}}</small>
				            @endif
			            </div>
			            <div class="col-md-2">
						    <div class="checkbox">
				              <label>
				                <input type="checkbox" id="admin_employment_ref_returned" name="admin_employment_ref_returned" value="1" user-id="{{$DBSApplication->userID}}" @if (isset($DBSApplication->admin_employment_ref_returned ) && $DBSApplication->admin_employment_ref_returned ) checked="checked" @endif>
				              </label>
				            </div>
				            EMP Received
				            <small id="admin_employment_ref_returned_confirm" class="label bg-green" style="display:none;">Updated</small>
				            <small id="admin_employment_ref_returned_error" class="label bg-red" style="display:none;">Error</small>
				            @if(date("Y-m-d", strtotime($DBSApplication->admin_employment_ref_returned_date)) != '1970-01-01')
				            	<small><br />{{date('d/F/Y H:i:s', strtotime($DBSApplication->admin_employment_ref_returned_date))}}</small>
				            @endif
			            </div>
		            </div>

					<hr style="padding: 0; margin: 15px -15px !important;" />

					<div class="row">
		            	<div class="col-md-12">
		            		<h3 class="box-title">Contact Details</h3>
		            	</div>
		            </div>

		            <div class="row">
		            	<div class="col-md-4">
						    <h5 class="strong">Email Address:</h5>
			            	{{$DBSApplication->application_email}}
			            </div>
			            <div class="col-md-4">
						    <h5 class="strong">Contact Number:</h5>
						    @if (strlen($DBSApplication->contact_number) > 0)
			            		+{{$DBSApplication->contact_number_country_code}} {{$DBSApplication->contact_number}}
			            	@endif
			            </div>
		            	<div class="col-md-4">
						    <h5 class="strong">Mobile Number:</h5>
						    @if (strlen($DBSApplication->mobile_number) > 0)
			            		+{{$DBSApplication->mobile_number_country_code}} {{$DBSApplication->mobile_number}}
			            	@endif
			            </div>
		            </div>

		            <hr style="padding: 0; margin: 15px -15px !important;" />
					<div class="row">
		            	<div class="col-md-12">
		            		<h3 class="box-title">About Applicant</h3>
		            	</div>
		            </div>
		            <div class="row">
		            	<div class="col-md-6">
		            		<div class="row">
		            			<div class="col-md-12">
				            		<h5 class="strong">Title:</h5>
				            		{{$DBSApplication->userTitle}}
						        </div>
						    </div>

						    <div class="row">
		            			<div class="col-md-12">
				            		<h5 class="strong">Date of Birth:</h5>
				            		{{date("d/m/Y", strtotime($DBSApplication->dob))}}
						        </div>
						    </div>

						    <div class="row">
		            			<div class="col-md-12">
				            		<h5 class="strong">Birth Country:</h5>
				            		{{ucfirst(strtolower($DBSApplication->birth_country_fullName))}}
						        </div>
						    </div>
						    <div class="row">
		            			<div class="col-md-12">
				            		<h5 class="strong">Forename:</h5>
				            		{{ucfirst(strtolower($DBSApplication->forename))}}
						        </div>
						    </div>
						    <div class="row">
		            			<div class="col-md-12">
				            		<h5 class="strong">Middle Names:</h5>
				            		{{ucfirst(strtolower($DBSApplication->middlename))}}
						        </div>
						    </div>
		            	</div>


		            	<div class="col-md-6">
		            		<div class="row">
		            			<div class="col-md-12">
				            		<h5 class="strong">Gender:</h5>
				            		{{ucfirst($DBSApplication->gender)}}
						        </div>
						    </div>

						    <div class="row">
		            			<div class="col-md-12">
				            		<h5 class="strong">Birth Town:</h5>
				            		{{ucfirst(strtolower($DBSApplication->birth_town))}}
						        </div>
						    </div>

						    <div class="row">
		            			<div class="col-md-12">
				            		<h5 class="strong">Birth Nationality:</h5>
				            		{{ucfirst(strtolower($DBSApplication->birth_nationality))}}
						        </div>
						    </div>

						    <div class="row">
		            			<div class="col-md-12">
				            		<h5 class="strong">Present Surname:</h5>
				            		{{ucfirst($DBSApplication->presentSurname)}}
						        </div>
						    </div>

		            	</div>	
					</div>



					<hr style="padding: 0; margin: 15px -15px !important;" />
					<div class="row">
		            	<div class="col-md-12">
		            		<h3 class="box-title">Other Names</h3>
		            	</div>
		            </div>
		            <div class="row">
		            	<div class="col-md-12">
				            @if(isset($DBSApplication->otherNames) && count($DBSApplication->otherNames) > 0)
				            	@foreach ($DBSApplication->otherNames as $otherName)
						            <div class="row">
						            	<div class="col-md-3">
										    <h5 class="strong">Period:</h5>
							            	{{date("d/m/Y", strtotime($otherName->dateFrom))}} - {{date("d/m/Y", strtotime($otherName->dateTo))}}
							            </div>
							            <div class="col-md-3">
										    <h5 class="strong">Forename:</h5>
							            	{{$otherName->other_forename}}
							            </div>
						            	<div class="col-md-3">
										    <h5 class="strong">Middlename:</h5>
							            	{{$otherName->other_middlename}}
							            </div>
							            <div class="col-md-3">
										    <h5 class="strong">Surname:</h5>
							            	{{$otherName->other_surname}}
							            </div>
						            </div>
						        @endforeach
						    @else
						    	<div class="row">
						    		<div class="col-md-3">
						    			No other names recorded
						    		</div>
						    	</div>
						   	@endif
				   		</div>
		            </div>


				   	<hr style="padding: 0; margin: 15px -10px !important;" />
					<div class="row">
		            	<div class="col-md-12">
		            		<h3 class="box-title">Current Address</h3>
		            	</div>
		            </div>

		            <div class="row">
			            <div class="col-md-4">
						    <h5 class="strong">Address Line 1:</h5>
			            	{{$DBSApplication->address_line_1}}
			            </div>
		            	<div class="col-md-4">
						    <h5 class="strong">Address Line 2:</h5>
			            	{{$DBSApplication->address_line_2}}
			            </div>
			            <div class="col-md-4">
						    <h5 class="strong">Town:</h5>
			            	{{ucfirst($DBSApplication->address_town)}}
			            </div>
		            </div>
		            <div class="row">
			            <div class="col-md-4">
						    <h5 class="strong">County:</h5>
			            	{{ucfirst($DBSApplication->address_county)}}
			            </div>
		            	<div class="col-md-4">
						    <h5 class="strong">Postcode:</h5>
			            	{{strtoupper($DBSApplication->address_postcode)}}
			            </div>
			            <div class="col-md-4">
						    <h5 class="strong">Country:</h5>
			            	{{ucfirst(strtolower($DBSApplication->address_country_fullName))}}
			            </div>
		            </div>
		            <div class="row">
			            <div class="col-md-12">
						    <h5 class="strong">Living at address since:</h5>
			            	{{date("d/m/Y", strtotime($DBSApplication->current_address_from))}}
			            </div>
		            </div>
		             

				   	<hr style="padding: 0; margin: 15px -10px !important;" />
					<div class="row">
		            	<div class="col-md-12">
		            		<h3 class="box-title">Previous Addresses</h3>
		            	</div>
		            </div>

		            @if(isset($DBSApplication->previousAddresses) && count($DBSApplication->previousAddresses) > 0)
		            	@foreach ($DBSApplication->previousAddresses as $key => $previousAddress)
		            		@if ($key != 0)
			            		<div class="row">
					            	<div class="col-md-12">
									    &nbsp;
						            </div>
					            </div>
				            @endif

				            <div class="row">
				            	<div class="col-md-12">
								    <span class="strong">Period:</span> 
					            	{{date("d/m/Y", strtotime($previousAddress->previous_address_from))}} - {{date("d/m/Y", strtotime($previousAddress->previous_address_to))}}
					            </div>
				            </div>
				            <div class="row">
					            <div class="col-md-4">
								    <h5 class="strong">Address Line 1:</h5>
					            	{{$previousAddress->previous_address_line_1}}
					            </div>
				            	<div class="col-md-4">
								    <h5 class="strong">Address Line 2:</h5>
					            	{{$previousAddress->previous_address_line_2}}
					            </div>
					            <div class="col-md-4">
								    <h5 class="strong">Town:</h5>
					            	{{ucfirst($previousAddress->previous_address_town)}}
					            </div>
				            </div>
				            <div class="row">
					            <div class="col-md-4">
								    <h5 class="strong">County:</h5>
					            	{{ucfirst($previousAddress->previous_address_county)}}
					            </div>
				            	<div class="col-md-4">
								    <h5 class="strong">Postcode:</h5>
					            	{{strtoupper($previousAddress->previous_address_postcode)}}
					            </div>
					            <div class="col-md-4">
								    <h5 class="strong">Country:</h5>
					            	{{ucfirst(strtolower($previousAddress->previous_address_country_fullName))}}
					            </div>
				            </div>
				        @endforeach
				    @else
				    	<div class="row">
				    		<div class="col-md-3">
				    			No other address recorded
				    		</div>
				    	</div>
				   	@endif



				   	<hr style="padding: 0; margin: 15px -10px !important;" />
					<div class="row">
		            	<div class="col-md-12">
		            		<h3 class="box-title">Supporting Evidence</h3>
		            	</div>
		            </div>
		            <div class="row">
		            	<div class="col-md-6">
		            		<div class="row">
		            			<div class="col-md-12">
				            		<h5 class="strong">National Insurance Number:</h5>
				            		@if (!empty($DBSApplication->supporting_nino)) {{$DBSApplication->supporting_nino}} @else &nbsp; @endif
						        </div>
						    </div>

						    <div class="row">
		            			<div class="col-md-12">
				            		<h5 class="strong">Passport No:</h5>
				            		@if (!empty($DBSApplication->supporting_passport)) {{$DBSApplication->supporting_passport}} @else &nbsp; @endif
						        </div>
						    </div>
		            	</div>


		            	<div class="col-md-6">
		            		<div class="row">
		            			<div class="col-md-12">
				            		<h5 class="strong">Driver Licence Number:</h5>
				            		@if (!empty($DBSApplication->supporting_dln)) {{ucfirst($DBSApplication->supporting_dln)}} @else &nbsp; @endif
						        </div>
						    </div>

						    <div class="row">
		            			<div class="col-md-12">
				            		<h5 class="strong">Passport Issued In:</h5>
				            		@if (!empty($DBSApplication->supporting_passport_country_fullName)) {{ucfirst(strtolower($DBSApplication->supporting_passport_country_fullName))}} @else &nbsp; @endif
						        </div>
						    </div>


		            	</div>	
					</div>

					<hr style="padding: 0; margin: 15px -10px !important;" />
					<div class="row">
		            	<div class="col-md-12">
		            		<h3 class="box-title">Supporting Documents</h3>
		            	</div>
		            </div>
		            <div class="row">
		            	<div class="col-md-6">
		            		@if(isset($DBSApplication->supporting_documents) && count($DBSApplication->supporting_documents)>0)
		            		<table style="width: 100%;">
		              			<thead>
			              			<tr>
			              				<th style="width:90%">Document Name</th>
			              				<th style="width:10%">Download</th>
			              			</tr>
			              		</thead>
			              		<tbody>		              			
				                    @foreach ($DBSApplication->supporting_documents as $document)
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

					<hr style="padding: 0; margin: 15px -10px !important;" />
					<div class="row">
		            	<div class="col-md-12">
		            		<h3 class="box-title">Other Information</h3>
		            	</div>
		            </div>
		            <div class="row">
		            	<div class="col-md-12">
		            		@if (!empty($DBSApplication->supporting_paper_certificate)) 
		            			<p class="strong">Applicant wants to receive a paper certificate</p>
		            			<p><span class="strong">Paper certificate to be posted to: </span> </p>
		            			@if (!empty($DBSApplication->user_paper_certificate_different_address == 0)) 
		            				<span>{{$DBSApplication->address_line_1}} {{$DBSApplication->address_line_2}} {{$DBSApplication->address_town}} {{$DBSApplication->address_county}} {{$DBSApplication->address_postcode}} {{$DBSApplication->address_country_fullName}}</span>
		            			@else 
		            				<span>{{$DBSApplication->certificate_address_recipient_name}} {{$DBSApplication->certificate_address_line_1}} {{$DBSApplication->certificate_address_line_2}} {{$DBSApplication->certificate_address_town}} {{$DBSApplication->certificate_address_county}} {{$DBSApplication->certificate_address_postcode}} {{$DBSApplication->certificate_address_country_fullName}}</span>
		            			@endif
		            		@endif
		            		@if (!empty($DBSApplication->dbs_consent)) <br />
		            			<p class="strong">Consent provided to RO to view the online DBS certificate when it has been issued.</p>
		            			<p><span class="strong">Consent date:</span> {{date("D jS M Y @ H:i:s", strtotime($DBSApplication->dbs_consent_date))}}</p>
		            			<p>
		            				<span class="strong">Third party email address to provide consent to view the DBS certificate once it has been issued:</span><br />
		            				{{$loggedUserOrganisationDetails->organisationEmail}}
		            			</p>
		            		@endif
		            	</div>            	
					</div>


					@if (\Auth::check()) 
			            @inject('checkAccess', 'App\Http\Controllers\Controller')
			            @if ($checkAccess->checkAccess('siteuser'))
			            	@if (!empty($DBSApplication->applicationStatus) && ($DBSApplication->applicationStatus == 0 || $DBSApplication->applicationStatus == 1)) 
			            	<hr style="padding: 0; margin: 15px -10px !important;" />
							<div class="row">
				            	<div class="col-md-12">
				            		<h3 class="box-title">Submit to DBS</h3>
				            	</div>
				            </div>
				            @endif
				            @if (!empty($DBSApplication->applicationStatus) && $DBSApplication->applicationStatus == 1) 
				            <div class="row">
				            	<div class="col-md-12">
				            		<p>You are submitting this application as <span class="strong">{{\Auth::user()->title}} {{\Auth::user()->firstName}} {{\Auth::user()->lastName}}</span> on behalf of <span class="strong">{{$roName}}</span></p>
				            		<p>Have you confirmed that sufficient and appropriate evidence has been supplied in support of this application?</p>

				            		<form id="submitDBS" method="POST" action="{{ env('APP_URL') }}applications/submitToDBS">
		        						{{ csrf_field() }}
		        						<div class="checkbox">
							              <label>
							                <input type="checkbox" id="admin_confirm_valid_nino" name="admin_confirm_valid_nino" value="1" @if (isset($DBSApplication->admin_confirm_valid_nino) && $DBSApplication->admin_confirm_valid_nino) checked="checked" @endif>I have seen evidence regarding the National Insurance Number in support of this application.
							              </label>
							            </div>

							            <div class="checkbox">
							              <label>
							                <input type="checkbox" id="admin_confirm_valid_dln" name="admin_confirm_valid_dln" value="1" @if (isset($DBSApplication->admin_confirm_valid_dln) && $DBSApplication->admin_confirm_valid_dln) checked="checked" @endif>I have seen a valid driving licence in support of this application.
							              </label>
							            </div>

							            <div class="checkbox">
							              <label>
							                <input type="checkbox" id="admin_confirm_valid_passport" name="admin_confirm_valid_passport" value="1" @if (isset($DBSApplication->admin_confirm_valid_passport) && $DBSApplication->admin_confirm_valid_passport) checked="checked" @endif>I have seen a valid passport in support of this application.
							              </label>
							            </div>

							            <br />
							            <div class="col-md-8">
						            		<div class="form-group">
								              	<label for="admin_further_evidence_details">Further details about evidence presented by the applicant</label>
								              	<textarea class="form-control" rows="5"  id="admin_further_evidence_details"  name="admin_further_evidence_details">@if(isset($DBSApplication->admin_further_evidence_details)){{$DBSApplication->admin_further_evidence_details}}@endif</textarea>
									            </div>
						            	</div>
						            	<div class="col-md-12">
							            	<span id="admin_minimum_one_box_error_block" class="help-block text-red block-hidden">You must confirm you have seen at least 2 of the 3 document options listed above or provide further details about the evidence provided.</span>
							            </div>
						            	<br />


		        						<div class="col-md-12">
		        							<label for="user_name_of_employer">Please enter your password to confirm your submission</label>
		        							<div class="input-group input-group-sm col-md-4">
								                <input class="form-control" type="password" name="submit_to_dbs_validation_password" id="submit_to_dbs_validation_password"  autocomplete="false">
							                    <span class="input-group-btn">
							                    	<input type="hidden" name="applicationID" value="{{$DBSApplication->id}}">
							                      <button type="button" class="btn forceBgClassified btn-flat submitToDBS">Submit</button>
							                    </span>
								            </div>
								            <span id="validation_password_error_block" class="help-block text-red block-hidden">You must enter your password to confirm your submission.</span>
							          	</div>
							        </form>


				            	</div>            	
							</div>
							@elseif (!empty($DBSApplication->applicationStatus) && $DBSApplication->applicationStatus == 0)
							<div class="row">
				            	<div class="col-md-12">
				            		<p class="strong">Application is incomplete and cannot yet be submitted</p>
				            	</div>
				            </div>
				            @endif
			            @endif
			        @endif

			        <hr style="padding: 0; margin: 15px -10px !important;" />
			        @if ($DBSApplication->bpssApplication == 1)
			        	<div class="row">
			            	<div class="col-md-12">
			            		<h3 class="box-title">BPSS Forms</h3>
			            	</div>
			            </div>

			        	<div class="row">
				            <div class="col-md-12">
					        	@if (isset($BPSSApplication->id) && !empty($BPSSApplication->id) && $BPSSApplication->applicationStatus == 1)
					         		<a href="{{ env('APP_URL') }}applicant/generateBPSSPDF/{{$BPSSApplication->id}}"  target="_blank"><i class="fa fa-file-pdf-o" style="font-size:24px; color:red"></i> Download BPSS Form</a>
					         	@else 
					         		<i class="fa fa-file-pdf-o" style="font-size:24px; "></i> BPSS From not available yet
					         	@endif
					        </div>
					    </div>

					    <div class="row">
				            <div class="col-md-12">
				            	&nbsp;
				            </div>
				        </div>

					   
			        @endif


				</div>

		      </div>
		      <!-- /.box -->
			</div>
		</div>
	</section>
</div>
@endsection


@section('pageCSS')
<style type="text/css">
.box-title{
	font-size:20px !important;
}
.strong{
	font-weight:bold;
}
.block-hidden{
	display: none;
}
</style>
@endsection

@section('pageJavascript')
@include('applicant.dbsValidations')
<script type="text/javascript">
	$(document).on("click", '#declaration_report', function() {
		$("#reviewDeclarationBlock").show();		
	});

	var checklistValue = 0;
	var userID = 0;

	$(document).on("click", '#admin_x3_ids_uploaded', function() {
		userID = $("#admin_x3_ids_uploaded").attr("user-id");
		if ($('#admin_x3_ids_uploaded').is(':checked')) {
			checklistValue = 1;
		} else {
			checklistValue = 0;
		}
		update_checklist(userID, 'admin_x3_ids_uploaded', checklistValue);		
	});

	$(document).on("click", '#admin_ids_verified', function() {
		userID = $("#admin_ids_verified").attr("user-id");
		if ($('#admin_ids_verified').is(':checked')) {
			checklistValue = 1;
		} else {
			checklistValue = 0;
		}
		update_checklist(userID, 'admin_ids_verified', checklistValue);		
	});

	$(document).on("click", '#admin_personal_ref_sent', function() {
		userID = $("#admin_personal_ref_sent").attr("user-id");
		if ($('#admin_personal_ref_sent').is(':checked')) {
			checklistValue = 1;
		} else {
			checklistValue = 0;
		}
		update_checklist(userID, 'admin_personal_ref_sent', checklistValue);		
	});

	$(document).on("click", '#admin_employment_ref_sent', function() {
		userID = $("#admin_employment_ref_sent").attr("user-id");
		if ($('#admin_employment_ref_sent').is(':checked')) {
			checklistValue = 1;
		} else {
			checklistValue = 0;
		}
		update_checklist(userID, 'admin_employment_ref_sent', checklistValue);		
	});

	$(document).on("click", '#admin_personal_ref1_returned', function() {
		userID = $("#admin_personal_ref1_returned").attr("user-id");
		if ($('#admin_personal_ref1_returned').is(':checked')) {
			checklistValue = 1;
		} else {
			checklistValue = 0;
		}
		update_checklist(userID, 'admin_personal_ref1_returned', checklistValue);		
	});

	$(document).on("click", '#admin_personal_ref2_returned', function() {
		userID = $("#admin_personal_ref2_returned").attr("user-id");
		if ($('#admin_personal_ref2_returned').is(':checked')) {
			checklistValue = 1;
		} else {
			checklistValue = 0;
		}
		update_checklist(userID, 'admin_personal_ref2_returned', checklistValue);		
	});

	$(document).on("click", '#admin_employment_ref_returned', function() {
		userID = $("#admin_employment_ref_returned").attr("user-id");
		if ($('#admin_employment_ref_returned').is(':checked')) {
			checklistValue = 1;
		} else {
			checklistValue = 0;
		}
		update_checklist(userID, 'admin_employment_ref_returned', checklistValue);		
	});



	function update_checklist(userID, itemName, checklistValue){
    $.ajax({
        url: "<?php echo env('APP_URL'); ?>" + "applications/updateChecklist", 
        method: "POST",
        data: {"_token":"{{ csrf_token() }}", "userID":userID,"itemName":itemName, "checklistValue":checklistValue},
        success: function(result){
          if (result == 1){
            $("#"+itemName+"_confirm").show();
          	setTimeout(function() {$("#"+itemName+"_confirm").hide()}, 2000);
          } else {
          	$("#"+itemName+"_error").show();
          	setTimeout(function() {$("#"+itemName+"_error").hide()}, 2000);
          }
        },
        error: function(result){
          $("#"+itemName+"_error").show();
          	setTimeout(function() {$("#"+itemName+"_error").hide()}, 2000);
        },
      });
  }
</script>
@endsection
