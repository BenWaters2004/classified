@extends('layout.default')

@section('title', "DBS Application Step 4")

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
        	<div class="col-md-12">

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
			</div>
			<div class="col-md-6">	
        		<div class="box-body">
        			<div class="progress">
		                <div class="progress-bar progress-bar-primary progress-bar-striped" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width: 70%">
		                  <span class="sr-only">70% Complete (success)</span>
		                </div>
		            </div>
		            <hr />
		            <a href="{{ env('APP_URL') }}applicant/dbsStep3"><button type="button" class="forceBgClassified btn btn-default btn-lg pull-left" style="margin-bottom: 10px;">Previous</button></a>
        		</div>

        		


		        <!-- form start -->
		        <form id="dbsStep3" method="POST" action="{{ env('APP_URL') }}applicant/saveStep4Data">
        			{{ csrf_field() }}
		          <div class="box-body">
			        <button type="button" class="forceBgClassified btn btn-lg pull-right submitStep3" style="margin-bottom: 10px; margin-top: -20px;">Save and Continue</button>
		            <hr />

		            <div id="errorMessageBlock" class="callout callout-danger block-hidden">
		                <p>There were some errors in the information supplied.<br />Please review the fields below to correct the errors.</p>
		            </div>

		            <h3>Previous addresses <span style="font-size: 16px;">(covering at least 5 years)</span></h3>

		            <div class="row">
		            	<div class="col-md-12" id="previousAddressesListBlock">
		            		@if(isset($loggedIntervals) && count($loggedIntervals)>0)
			                    @foreach ($loggedIntervals as $key => $loggedInterval)
			                    	<div class="row" id="loggedInterval_block_{{$loggedInterval['addressID']}}">
			                    		<div class="col-md-6"> {{$loggedInterval['address']}}</div>
			                    		<div class="col-md-5"> {{\Carbon\Carbon::parse($loggedInterval['start'])->format('d/m/Y')}} - {{\Carbon\Carbon::parse($loggedInterval['end'])->format('d/m/Y')}}</div>
			                    		<div class="col-md-1" style=" padding-left:0;">
			                    			@if($loggedInterval['addressID'] != 0)
			                    				<button id="remove_loggedInterval_{{$loggedInterval['addressID']}}" address-id="{{$loggedInterval['addressID']}}" type="button"  class="btn btn-danger pull-left" style="margin-bottom:5px;"><i class="icon fa fa-remove"></i></button>
			                    			@else
			                    				<button type="button"  class="btn btn-default pull-left" style="margin-bottom:5px;" disabled="disabled"><i class="icon fa fa-remove"></i></button>
			                    			@endif
			                    		</div>
			                    		<input id="loggedInterval_start_{{$key}}" type="hidden" value="{{$loggedInterval['start']}}" key-id="{{$key}}">
					            		<input id="loggedInterval_end_{{$key}}" type="hidden" value="{{$loggedInterval['end']}}">
					            		<input id="loggedInterval_address_{{$key}}" type="hidden" value="{{$loggedInterval['address']}}">
					            		<hr />
			                    	</div>
			                    @endforeach

			                @endif
		            	</div>
		            </div>
		            <din style="clear: both;"></din>

		            <div class="row">
		            	<div class="col-md-12">
		            		<div class="form-group">
				              	<label for="user_previous_address_line_1">Address Line 1</label>
				              	<input class="form-control" id="user_previous_address_line_1" name="user_previous_address_line_1" type="text" value=""  maxlength="60">
				              	<span class="help-block block-hidden">Address line cannot be empty.</span>
				            </div>
		            	</div>
		            </div>

		            <div class="row">
		            	<div class="col-md-12">
		            		<div class="form-group">
				              	<label for="user_previous_address_line_2">Address Line 2</label>
				              	<input class="form-control" id="user_previous_address_line_2" name="user_previous_address_line_2" type="text" value="" maxlength="60">
				              	<span class="help-block block-hidden">Address line must contain only letters, space, full-stop or hyphen.</span>
				            </div>
		            	</div>
		            </div>

		            <div class="row">
		            	<div class="col-md-6">
		            		<div class="form-group">
				              	<label for="user_previous_address_town">Town</label>
				              	<input class="form-control" id="user_previous_address_town" name="user_previous_address_town" type="text" value=""  maxlength="30">
				              	<span class="help-block block-hidden">Town cannot be empty.</span>
				            </div>
		            	</div>

		            	<div class="col-md-6">
		            		<div class="form-group">
				              	<label for="user_previous_address_county">County</label>
				              	<input class="form-control" id="user_previous_address_county" name="user_previous_address_county" type="text" value="" maxlength="30">
				              	<span class="help-block block-hidden">County must contain only letters, space, full-stop or hyphen.</span>
				            </div>
		            	</div>
		            </div>

		            <div class="row">
		            	<div class="col-md-6">
		            		<div class="form-group">
				              	<label for="user_previous_address_postcode">Postcode</label>
				              	<input class="form-control" id="user_previous_address_postcode" name="user_previous_address_postcode" type="text" value=""  maxlength="15">
				              	<span class="help-block block-hidden">Postcode cannot be empty.</span>
				            </div>
		            	</div>

		            	<div class="col-md-6">
		            		<div class="form-group">
				              	<label for="user_previous_address_country">Country</label>
				              	<select class="form-control" id="user_previous_address_country" name="user_previous_address_country">
				                    @if(isset($countries) && count($countries)>0)
					                    @foreach ($countries as $country)
					                    	<option value="{{$country->iso3}}">{{$country->nicename}}</option>
					                    @endforeach
					                @endif
				                </select>
				                <span class="help-block block-hidden">You must select a country.</span>
				            </div>
		            	</div>
		            </div>

		            <div class="row">
		            	<div class="col-md-12"><p>At this address:</p></div>
		            	<div class="col-md-6">
		            		<div class="form-group">
				              	<label for="user_previous_address_from">From</label>
				              	<div class="input-group date">
				                  <div class="input-group-addon">
				                    <i class="fa fa-calendar"></i>
				                  </div>
				                  <input class="form-control" id="user_previous_address_from" name="user_previous_address_from" type="text" value="" placeholder="dd/mm/yyyy" >
				                  
				                </div>
				                <span id="previous_address_from_error_block" class="help-block block-hidden">Please enter a date between now and your date of birth.</span>
				            </div>
		            	</div>

		            	<div class="col-md-6">
		            		<div class="form-group">
				              	<label for="user_previous_address_until">Until</label>
				              	<div class="input-group date">
				                  <div class="input-group-addon">
				                    <i class="fa fa-calendar"></i>
				                  </div>
				                  <input class="form-control" id="user_previous_address_until" name="user_previous_address_until" type="text" value="" placeholder="dd/mm/yyyy" >
				                  
				                </div>
				                <span id="user_previous_address_until_error_block" class="help-block block-hidden">Please enter a date between from date and your current address start date.</span>
				            </div>
		            	</div>
		            </div>

		            <div class="row">
		            	<div class="col-md-12">
		            		<div class="form-group">
		            			<span id="previous_address_overlap_error_block" class="help-block block-hidden"></span>
				              	<button id="user_save_previous_address"  type="button" class="btn btn-danger  pull-right" style="margin-bottom: 10px;" validation-flag="false">Save Address</button>
				            </div>
		            	</div>
		            </div>

		            <input id="validate_dob" type="hidden" value="{{$DBSApplication->dob}}">
		            
		           
		          </div>
		          <!-- /.box-body -->

		          <div class="box-footer">
		            <div class="form-group">
			            <!-- <a href="{{ env('APP_URL') }}applicant/dbsStep3"><button type="button" class="btn btn-default btn-lg pull-left" style="margin-bottom: 10px;">Previous</button></a> -->
			            <button type="button" class="forceBgClassified btn btn-lg pull-right submitStep3" style="margin-bottom: 10px;">Save and Continue</button>
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
