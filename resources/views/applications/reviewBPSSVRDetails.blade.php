@if (\Auth::check()) 
    @inject('checkAccess', 'App\Http\Controllers\Controller')
@endif

@extends('layout.admin')
@section('title', "BPSS - Verification Record")



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
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>

	<div class="row">
	    <!-- left column -->
	    <div class="col-md-12">
		    <!-- general form elements -->
		    <div class="box box-default">
		        <div class="box-header with-border">

		        	<div class="form-group">
			            <a href="{{ env('APP_URL') }}users/consolidatedSearch"><button type="button" class="btn btn-default pull-left" style="margin-bottom: 10px;">Back</button></a>
			            <div style="float: left; margin:0 0 0 25px; font-size: 24px; font-weight: bold;">BPSS - Verification Record</div> 
			            @if ($BPSSApplication->applicationStatus == 1)
			            	<a href="{{ env('APP_URL') }}applicant/modifyBPSSApplication/{{$BPSSApplication->id}}"><button type="button" class="btn btn-danger pull-right" style="margin-left: 20px;"><i class="fa fa-edit"></i> Send BPSS Form back to user</button></a>
			            @endif
			            @if (isset($BPSSVR->id) && date("Y-m-d",strtotime($BPSSVR->completedDate)) != '1970-01-01')
			            	<a href="{{ env('APP_URL') }}applications/downloadBPSSVRPDF/{{$BPSSVR->id}}" target="_blank"><button type="button" class="btn btn-success pull-right" style="margin-left: 20px;"><i class="fa fa-file-pdf-o"></i> Download BPSS VR PDF</button></a>
			            @else
			            	<button type="button" class="btn btn-danger pull-right" style="margin-left: 20px;" disabled="disabled"><i class="fa fa-file-pdf-o"></i> Save form first to enable Download BPSS VR PDF</button>
			            @endif
			        </div>
		        </div>
		        <form id="saveBPSSVRDetails" method="POST" action="{{ env('APP_URL') }}applications/updateBPSSVRDetails" enctype="multipart/form-data">
		        	{{ csrf_field() }}
			        <div class="box-body">
			        	<div class="row">
	            			<div class="col-md-3">
			            		<h5 class="strong">Approved Access No:</h5>
			            	</div>
			            	<div class="col-md-3">
			            		<input class="form-control" id="approved_access_no" name="approved_access_no" type="text" value="@if(isset($BPSSVR->approved_access_no)) {{$BPSSVR->approved_access_no}} @endif" maxlength="60" required>
					        </div>
					    </div>
					    <div class="row">
	            			<div class="col-md-3">
			            		<h5 class="strong">Expiry:</h5>
			            	</div>
			            	<div class="col-md-3">
			            		@php 
			            			$bpssvr_expiry = date('Y-m-d');
			            			if ($BPSSApplication->employement_type == 'contractor'){
			            				$bpssvr_expiry = date('Y-m-d', strtotime('+3 years'));
			            			} elseif ($BPSSApplication->employement_type == 'employee'){
			            				$bpssvr_expiry = date('Y-m-d', strtotime('+10 years'));
			            			}
			            		@endphp
			            		<input class="form-control" id="bpssvr_expiry" name="bpssvr_expiry" type="text" value="{{date('d/m/Y', strtotime($bpssvr_expiry))}}" disabled="disabled">
					        </div>
					    </div>
					    <hr style="padding: 0; margin: 15px -15px !important;" />
					</div>

			        <div class="box-header with-border bg-gray disabled">		        	
			          <h3 class="box-title">Part 1. Employee/Contractor/Applicant Details</h3>
			        </div>


			        <div class="box-body">
			        	<div class="row">
	            			<div class="col-md-3">
			            		<h5 class="strong">Present Surname:</h5>
			            	</div>
			            	<div class="col-md-3">
			            		{{$DBSApplication->presentSurname}}
					        </div>
					        <div class="col-md-3">
			            		<h5 class="strong">Present Forenames:</h5>
			            	</div>
			            	<div class="col-md-3">
			            		{{$DBSApplication->forename}}@if(strlen($DBSApplication->middlename)>0){{'&nbsp;'.$DBSApplication->middlename}}@endif
					        </div>
					    </div>

					    <div class="row">
			            	<div class="col-md-12">
					            @if(isset($DBSApplication->otherNames) && count($DBSApplication->otherNames) > 0)
					            	@foreach ($DBSApplication->otherNames as $otherName)
							            <div class="row">
								            <div class="col-md-3">
											    <h5 class="strong">Previous Surname:</h5>
								            </div>
								            <div class="col-md-3">
								            	{{$otherName->other_surname}}
								            </div>
							            	<div class="col-md-3">
											    <h5 class="strong">Previous Forename:</h5>
								            </div>
								            <div class="col-md-3">
								            	{{$otherName->other_forename}}{{$otherName->other_middlename}}@if(strlen($otherName->other_middlename)>0){{'&nbsp;'.$otherName->other_middlename}}@endif
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

			            <div class="row">
	            			<div class="col-md-3">
			            		<h5 class="strong">Address:</h5>
			            	</div>
			            	<div class="col-md-9">
			            		{{$DBSApplication->address_line_1}} {{$DBSApplication->address_line_2}}, {{ucfirst($DBSApplication->address_town)}}, {{ucfirst($DBSApplication->address_county)}}, {{ucfirst(strtolower($DBSApplication->address_country_fullName))}}
					        </div>
					    </div>
					    <div class="row">
	            			<div class="col-md-3">
			            		<h5 class="strong">Contact Telephone Number:</h5>
			            	</div>
			            	<div class="col-md-3">
			            		@if (strlen($DBSApplication->contact_number) > 0)
				            		+{{$DBSApplication->contact_number_country_code}} {{$DBSApplication->contact_number}}
				            	@endif
					        </div>
					        <div class="col-md-3">
			            		<h5 class="strong">Postcode:</h5>
			            	</div>
			            	<div class="col-md-3">
			            		{{strtoupper($DBSApplication->address_postcode)}}
					        </div>
					    </div>

					    <div class="row">
	            			<div class="col-md-3">
			            		<h5 class="strong">Photograph:</h5>
			            		<label class="custom-file-input" for="new_photograph"><small class="label label-primary">Select New Photograph</small><input type="file" name="new_photograph" id="new_photograph" paceholder="Select New Photograph" accept="image/*"></label>

			            		
			            	</div>
			            	<div class="col-md-3">
			            		@if(@getimagesize(Storage::disk('public_images')->url('bpssvr_photos/'.$BPSSVR->photo_path)))
									<img class="imgProfile" src="{{ Storage::disk('public_images')->url('bpssvr_photos/'.$BPSSVR->photo_path) }}" width="120" />
								@else
			            		<img class="profile-user-img img-responsive img-circle" src="{{ env('APP_URL') }}images/insert-picture-here.png" alt="Applicant picture" style="height: 100px; width:100px;">
			            		@endif
					        </div>
					    </div>
					    <hr style="padding: 0; margin: 15px -15px !important;" />
					</div>

					<div class="box-header with-border bg-gray disabled">		        	
			          <h3 class="box-title">Part 2. Nationality and Immigration Status</h3>
			        </div>


			        <div class="box-body">
			        	<div class="row">
	            			<div class="col-md-3">
			            		<h5 class="strong">Date Of Birth:</h5>
			            	</div>
			            	<div class="col-md-3">
			            		{{date("d/m/Y", strtotime($DBSApplication->dob))}}
					        </div>
					        <div class="col-md-3">
			            		<h5 class="strong">Town of Birth:</h5>
			            	</div>
			            	<div class="col-md-3">
			            		{{$DBSApplication->birth_town}}
					        </div>
					    </div>

					    <div class="row">
	            			<div class="col-md-3">
			            		<h5 class="strong">Country of Birth:</h5>
			            	</div>
			            	<div class="col-md-3">
			            		{{$DBSApplication->birth_country_fullName}}
					        </div>
					        <div class="col-md-3">
			            		<h5 class="strong">Nationality:</h5>
			            	</div>
			            	<div class="col-md-3">
			            		{{$BPSSApplication->present_nationality}}
					        </div>
					    </div>
					    <div class="row">
	            			<div class="col-md-3">
			            		<h5 class="strong">Dual Nationality:</h5>
			            	</div>
			            	<div class="col-md-3">
			            		@if(isset($BPSSApplication->dualNationalities) && count((array)$BPSSApplication->dualNationalities) > 0)
				            		@foreach ($BPSSApplication->dualNationalities as $key => $extraNationality)
				            			@if($key>0){{', '}}@endif{{$extraNationality->dual_citizenship_country_fullName}}
				            		@endforeach
			            		@endif
					        </div>
					        <div class="col-md-3">
			            		<h5 class="strong">Former Nationality:</h5>
			            	</div>
			            	<div class="col-md-3">
			            		{{$BPSSApplication->former_nationality_details}}
					        </div>
					    </div>
					    <div class="row">
	            			<div class="col-md-3">
			            		<h5 class="strong">British Naturalisation Certificate No.</h5>
			            	</div>
			            	<div class="col-md-3">
			            		{{$BPSSApplication->naturalisation_certificate_number}}
					        </div>
					        <div class="col-md-3">
			            		<h5 class="strong">Date Issued:</h5>
			            	</div>
			            	<div class="col-md-3">
			            		@if(date("Y-m-d", strtotime($BPSSApplication->naturalisation_certificate_date)) !='1970-01-01')
			            			{{date("d/m/Y", strtotime($BPSSApplication->naturalisation_certificate_date))}}
			            		@endif
					        </div>
					    </div>
					    <div class="row">
	            			<div class="col-md-3">
			            		<h5 class="strong">Dual Citizenship:</h5>
			            	</div>
			            	<div class="col-md-3">
			            		<div class="checkbox">
					              <label>
					                <input type="checkbox" disabled="disabled" @if($BPSSApplication->dual_citizenship) checked="checked" @endif>Yes &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					                <input type="checkbox" disabled="disabled" @if(isset($BPSSApplication->dual_citizenship) && empty($BPSSApplication->dual_citizenship)) checked="checked" @endif>No
					              </label>
					            </div>

					        </div>
					        <div class="col-md-3">
			            		<h5 class="strong">Lawfully in the UK:</h5>
			            	</div>
			            	<div class="col-md-3">
			            		<div class="checkbox">
					              <label>
				            		<input type="checkbox" disabled="disabled" @if($BPSSApplication->lawfully_resident_in_uk) checked="checked" @endif>Yes &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				            		<input type="checkbox" disabled="disabled" @if(isset($BPSSApplication->lawfully_resident_in_uk) && empty($BPSSApplication->lawfully_resident_in_uk)) checked="checked" @endif>No
				            	  </label>
					            </div>
					        </div>
					    </div>
					    <div class="row">
	            			<div class="col-md-3">
			            		<h5 class="strong">Restrictions on continued residence in the UK:</h5>
			            		<div class="checkbox">
					              <label>
					                <input type="checkbox" disabled="disabled" @if($BPSSApplication->continued_residence_restrictions) checked="checked" @endif>Yes &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					                <input type="checkbox" disabled="disabled" @if(isset($BPSSApplication->continued_residence_restrictions) && empty($BPSSApplication->continued_residence_restrictions)) checked="checked" @endif>No
					              </label>
					            </div>
			            	</div>
			            	<div class="col-md-3">
			            		@if($BPSSApplication->continued_residence_restrictions)
				            		<h5 class="strong">Details:</h5>
				            		{{$BPSSApplication->continued_residence_restrictions_details}}
			            		@endif
					        </div>
					        <div class="col-md-3">
			            		<h5 class="strong">Subject to Immigration Control:</h5>
			            		<div class="checkbox">
					              <label>
				            		<input type="checkbox" disabled="disabled" @if($BPSSApplication->subject_to_immigration_control) checked="checked" @endif>Yes &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				            		<input type="checkbox" disabled="disabled" @if(isset($BPSSApplication->subject_to_immigration_control) && empty($BPSSApplication->subject_to_immigration_control)) checked="checked" @endif>No
				            	  </label>
					            </div>
			            	</div>
			            	<div class="col-md-3">
			            		@if($BPSSApplication->subject_to_immigration_control)
				            		<h5 class="strong">Details:</h5>
				            		{{$BPSSApplication->subject_to_immigration_control_details}}
			            		@endif
					        </div>
					    </div>
					    <div class="row">
	            			<div class="col-md-3">
			            		<h5 class="strong">Restrictions on continued freedom to take employment in the UK:</h5>
			            		<div class="checkbox">
					              <label>
					                <input type="checkbox" disabled="disabled" @if($BPSSApplication->freedom_to_take_employment) checked="checked" @endif>Yes &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					                <input type="checkbox" disabled="disabled" @if(isset($BPSSApplication->freedom_to_take_employment) && empty($BPSSApplication->freedom_to_take_employment)) checked="checked" @endif>No
					              </label>
					            </div>
			            	</div>
			            	<div class="col-md-3">
			            		@if($BPSSApplication->freedom_to_take_employment)
				            		<h5 class="strong">Details:</h5>
				            		{{$BPSSApplication->freedom_to_take_employment_details}}
			            		@endif
					        </div>
					        <div class="col-md-3">
			            		<h5 class="strong">Home Office / Port Reference Number:</h5>
			            	</div>
			            	<div class="col-md-3">
			            		{{$BPSSApplication->ho_port_reference_number}}
					        </div>
					    </div>

					    <hr style="padding: 0; margin: 15px -15px !important;" />
					</div>

					<div class="box-header with-border bg-gray disabled">		        	
			          <h3 class="box-title">Part 2a. Verification of Identity</h3>
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

			            		@if(isset($DBSApplication->supporting_passport) && strlen($DBSApplication->supporting_passport) > 0)
			            			<div class="row">
			                    		<div class="col-md-6">Passport ({{$DBSApplication->supporting_passport.': '.$DBSApplication->supporting_passport_country_fullName }})</div>
			                    		<div class="col-md-3"><input class="form-control" id="passport_date_of_issue" name="passport_date_of_issue" type="text" value="@if (isset($BPSSVR->passport_date_of_issue) && strlen($BPSSVR->passport_date_of_issue) == 10 && date('Y-m-d', strtotime($BPSSVR->passport_date_of_issue)) != '1970-01-01' ){{date('d/m/Y', strtotime($BPSSVR->passport_date_of_issue))}}@endif" placeholder="dd/mm/yyyy" ></div>
			                    		<div class="col-md-3" style=" padding-left:0;">
			                    			<button  type="button"  class="btn btn-danger pull-left" style="margin-bottom:5px;" disabled="disabled"><i class="icon fa fa-remove"></i> Delete</button>
			                    		</div>
					            		<hr />
			                    	</div>
			            		@endif
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

					    <hr style="padding: 0; margin: 15px -15px !important;" />
					</div>

					<div class="box-header with-border bg-gray disabled">		        	
			      <h3 class="box-title">Part 2b. Supporting Documents</h3>
			    </div>
					<div class="box-body">
			      <div class="col-md-12">
              	<div class="row">
              		<div class="col-md-8">
	              		<table style="width: 100%;">
	              			<thead>
		              			<tr>
		              				<th style="width:70%">Document Name</th>
		              				<th style="width:20%">Document Type</th>
		              				<th style="width:10%">&nbsp;</th>
		              			</tr>
		              		</thead>
		              		<tbody id="supporting_documents_list">
	              			@if(isset($DBSApplication->supporting_documents) && count($DBSApplication->supporting_documents)>0)
			                    @foreach ($DBSApplication->supporting_documents as $document)
			                    	<tr id="supporting_document_row_{{$document->id}}">
				              			<td>
				              				@if(isset($document->document_type) && $document->document_type == 'pdf')
				              					<i class="fa fa-file-pdf-o" style="color: #FF0000; margin-right: 10px;" aria-hidden="true"></i>
				              				@elseif(isset($document->document_type) && $document->document_type == 'img')
				              					<i class="fa fa-file-image-o" style="color: #0000FF; margin-right: 10px;" aria-hidden="true"></i>
				              				@else
				              					<i class="fa fa-file-o" style="color: #FF00FF; margin-right: 10px;" aria-hidden="true"></i>
				              				@endif

				              				@if(isset($document->document_name) && !empty($document->document_name))
				              					{{$document->document_name}}
				              				@else
				              					Unknown name
				              				@endif
				              			</td>
				              			<td>
				              				@if(isset($document->document_category) && !empty($document->document_category))
				              					@php
					              				switch($document->document_category){
																    case("driving_license"):
																        echo 'Driving licence';
																        break;
																    case("passport"):
																        echo 'Passport';
																        break;
																    case("ni_proof_p45"):
																        echo 'NI proof (P45)';
																        break;
																    case("ni_proof_p60"):
																        echo 'NI proof (P60)';
																        break;
																    case("ni_proof_payslip"):
																        echo 'NI proof (Payslip)';
																        break;
																    case("ni_proof_other"):
																        echo 'NI proof (other)';
																        break;
																    case("other"):
																        echo 'Misc';
																        break;
																    default:
																        echo 'N/A';
																}
																@endphp
				              				@else
				              					N/A
				              				@endif
				              			</td>
				              			<td>
				              				<button id="remove_document_{{$document->id}}" doc-id="{{$document->id}}" type="button"  class="btn btn-danger pull-right" style="margin-bottom:5px;"><i class="icon fa fa-remove"></i></button>
				              			</td>
				              		</tr>
			                    @endforeach
			                @endif
		              		</tbody>
	              		</table>
	              	</div>
			            	<div class="col-md-4">
			            		<div class="row">
		              			<div class="col-md-12">
		              				<label for="user_upload_supporting_documents">Upload supporting documents</label>
              						<p>Only PDF documents and images are accepted!</p>
		              				<label for="user_dbs_profile_id_available">Select document type</label>
						              	<select class="form-control" id="new_document_category" name="new_document_category">
						                    <option value="driving_license">Driving licence</option>
						                    <option value="passport">Passport</option>
						                    <option value="ni_proof_p45">NI proof (P45)</option>
						                    <option value="ni_proof_p60">NI proof (P60)</option>
						                    <option value="ni_proof_payslip">NI proof (Payslip)</option>
						                    <option value="ni_proof_other">NI proof (other)</option>
						                    <option value="other">Other</option>
						                </select><br />&nbsp;<br />
					              </div>
					            </div>
			              	<label class="custom-file-input" style="width: 100%;" for="new_document">
			              		<div class="row">
			              			<div class="col-md-8">
					              		<small class="label label-primary">Select New Document</small>
					              		<input type="file" name="new_document" id="new_document" paceholder="Select New Document" accept="image/jpeg,image/gif,image/png,application/pdf">
					              	</div>
					              	<div class="col-md-4">
			              				<button type="button" id="upload_document" class="btn btn-danger btn-sm pull-right" style="margin-bottom: 10px; padding-top:2px; padding-bottom:2px;">Upload</button>
			              			</div>
			              		</div>
			              	</label>
					        	</div>
			          </div>

            </div> 	

					<hr style="padding: 0; margin: 15px -15px !important;" />
					</div>

					<div class="box-header with-border bg-gray disabled">		        	
			          <h3 class="box-title">Part 3. Reference Details</h3>
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
			            	@if(isset($BPSSVR_reference1->referee_name) && strlen($BPSSVR_reference1->referee_name)>0)
		            			<div class="col-md-3"><div class="with-border bg-gray disabled" style="width:10%; float:left; padding: 5px; text-align: center;">1.</div>
		            				<input style="width:88%; float:right;" class="form-control" id="reference1_referee_name" name="reference1_referee_name" type="text" value="@if(isset($BPSSVR_reference1->referee_name)){{$BPSSVR_reference1->referee_name}}@endif">
		            			</div>
		            			<div class="col-md-1">
		            				<input class="form-control" id="reference1_referee_relationship" name="reference1_referee_relationship" type="text" value="@if(isset($BPSSVR_reference1->referee_relationship)){{$BPSSVR_reference1->referee_relationship}}@endif">
		            			</div>
		            			<div class="col-md-2">
		            				<input class="form-control" id="reference1_referee_email" name="reference1_referee_email" type="text" value="@if(isset($BPSSVR_reference1->referee_email)){{$BPSSVR_reference1->referee_email}}@endif">
		            			</div>
		            			<div class="col-md-2">
		            				<input class="form-control" id="reference1_referee_address" name="reference1_referee_address" type="text" value="@if(isset($BPSSVR_reference1->referee_address)){{$BPSSVR_reference1->referee_address}}@endif">
		            			</div>
		            			<div class="col-md-2">
		            				<input class="form-control" id="reference1_referee_length_of_association" name="reference1_referee_length_of_association" type="text" value="@if(isset($BPSSVR_reference1->referee_length_of_association)){{$BPSSVR_reference1->referee_length_of_association}}@endif">
		            			</div>
		            			<div class="col-md-1">
		            				@if(isset($BPSSVR_reference1->referee_email))
		            				<button type="button" class="btn btn-success btn-flat emailReference1" style="padding: 5px 10px" data-toggle="modal" data-target="#emailModal" data-application-id="{{ $BPSSApplication->id }}" data-button-id="emailReference1" id="emailReference1" data-reference-email="@if(isset($BPSSVR_reference1->referee_email)){{$BPSSVR_reference1->referee_email}}@endif"><i class="fa fa-envelope"></i> Email</button>
		            				@endif
		            			</div>
		            			<div class="col-md-1">
		            				<!--
		            				<button type="button" class="btn btn-warning btn-flat uploadReference1PDF"><i class="fa fa-file-pdf-o"></i> Upload</button>
		            				-->

		            			</div>
		            			<input name="reference1_id" type="hidden" value="{{$BPSSVR_reference1->id}}">
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
			            	@if(isset($BPSSVR_reference2->referee_name) && strlen($BPSSVR_reference2->referee_name)>0)
		            			<div class="col-md-3"><div class="with-border bg-gray disabled" style="width:10%; float:left; padding: 5px; text-align: center;">2.</div>
		            				<input style="width:88%; float:right;" class="form-control" id="reference2_referee_name" name="reference2_referee_name" type="text" value="@if(isset($BPSSVR_reference2->referee_name)){{$BPSSVR_reference2->referee_name}}@endif">
		            			</div>
		            			<div class="col-md-1">
		            				<input class="form-control" id="reference2_referee_relationship" name="reference2_referee_relationship" type="text" value="@if(isset($BPSSVR_reference2->referee_relationship)){{$BPSSVR_reference2->referee_relationship}}@endif">
		            			</div>
		            			<div class="col-md-2">
		            				<input class="form-control" id="reference2_referee_email" name="reference2_referee_email" type="text" value="@if(isset($BPSSVR_reference2->referee_email)){{$BPSSVR_reference2->referee_email}}@endif">
		            			</div>
		            			<div class="col-md-2">
		            				<input class="form-control" id="reference2_referee_address" name="reference2_referee_address" type="text" value="@if(isset($BPSSVR_reference2->referee_address)){{$BPSSVR_reference2->referee_address}}@endif">
		            			</div>
		            			<div class="col-md-2">
		            				<input class="form-control" id="reference2_referee_length_of_association" name="reference2_referee_length_of_association" type="text" value="@if(isset($BPSSVR_reference2->referee_length_of_association)){{$BPSSVR_reference2->referee_length_of_association}}@endif">
		            			</div>
		            			<div class="col-md-1">
		            				@if(isset($BPSSVR_reference2->referee_email))
		            				<button type="button" class="btn btn-success btn-flat emailReference2" style="padding: 5px 10px" data-toggle="modal" data-target="#emailModal" data-button-id="emailReference2" id="emailReference2" data-reference-email="@if(isset($BPSSVR_reference2->referee_email)){{$BPSSVR_reference2->referee_email}}@endif" data-application-id="{{ $BPSSApplication->id }}" ><i class="fa fa-envelope"></i> Email</button>
		            				@endif
		            			</div>
		            			<div class="col-md-1">
		            				<!--
		            				<button type="button" class="btn btn-warning btn-flat uploadReference2PDF"><i class="fa fa-file-pdf-o"></i> Upload</button>
		            			-->
		            			</div>
		            			<input name="reference2_id" type="hidden" value="{{$BPSSVR_reference2->id}}">
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
			            	@if(isset($BPSSVR_reference3->referee_name) && strlen($BPSSVR_reference3->referee_name)>0)
		            			<div class="col-md-3"><div class="with-border bg-gray disabled" style="width:10%; float:left; padding: 5px; text-align: center;">3.</div>
		            				<input style="width:88%; float:right;" class="form-control" id="reference3_referee_name" name="reference3_referee_name" type="text" value="@if(isset($BPSSVR_reference3->referee_name)){{$BPSSVR_reference3->referee_name}}@endif">
		            			</div>
		            			<div class="col-md-1">
		            				<input class="form-control" id="reference3_referee_relationship" name="reference3_referee_relationship" type="text" value="@if(isset($BPSSVR_reference3->referee_relationship)){{$BPSSVR_reference3->referee_relationship}}@endif">
		            			</div>
		            			<div class="col-md-2">
		            				<input class="form-control" id="reference3_referee_email" name="reference3_referee_email" type="text" value="@if(isset($BPSSVR_reference3->referee_email)){{$BPSSVR_reference3->referee_email}}@endif">
		            			</div>
		            			<div class="col-md-2">
		            				<input class="form-control" id="reference3_referee_address" name="reference3_referee_address" type="text" value="@if(isset($BPSSVR_reference3->referee_address)){{$BPSSVR_reference3->referee_address}}@endif">
		            			</div>
		            			<div class="col-md-2">
		            				<input class="form-control" id="reference3_referee_length_of_association" name="reference3_referee_length_of_association" type="text" value="@if(isset($BPSSVR_reference3->referee_length_of_association)){{$BPSSVR_reference3->referee_length_of_association}}@endif">
		            			</div>
		            			<div class="col-md-1">
		            				@if(isset($BPSSVR_reference3->referee_email))
		            				<!--
		            				<button type="button" class="btn btn-success btn-flat emailReference3" style="padding: 5px 10px" data-toggle="modal" data-target="#emailModal" data-button-id="emailReference3" id="emailReference3" data-reference-email="@if(isset($BPSSVR_reference3->referee_email)){{$BPSSVR_reference3->referee_email}}@endif" data-application-id="{{ $BPSSApplication->id }}" ><i class="fa fa-envelope"></i> Email</button>
		            			-->
		            				@endif
		            			</div>
		            			<div class="col-md-1">
		            				<!--
		            				<button type="button" class="btn btn-warning btn-flat uploadReference3PDF"><i class="fa fa-file-pdf-o"></i> Upload</button>
		            			-->
		            			</div>
		            			<input name="reference3_id" type="hidden" value="{{$BPSSVR_reference3->id}}">
		            		@else
		            			<div class="col-md-3"><div class="with-border bg-gray disabled" style="width:10%; float:left; padding: 5px; text-align: center;">3.</div>
		            				<input style="width:88%; float:right;" class="form-control" id="reference3_referee_name" name="reference3_referee_name" type="text" value="">
		            			</div>
		            			<div class="col-md-1">
		            				<input class="form-control" id="reference3_referee_relationship" name="reference3_referee_relationship" type="text" value="">
		            			</div>
		            			<div class="col-md-2">
		            				<input class="form-control" id="reference3_referee_email" name="reference3_referee_email" type="text" value="">
		            			</div>
		            			<div class="col-md-2">
		            				<input class="form-control" id="reference3_referee_address" name="reference3_referee_address" type="text" value="">
		            			</div>
		            			<div class="col-md-2">
		            				<input class="form-control" id="reference3_referee_length_of_association" name="reference3_referee_length_of_association" type="text" value="">
		            			</div>
		            			<div class="col-md-1">
		            				&nbsp;
		            			</div>
		            			<div class="col-md-1">
		            				&nbsp;
		            			</div>
		            			<input name="reference3_id" type="hidden" value="0">
		            		@endif
			            </div>
			            <div class="row">
	            			<div class="col-md-12">&nbsp</div>
	            		</div>
			            <div class="row">
	            			<div class="col-md-6">
	            				@if(isset($referenceRecords) && intval($referenceRecords)>0)	
	            					<a href="{{ env('APP_URL') }}applications/loadReferenceLog/{{$BPSSVR->id}}" target="_blank">            				
		            					<button type="button" class="btn btn-warning btn-flat viewReferences"><i class="fa fa-search"></i> View Reference Details</button>
		            				</a>
            					@else 
            						<button type="button" class="btn btn-danger btn-flat" disabled="disabled" style="width:100% !important;"><i class="fa fa-exclamation-triangle"></i> No References Requested</button>
            					@endif
	            			</div>
			            </div>

			            
			            <hr style="padding: 0; margin: 15px -15px !important;" />
					</div>

					<div class="box-header with-border bg-gray disabled">		        	
			          <h3 class="box-title">Part 3a. Other Information</h3>
			        </div>


			        <div class="box-body">
			        	<div class="row">
			            	<div class="col-md-12">
			            		<div class="row">
			            			<div class="col-md-6">
			            				<div class="checkbox">
							              <label>
							                <input type="checkbox" value="1" name="verification_of_national_status_received"  @if(isset($BPSSVR->verification_of_national_status_received) && $BPSSVR->verification_of_national_status_received) checked="checked" @endif>Verification of Nationality Status Received
							              </label>
							            </div>
			            			</div>
			            			<div class="col-md-6">
			            				<div class="checkbox">
							              <label>
							                <input type="checkbox" value="1" name="last_years_employemnt_confirmed"  @if(isset($BPSSVR->last_years_employemnt_confirmed) && $BPSSVR->last_years_employemnt_confirmed) checked="checked" @endif>Last 3 years employment confirmed
							              </label>
							            </div>
			            			</div>
			            		</div>
			            	</div>
			            </div>

			            <div class="row">
			            	<div class="col-md-12">
			            		<div class="row">
			            			<div class="col-md-6">
			            				<div class="checkbox">
							              <label>
							                <input type="checkbox" value="1" name="academic_qualifications_received"  @if(isset($BPSSVR->academic_qualifications_received) && $BPSSVR->academic_qualifications_received) checked="checked" @endif>Academic qualifications received
							              </label>
							            </div>
			            			</div>
			            			<div class="col-md-6">
			            				<div class="checkbox">
							              <label>
							                <input type="checkbox" value="1" name="document_received_support_employment_history"  @if(isset($BPSSVR->document_received_support_employment_history) && $BPSSVR->document_received_support_employment_history) checked="checked" @endif>P60/P45 Received to support Employment History
							              </label>
							            </div>
			            			</div>
			            		</div>
			            	</div>
			            </div>

			            <div class="row">
			            	<div class="col-md-12">
			            		<div class="row">
			            			<div class="col-md-6">
			            				<div class="checkbox">
							              <label>
							                <input type="checkbox" value="1" name="denied_party_screening"  @if(isset($BPSSVR->denied_party_screening) && $BPSSVR->denied_party_screening) checked="checked" @endif>Denied Party Screening
							              </label>
							            </div>
			            			</div>
			            			<div class="col-md-6">
			            				<div class="form-group">
						                  <div class="radio">
						                    <label>
						                      <input type="radio" name="items_of_interest" id="items_of_interest" value="0" @if(!isset($BPSSVR->items_of_interest) || empty($BPSSVR->items_of_interest)) checked="checked" @endif> No Items of Interest
						                    </label>
						                  </div>
						                  <div class="radio">
						                    <label>
						                      <input type="radio" name="items_of_interest" id="items_of_interest" value="1" @if(isset($BPSSVR->items_of_interest) && $BPSSVR->items_of_interest) checked="checked" @endif> Items recorded see attached report
						                    </label>
						                  </div>
						                </div>
			            			</div>
			            		</div>
			            	</div>
			            </div>

			            <div class="row">
			            	<div class="col-md-12">
			            		<div class="row">
			            			<div class="col-md-6">
			            				<div class="checkbox">
							              <label>
							                <input type="checkbox" value="1" name="security_matrix"  @if(isset($BPSSVR->security_matrix) && $BPSSVR->security_matrix) checked="checked" @endif>Security Matrix
							              </label>
							            </div>
			            			</div>
			            			<div class="col-md-6">
			            				<div class="form-group">
						                  <div class="radio">
						                    <label>
						                      <input type="radio" name="clearance_type" id="bpss_clearance" value="bpss_clearance" @if(isset($BPSSVR->clearance_type) && $BPSSVR->clearance_type =='bpss_clearance') checked="checked" @endif> BPSS Clearance
						                    </label>
						                  </div>
						                  <div class="radio">
						                    <label>
						                      <input type="radio" name="clearance_type" id="sc_clearance" value="sc_clearance" @if(isset($BPSSVR->clearance_type) && $BPSSVR->clearance_type == 'sc_clearance') checked="checked" @endif> SC Clearance
						                    </label>
						                  </div>
						                </div>
			            			</div>
			            		</div>
			            	</div>
			            </div>
			        	<hr style="padding: 0; margin: 15px -15px !important;" />
					</div>

					<div class="box-body">
			            <div class="row">
			            	<div class="col-md-12">

			            		<table class="table">
					                <tbody>
						                <tr>
						                  <td colspan="4"  style="border: 1px solid #000000; color: #FF0000;">I certify that in accordance with the requirements of the Baseline Personnel Security Standard: I have personally examined the documents listed in Part 2 and Part 3 above and have satisfactorily established the details listed.</td>
						                </tr>
						                <tr>
						                  <th style="width: 15%; border: 1px solid #000000;">Name:</th>
						                  <td style="width: 35%; border: 1px solid #000000;">
						                  	@if (isset($BPSSVR->admin_approved_by) && !empty($BPSSVR->admin_approved_by)) 123
						                  		{{\Auth::user()->title}} {{\Auth::user()->firstName}} {{\Auth::user()->lastName}}
						                  	@endif
						                  </td>
						                  <th style="width: 15%; border: 1px solid #000000;">Post:</th>
						                  <td style="width: 35%; border: 1px solid #000000;">

						                  	@if(strlen(\Auth::user()->position)>0)
						                  	<!--	{{\Auth::user()->position}} -->
						                  	@else
						                  		<input class="form-control" id="admin_certify_post" name="admin_certify_post" type="text" value="@if(isset($BPSSVR->admin_certify_post) && strlen($BPSSVR->admin_certify_post)>0){{$BPSSVR->admin_certify_post}}@endif" required="required">
						                  	@endif
						                  </td>
						                </tr>
						                <tr>
						                  <th style="width: 15%; border: 1px solid #000000;">Date:</th>
						                  <td style="width: 35%; border: 1px solid #000000;">{{date('d/m/Y')}}</td>
						                  <th style="width: 15%; border: 1px solid #000000;">Telephone Number:</th>
						                  <td style="width: 35%; border: 1px solid #000000;">
						                  	@if(strlen(\Auth::user()->position)>0)
						                  		{{\Auth::user()->phoneNumber}}
						                  	@else
						                  		<input class="form-control" id="admin_certify_telephone_number" name="admin_certify_telephone_number" type="text" value="@if(isset($BPSSVR->admin_certify_telephone_number) && strlen($BPSSVR->admin_certify_telephone_number)>0){{$BPSSVR->admin_certify_telephone_number}}@endif" required="required">
						                  	@endif
						                  </td>
						                </tr>
						                <tr>
						                  <th style="width: 15%; border: 1px solid #000000;">Signature:</th>
						                  <td colspan="3" style="width: 35%; border: 1px solid #000000;">
						                  	@if (isset($BPSSVR->admin_approved_by) && !empty($BPSSVR->admin_approved_by))
						                  		Document was signed electronically by {{$BPSSVR->adminCompletedFullName}} on @if(isset($BPSSVR->completedDate) && date('Y-m-d',strtotime($BPSSVR->completedDate)) != '1970-01-01'){{\Carbon\Carbon::parse($BPSSVR->completedDate)->format('d/m/Y')}}@endif 
						                  		@if(strlen($BPSSVR->adminSignaturePath)>0)
						                  		
						                  			<img src="{{ env('APP_URL') }}downloadfile/signature/{{$BPSSVR->admin_approved_by}}/2" height="40px">
											            @endif
						                  	@else
							                  	Document will be electronically signed using the date: {{date('d/m/Y')}}
							                  @endif
							                </td>
						                </tr>
					                </tbody>
					            </table>

			            	</div>
			            </div>

					    <hr style="padding: 0; margin: 15px -15px !important;" />
					</div>


					<div class="box-header with-border bg-gray disabled">		        	
			          <h3 class="box-title">Part 4. Criminal Record Check</h3>
			        </div>


			        <div class="box-body">
			        	<div class="row">
			            	<div class="col-md-12">

			            		<table class="table">
					                <tbody>
						                <tr>
						                  <td colspan="4"  style="border: 1px solid #000000;">Recorded below are details of the external verification of unspent criminal record details using Disclosure Scotland or Disclosure Barring Service (DBS)</td>
						                </tr>
						                <tr>
						                  <th style="width: 20%; border: 1px solid #000000;">Type of Disclosure</th>
						                  <th style="width: 20%; border: 1px solid #000000;">Date Issue</th>
						                  <th style="width: 25%; border: 1px solid #000000;">Certificate Reference No</th>
						                  <th style="width: 35%; border: 1px solid #000000;">Comments</th>
						                </tr>
						                <tr>
						                  <td style="border: 1px solid #000000;">
						                  	<div class="form-group">
							                  <div class="radio">
							                    <label>
							                      <input type="radio" disabled="disabled" name="type_of_disclosure" id="type_of_disclosure_basic" value="basic" 
							                      @if(isset($BPSSVR->type_of_disclosure) && $BPSSVR->type_of_disclosure =='basic') checked="checked" 
							                      @elseif(isset($DBSApplication->dbsResponse_int023_DisclosureType) && $DBSApplication->dbsResponse_int023_DisclosureType =='basic') checked="checked" 
							                      @endif> Basic
							                    </label>
							                  </div>
							                  <div class="radio">
							                    <label>
							                      <input type="radio" disabled="disabled" name="type_of_disclosure" id="type_of_disclosure_basic_standard" value="standard" 
							                      @if(isset($BPSSVR->type_of_disclosure) && $BPSSVR->type_of_disclosure == 'standard') checked="checked"
							                      @elseif(isset($DBSApplication->dbsResponse_int023_DisclosureType) && $DBSApplication->dbsResponse_int023_DisclosureType =='standard') checked="checked" 
							                      @endif> Standard/Enhanced
							                    </label>
							                  </div>
							                </div>
						                  </td>
						                  <td style="border: 1px solid #000000;">@if(isset($DBSApplication->dbsResponse_int023_DisclosureIssueDate) && date('Y-m-d',strtotime($DBSApplication->dbsResponse_int023_DisclosureIssueDate)) != '1970-01-01'){{\Carbon\Carbon::parse($DBSApplication->dbsResponse_int023_DisclosureIssueDate)->format('d/m/Y')}}@endif</td>
						                  <td style="border: 1px solid #000000;">
						                  	<div class="form-group">
						                  		<input class="form-control" disabled="disabled" id="disclosure_certificate_reference_no" name="disclosure_certificate_reference_no" type="text" value="@if(strlen($DBSApplication->dbsResponse_int023_DisclosureNumber)>0){{$DBSApplication->dbsResponse_int023_DisclosureNumber}}@endif">
						                  	</div>
						                  </td>
						                  <td style="border: 1px solid #000000;">
						                  	<div class="form-group">
							                  <div class="radio">
							                    <label>
							                      <input type="radio" disabled="disabled" name="disclosure_comments" id="disclosure_comments_no_convictions" value="no_convictions_for_diclosure" 
							                      @if(isset($BPSSVR->disclosure_comments) && $BPSSVR->disclosure_comments == 'no_convictions_for_diclosure') checked="checked"
							                      @elseif(isset($DBSApplication->dbsResponse_int023_DisclosureStatus) && trim($DBSApplication->dbsResponse_int023_DisclosureStatus) =='Certificate contains no information') checked="checked"
							                      @endif> No Convictions for disclosure
							                    </label>
							                  </div>
							                  <div class="radio">
							                    <label>
							                      <input type="radio" disabled="disabled" name="disclosure_comments" id="disclosure_comments_see_notes" value="disclosure_comments_see_notes" 
							                      @if(isset($BPSSVR->disclosure_comments) && $BPSSVR->disclosure_comments =='see_notes_for_further_explanation') checked="checked" 
							                      @elseif(isset($DBSApplication->dbsResponse_int023_DisclosureStatus) && trim($DBSApplication->dbsResponse_int023_DisclosureStatus) =='Please wait to view applicant certificate')
							                      @endif> See notes for further explanations
							                    </label>
							                  </div>
							                </div>
						                  </td>
						                </tr>
					                </tbody>
					            </table>

			            	</div>
			            </div>
			            <hr style="padding: 0; margin: 15px -15px !important;" />
					</div>


					<div class="box-header with-border bg-gray disabled" style="color: #FF0000;">		        	
			          <h1 class="box-title">APPROVAL FOR ACCESS</h1>
			          <h3 class="box-title">(To be completed by the Site Security Controller)</h3>
			        </div>


			        <div class="box-body">
			        	<div class="row">
			            	<div class="col-md-12">
			            		<p style="color: #FF0000;">I certify that in accordance with the requirements of Baseline Security Personnel Standard that I, the undersigned have personally examined, or seen signed confirmation of, the candidates documents provided as evidence and am satisfied meets the BPSS requirements as specified by the cabinet office.</p>
			            	</div>
			            </div>
			            <div class="row">
			            	<div class="col-md-12" style="font-size: 30px;">
			            		<label><input type="radio" name="status" id="status_approved" value="approved" @if(isset($BPSSVR->status) && $BPSSVR->status == 'approved') checked="checked" @endif> Approved</label>
			            		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
							    <label><input type="radio" name="status" id="status_denied" value="denied" @if(isset($BPSSVR->status) && $BPSSVR->status =='denied') checked="checked" @endif> Denied</label>
			            	</div>
			            </div>

			            <div class="row">
			            	<div class="col-md-12">

			            		<table class="table">
					                <tbody>
						                <tr>
						                  <th style="width: 15%; border: 1px solid #000000;">Name:</th>
						                  <td style="width: 35%; border: 1px solid #000000;"><input class="form-control" id="admin_approval_name" name="admin_approval_name" type="text" value="@if(isset($BPSSVR->admin_approval_name) && strlen($BPSSVR->admin_approval_name)>0){{$BPSSVR->admin_approval_name}}@endif" required="required">
						                  </td>
						                  <th style="width: 15%; border: 1px solid #000000;">Title:</th>
						                  <td style="width: 35%; border: 1px solid #000000;">
						                  		<input class="form-control" id="admin_approval_title" name="admin_approval_title" type="text" value="@if(isset($BPSSVR->admin_approval_title) && strlen($BPSSVR->admin_approval_title)>0){{$BPSSVR->admin_approval_title}}@endif" required="required">
						                  </td>
						                </tr>
						                <tr>
						                  <th style="border: 1px solid #000000;">Date:</th>
						                  <td colspan="3" style="border: 1px solid #000000;">{{date('d/m/Y')}}</td>
						                </tr>
						                <tr>
						                  <th colspan="4" style="border: 1px solid #000000;">
							                  Notes:<br />
							                  <textarea class="form-control" rows="5"  id="admin_approval_notes"  name="admin_approval_notes">@if(isset($BPSSVR->admin_approval_notes)){{$BPSSVR->admin_approval_notes}}@endif</textarea>
							              </th>
						                </tr>
						                <tr>
						                  <td colspan="2" style="border: 1px solid #000000;">
						                  	<label><input type="radio" name="employement_type" id="employement_type_employee" value="employee" 
						                  		@if(isset($BPSSVR->employement_type) && $BPSSVR->employement_type == 'employee') checked="checked" 
						                  		@elseif( (!isset($BPSSVR->employement_type) || strlen($BPSSVR->employement_type)==0) && $BPSSApplication->employement_type == 'employee') checked="checked" 
						                  		@endif> Employee (10 years)</label>
						            		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
										    <label><input type="radio" name="employement_type" id="employement_type_contractor" value="contractor" 
										    	@if(isset($BPSSVR->employement_type) && $BPSSVR->employement_type =='contractor') checked="checked" 
						                  		@elseif( (!isset($BPSSVR->employement_type) || strlen($BPSSVR->employement_type)==0) && $BPSSApplication->employement_type == 'contractor') checked="checked" 
						                  		@endif> Contractor (3 years)</label>
						                  </td>
						                  <td colspan="2" style="border: 1px solid #000000;">
						                  	<label><input type="radio" name="application_type" id="application_type_initial_clearance" value="initial_clearance" 
						                  		@if(isset($BPSSVR->application_type) && $BPSSVR->application_type == 'initial_clearance') checked="checked" 
						                  		@elseif( (!isset($BPSSVR->application_type) || strlen($BPSSVR->application_type)==0) && $BPSSApplication->form_type == 'initial_clearance') checked="checked" 
						                  		@endif> Initial Clearance</label>
						            		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
										    <label><input type="radio" name="application_type" id="application_type_revalidation" value="revalidation" 
										    	@if(isset($BPSSVR->application_type) && $BPSSVR->application_type =='revalidation') checked="checked" 
						                  		@elseif( (!isset($BPSSVR->application_type) || strlen($BPSSVR->application_type)==0) && $BPSSApplication->form_type == 'revalidation') checked="checked" 
						                  		@endif> Revalidation</label>
						                  </td>
						                </tr>
						                <tr>
						                  <th style="border: 1px solid #000000;">Site:</th>
						                  <td style="border: 1px solid #000000;">{{$BPSSApplication->organisationName}}</td>
						                  <th style="border: 1px solid #000000;">Known Cargo:</th>
						                  <td style="border: 1px solid #000000;">
						                  	<div class="form-group" style="margin-bottom: 0;">

							                    <label>
							                      <input type="radio" name="known_cargo" id="known_cargo" value="yes" @if(isset($BPSSVR->known_cargo) && $BPSSVR->known_cargo == 'yes') checked="checked" @endif> Yes&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
							                    </label>

							                    <label>
							                      <input type="radio" name="known_cargo" id="known_cargo" value="no" @if(isset($BPSSVR->known_cargo) && $BPSSVR->known_cargo == 'no') checked="checked" @endif> No
							                    </label>

							                </div>
						                  </td>
						                </tr>
						                <tr>
						                  <th style="border: 1px solid #000000;">Contractor Company:</th>
						                  <td style="border: 1px solid #000000;">
						                  	<input class="form-control" id="contractor_company" name="contractor_company" type="text" value="@if(isset($BPSSVR->contractor_company) && strlen(trim($BPSSVR->contractor_company))>0){{$BPSSVR->contractor_company}}@elseif(isset($BPSSApplication->contractor_name_of_company) && strlen(trim($BPSSApplication->contractor_name_of_company))>0){{$BPSSApplication->contractor_name_of_company}}@endif">
						                  </td>
						                  <th style="border: 1px solid #000000;">Position:</th>
						                  <td style="border: 1px solid #000000;">
						                  	<input class="form-control" id="contractor_position" name="contractor_position" type="text" value="@if(isset($BPSSVR->contractor_position) && strlen(trim($BPSSVR->contractor_position))>0){{$BPSSVR->contractor_position}}@elseif(isset($BPSSApplication->contractor_position) && strlen(trim($BPSSApplication->contractor_position))>0){{$BPSSApplication->contractor_position}}@endif">
						                  </td>
						                </tr>
					                </tbody>
					            </table>

			            	</div>
			            </div>

			            <div class="row">
				            <div class="col-md-12">
			            		<p style="color: #FF0000;">
									<strong>Important: Data Protection Act (2018) and GDPR.</strong> This form contains "personal data" as defined by the Data Protection Act 2018 and the General Data Protection Regulation (GDPR). It has been supplied to the appropriate HR or Security authority exclusively for the purpose of the Baseline Personnel Security Standard (BPSS). Information relating to the applicant will not be shared with any unauthorised personnel. Copies of ID provided will only be kept during the process of the BPSS and then securely destroyed on completion of this process. Copies of ID provided will only be kept during the process of the BPSS and then securely destroyed on completion of this process.
									</p>
				            </div>
				        </div>
			            <hr style="padding: 0; margin: 15px -15px !important;" />
								
								@if ($BPSSVR->candidateStatus == 0)								
			            <div class="row">
				            <div class="col-md-1">
				            	@if ($checkAccess->checkAccess('superuser') || (\Auth::user()->id == 434) || (\Auth::user()->id == 312))
				          			@if ($BPSSVR->formStatus == 0)
				        					<button type="button" class="btn btn-warning btn-flat updateBPSSVR">Save Changes</button>
				        				@endif
											@endif
				          	</div>
				          	<div class="col-md-9">
				          		@if ($checkAccess->checkAccess('superuser') && date("Y-m-d",strtotime($BPSSVR->completedDate)) != '1970-01-01')
				          			@if ($BPSSVR->formStatus == 0)
												<button type="button" class="btn btn-success btn-flat" id="markAsComplete" application-id="{{$applicationID}}" user-id="{{$BPSSApplication->userID}}">Mark application for final review</button>
												@else
													<button type="button" class="btn btn-flat" disabled="disabled">Application is ready for review</button>
												@endif
											@elseif ($checkAccess->checkAccess('superuser') && date("Y-m-d",strtotime($BPSSVR->completedDate)) == '1970-01-01')
												<button type="button" class="btn btn-flat" disabled="disabled">You need to save the application first before you can mark it ready for review</button>
											@elseif ($checkAccess->checkAccess('siteuser'))
												@if ($BPSSVR->formStatus == 1)
													<button type="button" class="btn btn-danger btn-flat" id="markAsCompleteSite" application-id="{{$applicationID}}" user-id="{{$BPSSApplication->userID}}">Mark application as complete</button>
												@else
													<button type="button" class="btn btn-flat" disabled="disabled">Application not ready for review</button>
												@endif
											@endif
				          	</div>
				        	</div>
				        @elseif ($checkAccess->checkAccess('siteuser') && $BPSSVR->candidateStatus == 1)
				        	<button type="button" class="btn btn-danger btn-flat" id="resetBPSSVRCompletion" application-id="{{$applicationID}}" user-id="{{$BPSSApplication->userID}}">Reset Application Completion</button>
				        @endif 

				        <input type="hidden" name="formSubmitted" value="1" />
				        <input type="hidden" name="userID" value="{{$BPSSApplication->userID}}" />
				        <input type="hidden" id="applicationID" value="{{$applicationID}}" />

			          	<hr style="padding: 0; margin: 15px -15px !important;" />
					</div>
				</form>

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
 display:none;
}
</style>
@endsection

