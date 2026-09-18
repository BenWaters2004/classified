@extends('layout.default')

@section('title', "DBS Application Step 5")

@section('sidebar')
@endsection

@section('content')
<div class="row">
    <!-- left column -->
    <div class="col-md-12">
      <!-- general form elements -->
      <div class="box box-default">
        <div class="box-header with-border">
          <h3 class="box-title">Complete the form below to apply for security clearance.</h3>
        </div>
        <!-- /.box-header -->

        <div class="row">
        	<div class="col-md-6">
        		<div class="box-body">
        			<div class="progress">
		                <div class="progress-bar progress-bar-primary progress-bar-striped" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width: 90%">
		                  <span class="sr-only">90% Complete (success)</span>
		                </div>
		            </div>
		            <hr />
		            <a href="{{ env('APP_URL') }}applicant/dbsStep4"><button type="button" class="forceBgClassified btn btn-default btn-lg pull-left" style="margin-bottom: 10px;">Previous</button></a>
        		</div>
		        <!-- form start -->
		        <form id="dbsStep5" method="POST" action="{{ env('APP_URL') }}applicant/saveStep5Data" enctype="multipart/form-data">
        			{{ csrf_field() }}
		          <div class="box-body">
			        <button type="button" class="forceBgClassified btn btn-lg pull-right submitStep5" style="margin-bottom: 10px; margin-top: -20px;">Save and Continue</button>
		            <hr />

		            <div id="errorMessageBlock" class="callout callout-danger block-hidden">
		                <p>There were some errors in the information supplied.<br />Please review the fields below to correct the errors.</p>
		            </div>

		            <h3>Supporting Evidence</h3>
		           
		            <div class="row">
		            	<div class="col-md-5">
		            		<div class="form-group">
				              	<label for="user_supporting_nino">National Insurance Number</label>
				              	<div class="row" style="margin-bottom:10px;">
			            			<div class="col-md-12">
						              	<input class="form-control" id="user_supporting_nino" name="user_supporting_nino" type="text" value="{{$DBSApplication->supporting_nino}}" maxlength="9" placeholder="AB123456C">
						              	<span class="help-block block-hidden">NINO must not be more than 9 characters.</span>
						            </div>
						        </div>
					            <div class="row">
					              	<div class="col-md-12">
						              	<p id="temporaryNINOMessage" class="text-red block-hidden">Temporary NINO!</p>
						            </div>
					            </div>
				            </div>
		            	</div>

		            	<div class="col-md-7">
		            		
		            		<div class="form-group">
				              	<label for="user_supporting_dln">Driving License Number</label>
				              	<div class="row" style="margin-bottom:10px;">
			            			<div class="col-md-12">
			            				<select class="form-control" id="supporting_dln_type" name="supporting_dln_type">
						                    <option value="0" @if ($DBSApplication->supporting_dln_type == 0) selected="selected" @endif>Non-UK License</option>
						                    <option value="1" @if (strlen($DBSApplication->supporting_dln_type) == 0 || $DBSApplication->supporting_dln_type == 1) selected="selected" @endif>UK License</option>
					              	</div>
					            </div>
					            <div class="row">
					              	<div class="col-md-12">
					              		<input class="form-control" id="user_supporting_dln" name="user_supporting_dln" type="text" value="{{$DBSApplication->supporting_dln}}" maxlength="18" placeholder="Enter License Number">
					              	</div>
							    </div>
				              	<span  id="user_supporting_dln_error_block" class="help-block block-hidden text-red">Driver License must not be more than 18 characters.</span>
					            <div class="row">
					              	<div class="col-md-12">
					              		
					              	</div>
							    </div>
							    <span  id="user_supporting_dln_issue_date_error_block" class="help-block block-hidden text-red">Please enter the Issue Date</span>
				            </div>
						        
		            	</div>
		            </div>
		            <div class="row">
		            	<div class="col-md-5">
		            		<div class="form-group">
				              	<label for="user_supporting_passport">License Issue Date</label>
			            		<div class="input-group date">
				                    <div class="input-group-addon">
				                    	<i class="fa fa-calendar"></i>
				                    </div>
			              			<input class="form-control" id="user_supporting_dln_issue_date" id="user_supporting_dln_issue_date" name="user_supporting_dln_issue_date" type="text" value="{{date('d/m/Y', strtotime($DBSApplication->supporting_dln_issue_date))}}"  placeholder="dd/mm/yyyy">
			              		</div>
			              	</div>
		              		<span  id="user_supporting_passport_date_error_block" class="help-block block-hidden text-red">Please enter the Issue Date</span>
		              	</div>
		            	<div class="col-md-7">
		            		&nbsp;
		            	</div>

		            </div>
