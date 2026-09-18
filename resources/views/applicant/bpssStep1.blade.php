@extends('layout.default')

@section('title', "BPSS Application Step 1")

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
		        <!-- form start -->
		        <form id="bpssStep1" method="POST" action="{{ env('APP_URL') }}applicant/savebpssStep1">
        			{{ csrf_field() }}
		         	<div class="box-body">
		         		<div class="row">
        					<div class="col-md-9">
        						<h3>General Information</h3>
        					</div>
        					<div class="col-md-3">
					            <div class="form-group">
						            <button type="button" class="forceBgClassified btn btn-lg pull-right submitStep1" style="margin-bottom: 10px;">Save and Continue</button>
						        </div>
					            
					        </div>
					    </div>
					    <hr />

						<h3>Notice</h3>
						<p><strong>To Complete your BPSS clearance you will need:</strong><br>
							- If your British naturalised, a Naturalisation Certificate Number<br>
							- Your employment history (for the last 5 years)<br>
							- Your academic history (if you have attended in the last 5 years)<br>
							- Two people to provide personal references - These cannot be a relative and have to have known you for at least 5 years<br>
						</p>

					    
						<div class="row">
        					<div class="col-md-12">
					            <div id="errorMessageBlock" class="callout callout-danger block-hidden">
					                <p>There were some errors in the information supplied.<br />Please review the fields below to correct the errors.</p>
					            </div>
					        </div>
					    </div>



		            	<div class="row">
        					<div class="col-md-12">
        						<strong>Completing your BPSS Clearance:</strong><br />
								<p>Please ensure you answer all questions asked, you answer honestly and to the best of your ability.<br />
								If you have resided outside of England and Wales in the last 12 months you will be required to submit and supply your own 'Criminal Record Check'. Failure to comply with the requirements for the Baseline Security Standard may result in the delay in processing your application. If you require any assistance when completing please contact {{ env('APP_CONTACT_TEAM') }} (<a href="mailto:{{ env('APP_CONTACT_EMAIL') }}">{{ env('APP_CONTACT_EMAIL') }}</a>).</p>
					        </div>
					    </div>
					    <hr />

			            <div class="row">
			            	<div class="col-md-3">
			            		<div class="form-group">
			            			<label for="dbs_name">Name</label>
					              	<input class="form-control" name="dbs_name" type="text" value="@if(isset($DBSApplication->forename)) {{$DBSApplication->forename}} @endif @if(isset($DBSApplication->presentSurname)) {{$DBSApplication->presentSurname}} @endif" disabled="disabled">
					            </div>
			            	</div>

			            	<div class="col-md-3">
			            		<div class="form-group">
			            			<label for="dbs_email">Email</label>
					              	<input class="form-control" name="dbs_email" type="text" value="@if(isset($DBSApplication->application_email)) {{$DBSApplication->application_email}} @endif" disabled="disabled">
					            </div>
			            	</div>

			            	<div class="col-md-3">
			            		<div class="form-group">
			            			<label for="dbs_jobtitle">Job Title</label>
					              	<input class="form-control" name="dbs_jobtitle" type="text" value="@if(isset($DBSApplication->position_applied_for)) {{$DBSApplication->position_applied_for}} @endif" disabled="disabled">
					            </div>
			            	</div>

			            	<div class="col-md-3">
			            		<div class="form-group">
			            			<label for="mother_maiden_last_name">Mother's Maiden Last Name</label>
					              	<input class="form-control" id="mother_maiden_last_name" name="mother_maiden_last_name" type="text" value="@if(isset($BPSSApplication->mother_maiden_last_name)) {{$BPSSApplication->mother_maiden_last_name}} @endif" maxlength="60" required>
				                    <span class="help-block block-hidden">Please enter a value</span>
					            </div>
			            	</div>
			            </div>

			            <div class="row">
			            	<div class="col-md-3">
			            		<div class="form-group">
			            			<label for="employement_type">Known as Name</label>
					              	<input class="form-control" id="known_as_name" name="known_as_name" type="text" value="@if(isset($BPSSApplication->known_as_name) && !empty($BPSSApplication->known_as_name)) {{$BPSSApplication->known_as_name}} @endif" maxlength="60">
				                  	<span class="help-block block-hidden">Please enter a value</span>
					            </div>
			            	</div>

			            	<div class="col-md-3">
			            		<div class="form-group">
			            			<label for="employement_type">Application Type</label>
					              	<select class="form-control" id="employement_type" name="employement_type">
				                    	<option value="employee" @if(!isset($BPSSApplication->employement_type) || empty($BPSSApplication->employement_type) || $BPSSApplication->employement_type == 'employee') selected="selected" @endif>Employee</option>
				                    	<option value="contractor" @if(isset($BPSSApplication->employement_type) && $BPSSApplication->employement_type == 'contractor') selected="selected" @endif>Contractor</option>
				                  	</select>
				                  <span class="help-block block-hidden">Please select an option</span>
					            </div>
			            	</div>


		            		<div class="col-md-3">
			            		<div class="form-group">
			            			<label for="form_type">Application Step</label>
					              	<select class="form-control" id="form_type" name="form_type">
									  @if (!\Auth::user()->REVALonsite && !\Auth::user()->REVALoffsite)
									  	<option value="initial_clearance" @if(!isset($BPSSApplication->form_type) || empty($BPSSApplication->form_type) || $BPSSApplication->form_type == 'initial_clearance') selected="selected" @endif>Initial Clearance</option>
									  @endif
				                    	<option value="revalidation" @if(isset($BPSSApplication->form_type) && $BPSSApplication->form_type == 'revalidation') selected="selected" @endif>Revalidation</option>
				                  	</select>
				                  <span class="help-block block-hidden">Please select an option</span>
					            </div>
			            	</div>

			            	<div class="col-md-3">
			            		<div class="form-group">
				              		<label for="start_date">Start Date</label>
					            	<div class="input-group date">
					                  <div class="input-group-addon">
					                    <i class="fa fa-calendar"></i>
					                  </div>
					                  <input class="form-control" id="start_date" name="start_date" type="text"  value="@if (isset($BPSSApplication->start_date) && strlen($BPSSApplication->start_date) > 0){{date('d/m/Y', strtotime($BPSSApplication->start_date))}}@endif" placeholder="dd/mm/yyyy">
					                </div>
					                <span id="start_date_error_block" class="help-block block-hidden text-red">Please select a date.</span>
					            </div>
				            </div>


			            	<!-- <div id="contractorBlock" class="block-hidden">
					            <div class="col-md-3">
				            		<div class="form-group">
					              		<label for="subcontractor_company_name">Sub-Contractor Company Name</label>
						              	<input class="form-control" id="subcontractor_company_name" name="subcontractor_company_name" type="text" value="@if(isset($BPSSApplication->subcontractor_company_name)) {{$BPSSApplication->subcontractor_company_name}} @endif" maxlength="100">
					                    <span class="help-block block-hidden">Please enter a value</span>
						            </div>
					            </div>

			            	</div> -->


			            </div>
		            
			        </div>
			        <!-- /.box-body -->

		          	<div class="box-footer">
			            <div class="form-group">
				            <button type="button" class="forceBgClassified btn btn-lg pull-right submitStep1" style="margin-bottom: 10px;">Save and Continue</button>
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