@section('pageJavascript')
@include('applications.bpssVRValidations')
<script type="text/javascript">
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

  $(document).on("click", "#markAsComplete", function() {
    var applicationId = $("#markAsComplete").attr("application-id");
    var userID = $("#markAsComplete").attr("user-id");

    $.ajax({
      url: "<?php echo env('APP_URL'); ?>" + "applications/markApplicationForReview", 
      method: "POST",
      data: {"_token":"{{ csrf_token() }}", "userID":userID, "applicationId":applicationId},
      success: function(result){
        window.location.href = "<?php echo env('APP_URL'); ?>" + "applications/reviewBPSSVRDetails/" + userID;
      },
      error: function(result){
        window.location.href = "<?php echo env('APP_URL'); ?>" + "applications/reviewBPSSVRDetails/" + userID;
      },
    });
  });

  $(document).on("click", "#markAsCompleteSite", function() {
    var applicationId = $("#markAsCompleteSite").attr("application-id");
    var userID = $("#markAsCompleteSite").attr("user-id");

    $.ajax({
      url: "<?php echo env('APP_URL'); ?>" + "applications/markApplicationCompleteSite", 
      method: "POST",
      data: {"_token":"{{ csrf_token() }}", "userID":userID, "applicationId":applicationId},
      success: function(result){
        window.location.href = "<?php echo env('APP_URL'); ?>" + "applications/reviewBPSSVRDetails/" + userID;
      },
      error: function(result){
        window.location.href = "<?php echo env('APP_URL'); ?>" + "applications/reviewBPSSVRDetails/" + userID;
      },
    });
  });

  $(document).on("click", "#resetBPSSVRCompletion", function() {
    var applicationId = $("#resetBPSSVRCompletion").attr("application-id");
    var userID = $("#resetBPSSVRCompletion").attr("user-id");

    $.ajax({
      url: "<?php echo env('APP_URL'); ?>" + "applications/resetVrCompletion", 
      method: "POST",
      data: {"_token":"{{ csrf_token() }}", "userID":userID, "applicationId":applicationId},
      success: function(result){
        window.location.href = "<?php echo env('APP_URL'); ?>" + "applications/reviewBPSSVRDetails/" + userID;
      },
      error: function(result){
        window.location.href = "<?php echo env('APP_URL'); ?>" + "applications/reviewBPSSVRDetails/" + userID;
      },
    });
  });


  
  function updateUserRole(userID, roleID, updateType){
    
  }
 
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
</script>
@endsection


