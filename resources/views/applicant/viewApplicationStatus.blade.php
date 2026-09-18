@extends('layout.default')

@section('title', "Application Status")

@section('sidebar')
@endsection

@section('content')
<div class="row">
    <!-- left column -->
    <div class="col-md-12">
      <!-- general form elements -->
      <div class="box box-default">
        <div class="box-header with-border">
          <h3 class="box-title">Current Applications</h3>
        </div>
        <!-- /.box-header -->
<?php //echo'<pre>';print_r($DBSApplication);echo'</pre>'; exit;
?>
        <div class="row">
        	<div class="col-md-12">
	            <div class="row">
	            	<div class="col-md-12">
	            	@if(!isset($DBSApplication->id))	            	
	            		You don't have any DBS applications
	            	@else
	            		<table id="dbsApplications" class="table table-bordered table-striped">
					        <thead>
					          <tr>
					          	<th>Type</th>
					            <th>Name</th>
					            <th>Completed Date</th>
					            <th>Status</th>
					            <th>Actions</th>
					          </tr>
					        </thead>
					        <tbody>
					          <tr id="dbs_block_{{ $DBSApplication->userID }}">
					          	<td>DBS Application</td>
					            <td>{{$DBSApplication->userTitle}} {{$DBSApplication->forename}} {{$DBSApplication->presentSurname}}</td>
					            <td>{{date("d/m/Y", strtotime($DBSApplication->dbs_consent_date)) }}</td>
					            <td>
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
			                    </td>
					            <td>
					              <a href="{{ env('APP_URL') }}applicant/reviewDBSApplication/{{$DBSApplication->id}}"><button type="button" class="btn btn-success"><i class="fa fa-search"></i> View</button></a>
					              @if ($DBSApplication->applicationStatus == 1)
					              	<a href="{{ env('APP_URL') }}applicant/modifyDBSApplication/{{$DBSApplication->id}}"><button type="button" class="btn btn-danger"><i class="fa fa-edit"></i> Edit</button></a>
					              @endif
					            </td>
					          </tr>
					          @if(!empty($DBSApplication->bpssApplication))
					          	<tr id="bpss_block_{{ $DBSApplication->userID }}">
					          		<td>BPSS Application</td>
						            <td>{{$DBSApplication->userTitle}} {{$DBSApplication->forename}} {{$DBSApplication->presentSurname}}</td>
						            <td>
						            	@if(isset($BPSSApplication->id) && !empty($BPSSApplication->id && $BPSSApplication->applicationStatus == 1))
						            		{{date("d/m/Y", strtotime($BPSSApplication->completedDate)) }}
						            	@else
						            		&nbsp;
						            	@endif
						            </td>
						            <td>
						            	@if(isset($BPSSApplication->id) && !empty($BPSSApplication->id))
						                    @if ($BPSSApplication->applicationStatus == 0)
						                        <small class="label label-warning"><i class="fa fa-clock-o"></i> Incomplete</small>
						                    @elseif ($BPSSApplication->applicationStatus == 1)
						                        <small class="label label-success">Completed</small>
						                    @endif
						                @else
						                	<small class="label label-default">Not Started</small>
						                @endif
				                    </td>
						            <td>
						            	@if(isset($BPSSApplication->id) && !empty($BPSSApplication->id))
						            		@if ($BPSSApplication->applicationStatus == 0)
						              			<a href="{{ env('APP_URL') }}applicant/bpssStep1"><button type="button" class="btn btn-danger"><i class="fa fa-edit"></i> Continue BPSS Application</button></a>
						              		@elseif ($BPSSApplication->applicationStatus == 1)
							                  @if($BPSSApplication->completedDate <= '2018-10-30')
							                    <a href="{{ env('APP_URL') }}applicant/downloadBPSSPDF/{{$BPSSApplication->id}}" target="_blank"><button type="button" class="btn btn-danger" title="Download OLD BPSS Format" style=""><span style="font-weight: bold;"><i class="fa fa-file-pdf-o"></i> OLD BPSS Format</span></button></a>
							                  @else
							                    <a href="{{ env('APP_URL') }}applicant/generateBPSSPDF/{{$BPSSApplication->id}}" target="_blank"><button type="button" class="btn btn-danger" title="Download BPSS Form" style=""><span style="font-weight: bold;"><i class="fa fa-file-pdf-o"></i> BPSS</span></button></a>
							                  @endif
						              			<a href="{{ env('APP_URL') }}applicant/modifyBPSSApplication/{{$BPSSApplication->id}}"><button type="button" class="btn btn-danger"><i class="fa fa-edit"></i> Edit BPSS</button></a>
						              		@endif
						              	@else
						              		<a href="{{ env('APP_URL') }}applicant/bpssStep1"><button type="button" class="btn btn-default"><i class="fa fa-plus"></i> Start BPSS</button></a>
						              	@endif
						            </td>
						          </tr>
					          @endif

					        </tbody>
					      </table>
	            	@endif
	            	</div>		            
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

<script type="text/javascript">

</script>
@endsection
