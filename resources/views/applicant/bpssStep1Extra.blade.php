@extends('layout.default')

@section('title', "BPSS Application Step 1 Extra")

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
		        <form id="bpssStep1" method="POST" action="{{ env('APP_URL') }}applicant/savebpssStep1Extra">
        			{{ csrf_field() }}
		         	<div class="box-body">
		         		
        				<div class="row">
        					<div class="col-md-12">
					            <div class="form-group">
					            	<!-- <a href="{{ env('APP_URL') }}applicant/bpssStep4"><button type="button" class="btn btn-default btn-lg pull-left" style="margin-bottom: 10px;">Previous</button></a> -->
						            <button type="button" class="forceBgClassified btn btn-lg pull-right submitStep1Extra" style="margin-bottom: 10px; margin-top: -55px;">Save and Continue</button>
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

					    <div class="row">
        					<div class="col-md-12">
        						<h3>Contractor Company Information:</h3>
        						<p><strong>Note: </strong>Contractors to complete this section</p>
        					</div>
        				</div>
        				

			            <div class="row">					           
				            <div class="col-md-4">
			            		<div class="form-group">
				              		<label for="contractor_name_of_company">Name of Company:</label>
							        <input class="form-control" id="contractor_name_of_company" name="contractor_name_of_company" type="text" value="@if(isset($BPSSApplication->contractor_name_of_company)){{$BPSSApplication->contractor_name_of_company}}@endif" maxlength="255">
					            </div>
			            	</div>
			            	<div class="col-md-4">
			            		<div class="form-group">
				              		<label for="contractor_address_of_company">Address of Company:</label>
							        <input class="form-control" id="contractor_address_of_company" name="contractor_address_of_company" type="text" value="@if(isset($BPSSApplication->contractor_address_of_company)){{$BPSSApplication->contractor_address_of_company}}@endif" maxlength="255">
					            </div>
			            	</div>
			            	<div class="col-md-4">
			            		<div class="form-group">
				              		<label for="contractor_number_of_years_with_company">Number of years with the company:</label>
							        <input class="form-control" id="contractor_number_of_years_with_company" name="contractor_number_of_years_with_company" type="text" value="@if(isset($BPSSApplication->contractor_number_of_years_with_company)){{$BPSSApplication->contractor_number_of_years_with_company}}@endif" maxlength="255">
					            </div>
			            	</div>
			            </div>

			            <div class="row">					           
				            <div class="col-md-4">
			            		<div class="form-group">
				              		<label for="contractor_name_of_agency">Name of Agency:</label>
							        <input class="form-control" id="contractor_name_of_agency" name="contractor_name_of_agency" type="text" value="@if(isset($BPSSApplication->contractor_name_of_agency)){{$BPSSApplication->contractor_name_of_agency}}@endif" maxlength="255">
					            </div>
			            	</div>
			            	<div class="col-md-4">
			            		<div class="form-group">
				              		<label for="contractor_address_of_agency">Address of Agency:</label>
							        <input class="form-control" id="contractor_address_of_agency" name="contractor_address_of_agency" type="text" value="@if(isset($BPSSApplication->contractor_address_of_agency)){{$BPSSApplication->contractor_address_of_agency}}@endif" maxlength="255">
					            </div>
			            	</div>
			            	<div class="col-md-4">
			            		<div class="form-group">
				              		<label for="contractor_agency_contact">Agency Contact:</label>
							        <input class="form-control" id="contractor_agency_contact" name="contractor_agency_contact" type="text" value="@if(isset($BPSSApplication->contractor_agency_contact)){{$BPSSApplication->contractor_agency_contact}}@endif" maxlength="255">
					            </div>
			            	</div>
			            </div>
			            <div class="row">					           
				            <div class="col-md-4">
			            		<div class="form-group">
				              		<label for="contractor_utas_contact">Contact:</label>
							        <input class="form-control" id="contractor_utas_contact" name="contractor_utas_contact" type="text" value="@if(isset($BPSSApplication->contractor_utas_contact)){{$BPSSApplication->contractor_utas_contact}}@endif" maxlength="255">
					            </div>
			            	</div>
			            </div>

		            	 <hr />
		            	
		            	<div class="row">
			            	<div class="col-md-6">
			            		<div class="form-group">
			            			<label for="selfemployment">Have you been self-employed in the last 3 years?</label>
			            			<div class="row">
			            				<div class="col-md-3">
							              	<select class="form-control" id="selfemployment" name="selfemployment">
							                    <option value="1" @if ($BPSSApplication->selfemployment == 1) selected="selected" @endif>Yes</option>
							                    <option value="0" @if ($BPSSApplication->selfemployment == 0) selected="selected" @endif>No</option>
							                </select>
							            </div>
							        </div>
				                  <span class="help-block block-hidden">Please select an option</span>
					            </div>
			            	</div>

			            	<div id="selfemploymentOptionsBlock" class="col-md-6 block-hidden">
			            		<div class="form-group">
			            			<label for="selfemployment_documentation_option">Please Select documentation evidence:</label>
			            			<div class="row">
			            				<div class="col-md-12">
							              	<select class="form-control" id="selfemployment_documentation_option" name="selfemployment_documentation_option">
							                    <option value="self_assessment" @if ($BPSSApplication->selfemployment_documentation_option == 'self_assessment') selected="selected" @endif>Self Assessment Documents</option>
							                    <option value="accountant" @if ($BPSSApplication->selfemployment_documentation_option == 'accountant') selected="selected" @endif>Accountant Details</option>
							                </select>
							            </div>
							        </div>
				                  <span class="help-block block-hidden">Please select an option</span>
					            </div>
			            	</div>
			            </div>

			            <div id="selfemploymentDocumentationBlock" class="row block-hidden">
			            	<div class="col-md-6">&nbsp;</div>
				            <div class="col-md-6">
			            		Please email your Self Assessment documents to <a href="mailto:{{ env('APP_CONTACT_EMAIL') }}">{{ env('APP_CONTACT_EMAIL') }}</a>
				            </div>
		            	</div>

		            	<div id="selfemploymentAccountantBlock" class="block-hidden">
		            		<div class="row">
					            <div class="col-md-6">
				            		<div class="form-group">
						              	<label for="se_accountant_known_from">Period Known From</label>
						              	<div class="input-group date">
						                  <div class="input-group-addon">
						                    <i class="fa fa-calendar"></i>
						                  </div>
						                  <input class="form-control" id="se_accountant_known_from" name="se_accountant_known_from" type="text" value="@if (isset($BPSSApplication->se_accountant_known_from) && strlen($BPSSApplication->se_accountant_known_from) > 0){{date('d/m/Y', strtotime($BPSSApplication->se_accountant_known_from))}}@endif" placeholder="dd/mm/yyyy" >								                  
						                </div>
						                <span id="se_accountant_known_from_error_block" class="help-block block-hidden">Please enter a date between now and your date of birth.</span>
						            </div>
					            </div>

					            <div class="col-md-6">
				            		<div class="form-group">
						              	<label for="se_accountant_known_to">Period Known To</label>
						              	<div class="input-group date">
						                  <div class="input-group-addon">
						                    <i class="fa fa-calendar"></i>
						                  </div>
						                  <input class="form-control" id="se_accountant_known_to" name="se_accountant_known_to" type="text" value="@if (isset($BPSSApplication->se_accountant_known_to) && strlen($BPSSApplication->se_accountant_known_to) > 0){{date('d/m/Y', strtotime($BPSSApplication->se_accountant_known_to))}}@endif" placeholder="dd/mm/yyyy" >								                  
						                </div>
						                <span id="se_accountant_known_to_error_block" class="help-block block-hidden">Please enter a date between now and your date of birth.</span>
						            </div>
					            </div>
					            <input id="validate_dob" name="validate_dob" type="hidden"  value="@if (strlen($DBSApplication->dob) > 0){{date('Y-m-d', strtotime($DBSApplication->dob))}}@else{{date()}}@endif">
					        </div>

				            <div class="row">					           
					            <div class="col-md-12">
				            		<div class="form-group">
					              		<label for="se_accountant_name">Accountant Name:</label>
								        <input class="form-control" id="se_accountant_name" name="se_accountant_name" type="text" value="@if(isset($BPSSApplication->se_accountant_name)){{$BPSSApplication->se_accountant_name}}@endif" maxlength="100">
						            </div>
				            	</div>
				            </div>

				            <div class="row">					           
					            <div class="col-md-12">
				            		<div class="form-group">
					              		<label for="se_accountant_address">Address:</label>
								        <input class="form-control" id="se_accountant_address" name="se_accountant_address" type="text" value="@if(isset($BPSSApplication->se_accountant_address)){{$BPSSApplication->se_accountant_address}}@endif" maxlength="100">
						            </div>
				            	</div>
				            </div>

				            <div class="row">					           
					            <div class="col-md-6">
				            		<div class="form-group">
					              		<label for="se_accountant_town">Town:</label>
								        <input class="form-control" id="se_accountant_town" name="se_accountant_town" type="text" value="@if(isset($BPSSApplication->se_accountant_town)){{$BPSSApplication->se_accountant_town}}@endif" maxlength="50">
						            </div>
				            	</div>
				            	<div class="col-md-6">
				            		<div class="form-group">
					              		<label for="se_accountant_postcode">Postcode:</label>
								        <input class="form-control" id="se_accountant_postcode" name="se_accountant_postcode" type="text" value="@if(isset($BPSSApplication->se_accountant_postcode)){{$BPSSApplication->se_accountant_postcode}}@endif" maxlength="50">
						            </div>
				            	</div>
				            </div>

				            <div class="row">					           
					            <div class="col-md-6">
				            		<div class="form-group">
					              		<label for="se_accountant_email">Email:</label>
								        <input class="form-control" id="se_accountant_email" name="se_accountant_email" type="email" value="@if(isset($BPSSApplication->se_accountant_email)){{$BPSSApplication->se_accountant_email}}@endif" maxlength="100">
						            </div>
				            	</div>
				            	<div class="col-md-6">
				            		<div class="form-group">
					              		<label for="se_accountant_contact_number">Contact Number:</label>
								        <input class="form-control" id="se_accountant_contact_number" name="se_accountant_contact_number" type="text" value="@if(isset($BPSSApplication->se_accountant_contact_number)){{$BPSSApplication->se_accountant_contact_number}}@endif" maxlength="20">
						            </div>
				            	</div>
				            </div>

		            	</div>

			            



			        </div>
			        <!-- /.box-body -->

		          	<div class="box-footer">
			            <div class="form-group">
			            	<!-- <a href="{{ env('APP_URL') }}applicant/bpssStep4"><button type="button" class="btn btn-default btn-lg pull-left" style="margin-bottom: 10px;">Previous</button></a> -->
				            <button type="button" class="forceBgClassified btn btn-lg pull-right submitStep1Extra" style="margin-bottom: 10px;">Save and Continue</button>
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
