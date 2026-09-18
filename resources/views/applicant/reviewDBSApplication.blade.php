@if (\Auth::check()) 
    @inject('checkAccess', 'App\Http\Controllers\Controller')
@endif

@extends('layout.default')
@section('title', "Review DBS application")



@section('content')
<div class="row">
    <!-- left column -->
    <div class="col-md-12">
      <!-- general form elements -->
      <div class="box box-default">
        <div class="box-header with-border">

        	<div class="form-group pull-left">
        		<a href="{{ env('APP_URL') }}login"><button type="button" class="btn btn-warning" style="margin-bottom: 10px;">Back</button></a>
	        </div>
	        @if ($DBSApplication->applicationStatus == 1)
	        <div class="form-group pull-right">
        		<a href="{{ env('APP_URL') }}applicant/dbsReset"><button type="button" class="btn btn-success" style="margin-bottom: 10px;">Edit</button></a>
	        </div>
	        @endif
            <hr style="padding: 0; margin: 15px -15px !important;" />

          <h3 class="box-title">Application Details</h3>
        </div>
        <!-- /.box-header -->
	
		<div class="box-body">
	        <div class="row">
            	<div class="col-md-6">
            		<div class="row">
            			<div class="col-md-12">
		            		<h5 class="strong">Application Reference</h5>
		            		<div class="input-group">
		            			@if ($DBSApplication->applicationStatus == 0)
		                        	<small class="label label-primary"><i class="fa fa-clock-o"></i> Incomplete</small>
			                    @elseif ($DBSApplication->applicationStatus == 1)
			                        <small class="label label-warning">Pending Review</small>
			                    @elseif ($DBSApplication->applicationStatus == 2)
			                        <small class="label label-danger">Review Fail</small>
			                    @elseif ($DBSApplication->applicationStatus == 3)
			                        <small class="label label-warning">Pending Submission</small>
			                    @elseif ($DBSApplication->applicationStatus == 4)
			                        <small class="label label-success">Submitted to DBS</small>
			                    @elseif ($DBSApplication->applicationStatus == 5)
			                        <small class="label label-success">DBS Submission Success</small>
			                    @elseif ($DBSApplication->applicationStatus == 6)
			                        <small class="label label-danger">DBS Fail</small>
			                    @elseif ($DBSApplication->applicationStatus == 8)
					                <small class="label label-success">{{$DBSApplication->dbsResponse_int023_DisclosureStatus}}</small>
			                    @endif
				            </div>
				        </div>
				    </div>

				    <div class="row">
            			<div class="col-md-12">
		            		<h5 class="strong">Purpose for application</h5>
		            		{{ucfirst($DBSApplication->purpose_of_check)}}
				        </div>
				    </div>

				    <div class="row">
            			<div class="col-md-12">
		            		<h5 class="strong">Position applied for</h5>
		            		{{ucfirst($DBSApplication->position_applied_for)}}
				        </div>
				    </div>
            	</div>


            	<div class="col-md-6">
				    <div class="row">
            			<div class="col-md-12">
		            		<h5 class="strong">Employment Sector</h5>
		            		{{strtoupper($DBSApplication->employment_sector_name)}}
				        </div>
				    </div>

				    <div class="row">
            			<div class="col-md-12">
		            		<h5 class="strong">Name of Employer</h5>
		            		{{ucfirst($DBSApplication->name_of_employer)}}
				        </div>
				    </div>

            	</div>	
			</div>

			<div class="row">
            	<div class="col-md-12">
            		<h5 class="strong" id="declaration_report" style="cursor:pointer;">Declaration Details <i class="fa fa-arrow-down"></i></h5>
            		@if($DBSApplication->terms_accepted)
            			Terms accepted on <span class="strong">{{date("D jS M Y @ H:i:s", strtotime($DBSApplication->terms_accepted_date))}}
            		@else
            			<span class="text-red">Terms not accepted yet!</span>
            		@endif
            	</div>
            </div>
            
            <div id="reviewDeclarationBlock" style="display: none;">
	            <div class="row">
	            	<div class="col-md-8">
	            		<p>&nbsp;</p>
			            <h3>Privacy Policy - basics check declaration</h3>
			                I have read the Basic DBS Check Processing Privacy Policy <a href="https://www.gov.uk/government/publications/dbs-privacy-policies" target="_blank">https://www.gov.uk/government/publications/dbs-privacy-policies</a> and I understand how DBS will process my personal data.<br />
						<p>&nbsp;</p>
			            <table style="width: 100%; text-align: left;" class="table table-striped">
	                      	<tr>
	                      		<td style="border: 1px solid black;" class="col-md-9">Applicant must consent by typing I CONFIRM in box A</td>
	                      		<td style="border: 1px solid black;" class="col-md-3">A: @if(isset($DBSApplication->privacy_policy) && $DBSApplication->privacy_policy){{'I confirm'}}@endif</td>
	                      	</tr>
	                    </table>

	            	</div>
	            </div>

	            <div class="row">
	            	<div class="col-md-8">

			                By confirming your acceptance of the below declaration, you are giving us consent to receive an e-result regarding your basic DBS application. If consent is not given you have the option to complete a basic check via <a href="https://www.gov.uk/DBS" target="_blank">www.gov.uk/DBS</a> and I understand how DBS will process my personal data.<br />
			            <p>&nbsp;</p>
			            <h3>Consent to obtain basic check electronic result</h3>

			                I consent to the DBS providing an electronic result directly to the responsible organisation that has submitted my application. I understand that an electronic result contains a message that indicates either the certificate does not contain criminal record information or to await certificate which will indicate that my certificate contains criminal record information. In some cases the responsible organisation may provide this information directly to my employer prior to me receiving my certificate.<br />
					            I understand if I do not consent to an electronic result being issued to the responsible organisation submitting my application that I must not proceed with this application and I should apply directly to DBS <a href="https://www.gov.uk/request-copy-criminal-record" target="_blank">Request a basic DBS check - GOV.UK (www.gov.uk)</a>. I understand that to withdraw my consent whilst my application is in progress I must contact the DBS helpline 03000 200 190. My application will then be withdrawn.<br />

			            <table style="width: 100%; text-align: left;" class="table table-striped">
	                      	<tr>
	                      		<td style="border: 1px solid black;" class="col-md-9">Applicant must consent by typing I AGREE in box B</td>
	                      		<td style="border: 1px solid black;" class="col-md-3">B: @if(isset($DBSApplication->consent_basic_check) && $DBSApplication->consent_basic_check){{'I agree'}}@endif</td>
	                      	</tr>
	                    </table>
	            	</div>
	            </div>

	            <div class="row">
	            	<div class="col-md-8">

			                As the applicant you must explicitly confirm that you have provided complete and true information in support of this application.
			            <p>&nbsp;</p>
			            <h3>Declaration By Applicant</h3>

			                I have provided complete and true information in support of the application, and I understand that knowingly making a false statement for this purpose is a criminal offence.<br />

			            <table style="width: 100%; text-align: left;" class="table table-striped">
	                      	<tr>
	                      		<td style="border: 1px solid black;" class="col-md-9">Applicant must consent by typing I CONFIRM in box C</td>
	                      		<td style="border: 1px solid black;" class="col-md-3">C: @if(isset($DBSApplication->declaration_by_applicant) && $DBSApplication->declaration_by_applicant){{'I confirm'}}@endif</td>
	                      	</tr>
	                    </table>
	            	</div>
	            </div>

	        </div>


			<hr style="padding: 0; margin: 15px -15px !important;" />
			<div class="row">
            	<div class="col-md-12">
            		<h3 class="box-title">Contact Details</h3>
            	</div>
            </div>

            <div class="row">
            	<div class="col-md-4">
				    <h5 class="strong">Email Address</h5>
	            	{{$DBSApplication->application_email}}
	            </div>
	            <div class="col-md-4">
				    <h5 class="strong">Contact Number</h5>
	            	+{{$DBSApplication->contact_number_country_code}} {{$DBSApplication->contact_number}}
	            </div>
            	<div class="col-md-4">
				    <h5 class="strong">Mobile Number</h5>
	            	+{{$DBSApplication->mobile_number_country_code}} {{$DBSApplication->mobile_number}}
	            </div>
            </div>

            <hr style="padding: 0; margin: 15px -15px !important;" />
			<div class="row">
            	<div class="col-md-12">
            		<h3 class="box-title">About Applicant</h3>
            	</div>
            </div>
            <div class="row">
            	<div class="col-md-6">
            		<div class="row">
            			<div class="col-md-12">
		            		<h5 class="strong">Title</h5>
		            		{{$DBSApplication->userTitle}}
				        </div>
				    </div>

				    <div class="row">
            			<div class="col-md-12">
		            		<h5 class="strong">Date of Birth</h5>
		            		{{date("d/m/Y", strtotime($DBSApplication->dob))}}
				        </div>
				    </div>

				    <div class="row">
            			<div class="col-md-12">
		            		<h5 class="strong">Birth Country</h5>
		            		{{ucfirst(strtolower($DBSApplication->birth_country_fullName))}}
				        </div>
				    </div>
				    <div class="row">
            			<div class="col-md-12">
		            		<h5 class="strong">Forename</h5>
		            		{{ucfirst(strtolower($DBSApplication->forename))}}
				        </div>
				    </div>
				    <div class="row">
            			<div class="col-md-12">
		            		<h5 class="strong">Middle Names</h5>
		            		{{ucfirst(strtolower($DBSApplication->middlename))}}
				        </div>
				    </div>
            	</div>


            	<div class="col-md-6">
            		<div class="row">
            			<div class="col-md-12">
		            		<h5 class="strong">Gender</h5>
		            		{{ucfirst($DBSApplication->gender)}}
				        </div>
				    </div>

				    <div class="row">
            			<div class="col-md-12">
		            		<h5 class="strong">Birth Town</h5>
		            		{{ucfirst(strtolower($DBSApplication->birth_town))}}
				        </div>
				    </div>

				    <div class="row">
            			<div class="col-md-12">
		            		<h5 class="strong">Birth Nationality</h5>
		            		{{ucfirst(strtolower($DBSApplication->birth_nationality))}}
				        </div>
				    </div>

				    <div class="row">
            			<div class="col-md-12">
		            		<h5 class="strong">Present Surname</h5>
		            		{{ucfirst($DBSApplication->presentSurname)}}
				        </div>
				    </div>

            	</div>	
			</div>



			<hr style="padding: 0; margin: 15px -15px !important;" />
			<div class="row">
            	<div class="col-md-12">
            		<h3 class="box-title">Other Names</h3>
            	</div>
            </div>
            @if(isset($DBSApplication->otherNames) && count($DBSApplication->otherNames) > 0)
            	@foreach ($DBSApplication->otherNames as $otherName)
		            <div class="row">
		            	<div class="col-md-3">
						    <h5 class="strong">Period</h5>
			            	{{date("d/m/Y", strtotime($otherName->dateFrom))}} - {{date("d/m/Y", strtotime($otherName->dateTo))}}
			            </div>
			            <div class="col-md-3">
						    <h5 class="strong">Forename</h5>
			            	{{$otherName->other_forename}}
			            </div>
		            	<div class="col-md-3">
						    <h5 class="strong">Middlename</h5>
			            	{{$otherName->other_middlename}}
			            </div>
			            <div class="col-md-3">
						    <h5 class="strong">Surname</h5>
			            	{{$otherName->other_surname}}
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


		   	<hr style="padding: 0; margin: 15px -15px !important;" />
			<div class="row">
            	<div class="col-md-12">
            		<h3 class="box-title">Current Address</h3>
            	</div>
            </div>

            <div class="row">
	            <div class="col-md-4">
				    <h5 class="strong">Address Line 1</h5>
	            	{{$DBSApplication->address_line_1}}
	            </div>
            	<div class="col-md-4">
				    <h5 class="strong">Address Line 2</h5>
	            	{{$DBSApplication->address_line_2}}
	            </div>
	            <div class="col-md-4">
				    <h5 class="strong">Town</h5>
	            	{{ucfirst($DBSApplication->address_town)}}
	            </div>
            </div>
            <div class="row">
	            <div class="col-md-4">
				    <h5 class="strong">County</h5>
	            	{{ucfirst($DBSApplication->address_county)}}
	            </div>
            	<div class="col-md-4">
				    <h5 class="strong">Postcode</h5>
	            	{{strtoupper($DBSApplication->address_postcode)}}
	            </div>
	            <div class="col-md-4">
				    <h5 class="strong">Country</h5>
	            	{{ucfirst(strtolower($DBSApplication->address_country_fullName))}}
	            </div>
            </div>
             

		   	<hr style="padding: 0; margin: 15px -15px !important;" />
			<div class="row">
            	<div class="col-md-12">
            		<h3 class="box-title">Previous Addresses</h3>
            	</div>
            </div>

            @if(isset($DBSApplication->previousAddresses) && count($DBSApplication->previousAddresses) > 0)
            	@foreach ($DBSApplication->previousAddresses as $key => $previousAddress)
            		@if ($key != 0)
	            		<div class="row">
			            	<div class="col-md-12">
							    &nbsp;
				            </div>
			            </div>
		            @endif

		            <div class="row">
		            	<div class="col-md-12">
						    <span class="strong">Period</span> 
			            	{{date("d/m/Y", strtotime($previousAddress->previous_address_from))}} - {{date("d/m/Y", strtotime($previousAddress->previous_address_to))}}
			            </div>
		            </div>
		            <div class="row">
			            <div class="col-md-4">
						    <h5 class="strong">Address Line 1</h5>
			            	{{$previousAddress->previous_address_line_1}}
			            </div>
		            	<div class="col-md-4">
						    <h5 class="strong">Address Line 2</h5>
			            	{{$previousAddress->previous_address_line_2}}
			            </div>
			            <div class="col-md-4">
						    <h5 class="strong">Town</h5>
			            	{{ucfirst($previousAddress->previous_address_town)}}
			            </div>
		            </div>
		            <div class="row">
			            <div class="col-md-4">
						    <h5 class="strong">County</h5>
			            	{{ucfirst($previousAddress->previous_address_county)}}
			            </div>
		            	<div class="col-md-4">
						    <h5 class="strong">Postcode</h5>
			            	{{strtoupper($previousAddress->previous_address_postcode)}}
			            </div>
			            <div class="col-md-4">
						    <h5 class="strong">Country</h5>
			            	{{ucfirst(strtolower($previousAddress->previous_address_country_fullName))}}
			            </div>
		            </div>
		        @endforeach
		    @else
		    	<div class="row">
		    		<div class="col-md-3">
		    			No other address recorded
		    		</div>
		    	</div>
		   	@endif



		   	<hr style="padding: 0; margin: 15px -15px !important;" />
			<div class="row">
            	<div class="col-md-12">
            		<h3 class="box-title">Supporting Evidence</h3>
            	</div>
            </div>
            <div class="row">
            	<div class="col-md-6">
            		<div class="row">
            			<div class="col-md-12">
		            		<h5 class="strong">National Insurance Number</h5>
		            		@if (!empty($DBSApplication->supporting_nino)) {{$DBSApplication->supporting_nino}} @else &nbsp; @endif
				        </div>
				    </div>

				    <div class="row">
            			<div class="col-md-12">
		            		<h5 class="strong">Passport No</h5>
		            		@if (!empty($DBSApplication->supporting_passport)) {{$DBSApplication->supporting_passport}} @else &nbsp; @endif
				        </div>
				    </div>
            	</div>


            	<div class="col-md-6">
            		<div class="row">
            			<div class="col-md-12">
		            		<h5 class="strong">UK Drivers Licence Number</h5>
		            		@if (!empty($DBSApplication->supporting_dln)) {{ucfirst($DBSApplication->supporting_dln)}} @else &nbsp; @endif
				        </div>
				    </div>

				    <div class="row">
            			<div class="col-md-12">
		            		<h5 class="strong">Passport Issued In</h5>
		            		@if (!empty($DBSApplication->supporting_passport_country_fullName)) {{ucfirst(strtolower($DBSApplication->supporting_passport_country_fullName))}} @else &nbsp; @endif
				        </div>
				    </div>


            	</div>	
			</div>

			<hr style="padding: 0; margin: 15px -15px !important;" />
			<div class="row">
            	<div class="col-md-12">
            		<h3 class="box-title">Other Information</h3>
            	</div>
            </div>
            <div class="row">
            	<div class="col-md-12">
            		@if (!empty($DBSApplication->supporting_paper_certificate)) 
            			<p class="strong">Applicant wants to receive a paper certificate</p>
            			<p><span class="strong">Paper certificate to be posted to: </span> </p>
            			@if (!empty($DBSApplication->user_paper_certificate_different_address == 0)) 
            				<span>{{$DBSApplication->address_line_1}} {{$DBSApplication->address_line_2}} {{$DBSApplication->address_town}} {{$DBSApplication->address_county}} {{$DBSApplication->address_postcode}} {{$DBSApplication->address_country_fullName}}</span>
            			@else 
            				<span>{{$DBSApplication->certificate_address_recipient_name}} {{$DBSApplication->certificate_address_line_1}} {{$DBSApplication->certificate_address_line_2}} {{$DBSApplication->certificate_address_town}} {{$DBSApplication->certificate_address_county}} {{$DBSApplication->certificate_address_postcode}} {{$DBSApplication->certificate_address_country_fullName}}</span>
            			@endif
            		@endif
            		@if (!empty($DBSApplication->dbs_consent)) <br />
            			<p class="strong">Consent provided to RO to view the online DBS certificate when it has been issued.</p>
            			<p><span class="strong">Consent date:</span> {{date("D jS M Y @ H:i:s", strtotime($DBSApplication->dbs_consent_date))}}</p>
            			<p>
            				<span class="strong">Third party email address to provide consent to view the DBS certificate once it has been issued:</span><br />
            				{{$loggedUserOrganisationDetails->organisationEmail}}
            			</p>
            		@endif
            	</div>            	
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
$(document).on("click", '#declaration_report', function() {
		$("#reviewDeclarationBlock").show();		
	});
</script>
@endsection
