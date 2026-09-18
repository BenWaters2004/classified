@extends('layout.default')

@section('title', "BPSS Application Step 5")

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
		        <form id="bpssStep5" method="POST" action="{{ env('APP_URL') }}applicant/savebpssStep5">
        			{{ csrf_field() }}
		         	<div class="box-body">
		         		
        				<div class="row">
        					<div class="col-md-12">
					            <div class="form-group">
					            	<a href="{{ env('APP_URL') }}applicant/bpssStep3"><button type="button" class="forceBgClassified btn btn-default btn-lg pull-left" style="margin-bottom: 10px;">Previous</button></a>
						            <button type="button" class="forceBgClassified btn btn-lg pull-right submitStep5" style="margin-bottom: 10px;">Save and Continue</button>
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
        						<h3>Criminal Record Declaration</h3>
        					</div>
        				</div>

			            <hr />
			            <div class="row">
			            	<div class="col-md-12">
			            		<p><strong>Guidance Note:</strong>The Company has Government contracts, some or all of which requires the company to hold material or information, which is the property of the Government. The company has a duty to protect these assets while in its possession and this obligation extends to its employees and agents. Since you are or may become such a person please complete the following sections.</p>
			            		<p>Please answer the following questions honestly. In addition to your self-declaration below, a check against the National Collection of Criminal Records will be undertaken and documentary evidence sought to confirm your answers in the form of Police Act Disclosure which will be paid for by {{ env('APP_COMPANY_NAME') }}. By signing the declaration in part 5 you are giving us permission to do so.</p>
			            		<p>The information you give will be treated in strict confidence.</p>
			            	</div>
			            </div>

			            <div class="row">
			            	<div class="col-md-12">
			            		<div class="form-group">
			            			<label for="convicted_by_court">Have you ever been convicted or found guilty by a court of any offence in any country (excluding parking but including all motoring offences even where a spot fine has been administrated by the police) or have you ever been put on probation (probation orders are now called community rehabilitation orders) or absolutely/conditionally discharged or bound over after being charged with any offence or is there any action pending against you? You need not declare convictions which are "spent" under the Rehabilitation of Offenders Act (1974).</label>
					              	<input type="checkbox" id="convicted_by_court_yes" @if ($BPSSApplication->convicted_by_court == 1) checked="checked" @endif> Yes 
					              	&nbsp;&nbsp;&nbsp;&nbsp;
					              	<input type="checkbox" id="convicted_by_court_no" @if ($BPSSApplication->convicted_by_court === 0) checked="checked" @endif> No
					              	&nbsp;&nbsp;&nbsp;&nbsp; If yes, please give details in the text box bellow
					              	<input type="hidden" id="convicted_by_court" name="convicted_by_court" value="{{$BPSSApplication->convicted_by_court}}">
				                  <span class="help-block text-red block-hidden" id="convicted_by_court_error"><br />You must select Yes or No</span>
					            </div>
			            	</div>
			            	
			            </div>
						<hr />
			            <div class="row">
			            	<div class="col-md-12">
			            		<div class="form-group">
			            			<label for="convicted_by_court_martial">Have you ever been convicted by a Court Martial or sentenced to detention or dismissal whilst serving in the Armed Forces of the UK or any Commonwealth or foreign country? You need not declare convictions which are "spent" under the Rehabilitation of Offenders Act (1974).</label>
					              	<input type="checkbox" id="convicted_by_court_martial_yes" @if ($BPSSApplication->convicted_by_court_martial == 1) checked="checked" @endif> Yes 
					              	&nbsp;&nbsp;&nbsp;&nbsp;
					              	<input type="checkbox" id="convicted_by_court_martial_no" @if ($BPSSApplication->convicted_by_court_martial === 0) checked="checked" @endif> No
					              	&nbsp;&nbsp;&nbsp;&nbsp; If yes, please give details in the text box bellow
					              	<input type="hidden" id="convicted_by_court_martial" name="convicted_by_court_martial" value="{{$BPSSApplication->convicted_by_court_martial}}">
				                  <span class="help-block text-red block-hidden" id="convicted_by_court_martial_error"><br />You must select Yes or No</span>
					            </div>
			            	</div>
			            	
			            </div>
			            <hr />
			            <div class="row">
			            	<div class="col-md-12">
			            		<div class="form-group">
			            			<label for="background_reliability">Do you know of any other matters in your background which might cause your reliability or suitability to have access to government assets to be called into question?</label>
					              	<input type="checkbox" id="background_reliability_yes" @if ($BPSSApplication->background_reliability == 1) checked="checked" @endif> Yes 
					              	&nbsp;&nbsp;&nbsp;&nbsp;
					              	<input type="checkbox" id="background_reliability_no" @if ($BPSSApplication->background_reliability === 0) checked="checked" @endif> No
					              	&nbsp;&nbsp;&nbsp;&nbsp; If yes, please give details in the text box bellow
					              	<input type="hidden" id="background_reliability" name="background_reliability" value="{{$BPSSApplication->background_reliability}}">
				                  <span class="help-block text-red block-hidden" id="background_reliability_error"><br />You must select Yes or No</span>
					            </div>
			            	</div>
			            	
			            </div>
			            <hr />
			            <div class="col-md-12">
		            		<div class="form-group">
			              		<label for="convictions_details">If you answered YES to any of the questions on this form, please give details bellow:</label>
						        <textarea class="form-control" rows="6"  id="convictions_details"  name="convictions_details">@if(isset($BPSSApplication->convictions_details)){{$BPSSApplication->convictions_details}}@endif</textarea>
				            </div>
			            </div>

			        </div>
			        <!-- /.box-body -->

		          	<div class="box-footer">
			            <div class="form-group">
			            	<!-- <a href="{{ env('APP_URL') }}applicant/bpssStep3"><button type="button" class="btn btn-default btn-lg pull-left" style="margin-bottom: 10px;">Previous</button></a> -->
				            <button type="button" class="forceBgClassified btn btn-lg pull-right submitStep5" style="margin-bottom: 10px;">Save and Continue</button>
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

$('#convicted_by_court_yes').change(function() {
   if ($(this).is(':checked')) {
      $('#convicted_by_court').val('1');
      $('#convicted_by_court_no').prop('checked', false);
   } else {
      $('#convicted_by_court').val('');
   }
});

$('#convicted_by_court_no').change(function() {
   if ($(this).is(':checked')) {
      $('#convicted_by_court').val('0');
      $('#convicted_by_court_yes').prop('checked', false);
   } else {
      $('#convicted_by_court').val('');
   }
});

$('#convicted_by_court_martial_yes').change(function() {
   if ($(this).is(':checked')) {
      $('#convicted_by_court_martial').val('1');
      $('#convicted_by_court_martial_no').prop('checked', false);
   } else {
      $('#convicted_by_court_martial').val('');
   }
});

$('#convicted_by_court_martial_no').change(function() {
   if ($(this).is(':checked')) {
      $('#convicted_by_court_martial').val('0');
      $('#convicted_by_court_martial_yes').prop('checked', false);
   } else {
      $('#convicted_by_court_martial').val('');
   }
});

$('#background_reliability_yes').change(function() {
   if ($(this).is(':checked')) {
      $('#background_reliability').val('1');
      $('#background_reliability_no').prop('checked', false);
   } else {
      $('#background_reliability').val('');
   }
});

$('#background_reliability_no').change(function() {
   if ($(this).is(':checked')) {
      $('#background_reliability').val('0');
      $('#background_reliability_yes').prop('checked', false);
   } else {
      $('#background_reliability').val('');
   }
});

</script>
@endsection