<hr />
		            <div class="row">
		            	<div class="col-md-5">
		            		<div class="form-group">
				              	<label for="user_supporting_passport">Passport Number</label>
				              	<input class="form-control" id="user_supporting_passport" name="user_supporting_passport" type="text" value="{{$DBSApplication->supporting_passport}}"  maxlength="11">
				              	<span class="help-block block-hidden">Passport Number must not be more than 11 characters.</span>
				            </div>
		            	</div>

		            	<div class="col-md-7">
		            		<div class="form-group">
				              	<label for="user_supporting_passport_country">Passport Issue Country</label>
				              	<select class="form-control" id="user_supporting_passport_country" name="user_supporting_passport_country">
				                    @if(isset($countries) && count($countries)>0)
					                    @foreach ($countries as $country)
					                    	<option value="{{$country->iso3}}" @if ($DBSApplication->supporting_passport_country == $country->iso3) selected="selected" @endif>{{$country->nicename}}</option>
					                    @endforeach
					                @endif
				                </select>
				                <span class="help-block block-hidden">You must select the country who issued your passport.</span>
				            </div>
		            	</div>
		            </div>
		            <div class="row">
		            	<div class="col-md-5">
		            		<div class="form-group">
				              	<label for="user_supporting_passport">Passport Issue Date</label>
			            		<div class="input-group date">
				                    <div class="input-group-addon">
				                    	<i class="fa fa-calendar"></i>
				                    </div>
			              			<input class="form-control" id="user_supporting_passport_date" id="user_supporting_passport_date" name="user_supporting_passport_date" type="text" value="{{date('d/m/Y', strtotime($DBSApplication->supporting_passport_date))}}"  placeholder="dd/mm/yyyy">
			              		</div>
			              	</div>
		              		<span  id="user_supporting_passport_date_error_block" class="help-block block-hidden text-red">Please enter the Issue Date</span>
		              	</div>
		            	<div class="col-md-7">
		            		&nbsp;
		            	</div>

		            </div>
