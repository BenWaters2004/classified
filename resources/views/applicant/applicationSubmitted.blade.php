@extends('layout.default')

@section('title', "DBS Application Submitted")

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
		        <!-- form start -->

		          <div class="box-body">

		          	<div class="progress">
		                <div class="progress-bar progress-bar-primary progress-bar-striped" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width: 100%">
		                  <span class="sr-only">100% Complete (success)</span>
		                </div>
		            </div>

		            <hr />

		            <h3>Application submitted</h3>
		           
		            <div class="row">
		            	<div class="col-md-12">
		            		<p>Once your application has been cleared, we will send you a confirmation email containing your application reference number</p>
		            	</div>
		            </div>

		            
		            @if (isset($bpssRequired->bpssApplication) && !empty($bpssRequired->bpssApplication))
		            <div class="row">
		            	<div class="col-md-12">
		            		<p>You are now required to add additional information to complete the security clearance process for your Baseline Personnel Security Standard (BPSS)</p>
		            		<p><a href="{{ env('APP_URL') }}applicant/bpssStep1"><button type="button" class="btn btn-warning">Complete BPSS form</button></a></p>
		            	</div>
		            </div>
					@elseif (isset($bpssRequired->useYoti) && !empty($bpssRequired->useYoti) && $bpssRequired->useYoti == 1)
					<div class="row">
		            	<div class="col-md-12">
		            		<p>You are now required to add additional information to complete the security clearance process</p>
		            		<p><a href="{{ env('APP_URL') }}applicant/yotiDisclaimer"><button type="button" class="btn btn-warning">Complete ID Check</button></a></p>
		            	</div>
		            </div>
		            @endif

		          </div>
		          <!-- /.box-body -->
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
