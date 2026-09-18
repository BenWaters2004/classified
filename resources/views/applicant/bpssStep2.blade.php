@extends('layout.default')

@section('title', "BPSS Application Step 2")

@section('sidebar')
@endsection

@section('content')
<div class="row">
    <!-- left column -->
    <div class="col-md-12">
      <!-- general form elements -->
	    <h2>Complete the form below to apply for Basic Personnel Security Standard</h2>
	  
      <div class="box box-default">


        <div class="row">
        	<div class="col-md-12">
        		<div class="box-body">
		            <a href="{{ env('APP_URL') }}applicant/bpssStep1"><button type="button" class="forceBgClassified btn btn-default btn-lg pull-left" style="margin-bottom: 10px;">Previous</button></a>
        		</div>

		        <!-- form start -->
		        <form id="bpssStep1" method="POST" action="{{ env('APP_URL') }}applicant/savebpssStep2">
        			{{ csrf_field() }}
		         	<div class="box-body">
		         		
        				<div class="row">
        					<div class="col-md-12">
					            <div class="form-group">
					            	<!-- <a href="{{ env('APP_URL') }}applicant/bpssStep1"><button type="button" class="btn btn-default btn-lg pull-left" style="margin-bottom: 10px;">Previous</button></a> -->
						            <button type="button" class="forceBgClassified btn btn-lg pull-right submitStep2" style="margin-bottom: 10px; margin-top: -55px;">Save and Continue</button>
						        </div>
					            <hr />
					        </div>
					    </div>

					    <div class="row">
        					<div class="col-md-12">
        						<h3>Nationality and Immigration Status</h3>
        					</div>
        				</div>
					    <hr />
		            	<div class="row">
        					<div class="col-md-12">
					            <div id="errorMessageBlock" class="callout callout-danger block-hidden">
					                <p>There were some errors in the information supplied.<br />Please review the fields below to correct the errors.</p>
					            </div>
					        </div>
					    </div>

			            <div class="row">
			            	<div class="col-md-3">
			            		<div class="form-group">
			            			<label for="dbs_birth_nationality">Nationality at  Birth:</label>
					              	<input class="form-control" name="dbs_birth_nationality" type="text" value="@if(isset($DBSApplication->birth_nationality)) {{$DBSApplication->birth_nationality}} @endif" disabled="disabled">
					            </div>
			            	</div>

			            	<div class="col-md-3">
			            		<div class="form-group">
			            			<label for="present_nationality">Present Nationality</label>
					              	<input class="form-control" id="present_nationality" name="present_nationality" type="text" value="@if(isset($BPSSApplication->present_nationality)) {{$BPSSApplication->present_nationality}} @endif" maxlength="60" required>
				                    <span class="help-block block-hidden">Please enter a value</span>
					            </div>
			            	</div>

			            	<div class="col-md-6">
			            		<div class="form-group">
			            			<label for="national_identity_card">Do you have a National Identity card</label>
					              	<div class="row">
			            				<div class="col-md-3">
					              			<select class="form-control" id="national_identity_card" name="national_identity_card">
							                    <option value="1" @if ($BPSSApplication->national_identity_card == 1) selected="selected" @endif>Yes</option>
							                    <option value="0" @if ($BPSSApplication->national_identity_card == 0) selected="selected" @endif>No</option>
							                </select>
							            </div>
							            <div class="col-md-9 block-hidden" id="nationalIdentityCardBlock">
							            	<input class="form-control" id="national_identity_card_number" name="national_identity_card_number" type="text" value="@if(isset($BPSSApplication->national_identity_card_number)) {{$BPSSApplication->national_identity_card_number}} @endif" maxlength="60" placeholder="National Identity Card Number">
				                    		<span class="help-block block-hidden">Please enter a value</span>
							            </div>
							        </div>
					            </div>
			            	</div>

			            </div>

			            <hr />

			            <div class="row">
			            	<div class="col-md-6">
			            		<div class="form-group">
			            			<label for="passport">Do you have a passport? (if multiple country passports please list all)</label>
			            			@if(isset($DBSApplication->supporting_passport) && strlen($DBSApplication->supporting_passport) > 0 && strlen($DBSApplication->supporting_passport_country) > 0)
			            				<input class="form-control" type="text" value="Yes" disabled="disabled">
			            				<input type="hidden" value="1" name="passport">
			            			@else
					              	<select class="form-control" id="passport" name="passport">
					                    <option value="1" @if ($BPSSApplication->passport == 1) selected="selected" @endif>Yes</option>
					                    <option value="0" @if ($BPSSApplication->passport == 0) selected="selected" @endif>No</option>
					                </select>
					                @endif
				                  <span class="help-block block-hidden">Please select an option</span>
					            </div>
			            	</div>
			            </div>

			            <div class="row">
			            	<div class="col-md-12 block-hidden" id="passportsListBlock" @if(isset($savedPassports) && count($savedPassports)>0) show-flag="true" @else show-flag="false" @endif>
			            		<div class="row">
			            			<div class="col-md-4">
			            				<label>Passport Number</label>
			            			</div>
			            			<div class="col-md-4">
			            				<label>Country of Issue</label>
			            			</div>
			            		</div>

			            		@if(isset($DBSApplication->supporting_passport) && strlen($DBSApplication->supporting_passport) > 0 && strlen($DBSApplication->supporting_passport_country) > 0)
			            			<div class="row">
				                    		<div class="col-md-4">{{$DBSApplication->supporting_passport}}</div>
				                    		<div class="col-md-4">{{$DBSApplication->supporting_passport_country_fullName}}</div>
				                    		<div class="col-md-2" style=" padding-left:0;">
				                    			<button  type="button"  class="btn btn-danger pull-left" style="margin-bottom:5px;" disabled="disabled"><i class="icon fa fa-remove"></i> Delete</button>
				                    		</div>
						            		<hr />
				                    	</div>
			            		@endif
			            		@if(isset($savedPassports) && count($savedPassports)>0)
				                    @foreach ($savedPassports as $key => $passport)
				                    	<div class="row" id="passport_block_{{$passport->id}}">
				                    		<div class="col-md-4">{{$passport->passport_number}}</div>
				                    		<div class="col-md-4">{{$passport->country_of_issue_name}}</div>
				                    		<div class="col-md-2" style=" padding-left:0;">
				                    			<button id="remove_passport_block_{{$passport->id}}" passport-id="{{$passport->id}}" type="button"  class="btn btn-danger pull-left" style="margin-bottom:5px;"><i class="icon fa fa-remove"></i> Delete</button>
				                    		</div>
						            		<hr />
				                    	</div>
				                    @endforeach
				                @endif
			            	</div>
			            </div>

			            <div class="row">
			            	<div id="addNewPassportBlock" class="block-hidden">
					            <div class="col-md-4">
				            		<div class="form-group">
					              		<label for="passport_number">Passport Number</label>
						              	<input class="form-control" id="passport_number" name="passport_number" type="text" value="" maxlength="100">
					                    <span class="help-block block-hidden">Please enter a value</span>
						            </div>
					            </div>

					            <div class="col-md-4">
				            		<div class="form-group">
						                <label for="country_of_issue">Country of Issue</label>
						                <select class="form-control" id="country_of_issue" name="country_of_issue">
						                    @if(isset($countries) && count($countries)>0)
							                    @foreach ($countries as $country)
							                    	<option value="{{$country->iso3}}"  @if ($country->iso3 == 225) selected="selected" @endif>{{$country->nicename}}</option>
							                    @endforeach
							                @endif
					                  </select>
					                  <span class="help-block block-hidden">You must select a country of issue.</span>
						            </div>
				            	</div>

				            	<div class="col-md-2">
				            		<div class="form-group">
				            			<label for="save_passport_details">&nbsp; </label>
						              	<button id="save_passport_details"  type="button" class="btn btn-danger form-control" style="width: 180px;"><i class="fa fa-save" style="margin-right: 10px;"></i>Save Passport Details</button>
						            </div>
				            	</div>

			            	</div>
			            </div>

			            <hr />

			            <div class="row">
			            	<div class="col-md-3">
			            		<div class="form-group">
			            			<label for="dual_citizenship">Do you hold dual citizenship?</label>
					              	<select class="form-control" id="dual_citizenship" name="dual_citizenship">
					                    <option value="1" @if ($BPSSApplication->dual_citizenship == 1 || $countDualCitizenships > 1) selected="selected" @endif>Yes</option>
					                    <option value="0" @if ($BPSSApplication->dual_citizenship == 0) selected="selected" @endif>No</option>
					                </select>
				                  <span class="help-block block-hidden">Please select an option</span>
					            </div>
			            	</div>
			            </div>

			            <div id="dualCitizenshipBlock" class="block-hidden">
				            <div class="row">
				            	<div class="col-md-12" id="passportsListBlock" @if(isset($dualCitizenships) && count($dualCitizenships)>0) show-flag="true" @else show-flag="false" @endif>
				            		<div class="row">
				            			<div class="col-md-4">
				            				<label>Country of Nationality</label>
				            			</div>
				            		</div>

				            		@if(isset($dualCitizenships) && count($dualCitizenships)>0)
					                    @foreach ($dualCitizenships as $key => $citizenshipCountry)
					                    	<div class="row" id="citizenshipCountry_block_{{$citizenshipCountry->id}}">
					                    		<div class="col-md-4">{{$citizenshipCountry->country_of_issue_name}}</div>
					                    		<div class="col-md-2" style=" padding-left:0;">
					                    			<button id="remove_citizenshipCountry_block_{{$citizenshipCountry->id}}" citizenshipCountry-id="{{$citizenshipCountry->id}}" type="button"  class="btn btn-danger pull-left" style="margin-bottom:5px;"><i class="icon fa fa-remove"></i> Delete</button>
					                    		</div>
					                    	</div>
					                    @endforeach
					                @endif
				            	</div>
				            </div>
				            <div  id="dualCitizenshipAjaxBlock"></div>
				            <hr />
				            <div class="row">
					            <div class="col-md-4">
				            		<div class="form-group">
						                <label for="country_of_issue">Country of Nationality</label>
						                <select class="form-control" id="country_of_nationality" name="country_of_nationality">
						                    @if(isset($countries) && count($countries)>0)
							                    @foreach ($countries as $country)
							                    	<option value="{{$country->iso3}}"  @if ($country->iso3 == 225) selected="selected" @endif>{{$country->nicename}}</option>
							                    @endforeach
							                @endif
					                  </select>
					                  <span class="help-block block-hidden">You must select a country of issue.</span>
						            </div>
				            	</div>

				            	<div class="col-md-2">
				            		<div class="form-group">
				            			<label for="save_citizenshipCountry_details">&nbsp; </label>
						              	<button id="save_citizenshipCountry_details"  type="button" class="btn btn-danger form-control" style="width: 220px;"><i class="fa fa-save" style="margin-right: 10px;"></i>Save Country of Nationality</button>
						            </div>
				            	</div>
				            </div>
				        </div>

			            {{--
			            <div class="row">
			            	<div id="dualCitizenshipBlock" class="block-hidden">
					            <div class="col-md-12">
				            		<div class="form-group">
					              		<label for="dual_citizenship_details">Please give details of dual citizenship/nationality status:</label>
								        <textarea class="form-control" rows="5"  id="dual_citizenship_details"  name="dual_citizenship_details">@if(isset($BPSSApplication->dual_citizenship_details)){{$BPSSApplication->dual_citizenship_details}}@endif</textarea>
						            </div>
					            </div>
			            	</div>
			            </div>
			            --}}
			            <hr />

			            <div class="row">
			            	<div class="col-md-12">
			            		<div class="form-group">
			            			<label for="former_nationality">Have you ever possessed any other nationality or citizenship?</label>
			            			<div class="row">
			            				<div class="col-md-3">
							              	<select class="form-control" id="former_nationality" name="former_nationality">
							                    <option value="1" @if ($BPSSApplication->former_nationality == 1) selected="selected" @endif>Yes</option>
							                    <option value="0" @if ($BPSSApplication->former_nationality == 0) selected="selected" @endif>No</option>
							                </select>
							            </div>
							        </div>
				                  <span class="help-block block-hidden">Please select an option</span>
					            </div>
			            	</div>
			            </div>

			            <div class="row">
			            	<div id="formerNationalityBlock" class="block-hidden">
					            <div class="col-md-12">
				            		<div class="form-group">
					              		<label for="former_nationality_details">Former Nationality:</label>
								        <input class="form-control" id="former_nationality_details" name="former_nationality_details" type="text" value="@if(isset($BPSSApplication->former_nationality_details)) {{$BPSSApplication->former_nationality_details}} @endif" maxlength="255">
						            </div>
					            </div>
			            	</div>
			            </div>

			            <hr />

			            <div class="row">
			            	<div class="col-md-12">
			            		<div class="form-group">
			            			<label>If British naturalised please provide Naturalisation Certificate:</label>
			            			<div class="row">
			            				<div class="col-md-3">
			            					<label for="naturalisation_certificate_number">Number</label>
							              	<input class="form-control" id="naturalisation_certificate_number" name="naturalisation_certificate_number" type="text" value="@if(isset($BPSSApplication->naturalisation_certificate_number)) {{$BPSSApplication->naturalisation_certificate_number}} @endif" maxlength="255">
							            </div>

							            <div class="col-md-3">
			            					<div class="form-group">
								              	<label for="naturalisation_certificate_date">Date</label>
								              	<div class="input-group date">
								                  <div class="input-group-addon">
								                    <i class="fa fa-calendar"></i>
								                  </div>
								                  <input class="form-control" id="naturalisation_certificate_date" name="naturalisation_certificate_date" type="text" value="@if (isset($BPSSApplication->naturalisation_certificate_date) && strlen($BPSSApplication->naturalisation_certificate_date) > 0){{date('d/m/Y', strtotime($BPSSApplication->naturalisation_certificate_date))}}@endif" placeholder="dd/mm/yyyy" >								                  
								                </div>
								                <span id="previous_address_from_error_block" class="help-block block-hidden">Please enter a date between now and your date of birth.</span>
								            </div>
							            </div>
							        </div>
				                  <span class="help-block block-hidden">Please enter the naturalisation details</span>
					            </div>
			            	</div>
			            </div>

			            <hr />

			            <div class="row">
			            	<div class="col-md-12">
			            		<div class="form-group">
			            			<label for="subject_to_immigration_control">Are you subject to immigration control?</label>
			            			<div class="row">
			            				<div class="col-md-3">
							              	<select class="form-control" id="subject_to_immigration_control" name="subject_to_immigration_control">
							                    <option value="1" @if ($BPSSApplication->subject_to_immigration_control == 1) selected="selected" @endif>Yes</option>
							                    <option value="0" @if ($BPSSApplication->subject_to_immigration_control == 0) selected="selected" @endif>No</option>
							                </select>
							            </div>
							        </div>
				                  <span class="help-block block-hidden">Please select an option</span>
					            </div>
			            	</div>
			            </div>

			            <div class="row">
			            	<div id="immigrationControlBlock" class="block-hidden">
					            <div class="col-md-12">
				            		<div class="form-group">
					              		<label for="subject_to_immigration_control_details">If YES please specify:</label>
								        <textarea class="form-control" rows="5"  id="subject_to_immigration_control_details"  name="subject_to_immigration_control_details">@if(isset($BPSSApplication->subject_to_immigration_control_details)){{$BPSSApplication->subject_to_immigration_control_details}}@endif</textarea>
						            </div>
					            </div>
			            	</div>
			            </div>

		            	
		            	<hr />

			            <div class="row">
			            	<div class="col-md-12">
			            		<div class="form-group">
			            			<label for="lawfully_resident_in_uk">Are you lawfully resident in the UK?</label>
			            			<div class="row">
			            				<div class="col-md-3">
							              	<select class="form-control" id="lawfully_resident_in_uk" name="lawfully_resident_in_uk">
							                    <option value="1" @if ($BPSSApplication->lawfully_resident_in_uk == 1) selected="selected" @endif>Yes</option>
							                    <option value="0" @if ($BPSSApplication->lawfully_resident_in_uk == 0) selected="selected" @endif>No</option>
							                </select>
							            </div>
							        </div>
				                  <span class="help-block block-hidden">Please select an option</span>
				                  <span id="validateLawfullyResidentWarning" class="help-block text-red block-hidden">Warning: You have selected 'No' and this might impact your application, depending on your circumstances!</span>
					            </div>
			            	</div>
			            </div>

			            <hr />

			            <div class="row">
			            	<div class="col-md-12">
			            		<div class="form-group">
			            			<label for="continued_residence_restrictions">Are there any restrictions on your continued residence in the UK?</label>
			            			<div class="row">
			            				<div class="col-md-3">
							              	<select class="form-control" id="continued_residence_restrictions" name="continued_residence_restrictions">
							                    <option value="1" @if ($BPSSApplication->continued_residence_restrictions == 1) selected="selected" @endif>Yes</option>
							                    <option value="0" @if ($BPSSApplication->continued_residence_restrictions == 0) selected="selected" @endif>No</option>
							                </select>
							            </div>
							        </div>
				                  <span class="help-block block-hidden">Please select an option</span>
					            </div>
			            	</div>
			            </div>

			            <div class="row">
			            	<div id="residenceRestrictionsBlock" class="block-hidden">
					            <div class="col-md-12">
				            		<div class="form-group">
					              		<label for="continued_residence_restrictions_details">If YES please specify:</label>
								        <textarea class="form-control" rows="5"  id="continued_residence_restrictions_details"  name="continued_residence_restrictions_details">@if(isset($BPSSApplication->continued_residence_restrictions_details)){{$BPSSApplication->continued_residence_restrictions_details}}@endif</textarea>
						            </div>
					            </div>
			            	</div>
			            </div>

			            <hr />

			            <div class="row">
			            	<div class="col-md-12">
			            		<div class="form-group">
			            			<label for="freedom_to_take_employment">Are there any restrictions on your continued freedom to take employment in the UK?</label>
			            			<div class="row">
			            				<div class="col-md-3">
							              	<select class="form-control" id="freedom_to_take_employment" name="freedom_to_take_employment">
							                    <option value="1" @if ($BPSSApplication->freedom_to_take_employment == 1) selected="selected" @endif>Yes</option>
							                    <option value="0" @if ($BPSSApplication->freedom_to_take_employment == 0) selected="selected" @endif>No</option>
							                </select>
							            </div>
							        </div>
				                  <span class="help-block block-hidden">Please select an option</span>
					            </div>
			            	</div>
			            </div>

			            <div class="row">
			            	<div id="freedomToTakeRmploymentBlock" class="block-hidden">
					            <div class="col-md-12">
				            		<div class="form-group">
					              		<label for="freedom_to_take_employment_details">If YES please specify:</label>
								        <textarea class="form-control" rows="5"  id="freedom_to_take_employment_details"  name="freedom_to_take_employment_details">@if(isset($BPSSApplication->freedom_to_take_employment_details)){{$BPSSApplication->freedom_to_take_employment_details}}@endif</textarea>
						            </div>
					            </div>
			            	</div>
			            </div>

			            <hr />

			            <div class="row">
			            	<div class="col-md-12">
			            		<div class="form-group">
			            			<label for="freedom_to_take_employment">If applicable, please state your Home Office / Port reference number:</label>
			            			<div class="row">
			            				<div class="col-md-3">
							              	<input class="form-control" id="ho_port_reference_number" name="ho_port_reference_number" type="text" value="@if(isset($BPSSApplication->ho_port_reference_number)) {{$BPSSApplication->ho_port_reference_number}} @endif" maxlength="255">
							            </div>
							        </div>
				                  <span class="help-block block-hidden">Please enter a value</span>
					            </div>
			            	</div>
			            </div>


			        </div>
			        <!-- /.box-body -->

		          	<div class="box-footer">
			            <div class="form-group">
			            	<!-- <a href="{{ env('APP_URL') }}applicant/bpssStep1"><button type="button" class="btn btn-default btn-lg pull-left" style="margin-bottom: 10px;">Previous</button></a> -->
				            <button type="button" class="forceBgClassified btn btn-lg pull-right submitStep2" style="margin-bottom: 10px;">Save and Continue</button>
				        </div>
		          	</div>
		        </form>
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
@include('applicant.bpssValidations')
<script type="text/javascript">

</script>
@endsection