<hr />
		            <div class="row">
		            	<div class="col-md-6">
		            		<label for="user_dbs_profile_id_available">Do you have a DBS Profile ID?</label>
			              	<select class="form-control" id="user_dbs_profile_id_available" name="user_dbs_profile_id_available">
			                    <option value="1" @if ($DBSApplication->user_dbs_profile_id_available == 1) selected="selected" @endif>Yes</option>
			                    <option value="0" @if ($DBSApplication->user_dbs_profile_id_available == 0) selected="selected" @endif>No</option>
			                </select>
				        </div>

		            	<div class="col-md-6">
		            		<div class="form-group">
				              <label for="user_dbs_profile_id">DBS Profile ID</label>
				              <input class="form-control" id="user_dbs_profile_id" name="user_dbs_profile_id" type="text" value="{{$DBSApplication->user_dbs_profile_id}}" maxlength="11">
				              <span class="help-block block-hidden">You must enter a profile ID.</span>
				            </div>
		            	</div>
		            </div>

		            <span id="minimum_one_document_error_block" class="help-block text-red block-hidden">You must submit at least one of the 3 documents requested above.</span>
		            <input id="validate_dob" type="hidden" value="{{$DBSApplication->dob}}">
		            <input id="validate_gender" type="hidden" value="{{$DBSApplication->gender}}">
		            <input id="validate_middlename" type="hidden" value="{{$DBSApplication->middlename}}">
		            <hr />

		            <div class="row">
		            	<div class="col-md-12">
		            		<label for="user_upload_supporting_documents">Upload supporting documents</label><br>
							<P><strong>You will need to upload 3 forms of ID to your DBS profile, these ID need to include at least 2 of the following:</strong><br>
							- A valid UK driving licence<br>
							- A valid passport<br>
							- Proof of national insurance: Most recent (year) P60/45, or HMRC (current year) or NI Number letter (accessed from HMRC site)<br>
							- Birth/Marriage Certificate<br><br>
							
							<strong>As well as one of the following:</strong><br>
							- Bank statement from the last 3 months<br>
							- Utility Bill from the last 3 months (not a mobile phone bill/letter)<br>
							- Council Tax Letter dated within the last 12 months.</P><br>
							<p>Please upload the documents that you would like to attach to your application.<br />Only PDF documents and images are accepted!</p>
			              	<div class="row">
			              		<div class="col-md-12">
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

			              	</div>
			            </div>
			        </div>
			        <hr />
			        <div class="row">
		            	<div class="col-md-12">
		            		<div class="row">
	              			<div class="col-md-8">
	              				<label for="user_dbs_profile_id_available">Select document type</label>
					              	<select class="form-control" id="new_document_category" name="new_document_category">
					                    <option value="driving_license">Driving licence</option>
					                    <option value="passport">Passport</option>
					                    <option value="ni_proof_p45">NI proof (P45)</option>
					                    <option value="ni_proof_p60">NI proof (P60)</option>
					                    <option value="ni_proof_payslip">NI proof (Payslip)</option>
					                    <option value="ni_proof_other">NI proof (other)</option>
					                    <option value="other">Other</option>
					                </select>
				              </div>
				            </div>
		              	<label class="custom-file-input" style="width: 100%;" for="new_document">
		              		<div class="row">
		              			<div class="col-md-8">
				              		<small class="label label-primary">Select New Document</small>
				              		<input type="file" name="new_document" id="new_document" paceholder="Select New Document" accept="image/jpeg,image/gif,image/png,application/pdf">
				              	</div>
				              	<div class="col-md-4">
		              				<button type="button" id="upload_document" class="btn btn-danger btn-sm pull-right" style="margin-bottom: 10px; padding-top:8px; padding-bottom:8px;">Upload</button>
		              			</div>
		              		</div>
		              	</label>
				        	</div>
		          </div>
		            <hr />

		            <div class="row">
		            	<div class="col-md-12">
		            		<label for="user_supporting_paper_certificate">Do you want to receive a paper certificate?</label>
			              	<select class="form-control" id="user_supporting_paper_certificate" name="user_supporting_paper_certificate" style="width: 20%;">
			                    <option value="1" @if ($DBSApplication->supporting_paper_certificate == 1) selected="selected" @endif>Yes</option>
			                    <option value="0" @if ($DBSApplication->supporting_paper_certificate == 0) selected="selected" @endif>No</option>
			                </select>
				        </div>
		            </div>

		            <div class="row block-hidden" id="userPaperCertificateAddressOptionsBlock">
		            	<div class="col-md-12">
		            		<div class="form-group">
		            			<div class="row">
		            				<div class="col-md-6">
					                  <div class="radio">
					                    <label>
					                      <input name="user_paper_certificate_different_address" id="certificate_option_current_address" value="0" type="radio" @if ($DBSApplication->user_paper_certificate_different_address == 0) checked="checked" @endif>
					                      Current Address
					                    </label>
					                  </div>
					                </div>
		            				<div class="col-md-6">
					                  <div class="radio">
					                    <label>
					                      <input name="user_paper_certificate_different_address" id="certificate_option_differentt_address" value="1" type="radio" @if ($DBSApplication->user_paper_certificate_different_address == 1) checked="checked" @endif>
					                      Different Address
					                    </label>
					                  </div>
					              </div>
					            </div>
			                </div>
				        </div>

				        <div class="col-md-12" id="currentAddressBlock">
		            		<p class="strong">
		            			{{$DBSApplication->address_line_1}}<br />
		            			{{$DBSApplication->address_line_2}}<br />
		            			{{$DBSApplication->address_town}}<br />
		            			{{$DBSApplication->address_county}}<br />
		            			{{$DBSApplication->address_postcode}}<br />
		            			{{$DBSApplication->address_country_fullName}}
		            		</p>
				        </div>


				        <div class="col-md-12  block-hidden" id="newAddressBlock">

				        	<div class="row">
				            	<div class="col-md-12">
				            		<div class="form-group">
						              	<label for="certificate_address_recipient_name">Recipient Name</label>
						              	<input class="form-control" id="certificate_address_recipient_name" name="certificate_address_recipient_name" type="text" value="{{$DBSApplication->certificate_address_recipient_name}}" maxlength="60">
						              	<span class="help-block block-hidden">Recipient line must contain only letters, space, full-stop or hyphen.</span>
						            </div>
				            	</div>
				            </div>

					        <div class="row">
				            	<div class="col-md-12">
				            		<div class="form-group">
						              	<label for="certificate_address_line_1">Address Line 1</label>
						              	<input class="form-control" id="certificate_address_line_1" name="certificate_address_line_1" type="text" value="{{$DBSApplication->certificate_address_line_1}}" maxlength="60">
						              	<span class="help-block block-hidden">Address line cannot be empty.</span>
						            </div>
				            	</div>
				            </div>

				            <div class="row">
				            	<div class="col-md-12">
				            		<div class="form-group">
						              	<label for="certificate_address_line_2">Address Line 2</label>
						              	<input class="form-control" id="certificate_address_line_2" name="certificate_address_line_2" type="text" value="{{$DBSApplication->certificate_address_line_2}}" maxlength="60">
						              	<span class="help-block block-hidden">Address line must contain only letters, space, full-stop or hyphen.</span>
						            </div>
				            	</div>
				            </div>

				            <div class="row">
				            	<div class="col-md-6">
				            		<div class="form-group">
						              	<label for="certificate_address_town">Town</label>
						              	<input class="form-control" id="certificate_address_town" name="certificate_address_town" type="text" value="{{$DBSApplication->certificate_address_town}}" maxlength="30">
						              	<span class="help-block block-hidden">Town cannot be empty.</span>
						            </div>
				            	</div>

				            	<div class="col-md-6">
				            		<div class="form-group">
						              	<label for="certificate_address_county">County</label>
						              	<input class="form-control" id="certificate_address_county" name="certificate_address_county" type="text" value="{{$DBSApplication->certificate_address_county}}" maxlength="30">
						              	<span class="help-block block-hidden">County must contain only letters, space, full-stop or hyphen.</span>
						            </div>
				            	</div>
				            </div>

				            <div class="row">
				            	<div class="col-md-6">
				            		<div class="form-group">
						              	<label for="certificate_address_postcode">Postcode</label>
						              	<input class="form-control" id="certificate_address_postcode" name="certificate_address_postcode" type="text" value="{{$DBSApplication->certificate_address_postcode}}" maxlength="15">
						              	<span class="help-block block-hidden">Postcode cannot be empty.</span>
						            </div>
				            	</div>

				            	<div class="col-md-6">
				            		<div class="form-group">
						              	<label for="certificate_address_country">Country</label>
						              	<select class="form-control" id="certificate_address_country" name="certificate_address_country">
						                    @if(isset($countries) && count($countries)>0)
							                    @foreach ($countries as $country)
							                    	<option value="{{$country->iso3}}" @if ($DBSApplication->certificate_address_country == $country->iso3) selected="selected" @endif>{{$country->nicename}}</option>
							                    @endforeach
							                @endif
						                </select>
						                <span class="help-block block-hidden">You must select a country.</span>
						            </div>
				            	</div>
				            </div>
			            </div>

			        </div>

			        <div class="row">
		            	<div class="col-md-12">
			            	<div class="checkbox">
				              <label>
				                <input type="checkbox" id="user_checkbox_consent" name="user_dbs_consent" value="1" @if (isset($DBSApplication->dbs_consent) && $DBSApplication->dbs_consent) checked="checked" @endif>
				                Is Consent Provided to RO to see the candidates certificate before the applicant?<br />
				              </label>
				            </div>
				        </div>
		            </div>

		          </div>
		          <!-- /.box-body -->

		          <div class="box-footer">
		            <div class="form-group">
			           <!--  <a href="{{ env('APP_URL') }}applicant/dbsStep4"><button type="button" class="btn btn-default btn-lg pull-left" style="margin-bottom: 10px;">Previous</button></a> -->
			            <button type="button" class="btn forceBgClassified btn-lg pull-right submitStep5" style="margin-bottom: 10px;">Save and Continue</button>
			        </div>
		          </div>
		        </form>
		    </div>

        	<div class="col-md-6">
        		@include('applicant.rightbox')
		    </div>
		</div>


      </div>
      <!-- /.box -->
	</div>
</div>

@endsection


@section('pageCSS')
 
@endsection

@section('pageJavascript')
@include('applicant.dbsValidations')
@endsection
