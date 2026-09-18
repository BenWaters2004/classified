@extends('layout.default')

@section('title', "DBS Application Step 3")

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
		                <div class="progress-bar progress-bar-primary progress-bar-striped" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width: 50%">
		                  <span class="sr-only">50% Complete (success)</span>
		                </div>
		            </div>
		            <hr />
		            <a href="{{ env('APP_URL') }}applicant/dbsStep2"><button type="button" class="forceBgClassified btn btn-default btn-lg pull-left" style="margin-bottom: 10px;">Previous</button></a>
        		</div>
		        <!-- form start -->
		        <form id="dbsStep2" method="POST" action="{{ env('APP_URL') }}applicant/saveStep3Data">
        			{{ csrf_field() }}
		          <div class="box-body">
					<button type="button" class="forceBgClassified btn btn-lg pull-right submitStep2" style="margin-bottom: 10px; margin-top: -20px;">Save and Continue</button>
		            <hr />

		            <div id="errorMessageBlock" class="callout callout-danger block-hidden">
		                <p>There were some errors in the information supplied.<br />Please review the fields below to correct the errors.</p>
		            </div>

		            <h3>Your contact details</h3>
		            <div class="row">
		            	<div class="col-md-12">
		            		<div class="form-group">
				              	<label for="user_email">Email Address</label>
				              	<div class="input-group">
				                  <div class="input-group-addon">
				                    <i class="fa fa-envelope"></i>
				                  </div>
				                  <input class="form-control" id="user_email" name="user_email" type="email" value="{{$DBSApplication->application_email}}" maxlength="255" required="required">
				                </div>
				                <span class="help-block block-hidden">Please enter a valid email address.</span>
				            </div>
		            	</div>
		            </div>
		            <div class="row">
		            	<div class="col-md-12">
		            		<div class="form-group">
				              	<label for="user_contact_number">Contact Number</label>
				              	<div class="input-group">
				                  <div class="input-group-addon"><i class="fa fa-phone"></i></div>
				                  <div class="input-group-addon">
				                  	<select  style="border: 0; width:80px;" id="user_contact_number_country_code" name="user_contact_number_country_code">
					                    <option value="">Code</option>
					                    @if(isset($countryTelephoneCodes) && count($countryTelephoneCodes)>0)
						                    @foreach ($countryTelephoneCodes as $countryTelephoneCode)
						                    	@if (!empty($countryTelephoneCode))
						                    	<option value="{{$countryTelephoneCode}}" @if ($DBSApplication->contact_number_country_code == $countryTelephoneCode) selected="selected" @endif>+{{$countryTelephoneCode}}</option>
						                    	@endif
						                    @endforeach
						                @endif
					                </select>
					              </div>
				                  <input class="form-control onlyNumbers" id="user_contact_number" name="user_contact_number" type="tel" value="{{$DBSApplication->contact_number}}" maxlength="16">
				                </div>
				                <span id="contact_number_error_block" class="help-block block-hidden">Phone number cannot be longer that 16 digits.</span>
				            </div>
		            	</div>
		            </div>
		            <div class="row">
		            	<div class="col-md-12">
		            		<div class="form-group">
				              	<label for="user_email">Mobile Number</label>
				              	<div class="input-group">
				                  <div class="input-group-addon"><i class="fa fa-phone"></i></div>
				                  <div class="input-group-addon">
				                  	<select  style="border: 0; width:80px;" id="user_mobile_number_country_code" name="user_mobile_number_country_code">
					                    <option value="">Code</option>
					                    @if(isset($countryTelephoneCodes) && count($countryTelephoneCodes)>0)
						                    @foreach ($countryTelephoneCodes as $countryTelephoneCode)
						                    	@if (!empty($countryTelephoneCode))
						                    	<option value="{{$countryTelephoneCode}}" @if ($DBSApplication->mobile_number_country_code == $countryTelephoneCode) selected="selected" @endif>+{{$countryTelephoneCode}}</option>
						                    	@endif
						                    @endforeach
						                @endif
					                </select>
					              </div>
				                  <input class="form-control onlyNumbers" id="user_mobile_number" name="user_mobile_number" type="tel" value="{{$DBSApplication->mobile_number}}" maxlength="16">
				                </div>
				                <span id="mobile_number_error_block" class="help-block block-hidden">Phone number cannot be longer that 16 digits.</span>
				            </div>
		            	</div>
		            </div>

		            <h3>Current address</h3>
		            <div class="row">
		            	<div class="col-md-12">
		            		<div class="form-group">
				              	<label for="user_address_line_1">Address Line 1</label>
				              	<input class="form-control" id="user_address_line_1" name="user_address_line_1" type="text" value="{{$DBSApplication->address_line_1}}" required="required" maxlength="60">
				              	<span class="help-block block-hidden">Address line cannot be empty.</span>
				            </div>
		            	</div>
		            </div>

		            <div class="row">
		            	<div class="col-md-12">
		            		<div class="form-group">
				              	<label for="user_address_line_2">Address Line 2</label>
				              	<input class="form-control" id="user_address_line_2" name="user_address_line_2" type="text" value="{{$DBSApplication->address_line_2}}"  maxlength="60">
				              	<span class="help-block block-hidden">Address line cannot be empty.</span>
				            </div>
		            	</div>
		            </div>

		            <div class="row">
		            	<div class="col-md-6">
		            		<div class="form-group">
				              	<label for="user_address_town">Town</label>
				              	<input class="form-control" id="user_address_town" name="user_address_town" type="text" value="{{$DBSApplication->address_town}}" required="required" maxlength="30">
				              	<span class="help-block block-hidden">Town cannot be empty.</span>
				            </div>
		            	</div>

		            	<div class="col-md-6">
		            		<div class="form-group">
				              	<label for="user_address_county">County</label>
				              	<input class="form-control" id="user_address_county" name="user_address_county" type="text" value="{{$DBSApplication->address_county}}" maxlength="30">
				              	<span class="help-block block-hidden">County must contain only letters, space, full-stop or hyphen.</span>
				            </div>
		            	</div>
		            </div>

		            <div class="row">
		            	<div class="col-md-6">
		            		<div class="form-group">
				              	<label for="user_address_postcode">Postcode</label>
				              	<input class="form-control" id="user_address_postcode" name="user_address_postcode" type="text" value="{{$DBSApplication->address_postcode}}"  maxlength="15">
				              	<span class="help-block block-hidden">Postcode cannot be empty.</span>
				            </div>
		            	</div>

		            	<div class="col-md-6">
		            		<div class="form-group">
				              	<label for="user_address_country">Country</label>
				              	<select class="form-control" id="user_address_country" name="user_address_country">
				                    @if(isset($countries) && count($countries)>0)
					                    @foreach ($countries as $country)
					                    	<option value="{{$country->iso3}}" @if ($DBSApplication->address_country == $country->iso3) selected="selected" @endif>{{$country->nicename}}</option>
					                    @endforeach
					                @endif
				                </select>
				                <span class="help-block block-hidden">You must select a country.</span>
				            </div>
		            	</div>
		            </div>

		            <div class="row">
		            	<div class="col-md-12">
		            		<div class="form-group">
				              <label for="user_current_address_from">When did you start living at your current address?</label>
				              <div class="input-group date">
				                  <div class="input-group-addon">
				                    <i class="fa fa-calendar"></i>
				                  </div>
				                  <input class="form-control" id="user_current_address_from" name="user_current_address_from" type="text" value="@if (strlen($DBSApplication->current_address_from) > 0){{date('d/m/Y', strtotime($DBSApplication->current_address_from))}}@endif" placeholder="dd/mm/yyyy">
				                  <input id="validate_dob" type="hidden" value="{{$DBSApplication->dob}}">
				                </div>
				                <span id="validate_dob_error_block" class="help-block block-hidden">Please enter a date between now and your date of birth.</span>
				            </div>
		            	</div>
		            </div>

		           
		          </div>
		          <!-- /.box-body -->

		          <div class="box-footer">
		            <div class="form-group">
			            <!-- <a href="{{ env('APP_URL') }}applicant/dbsStep2"><button type="button" class="btn btn-default btn-lg pull-left" style="margin-bottom: 10px;">Previous</button></a> -->
			            <button type="button" class="forceBgClassified btn btn-lg pull-right  submitStep2" style="margin-bottom: 10px;">Save and Continue</button>
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
