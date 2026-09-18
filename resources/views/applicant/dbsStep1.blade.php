@extends('layout.default')

@section('title', "DBS Application Step 1")

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
		        <!-- form start -->
		        <form id="dbsStep1" method="POST" action="{{ env('APP_URL') }}applicant/saveStep1Data">
        			{{ csrf_field() }}
		          <div class="box-body">

		          	<div class="progress">
		                <div class="progress-bar progress-bar-primary progress-bar-striped" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width: 10%">
		                  <span class="sr-only">0% Complete (success)</span>
		                </div>
		            </div>
		            <hr />
		            <div class="form-group">
			            <button type="button" class="forceBgClassified btn btn-lg pull-right submitStep1" style="margin-bottom: 10px;">Save and Continue</button>
			        </div>
		            <hr />

					<h3>Notice</h3>
					<p><strong>To complete this DBS check you will need:</strong><br>
					- You will need your National Insurance Number,<br>
					- Driving Licence Number and Issue date,<br>
					- Passport Number and Issue Date<br><br>

					<strong>You will also need to upload 3 forms of ID to your DBS profile, these ID need to include at least 2 of the following:</strong><br>
					- A valid UK driving licence<br>
					- A valid passport<br>
					- Proof of national insurance: Most recent (year) P60/45, or HMRC (current year) or NI Number letter (accessed from HMRC site)<br>
					- Birth/Marriage Certificate<br><br>
					
					<strong>As well as one of the following:</strong><br>
					- Bank statement from the last 3 months<br>
					- Utility Bill from the last 3 months (not a mobile phone bill/letter)<br>
					- Council Tax Letter dated within the last 12 months.
					</p>

		            <div id="errorMessageBlock" class="callout callout-danger block-hidden">
		                <p>There were some errors in the information supplied.<br />Please review the fields below to correct the errors.</p>
		            </div>

		            <h3>Purpose for application</h3>
		            <div class="row">
		            	<div class="col-md-12">
		            		<div class="form-group">
				              <select class="form-control" id="user_purpose_of_check" name="user_purpose_of_check">
			                    <!-- <option value="" @if(!isset($DBSApplication->purpose_of_check) || empty($DBSApplication->purpose_of_check)) selected="selected" @endif>Purpose of Check</option>
			                    <option value="personal interest" @if(isset($DBSApplication->purpose_of_check) && strtolower($DBSApplication->purpose_of_check) == 'personal interest') selected="selected" @endif>Personal Interest</option> -->
			                    <option value="employment" @if(isset($DBSApplication->purpose_of_check) && strtolower($DBSApplication->purpose_of_check) == 'employment') selected="selected" @endif>Employment</option>
			                    <!-- <option value="other" @if(isset($DBSApplication->purpose_of_check) && strtolower($DBSApplication->purpose_of_check) == 'other') selected="selected" @endif>Other</option> -->
			                  </select>
			                  <span class="help-block block-hidden">Please select an option</span>
				            </div>
		            	</div>
		            </div>

		            <div id="employmentSectorBlock" class="block-hidden">
			            <div class="row">
			            	<div class="col-md-12">
			            		<div class="form-group">
			            		  <label for="user_employment_sector">Employment sector</label>
					              <select class="form-control" id="user_employment_sector" name="user_employment_sector">
				                   <!--  <option value="">Employment sector</option> -->
				                    @if(isset($employmentSectors) && count($employmentSectors)>0)
										@foreach ($employmentSectors as $employmentSector)
											<option value="{{ $employmentSector->id }}" 
												@if(isset($DBSApplication->employment_sector) && $DBSApplication->employment_sector == $employmentSector->id) 
													selected="selected" 
												@endif>
												{{ $employmentSector->employment_sector_name }}
											</option>
										@endforeach
					                @endif
				                  </select>
				                  <span class="help-block block-hidden">Please select the employment sector</span>
					            </div>
			            	</div>
			            </div>

		            

			            <div class="row">
			            	<div class="col-md-6">
			            		<div class="form-group">
					              	<label for="user_position_applied_for">Position applied for</label>
					              	<input class="form-control" id="user_position_applied_for" name="user_position_applied_for" type="text" value="@if(isset($DBSApplication->position_applied_for)) {{$DBSApplication->position_applied_for}} @endif" maxlength="60">
					              	<span class="help-block block-hidden">Cannot be blank</span>
					            </div>
			            	</div>

			            	<div class="col-md-6">
			            		<div class="form-group">
					              	<label for="user_name_of_employer">Name of Employer</label>
					              	<input class="form-control" id="user_name_of_employer" name="user_name_of_employer" type="text" value="@if(isset($DBSApplication->name_of_employer)) {{$DBSApplication->name_of_employer}} @endif" maxlength="60">
					              	<span class="help-block block-hidden">Cannot be blank</span>
					            </div>
			            	</div>

			            </div>
			        </div>


		            <hr>

			        <div id="declarationEmploymentBlock" class="block-hidden">
			            <div class="row">
			            	<div class="col-md-12">
			            		<p><strong>Please complete the declaration with your explicit consent typed into boxes A, B & C below</strong>, confirming that you consent to BluescreenIT Ltd on behalf of your company accessing your Personal Data and providing to Disclosure & Barring Service (DBS), <strong>we are not able to review and submit your application to DBS without your written consent.</strong></p>
			            		<p>The Privacy policy below outlines how your personal data will be used by DBS (Disclosure and Barring Service) and outlines your rights under the General Data Protection Regulation.</p>
			            	</div><br />
			            </div>

				        

			            <div class="row">
			            	<div class="col-md-12">
			            		<p>&nbsp;</p>
					            <h3>Privacy Policy - basics check declaration</h3>
					                I have read the Basic DBS Check Processing Privacy Policy <a href="https://www.gov.uk/government/publications/dbs-privacy-policies" target="_blank">https://www.gov.uk/government/publications/dbs-privacy-policies</a> and I understand how DBS will process my personal data.<br />
								<p>&nbsp;</p>
					            <table style="width: 100%; text-align: left;" class="table table-striped">
			                      	<tr>
			                      		<td style="border: 1px solid black;" class="col-md-9">Applicant must consent by typing I CONFIRM in box A</td>
			                      		<td style="border: 1px solid black;" class="col-md-3">A: <input style="width:90px; float:right;" class="form-control" name="user_privacy_policy_accepted" id="user_privacy_policy_accepted" type="text" value="@if(isset($DBSApplication->privacy_policy) && $DBSApplication->privacy_policy){{'I confirm'}}@endif" ></td>
			                      	</tr>
			                    </table>
			                    <span id="user_privacy_policy_accepted_error_block" class="help-block block-hidden text-red">Can you please confirm by typing "I CONFIRM" or "I confirm" in the above box</span>

			            	</div>
			            </div>

			            <div class="row">
			            	<div class="col-md-12">

					                By confirming your acceptance of the below declaration, you are giving us consent to receive an e-result regarding your basic DBS application. If consent is not given you have the option to complete a basic check via <a href="https://www.gov.uk/DBS" target="_blank">www.gov.uk/DBS</a> and I understand how DBS will process my personal data.<br />
					            <p>&nbsp;</p>
					            <h3>Consent to obtain basic check electronic result</h3>

					                I consent to the DBS providing an electronic result directly to the responsible organisation that has submitted my application. I understand that an electronic result contains a message that indicates either the certificate does not contain criminal record information or to await certificate which will indicate that my certificate contains criminal record information. In some cases the responsible organisation may provide this information directly to my employer prior to me receiving my certificate.<br />
					                I understand if I do not consent to an electronic result being issued to the responsible organisation submitting my application that I must not proceed with this application and I should apply directly to DBS <a href="https://www.gov.uk/request-copy-criminal-record" target="_blank">Request a basic DBS check - GOV.UK (www.gov.uk)</a> I understand that to withdraw my consent whilst my application is in progress I must contact the DBS helpline 03000 200 190. My application will then be withdrawn.<br />

					            <table style="width: 100%; text-align: left;" class="table table-striped">
			                      	<tr>
			                      		<td style="border: 1px solid black;" class="col-md-9">Applicant must consent by typing I AGREE in box B</td>
			                      		<td style="border: 1px solid black;" class="col-md-3">B: <input style="width:90px; float:right;" class="form-control" name="user_consent_basic_check" id="user_consent_basic_check" type="text" value="@if(isset($DBSApplication->consent_basic_check) && $DBSApplication->consent_basic_check){{'I agree'}}@endif" ></td>
			                      	</tr>
			                    </table>
			                    <span id="user_consent_basic_check_error_block" class="help-block block-hidden text-red">Can you please consent by typing "I AGREE" or "I agree" in the above box</span>
			            	</div>
			            </div>

			            <div class="row">
			            	<div class="col-md-12">

					                As the applicant you must explicitly confirm that you have provided complete and true information in support of this application.
					            <p>&nbsp;</p>
					            <h3>Declaration By Applicant</h3>
	
					                I have provided complete and true information in support of the application, and I understand that knowingly making a false statement for this purpose is a criminal offence.<br />

					            <table style="width: 100%; text-align: left;" class="table table-striped">
			                      	<tr>
			                      		<td style="border: 1px solid black;" class="col-md-9">Applicant must consent by typing I CONFIRM in box C</td>
			                      		<td style="border: 1px solid black;" class="col-md-3">C: <input style="width:90px; float:right;" class="form-control" id="user_declaration_by_applicant_accepted" name="user_declaration_by_applicant_accepted" type="text" value="@if(isset($DBSApplication->declaration_by_applicant) && $DBSApplication->declaration_by_applicant){{'I confirm'}}@endif" ></td>
			                      	</tr>
			                    </table>
			                    <span id="user_declaration_by_applicant_accepted_error_block" class="help-block block-hidden text-red">Can you please confirm by typing "I CONFIRM" or "I confirm" in the above box</span>
			            	</div>
			            </div>

			        </div>
			        <h3>Please Note</h3>
			        <div class="row">
			            	<div class="col-md-12">
			            		<p>You must provide complete and true information in support of the application and you understand that knowingly making a false statement for this purpose is a criminal offence.</p>
			            		<p class="strong">To proceed with your application, please confirm your acceptance of these terms.</p>
					        </div>
			            </div>

		            <div class="row">
		            	<div class="col-md-12">
		            		<div class="checkbox">
				              <label>
				                <input type="checkbox" id="user_terms_accepted" name="user_terms_accepted" value="1" required="required" @if(isset($DBSApplication->terms_accepted) && $DBSApplication->terms_accepted) checked="checked" @endif> I accept these terms.<br />
				                <span id="user_terms_accepted_error_block" class="help-block block-hidden">You must accept the declaration before you can continue.</span>
				              </label>

				            </div>
				        </div>
				    </div>


		            
		          </div>
		          <!-- /.box-body  -->

		          <div class="box-footer">
		            <div class="form-group">
			            <button type="button" class="forceBgClassified btn btn-lg pull-right submitStep1" style="margin-bottom: 10px;">Save and Continue</button>
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
<script type="text/javascript">

</script>
@endsection
