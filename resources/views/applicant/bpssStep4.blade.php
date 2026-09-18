@extends('layout.default')

@section('title', "BPSS Application Step 4")

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
		            <a href="{{ env('APP_URL') }}applicant/bpssStep3"><button type="button" class="forceBgClassified btn btn-default btn-lg pull-left" style="margin-bottom: 10px;">Previous</button></a>
        		</div>
        		
		        
		        <!-- form start -->
		        <form id="bpssStep1" method="POST" action="{{ env('APP_URL') }}applicant/savebpssStep4">
        			{{ csrf_field() }}
		         	<div class="box-body">
		         		
        				<div class="row">
        					<div class="col-md-12">
					            <div class="form-group">
					            	<!-- <a href="{{ env('APP_URL') }}applicant/bpssStep3"><button type="button" class="btn btn-default btn-lg pull-left" style="margin-bottom: 10px;">Previous</button></a> -->
						            <button type="button" class="forceBgClassified btn btn-lg pull-right submitStep4" style="margin-bottom: 10px; margin-top: -55px;">Save and Continue</button>
						        </div>
					            <hr />
					        </div>
					    </div>
		            	<div class="row">
        					<div class="col-md-12">
					            <div id="errorMessageBlock" class="callout callout-danger block-hidden">
					                <p>There were some errors in the information supplied.<br />Please review the fields below to correct the errors.</p>
					            </div>
					        </div>
					    </div>
{{--
					    <div class="row">
        					<div class="col-md-12">
        						<h3>Police Act Disclosure Application</h3>
        						<p>By signing the declaration in section 5 you are consenting to some or all of these statements.</p>
        					</div>
        				</div>
        				<div class="row">
						    <div class="col-md-12">
			            		<label>
					                <input type="checkbox" id="consent_responsible_body_submitting_dbs_application" name="consent_responsible_body_submitting_dbs_application" value="1" @if(isset($BPSSApplication->consent_responsible_body_submitting_dbs_application) && $BPSSApplication->consent_responsible_body_submitting_dbs_application) checked="checked" @endif> The Responsible Body will be submitting an application for a basic disclosure certificate on my behalf.
					            </label>
			            	</div>
			            	<div class="col-md-12">
			            		<label>
					                <input type="checkbox" id="electronic_notification_state" name="electronic_notification_state" value="1" @if(isset($BPSSApplication->electronic_notification_state) && $BPSSApplication->electronic_notification_state) checked="checked" @endif> The electronic notification will state either the “Certificate contains no information” or "Please wait to view the applicants certificate".
					            </label>
			            	</div>
			            	<div class="col-md-12">
			            		<label>
					                <input type="checkbox" id="read_and_understood_dbs_Statement" name="read_and_understood_dbs_Statement" value="1" @if(isset($BPSSApplication->read_and_understood_dbs_Statement) && $BPSSApplication->read_and_understood_dbs_Statement) checked="checked" @endif> You have read and understood the DBS statement of fair processing, <a href="https://www.gov.uk/government/publications/dbs-privacy-policies-for-basic-checks" target="_blank">Privacy Policy for Applicants. UK Gov DBS Privacy Policies for Basic Checks</a>.
					            </label><br />
					            <span class="help-block text-red block-hidden" id="police_disclosure_error">You must agree all 3 options above</span>
			            	</div>
			            </div>
--}}

					    <div class="row">
        					<div class="col-md-12">
        						<h3>Declaration</h3>
        					</div>
        				</div>

			            <hr />
			            <div class="row">
			            	<div class="col-md-12">
			            		<p>I give permission to {{ env('APP_COMPANY_NAME') }} to confirm factual information from previous/current employer(s), covering the last 5 (permanent employee) / 3 (contractor/temporary worker) years of my employment. I undertake to notify any material changes in the information I have given in the Nationality and Immigration Status section.</p>
			            	</div>
			            </div>

			            <div class="row">
			            	<div class="col-md-4">
			            		<div class="form-group">
			            			<label for="is_consent_provided">Is Consent provided?</label>
					              	<select class="form-control" id="is_consent_provided" name="is_consent_provided">
					                    <option value="1" @if ($BPSSApplication->is_consent_provided == 1) selected="selected" @endif>Yes</option>
					                    <option value="0" @if ($BPSSApplication->is_consent_provided == 0) selected="selected" @endif>No</option>
					                </select>
				                  <span class="help-block text-red block-hidden" id="consent_provided_error">You must provide consent</span>
					            </div>
			            	</div>
			            	<div class="col-md-8">
			            		<label for="is_consent_provided">&nbsp;</label>
			            		<p class="strong">
			            			@if (strlen($appSettings->consent_responsible_body_email_label) > 0) {{ $appSettings->consent_responsible_body_email_label }} @endif 
			            			@if (strlen($appSettings->consent_responsible_body_email) > 0) {{ $appSettings->consent_responsible_body_email }} @endif 
			            		</p><br />
			            		
			            	</div>
			            </div>
					    
{{--
					    <hr />
			            <div class="row">
			            	<div class="col-md-12">
			            		<p>Have you held or currently hold a higher level security clearance through your current employment or a previous employer within the last 12 months, if so please give details below:</p>
			            	</div>
			            </div>
			            <div class="row">
				            <div class="col-md-3">
			            		<label>
					                <input type="checkbox" id="clearance_level_sc" name="clearance_level_sc" value="1" @if(isset($BPSSApplication->clearance_level_sc) && $BPSSApplication->clearance_level_sc) checked="checked" @endif> SC
					            </label>
			            	<br />
			            		<label>
					                <input type="checkbox" id="clearance_level_dv" name="clearance_level_dv" value="1" @if(isset($BPSSApplication->clearance_level_dv) && $BPSSApplication->clearance_level_dv) checked="checked" @endif> DV
					            </label>
			            	</div>
			            </div>
			            <div class="row">
			            	<div class="col-md-3">
				            		<div class="form-group">
						              	<label for="date_issued">Date Issued</label>
						              	<div class="input-group date">
						                  <div class="input-group-addon">
						                    <i class="fa fa-calendar"></i>
						                  </div>
						                  <input class="form-control" id="date_issued" name="date_issued" type="text" value="@if(isset($BPSSApplication->date_issued) && strlen($BPSSApplication->date_issued) == 10){{\Carbon\Carbon::parse($BPSSApplication->date_issued)->format('d/m/Y')}}@endif" placeholder="dd/mm/yyyy" >								                  
						                </div>
						                <span id="employment_date_to_error_block" class="help-block block-hidden">Please enter a date between now and your date of birth.</span>
						            </div>
					            </div>

			            	<div class="col-md-9">
			            		<div class="form-group">
				              		<label for="clearance_level_details">Where was it issued? Please give details of the company name and address:</label>
							        <textarea class="form-control" rows="5"  id="clearance_level_details"  name="clearance_level_details">@if(isset($BPSSApplication->clearance_level_details)){{$BPSSApplication->clearance_level_details}}@endif</textarea>
					            </div>
				            </div>
			            </div>
--}}
					    
			            
		            	
			            



			        </div>
			        <!-- /.box-body -->

		          	<div class="box-footer">
			            <div class="form-group">
			            	<!-- <a href="{{ env('APP_URL') }}applicant/bpssStep3"><button type="button" class="btn btn-default btn-lg pull-left" style="margin-bottom: 10px;">Previous</button></a> -->
				            <button type="button" class="forceBgClassified btn btn-lg pull-right submitStep4" style="margin-bottom: 10px;">Save and Continue</button>
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
@include('applicant.bpssValidations')<!-- 
<script type="text/javascript">

</script> -->
@endsection
