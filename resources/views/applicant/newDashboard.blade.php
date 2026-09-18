@extends('layout.default')

@section('title', "Admin - Dashboard")

@section('sidebar')
@endsection

@section('content')
    <div class="container-fluid">
    <section class="content">
	    <div class="row">
	        
	          	@if (!isset($DBSApplication->applicationStatus) || $DBSApplication->applicationStatus == 0)
		          	<a href="{{ env('APP_URL') }}applicant/dbsStep1/">
				        <div class="col-lg-3 col-xs-6">
				          	<!-- small box -->
			            	<div class="small-box bg-info">
					            <div class="inner">
					              <h3 style="color: #fff;">DBS</h3>
					              <p>Not started / Incomplete</p>
					            </div>
					            <div class="icon">
					              <i class="ion ion-help"></i>
					            </div>
					            <span class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></span>
					        </div>
					    </div>
					</a>
	            @elseif (isset($DBSApplication->applicationStatus) && $DBSApplication->applicationStatus == 1)
	                <a href="{{ env('APP_URL') }}applicant/reviewDBSApplication/{{$DBSApplication->id}}">
				        <div class="col-lg-3 col-xs-6">
				          	<!-- small box -->
			            	<div class="small-box bg-orange">
					            <div class="inner">
					              <h3 style="color: #fff;">DBS</h3>
					              <p>Pending Review</p>
					            </div>
					            <div class="icon">
					              <i class="ion ion-clock"></i>
					            </div>
					            <span class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></span>
					        </div>
					    </div>
					</a>
	            @elseif (isset($DBSApplication->applicationStatus) && $DBSApplication->applicationStatus == 2)
	            	<a href="{{ env('APP_URL') }}applicant/reviewDBSApplication/{{$DBSApplication->id}}">
				        <div class="col-lg-3 col-xs-6">
				          	<!-- small box -->
			            	<div class="small-box bg-red">
					            <div class="inner">
					              <h3 style="color: #fff;">DBS</h3>
					              <p>Review Fail</p>
					            </div>
					            <div class="icon">
					              <i class="ion ion-clock"></i>
					            </div>
					            <span class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></span>
					        </div>
					    </div>
					</a>
	            @elseif (isset($DBSApplication->applicationStatus) && $DBSApplication->applicationStatus == 3)
	                <a href="{{ env('APP_URL') }}applicant/reviewDBSApplication/{{$DBSApplication->id}}">
				        <div class="col-lg-3 col-xs-6">
				          	<!-- small box -->
			            	<div class="small-box bg-orange">
					            <div class="inner">
					              <h3 style="color: #fff;">DBS</h3>
					              <p>Pending Submission</p>
					            </div>
					            <div class="icon">
					              <i class="ion ion-clock"></i>
					            </div>
					            <span class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></span>
					        </div>
					    </div>
					</a>
	            @elseif (isset($DBSApplication->applicationStatus) && $DBSApplication->applicationStatus == 4)
	                <a href="{{ env('APP_URL') }}applicant/reviewDBSApplication/{{$DBSApplication->id}}">
				        <div class="col-lg-3 col-xs-6">
				          	<!-- small box -->
			            	<div class="small-box bg-orange">
					            <div class="inner">
					              <h3 style="color: #fff;">DBS</h3>
					              <p>Submitted to DBS</p>
					            </div>
					            <div class="icon">
					              <i class="ion ion-clock"></i>
					            </div>
					            <span class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></span>
					        </div>
					    </div>
					</a>
	            @elseif (isset($DBSApplication->applicationStatus) && $DBSApplication->applicationStatus == 5)
	                <a href="{{ env('APP_URL') }}applicant/reviewDBSApplication/{{$DBSApplication->id}}">
				        <div class="col-lg-3 col-xs-6">
				          	<!-- small box -->
			            	<div class="small-box bg-orange">
					            <div class="inner">
					              <h3 style="color: #fff;">DBS</h3>
					              <p>DBS Submission Success</p>
					            </div>
					            <div class="icon">
					              <i class="ion ion-clock"></i>
					            </div>
					            <span class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></span>
					        </div>
					    </div>
					</a>
	            @elseif (isset($DBSApplication->applicationStatus) && $DBSApplication->applicationStatus == 6)
	                <a href="{{ env('APP_URL') }}applicant/reviewDBSApplication/{{$DBSApplication->id}}">
				        <div class="col-lg-3 col-xs-6">
				          	<!-- small box -->
			            	<div class="small-box bg-orange">
					            <div class="inner">
					              <h3 style="color: #fff;">DBS</h3>
					              <p>DBS Fail</p>
					            </div>
					            <div class="icon">
					              <i class="ion ion-clock"></i>
					            </div>
					            <span class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></span>
					        </div>
					    </div>
					</a>
	            @elseif (isset($DBSApplication->applicationStatus) && $DBSApplication->applicationStatus == 8)
	                <a href="{{ env('APP_URL') }}applicant/reviewDBSApplication/{{$DBSApplication->id}}">
				        <div class="col-lg-3 col-xs-6">
				          	<!-- small box -->
			            	<div class="small-box bg-green">
					            <div class="inner">
					              <h3 style="color: #fff;">DBS</h3>
					              <p>{{$DBSApplication->dbsResponse_int023_DisclosureStatus}}&nbsp;</p>
					            </div>
					            <div class="icon">
					              <i class="ion ion-clock"></i>
					            </div>
					            <span class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></span>
					        </div>
					    </div>
					</a>
	            @endif

	          

	            @if (\Auth::user()->bpssApplication)
	            	@if (!isset($BPSSApplication->applicationStatus) || empty($BPSSApplication->applicationStatus) || $BPSSApplication->applicationStatus == 0)
				        <a href="{{ env('APP_URL') }}applicant/bpssStep1">
					        <div class="col-lg-3 col-xs-6">
					          <!-- small box -->
					          <div class="small-box bg-red">
					            <div class="inner">
									<h3 style="color: #fff;">BPSS</h3>

					              <p>Incomplete</p>
					            </div>
					            <div class="icon">
					              <i class="ion ion-ios-paper"></i>
					            </div>
					            <span class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></span>
					          </div>
					        </div>
				        </a>
				    @elseif (isset($BPSSApplication->applicationStatus) && $BPSSApplication->applicationStatus == 1)
				    	<span>
					        <div class="col-lg-3 col-xs-6">
					          <!-- small box -->
					          <div class="small-box bg-green">
					            <div class="inner">
										<h3 style="color: #fff;">BPSS</h3>
					              <p>Complete</p>
					            </div>
					            <div class="icon">
					              <i class="ion ion-ios-paper"></i>
					            </div>
					            <span class="small-box-footer">
					            	<a style="color: #fff; text-decoration: none;" target="_blank" href="{{ env('APP_URL') }}applicant/generateBPSSPDF/{{$BPSSApplication->id}}">Download PDF <i class="fa fa-arrow-circle-right"></i></a> &nbsp;&nbsp;&nbsp;
					            	<a style="color: #fff; text-decoration: none;" href="{{ env('APP_URL') }}applicant/modifyBPSSApplication/{{$BPSSApplication->id}}">Edit BPSS <i class="fa fa-arrow-circle-right"></i></a>

					            </span>
					          </div>
					        </div>
				        </span>
				    @endif
			    @endif


				@if (\Auth::user()->useYoti)
					@if (\Auth::user()->useYoti == 1)
						<a href="{{ env('APP_URL') }}applicant/yotiDisclaimer">
							<div class="col-lg-3 col-xs-6">
								<!-- small box -->
								<div class="small-box bg-info">
									<div class="inner">
										<h3 style="color: #fff;">ID Check</h3>
										<p>Incomplete</p>
									</div>
									<div class="icon">
										<i class="ion ion-help-circled"></i>
									</div>
									<span class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></span>
								</div>
							</div>
						</a>
					@elseif (\Auth::user()->useYoti == 2)
						<a href="#">
							<div class="col-lg-3 col-xs-6">
								<!-- small box -->
								<div class="small-box bg-green">
									<div class="inner">
										<h3 style="color: #fff;">ID Check</h3>
										<p>Complete</p>
									</div>
									<div class="icon">
										<i class="ion ion-checkmark"></i>
									</div>
									<span class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></span>
								</div>
							</div>
						</a>
					@elseif (\Auth::user()->useYoti == 3)
						<a href="#">
							<div class="col-lg-3 col-xs-6">
								<!-- small box -->
								<div class="small-box bg-red">
									<div class="inner">
										<h3 style="color: #fff;">ID Check</h3>
										<p>Failed - an admin will be in touch to explain.</p>
									</div>
									<div class="icon">
										<i class="ion ion-close"></i>
									</div>
									<span class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></span>
								</div>
							</div>
						</a>
					@endif
				@endif

				

			    <a href="{{ env('APP_URL') }}applicant/faq">
			        <div class="col-lg-3 col-xs-6">
			          <!-- small box -->
			          <div class="small-box bg-aqua">
			            <div class="inner">
			              <h3 style="color: #fff;">FAQ</h3>

			              <p>Frequently Asked Questions</p>
			            </div>
			            <div class="icon">
			              <i class="ion ion-help-circled"></i>
			            </div>
			            <span class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></span> 
			          </div>
			        </div>
		        </a>

		        <a href="{{ env('APP_URL') }}applicant/editProfile">
			        <div class="col-lg-3 col-xs-6">
			          <!-- small box -->
			          <div class="small-box bg-aqua">
			            <div class="inner">
			              <h3 style="color: #fff;">Profile</h3>

			              <p>View and edit your profile</p>
			            </div>
			            <div class="icon">
			              <i class="ion ion-person"></i>
			            </div>
			            <span class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></span>
			          </div>
			        </div>
		        </a>

	    </div>
    </section>
</div>

@endsection


@section('pageCSS')
 
@endsection

@section('pageJavascript')
<script type="text/javascript">
$('#originalDate, #newDate').datepicker({
    autoclose: true,
    format: "mm/dd/yyyy",
});
</script>
@endsection
