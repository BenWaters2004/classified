@extends('layout.default')

@section('title', "DBS Application Step 2")

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
		                <div class="progress-bar progress-bar-primary progress-bar-striped" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width: 30%">
		                  <span class="sr-only">30% Complete (success)</span>
		                </div>
		            </div>
		            <hr />
		            <a href="{{ env('APP_URL') }}applicant/dbsStep1"><button type="button" class="forceBgClassified btn btn-default btn-lg pull-left" style="margin-bottom: 10px;">Previous</button></a>
        		</div>
		        <!-- form start -->
		        <form id="dbsStep4" method="POST" action="{{ env('APP_URL') }}applicant/saveStep2Data">
        			{{ csrf_field() }}
		          <div class="box-body">
			        <button type="button" class="forceBgClassified btn btn-lg pull-right submitStep4" style="margin-bottom: 10px; margin-top: -20px;">Save and Continue</button>
		            <hr />

		            <div id="errorMessageBlock" class="callout callout-danger block-hidden">
		                <p>There were some errors in the information supplied.<br />Please review the fields below to correct the errors.</p>
		            </div>

		            <h3>About you</h3>
		            <div class="row">
		            	<div class="col-md-4">
		            		<div class="form-group">
				              <label for="user_title">Title</label>
				              <select class="form-control" id="user_title" name="user_title">
			                    <option value="">Select</option>
			                    @if(isset($userTitles) && count($userTitles)>0)
				                    @foreach ($userTitles as $userTitle)
				                    	<option value="{{$userTitle->id}}" @if ($DBSApplication->title == $userTitle->id) selected="selected" @endif>{{$userTitle->userTitle}}</option>
				                    @endforeach
				                @endif
			                  </select>
			                  <span class="help-block block-hidden">Phone number cannot be longer that 16 digits.</span>
				            </div>
		            	</div>

		            	<div class="col-md-4">
		            		<div class="form-group">
				              <label for="user_gender">Sex</label>
				              <select class="form-control" id="user_gender" name="user_gender">
			                    <option value="">Select</option>
			                    <option value="male" @if ($DBSApplication->gender == 'male') selected="selected" @endif>Male</option>
			                    <option value="female" @if ($DBSApplication->gender == 'female') selected="selected" @endif>Female</option>
			                  </select>
			                  <span class="help-block block-hidden">Please select gender.</span>
				            </div>
		            	</div>

		            	<div class="col-md-4">
		            		<div class="form-group">
				              	<label for="user_dob">Date of Birth</label>
				              	<div class="input-group date">
				                  <div class="input-group-addon">
				                    <i class="fa fa-calendar"></i>
				                  </div>
				                  <input class="form-control" id="user_dob" name="user_dob" type="text"  value="@if (strlen($DBSApplication->dob) > 0){{date('d/m/Y', strtotime($DBSApplication->dob))}}@endif" placeholder="dd/mm/yyyy" required="required">
				                </div>
				                <span id="user_dob_error_block" class="help-block block-hidden">Please enter a date.</span>
				            </div>
		            	</div>

		            </div>
		            <div class="row">
		            	<div class="col-md-4">
		            		<div class="form-group">
				              	<label for="user_birth_town">Birth Town</label>
				              	<input class="form-control" id="user_birth_town" name="user_birth_town" type="text" value="{{$DBSApplication->birth_town}}" required="required" maxlength="30">
				              	<span class="help-block block-hidden">Birth town cannot be empty.</span>
				            </div>
		            	</div>

		            	<div class="col-md-4">
		            		<div class="form-group">
				                <label for="user_birth_country">Birth Country</label>
				                <select class="form-control" id="user_birth_country" name="user_birth_country">
				                    @if(isset($countries) && count($countries)>0)
					                    @foreach ($countries as $country)
					                    	<option value="{{$country->iso3}}"  @if ($DBSApplication->birth_country == $country->iso3) selected="selected" @endif>{{$country->nicename}}</option>
					                    @endforeach
					                @endif
			                  </select>
			                  <span class="help-block block-hidden">You must select a birth country.</span>
				            </div>
		            	</div>

		            	<div class="col-md-4">
		            		<div class="form-group">
				              	<label for="user_birth_nationality">Birth Nationality</label>
				              	<input class="form-control" id="user_birth_nationality" name="user_birth_nationality" type="text" value="{{$DBSApplication->birth_nationality}}" required="required">
				              	<span class="help-block block-hidden">Nationality must contain only letters, space, full-stop or hyphen.</span>
				            </div>
		            	</div>
		            </div>

		            <div class="row">
		            	<div class="col-md-4">
		            		<div class="form-group">
				              	<label for="user_forename">Forename</label>
				              	<input class="form-control" id="user_forename" name="user_forename" type="text" value="{{$DBSApplication->forename}}" required="required" maxlength="60">
				              	<span class="help-block block-hidden">Forename cannot be empty.</span>
				            </div>
		            	</div>

		            	<div class="col-md-4">
		            		<div class="form-group">
				              	<label for="user_middlename">Middlename</label>
				              	<input class="form-control" id="user_middlename" name="user_middlename" type="text" value="{{$DBSApplication->middlename}}"  maxlength="50">
				              	<span class="help-block block-hidden">Middlename cannot be more than 50 chars.</span>
				            </div>
		            	</div>

		            	<div class="col-md-4">
		            		<div class="form-group">
				              	<label for="user_present_surname">Present Surname</label>
				              	<input class="form-control" id="user_present_surname" name="user_present_surname" type="text" value="{{$DBSApplication->presentSurname}}" required="required" maxlength="50">
				              	<span class="help-block block-hidden">Surname cannot be empty.</span>
				            </div>
		            	</div>
		            </div>

		            <div class="row">
		            	<div class="col-md-12">
			            	<div class="checkbox">
			            		<p>Have you had any previous names by which you've been known?</p>
				              <label>
				                <input type="checkbox" id="user_checkbox_previous_names" name="user_checkbox_previous_names" value="1" @if(isset($DBSApplication->previous_names) && $DBSApplication->previous_names) checked="checked" @endif> Yes
				              </label>
				              <!-- <span class="glyphicon glyphicon-question-sign pull-right" title="If selected gender is 'Female' and the title is different than 'Miss' then you must enter your previous name. If no previous name then please re-enter your name in the other Names section." style="color:#FFA500; font-size: 20px; width:30px;"></span> -->
				            </div>

				        </div>
		            </div>
