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
          <h3 class="box-title">&nbsp;</h3>
        </div>
        <!-- /.box-header -->

        <div class="row">
        	<div class="col-md-6">
		        <!-- form start -->

        			{{ csrf_field() }}
		          <div class="box-body">

		            <h3>Application submitted</h3>

					@if (\Auth::user()->useYoti)
						@if (\Auth::user()->useYoti == 1)
							<a href="{{ env('APP_URL') }}applicant/yotiDisclaimer" class="btn btn-warning">
								Continue to ID check
							</a>
						@else
							<div class="row">
								<div class="col-md-12">
									<p><a href="{{ env('APP_URL') }}applicant/dashboard"><button type="button" class="btn btn-warning">Back to Dashboard</button></a></p>
								</div>
							</div>
						@endif
					@else
						<div class="row">
							<div class="col-md-12">
								<p><a href="{{ env('APP_URL') }}applicant/dashboard"><button type="button" class="btn btn-warning">Back to Dashboard</button></a></p>
							</div>
						</div>
					@endif
		            


		          </div>
		          <!-- /.box-body -->

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
