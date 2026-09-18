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
		        <form id="bpssStep1" method="POST" action="{{ env('APP_URL') }}applicant/savebpssStep6">
        			{{ csrf_field() }}
		         	<div class="box-body">
		         		
        				<div class="row">
        					<div class="col-md-12">
					            <div class="form-group">
					            	<!-- <a href="{{ env('APP_URL') }}applicant/bpssStep5"><button type="button" class="btn btn-default btn-lg pull-left" style="margin-bottom: 10px;">Previous</button></a> -->
						            <button type="button" class="forceBgClassified btn btn-lg pull-right submitStep6" style="margin-bottom: 10px;">Save and Continue</button>
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
        						<h3>Additional Information:</h3>
        					</div>
        				</div>
        				

			            <div class="row">					           
				            <div class="col-md-12">
			            		<div class="form-group">
				              		<label for="additional_information">Please enter below any additional information that you want to add to your application</label>
							        <textarea class="form-control" rows="5"  id="additional_information"  name="additional_information">@if(isset($BPSSApplication->additional_information)){{$BPSSApplication->additional_information}}@endif</textarea>
					            </div>
					        </div>
			            </div>

		            	 <hr />
		            	
			            



			        </div>
			        <!-- /.box-body -->

		          	<div class="box-footer">
			            <div class="form-group">
			            	<!-- <a href="{{ env('APP_URL') }}applicant/bpssStep5"><button type="button" class="btn btn-default btn-lg pull-left" style="margin-bottom: 10px;">Previous</button></a> -->
				            <button type="button" class="forceBgClassified btn btn-lg pull-right submitStep6" style="margin-bottom: 10px;">Save and Continue</button>
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