<div id="otherNamesBlock" class="block-hidden">
<input id="skipOtherNamesValidation" name="skipOtherNamesValidation" type="hidden" value="0"  maxlength="50">
		            <hr>
		            <h3>Other Names</h3>

		            <div class="row">
		            	<div class="col-md-12">
		            		<div class="callout callout-warning">
				                <p>Please enter any previous names you may have had. If you have no previous names then please re-enter your name in the this section.</p>
				            </div>
		            	</div>
		            </div>

		            <div class="row">
		            	<div class="col-md-12" id="otherNamesListBlock">
		            		@if(isset($otherNames) && count($otherNames)>0)
			                    @foreach ($otherNames as $otherName)
			                    	<div class="row" id="other_names_block_{{$otherName->id}}">
			                    		<div class="col-md-4"> {{$otherName->other_forename}} {{$otherName->other_middlename}} {{$otherName->other_surname}}</div>
			                    		<div class="col-md-6"> From: {{\Carbon\Carbon::parse($otherName->dateFrom)->format('d/m/Y')}} To: {{\Carbon\Carbon::parse($otherName->dateTo)->format('d/m/Y')}}</div>
			                    		<div class="col-md-2"><button id="remove_other_names_{{$otherName->id}}" name-id="{{$otherName->id}}" type="button"  class="btn btn-danger" style="margin-bottom:5px;"><i class="icon fa fa-remove"></i></button></div>
			                    	</div>
			                    @endforeach

			                @endif
		            	</div>
		            </div>

		            <din style="clear: both;"></din>
		            <div class="row">
		            	<div class="col-md-4">
		            		<div class="form-group">
				              	<label for="user_other_forename">Forename</label>
				              	<input class="form-control" id="user_other_forename" name="user_other_forename" type="text" value="" maxlength="60">
				              	<span class="help-block block-hidden">Forename cannot be empty.</span>
				            </div>
		            	</div>

		            	<div class="col-md-4">
		            		<div class="form-group">
				              	<label for="user_other_middlename">Middlename</label>
				              	<input class="form-control" id="user_other_middlename" name="user_other_middlename" type="text" value="" maxlength="50">
				              	<span class="help-block block-hidden">Middlename cannot be more than 50 chars.</span>
				            </div>
		            	</div>

		            	<div class="col-md-4">
		            		<div class="form-group">
				              	<label for="user_other_surname">Surname</label>
				              	<input class="form-control" id="user_other_surname" name="user_other_surname" type="text" value="" maxlength="50">
				              	<span class="help-block block-hidden">Surname cannot be empty.</span>
				            </div>
		            	</div>
		            </div>

		            <div class="row">
		            	<div class="col-md-12"><p>Over what period:</p></div>
		            	<div class="col-md-6">
		            		<div class="form-group">
				              	<label for="user_other_names_from">From</label>
				              	<div class="input-group date">
				                  <div class="input-group-addon">
				                    <i class="fa fa-calendar"></i>
				                  </div>
				                  <input class="form-control" id="user_other_names_from" name="user_other_names_from" type="text" value="" placeholder="dd/mm/yyyy">
				                </div>
				                <span id="user_other_names_from_error_block" class="help-block block-hidden">Please enter a date.</span>
				            </div>
		            	</div>

		            	<div class="col-md-6">
		            		<div class="form-group">
				              	<label for="user_other_names_to">To</label>
				              	<div class="input-group date">
				                  <div class="input-group-addon">
				                    <i class="fa fa-calendar"></i>
				                  </div>
				                  <input class="form-control" id="user_other_names_to" name="user_other_names_to" type="text" value="" placeholder="dd/mm/yyyy">
				                </div>
				                <span id="user_other_names_to_error_block" class="help-block block-hidden">Please enter a date.</span>
				            </div>
		            	</div>
		            </div>

		            <div class="row">
		            	<div class="col-md-8 text-red">
		            		*if only adding one additional surname click next
		            	</div>
		            	<div class="col-md-4">
		            		<div class="form-group">
				              	<button id="user_other_names_add_another"  type="button" class="btn btn-danger  pull-right" style="margin-bottom: 10px;">Save Name</button>
				            </div>
		            	</div>
		            </div>
		            
</div>		            
		          </div>
		          <!-- /.box-body -->

		          <div class="box-footer">
		            <div class="form-group">
			            <!-- <a href="{{ env('APP_URL') }}applicant/dbsStep1"><button type="button" class="btn btn-default btn-lg pull-left" style="margin-bottom: 10px;">Previous</button></a> -->
			            <button type="button" class="forceBgClassified btn btn-lg pull-right submitStep4" style="margin-bottom: 10px;">Save and Continue</button>
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
