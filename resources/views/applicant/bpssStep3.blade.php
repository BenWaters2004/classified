@extends('layout.default')

@section('title', "BPSS Application Step 3")

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
		            <a href="{{ env('APP_URL') }}applicant/bpssStep2"><button type="button" class="forceBgClassified btn btn-default btn-lg pull-left" style="margin-bottom: 10px;">Previous</button></a>
        		</div>

		        <!-- form start -->
		        <form id="bpssStep1" method="POST" action="{{ env('APP_URL') }}applicant/savebpssStep3">
        			{{ csrf_field() }}
		         	<div class="box-body">
		         		
        				<div class="row">
        					<div class="col-md-12">
					            <div class="form-group">
					            	<!-- <a href="{{ env('APP_URL') }}applicant/bpssStep2"><button type="button" class="btn btn-default btn-lg pull-left" style="margin-bottom: 10px;">Previous</button></a> -->
						            <button type="button" class="forceBgClassified btn btn-lg pull-right submitStep3" style="margin-bottom: 10px; margin-top: -55px;">Save and Continue</button>
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
        						<h3>Academic period</h3>
        						<p>If you have attended college/university in the last 5 years please give details below</p>
        					</div>
        				</div>
					    <hr />



			            <div class="row">
			            	<div class="col-md-3">
			            		<div class="form-group">
			            			<label>
				                <input type="checkbox" id="school_data" name="school_data" value="1" @if(isset($BPSSApplication->school_data) && $BPSSApplication->school_data) checked="checked" @endif> School<br />
				                <span id="school_error_block" class="help-block block-hidden">You must enter the school details.</span>
				              </label>
					            </div>
			            	</div>
			            </div>

			            <div id="schoolDataBlock" class="block-hidden">
			            	<div class="row">
					            <div class="col-md-4">
				            		<div class="form-group">
					              		<label for="school_name">School Name:</label>
								        <input class="form-control" id="school_name" name="school_name" type="text" value="@if(isset($BPSSApplication->school_name)) {{$BPSSApplication->school_name}} @endif" maxlength="255">
						            </div>
					            </div>

					            <div class="col-md-4">
				            		<div class="form-group">
						              	<label for="school_date_from">Date From:</label>
						              	<div class="input-group date">
						                  <div class="input-group-addon">
						                    <i class="fa fa-calendar"></i>
						                  </div>
						                  <input class="form-control" id="school_date_from" name="school_date_from" type="text" value="@if (isset($BPSSApplication->school_date_from) && strlen($BPSSApplication->school_date_from) > 0){{date('d/m/Y', strtotime($BPSSApplication->school_date_from))}}@endif" placeholder="dd/mm/yyyy" >								                  
						                </div>
						                <span id="previous_address_from_error_block" class="help-block block-hidden">Please enter a date between now and your date of birth.</span>
						            </div>
					            </div>

					            <div class="col-md-4">
				            		<div class="form-group">
						              	<label for="school_date_to">Date to:</label>
						              	<div class="input-group date">
						                  <div class="input-group-addon">
						                    <i class="fa fa-calendar"></i>
						                  </div>
						                  <input class="form-control" id="school_date_to" name="school_date_to" type="text" value="@if (isset($BPSSApplication->school_date_to) && strlen($BPSSApplication->school_date_to) > 0){{date('d/m/Y', strtotime($BPSSApplication->school_date_to))}}@endif" placeholder="dd/mm/yyyy" >								                  
						                </div>
						                <span id="previous_address_from_error_block" class="help-block block-hidden">Please enter a date between now and your date of birth.</span>
						            </div>
					            </div>

			            	</div>

			            	<div class="row">
					            <div class="col-md-4">
				            		<div class="form-group">
					              		<label for="school_contact_name">Contact Name:</label>
								        <input class="form-control" id="school_contact_name" name="school_contact_name" type="text" value="@if(isset($BPSSApplication->school_contact_name)) {{$BPSSApplication->school_contact_name}} @endif" maxlength="255">
						            </div>
					            </div>

					            <div class="col-md-8">
				            		<div class="form-group">
					              		<label for="school_contact_address">Contact Address:</label>
								        <input class="form-control" id="school_contact_address" name="school_contact_address" type="text" value="@if(isset($BPSSApplication->school_contact_address)) {{$BPSSApplication->school_contact_address}} @endif" maxlength="255">
						            </div>
					            </div>

			            	</div>
			            	<hr />
			            </div>

			            <div class="row">
			            	<div class="col-md-3">
			            		<div class="form-group">
			            			<label>
				                <input type="checkbox" id="college_data" name="college_data" value="1" @if(isset($BPSSApplication->college_data) && $BPSSApplication->college_data) checked="checked" @endif> College<br />
				                <span id="college_error_block" class="help-block block-hidden">You must enter the college details.</span>
				              </label>
					            </div>
			            	</div>
			            </div>

			            <div id="collegeDataBlock" class="block-hidden">
			            	<div class="row">
					            <div class="col-md-4">
				            		<div class="form-group">
					              		<label for="college_name">College Name:</label>
								        <input class="form-control" id="college_name" name="college_name" type="text" value="@if(isset($BPSSApplication->college_name)) {{$BPSSApplication->college_name}} @endif" maxlength="255">
						            </div>
					            </div>

					            <div class="col-md-4">
				            		<div class="form-group">
						              	<label for="college_date_from">Date From:</label>
						              	<div class="input-group date">
						                  <div class="input-group-addon">
						                    <i class="fa fa-calendar"></i>
						                  </div>
						                  <input class="form-control" id="college_date_from" name="college_date_from" type="text" value="@if (isset($BPSSApplication->college_date_from) && strlen($BPSSApplication->college_date_from) > 0){{date('d/m/Y', strtotime($BPSSApplication->college_date_from))}}@endif" placeholder="dd/mm/yyyy" >								                  
						                </div>
						                <span id="previous_address_from_error_block" class="help-block block-hidden">Please enter a date between now and your date of birth.</span>
						            </div>
					            </div>

					            <div class="col-md-4">
				            		<div class="form-group">
						              	<label for="college_date_to">Date to:</label>
						              	<div class="input-group date">
						                  <div class="input-group-addon">
						                    <i class="fa fa-calendar"></i>
						                  </div>
						                  <input class="form-control" id="college_date_to" name="college_date_to" type="text" value="@if (isset($BPSSApplication->college_date_to) && strlen($BPSSApplication->college_date_to) > 0){{date('d/m/Y', strtotime($BPSSApplication->college_date_to))}}@endif" placeholder="dd/mm/yyyy" >								                  
						                </div>
						                <span id="previous_address_from_error_block" class="help-block block-hidden">Please enter a date between now and your date of birth.</span>
						            </div>
					            </div>

			            	</div>

			            	<div class="row">
					            <div class="col-md-4">
				            		<div class="form-group">
					              		<label for="college_contact_name">Contact Name:</label>
								        <input class="form-control" id="college_contact_name" name="college_contact_name" type="text" value="@if(isset($BPSSApplication->college_contact_name)) {{$BPSSApplication->college_contact_name}} @endif" maxlength="255">
						            </div>
					            </div>

					            <div class="col-md-8">
				            		<div class="form-group">
					              		<label for="college_contact_address">Contact Address:</label>
								        <input class="form-control" id="college_contact_address" name="college_contact_address" type="text" value="@if(isset($BPSSApplication->college_contact_address)) {{$BPSSApplication->college_contact_address}} @endif" maxlength="255">
						            </div>
					            </div>

			            	</div>
			            	<hr />
			            </div>

			            <div class="row">
			            	<div class="col-md-3">
			            		<div class="form-group">
			            			<label>
				                <input type="checkbox" id="university_data" name="university_data" value="1" @if(isset($BPSSApplication->university_data) && $BPSSApplication->university_data) checked="checked" @endif> University<br />
				                <span id="university_error_block" class="help-block block-hidden">You must enter the university details.</span>
				              </label>
					            </div>
			            	</div>
			            </div>

			            <div id="universityDataBlock" class="block-hidden">
			            	<div class="row">
					            <div class="col-md-4">
				            		<div class="form-group">
					              		<label for="university_name">University Name:</label>
								        <input class="form-control" id="university_name" name="university_name" type="text" value="@if(isset($BPSSApplication->university_name)) {{$BPSSApplication->university_name}} @endif" maxlength="255">
						            </div>
					            </div>

					            <div class="col-md-4">
				            		<div class="form-group">
						              	<label for="university_date_from">Date From:</label>
						              	<div class="input-group date">
						                  <div class="input-group-addon">
						                    <i class="fa fa-calendar"></i>
						                  </div>
						                  <input class="form-control" id="university_date_from" name="university_date_from" type="text" value="@if (isset($BPSSApplication->university_date_from) && strlen($BPSSApplication->university_date_from) > 0){{date('d/m/Y', strtotime($BPSSApplication->university_date_from))}}@endif" placeholder="dd/mm/yyyy" >								                  
						                </div>
						                <span id="previous_address_from_error_block" class="help-block block-hidden">Please enter a date between now and your date of birth.</span>
						            </div>
					            </div>

					            <div class="col-md-4">
				            		<div class="form-group">
						              	<label for="university_date_to">Date to:</label>
						              	<div class="input-group date">
						                  <div class="input-group-addon">
						                    <i class="fa fa-calendar"></i>
						                  </div>
						                  <input class="form-control" id="university_date_to" name="university_date_to" type="text" value="@if (isset($BPSSApplication->university_date_to) && strlen($BPSSApplication->university_date_to) > 0){{date('d/m/Y', strtotime($BPSSApplication->university_date_to))}}@endif" placeholder="dd/mm/yyyy" >								                  
						                </div>
						                <span id="previous_address_from_error_block" class="help-block block-hidden">Please enter a date between now and your date of birth.</span>
						            </div>
					            </div>

			            	</div>

			            	<div class="row">
					            <div class="col-md-4">
				            		<div class="form-group">
					              		<label for="university_contact_email">Contact Email:</label>
								        <input class="form-control" id="university_contact_email" name="university_contact_email" type="email" value="@if(isset($BPSSApplication->university_contact_email)){{$BPSSApplication->university_contact_email}}@endif" maxlength="255">
						            </div>
					            </div>

					            <div class="col-md-8">
				            		<div class="form-group">
					              		<label for="university_contact_number">Contact Address:</label>
								        <input class="form-control" id="university_contact_number" name="university_contact_number" type="text" value="@if(isset($BPSSApplication->university_contact_number)) {{$BPSSApplication->university_contact_number}} @endif" maxlength="255">
						            </div>
					            </div>

			            	</div>
			            	<hr />
			            </div>


			            <hr />
			            <div class="row">
        					<div class="col-md-12">
								@if (\Auth::user()->REVALonsite || \Auth::user()->REVALoffsite)
									<h3>Current Employer</h3>
									<p>Please provide details of your current employer</p>
								@else 
									<h3>Employment History</h3>
									<p>Please provide details of your employment covering the last 5 years</p>
									<p>If you have not been employed in the last 5 years, please put the company name as "no relevant employment history".</p>
								@endif
        					</div>
        				</div>
        				<span id="employment_error_block" class="help-block block-hidden text-red">Please enter at least one previous employment.</span>
        				<input name="check_employment_set" id="check_employment_set" type="hidden" value="@if(isset($check_employment_set) && !empty($check_employment_set)){{$check_employment_set}}@else{{'0'}}@endif">
					    <hr />

					    <div id="employmentBlock">
		            		@if(isset($employmentHistory) && count($employmentHistory)>0)
			                    @foreach ($employmentHistory as $key => $employment)
			                    	<div class="row" id="employment_block_{{$employment->id}}">
			                    		<div class="col-md-12">
			                    			<div class="row">
			                    				<div class="col-md-4">
			                    					Period Covered: {{\Carbon\Carbon::parse($employment->date_from)->format('d/m/Y')}} - {{\Carbon\Carbon::parse($employment->date_to)->format('d/m/Y')}}
			                    				</div>
			                    				<div class="col-md-4">
			                    					Company Name: {{$employment->company_name}}
			                    				</div>
			                    				<div class="col-md-4">
			                    					Email: {{$employment->email_address}}
			                    				</div>
			                    			</div>
			                    			<div class="row">
			                    				<div class="col-md-12">
			                    					Address: {{$employment->company_address_line}}
			                    				</div>
			                    			</div>
			                    			<div class="row">
			                    				<div class="col-md-3">
			                    					Town: {{$employment->company_address_town}}
			                    				</div>
			                    				<div class="col-md-3">
			                    					Postcode: {{$employment->company_address_postcode}}
			                    				</div>
			                    				<div class="col-md-4">
			                    					Allow referee contact: @if($employment->referee_allow_contact) Yes @else No @endif | P60 enclosed: @if($employment->p60_enclosed) Yes @else No @endif
			                    				</div>
			                    				<div class="col-md-2" style=" padding-left:0;">
					                    			<button id="remove_employment_block_{{$employment->id}}" employment-id="{{$employment->id}}" type="button"  class="btn btn-danger pull-left" style="margin-bottom:5px;"><i class="icon fa fa-remove"></i> Delete</button>
					                    		</div>
			                    			</div>				                    		
					            			<hr />
			                    		</div>
			                    	</div>
			                    @endforeach
			                @endif
			            </div>
			            
		            	<div id="addNewEmploymentBlock">
		            		<div class="row">
					            <div class="col-md-3">
				            		<div class="form-group">
						              	<label for="date_from">Period From</label>
						              	<div class="input-group date">
						                  <div class="input-group-addon">
						                    <i class="fa fa-calendar"></i>
						                  </div>
						                  <input class="form-control" id="employment_date_from" name="date_from" type="text" value="" placeholder="dd/mm/yyyy" >								                  
						                </div>
						                <span id="employment_date_from_error_block" class="help-block block-hidden">Please enter a valid start date.</span>
						            </div>
					            </div>

					            <div class="col-md-3">
				            		<div class="form-group">
						              	<label for="date_to">Period To</label>
						              	<div class="input-group date">
						                  <div class="input-group-addon">
						                    <i class="fa fa-calendar"></i>
						                  </div>
						                  <input class="form-control" id="employment_date_to" name="date_to" type="text" value="" placeholder="dd/mm/yyyy" >								                  
						                </div>
						                <span id="employment_date_to_error_block" class="help-block block-hidden">Please enter a valid end date.</span>
						            </div>
					            </div>

					            <div class="col-md-3">
				            		<label>
						                <span class="text-red">Do not contact referee</span><br /><input type="checkbox" id="referee_allow_contact" name="referee_allow_contact" value="1">
						            </label>
				            	</div>
				            	<div class="col-md-3">
									@if (\Auth::user()->REVALonsite || \Auth::user()->REVALoffsite)
										<input type="hidden" id="p60_enclosed" name="p60_enclosed" value="1" >
									@else 
										<label>
											P60 enclosed<br /><input type="checkbox" id="p60_enclosed" name="p60_enclosed" value="1">
										</label>
									@endif
				            	</div>
				            </div>

				            <div class="row">					           
					            <div class="col-md-6">
				            		<div class="form-group">
					              		<label for="company_name">Company Name:</label>
								        <input class="form-control" id="company_name" name="company_name" type="text" value="" maxlength="255">
						            </div>
						            <span id="company_name_error_block" class="help-block block-hidden">Please enter the company name.</span>
				            	</div>
				            	<div class="col-md-6">
				            		<div class="form-group">
					              		<label for="email_address">Email Address:</label>
								        <input class="form-control" id="email_address" name="email_address" type="email" value="" maxlength="255">
						            </div>
				            	</div>
				            </div>

				            <div class="row">					           
					            <div class="col-md-12">
				            		<div class="form-group">
					              		<label for="company_address_line">Company Address:</label>
								        <input class="form-control" id="company_address_line" name="company_address_line" type="text" value="" maxlength="255">
						            </div>
				            	</div>
				            </div>

				            <div class="row">					           
					            <div class="col-md-4">
				            		<div class="form-group">
					              		<label for="company_address_town">Town:</label>
								        <input class="form-control" id="company_address_town" name="company_address_town" type="text" value="" maxlength="255">
						            </div>
				            	</div>
				            	<div class="col-md-4">
				            		<div class="form-group">
					              		<label for="company_address_postcode">Postcode:</label>
								        <input class="form-control" id="company_address_postcode" name="company_address_postcode" type="text" value="" maxlength="255">
						            </div>
				            	</div>
				            	<div class="col-md-4">
				            		<div class="form-group">
				            			<label for="save_employment_details">&nbsp; </label>
						              	<button id="save_employment_details"  type="button" class="btn btn-danger form-control" ><i class="fa fa-save" style="margin-right: 10px;"></i>Save Employment Details</button>
						            </div>
				            	</div>

			            	</div>
			            </div>

			            <hr />
			            <div class="row">
        					<div class="col-md-12">
        						<h3>Employment Gaps</h3>
        					</div>
        				</div>
					    <hr />

					    <div class="row">
			            	<div class="col-md-12">
			            		<div class="form-group">
			            			<label for="unemployment">Have you been unemployed in the last 5 years?</label>
			            			<div class="row">
			            				<div class="col-md-3">
							              	<select class="form-control" id="unemployment" name="unemployment">
							                    <option value="1" @if ($BPSSApplication->unemployment == 1) selected="selected" @endif>Yes</option>
							                    <option value="0" @if ($BPSSApplication->unemployment == 0) selected="selected" @endif>No</option>
							                </select>
							            </div>
							        </div>
				                  <span class="help-block block-hidden">Please select an option</span>
					            </div>
			            	</div>
			            </div>

					    <div id="unemploymentBlock">
		            		@if(isset($unemploymentRecords) && count($unemploymentRecords)>0)
			                    @foreach ($unemploymentRecords as $key => $unemployment)
			                    	<div class="row" id="unemployment_block_{{$unemployment->id}}">
			                    		<div class="col-md-6">
	                    					Period Covered: {{\Carbon\Carbon::parse($unemployment->date_from)->format('d/m/Y')}} - {{\Carbon\Carbon::parse($unemployment->date_to)->format('d/m/Y')}}
	                    				</div>
	                    				<div class="col-md-2" style=" padding-left:0;">
			                    			<button id="remove_unemployment_block_{{$unemployment->id}}" unemployment-id="{{$unemployment->id}}" type="button"  class="btn btn-danger pull-left" style="margin-bottom:5px;"><i class="icon fa fa-remove"></i> Delete</button>
			                    		</div>
			                    		<hr />
			                    	</div>
			                    @endforeach
			                @endif
			            </div>
			            
		            	<div id="addNewUnemploymentBlock">
		            		<div class="row">
					            <div class="col-md-3">
				            		<div class="form-group">
						              	<label for="unemployment_date_from">Date From</label>
						              	<div class="input-group date">
						                  <div class="input-group-addon">
						                    <i class="fa fa-calendar"></i>
						                  </div>
						                  <input class="form-control" id="unemployment_date_from" name="unemployment_date_from" type="text" value="" placeholder="dd/mm/yyyy" >								                  
						                </div>
						                <span id="unemployment_date_from_error_block" class="help-block block-hidden">Please enter a date between now and your date of birth.</span>
						            </div>
					            </div>

					            <div class="col-md-3">
				            		<div class="form-group">
						              	<label for="unemployment_date_to">Date To</label>
						              	<div class="input-group date">
						                  <div class="input-group-addon">
						                    <i class="fa fa-calendar"></i>
						                  </div>
						                  <input class="form-control" id="unemployment_date_to" name="unemployment_date_to" type="text" value="" placeholder="dd/mm/yyyy" >								                  
						                </div>
						                <span id="unemployment_date_to_error_block" class="help-block block-hidden">Please enter a date between now and your date of birth.</span>
						            </div>
					            </div>

				            	<div class="col-md-3">
				            		<div class="form-group">
				            			<label for="save_unemployment_details">&nbsp; </label>
						              	<button id="save_unemployment_details"  type="button" class="btn btn-warning form-control" ><i class="fa fa-save" style="margin-right: 10px;"></i>Save Unemployment Details</button>
						            </div>
				            	</div>

			            	</div>
			            </div>

			            <hr />
						@if (\Auth::user()->REVALonsite)
							<input type="hidden" id="p60_enclosed" name="p60_enclosed" value="1" >
						@else 
							<label>
								P60 enclosed<br /><input type="checkbox" id="p60_enclosed" name="p60_enclosed" value="1">
							</label>

							<div class="row">
								<div class="col-md-12">
									<h3>Personal/Character Referee</h3>
								</div>
							</div>
							<hr />
							<div class="row">					           
								<div class="col-md-12" style="font-size:16px; color: red; font-weight: bold;">
									*Please ensure you provide an email address for each Personal Referee. And that the individual is not related to you, the person that you have nominated must have known you well for 5 or more years.
								</div>

							</div>

							<div class="row">
								<div class="col-md-3">
									<h3>Referee 1*</h3>
								</div>

								<div class="col-md-3">
									<div class="form-group">
										<label for="reference1_relationship">Relationship to reference:</label>
										<input class="form-control" id="reference1_relationship" name="reference1_relationship" type="text" value="@if(isset($reference1->relationship)){{$reference1->relationship}}@endif" maxlength="30" required="required">
									</div>
								</div>

								<div class="col-md-3">
									<div class="form-group">
										<label for="date_from">Period Known From</label>
										<div class="input-group date">
										<div class="input-group-addon">
											<i class="fa fa-calendar"></i>
										</div>
										<input class="form-control" id="reference1_date_from" name="reference1_date_from" type="text" value="@if(isset($reference1->date_from) && strlen($reference1->date_from) == 10){{\Carbon\Carbon::parse($reference1->date_from)->format('d/m/Y')}}@endif" placeholder="dd/mm/yyyy"  required="required">								                  
										</div>
									</div>
								</div>

								<div class="col-md-3">
									<div class="form-group">
										<label for="date_to">Period Known To</label>
										<div class="input-group date">
										<div class="input-group-addon">
											<i class="fa fa-calendar"></i>
										</div>
										<input class="form-control" id="reference1_date_to" name="reference1_date_to" type="text" value="@if(isset($reference1->date_to) && strlen($reference1->date_to) == 10){{\Carbon\Carbon::parse($reference1->date_to)->format('d/m/Y')}}@endif" placeholder="dd/mm/yyyy"  required="required">								                  
										</div>
									</div>

								</div>
							</div>
							<div class="row">					           
								<div class="col-md-12" style="font-size:12px; color: red;">
									Warning: Personal references cannot be relatives e.g. Mother, Father, Brother, Sister, Cousin, Aunt, Uncle, Grandparent, Gran, Grandad, Grandma, Son, Daughter, Girlfriend, Boyfriend, Spouse, Family of spouse, Partner, Family of partner.
								</div>
								<div id="reference1_error_block" class="col-md-12 help-block block-hidden text-red">
									The reference must know you for at least 5 years.
								</div>
							</div>

							<div class="row">					           
								<div class="col-md-6">
									<div class="form-group">
										<label for="reference1_referee_name">Name:</label>
										<input class="form-control" id="reference1_referee_name" name="reference1_referee_name" type="text" value="@if(isset($reference1->referee_name)){{$reference1->referee_name}}@endif" maxlength="255" required="required">
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<label for="reference1_referee_email">Email:</label>
										<input class="form-control" id="reference1_referee_email" name="reference1_referee_email" type="email" value="@if(isset($reference1->referee_email)){{$reference1->referee_email}}@endif" maxlength="255">
									</div>
								</div>
							</div>

							<div class="row">					           
								<div class="col-md-8">
									<div class="form-group">
										<label for="reference1_referee_address_line">Address:</label>
										<input class="form-control" id="reference1_referee_address_line" name="reference1_referee_address_line" type="text" value="@if(isset($reference1->referee_address_line)){{$reference1->referee_address_line}}@endif" maxlength="255" required="required">
									</div>
								</div>

								<div class="col-md-4">
									<div class="form-group">
										<label for="reference1_referee_contact_number">Contact Number:</label>
										<input class="form-control" id="reference1_referee_contact_number" name="reference1_referee_contact_number" type="text" value="@if(isset($reference1->referee_contact_number)){{$reference1->referee_contact_number}}@endif" maxlength="255">
									</div>
								</div>
							</div>

							<div class="row">					           
								<div class="col-md-4">
									<div class="form-group">
										<label for="reference1_referee_address_town">Town:</label>
										<input class="form-control" id="reference1_referee_address_town" name="reference1_referee_address_town" type="text" value="@if(isset($reference1->referee_address_town)){{$reference1->referee_address_town}}@endif" maxlength="255" required="required">
									</div>
								</div>
								<div class="col-md-4">
									<div class="form-group">
										<label for="reference1_referee_address_postcode">Postcode:</label>
										<input class="form-control" id="reference1_referee_address_postcode" name="reference1_referee_address_postcode" type="text" value="@if(isset($reference1->referee_address_postcode)){{$reference1->referee_address_postcode}}@endif" maxlength="15"  required="required">
										<input name="reference1_id" type="hidden" value="@if(isset($reference1->id) && !empty($reference1->id)){{$reference1->id}}@else{{'0'}}@endif">
									</div>
								</div>

							</div>

							<hr />


							<div class="row">
								<div class="col-md-3">
									<h3>Referee 2*</h3>
								</div>

								<div class="col-md-3">
									<div class="form-group">
										<label for="reference2_relationship">Relationship to reference:</label>
										<input class="form-control" id="reference2_relationship" name="reference2_relationship" type="text" value="@if(isset($reference2->relationship)){{$reference2->relationship}}@endif" maxlength="30" required="required">
									</div>
								</div>

								<div class="col-md-3">
									<div class="form-group">
										<label for="date_from">Period Known From</label>
										<div class="input-group date">
										<div class="input-group-addon">
											<i class="fa fa-calendar"></i>
										</div>
										<input class="form-control" id="reference2_date_from" name="reference2_date_from" type="text" value="@if(isset($reference2->date_from) && strlen($reference2->date_from) == 10){{\Carbon\Carbon::parse($reference2->date_from)->format('d/m/Y')}}@endif" placeholder="dd/mm/yyyy" >								                  
										</div>
									</div>
								</div>

								<div class="col-md-3">
									<div class="form-group">
										<label for="date_to">Period Known To</label>
										<div class="input-group date">
										<div class="input-group-addon">
											<i class="fa fa-calendar"></i>
										</div>
										<input class="form-control" id="reference2_date_to" name="reference2_date_to" type="text" value="@if(isset($reference2->date_to) && strlen($reference2->date_to) == 10){{\Carbon\Carbon::parse($reference2->date_to)->format('d/m/Y')}}@endif" placeholder="dd/mm/yyyy" >								                  
										</div>
									</div>
								</div>
							</div>

							<div class="row">					           
								<div class="col-md-12" style="font-size:12px; color: red;">
									Warning: Personal references cannot be relatives e.g. Mother, Father, Brother, Sister, Cousin, Aunt, Uncle, Grandparent, Gran, Grandad, Grandma, Son, Daughter, Girlfriend, Boyfriend, Spouse, Family of spouse, Partner, Family of partner.
								</div>
								<div id="reference2_error_block" class="col-md-12 help-block block-hidden text-red">
									The reference must know you for at least 5 years.
								</div>
							</div>
							
							<div class="row">					           
								<div class="col-md-6">
									<div class="form-group">
										<label for="reference2_referee_name">Name:</label>
										<input class="form-control" id="reference2_referee_name" name="reference2_referee_name" type="text" value="@if(isset($reference2->referee_name)){{$reference2->referee_name}}@endif" maxlength="255">
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<label for="reference2_referee_email">Email:</label>
										<input class="form-control" id="reference2_referee_email" name="reference2_referee_email" type="email" value="@if(isset($reference2->referee_email)){{$reference2->referee_email}}@endif" maxlength="255">
									</div>
								</div>
							</div>

							<div class="row">					           
								<div class="col-md-8">
									<div class="form-group">
										<label for="reference2_referee_address_line">Address:</label>
										<input class="form-control" id="reference2_referee_address_line" name="reference2_referee_address_line" type="text" value="@if(isset($reference2->referee_address_line)){{$reference2->referee_address_line}}@endif" maxlength="255">
									</div>
								</div>

								<div class="col-md-4">
									<div class="form-group">
										<label for="reference2_referee_contact_number">Contact Number:</label>
										<input class="form-control" id="reference2_referee_contact_number" name="reference2_referee_contact_number" type="text" value="@if(isset($reference2->referee_contact_number)){{$reference2->referee_contact_number}}@endif" maxlength="255">
									</div>
								</div>
							</div>

							<div class="row">					           
								<div class="col-md-4">
									<div class="form-group">
										<label for="reference2_referee_address_town">Town:</label>
										<input class="form-control" id="reference2_referee_address_town" name="reference2_referee_address_town" type="text" value="@if(isset($reference2->referee_address_town)){{$reference2->referee_address_town}}@endif" maxlength="255">
									</div>
								</div>
								<div class="col-md-4">
									<div class="form-group">
										<label for="reference2_referee_address_postcode">Postcode:</label>
										<input class="form-control" id="reference2_referee_address_postcode" name="reference2_referee_address_postcode" type="text" value="@if(isset($reference2->referee_address_postcode)){{$reference2->referee_address_postcode}}@endif" maxlength="15">
										<input name="reference2_id" type="hidden" value="@if(isset($reference2->id) && !empty($reference2->id)){{$reference2->id}}@else{{'0'}}@endif">
									</div>
								</div>

							</div>
						@endif
		            	

			        </div>
			        <!-- /.box-body -->

		          	<div class="box-footer">
			            <div class="form-group">
			            	<!-- <a href="{{ env('APP_URL') }}applicant/bpssStep2"><button type="button" class="btn btn-default btn-lg pull-left" style="margin-bottom: 10px;">Previous</button></a> -->
				            <button type="button" class="forceBgClassified btn btn-lg pull-right submitStep3" style="margin-bottom: 10px;">Save and Continue</button>
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
