@extends('layout.admin')

@section('title', 'Manage Evaluations')

@section('content')

<?php //echo'<pre>';print_r($allusers);echo'</pre>'; ?>

<div class="container-fluid">
  <section class="content">
  
  <div class="row">
    <div class="col-md-12">
      <div class="box">
        <div class="box-header">
          <h3 class="box-title">Search Applications</h3>
        </div><!-- /.box-header -->
        <div class="box-body">
          @if(isset($applications) && count($applications)>0)
            <table id="searchApplications" class="table table-bordered table-striped">
              <thead>
                <tr>
                  <th>Email</th>
                  <th width="25%">Name</th>
                  <th>Organisation</th>
                  <th>DBS Status</th>
                  <th>Started/Completed</th>
                  <th>BPSS</th>
                  <th>IDs Verified</th>
                  <th>GDPR</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                @foreach ($applications as $application)
                <tr id="user_block_{{ $application->id }}">
                  <td style="word-break:break-all;">{{$application->userEmail}}</td>
                  <td  style="word-break:break-all;">
                    <a href="{{ env('APP_URL') }}applications/viewApplicationDetails/{{$application->id}}">{{$application->presentSurname}}, {{$application->forename}} {{$application->middlename}}</a>
                    @if(count($application->otherNames)>0)
                      @foreach ($application->otherNames as $otherName)
                        <br />{{$otherName->other_surname}}, {{$otherName->other_forename}} {{$otherName->other_middlename}}
                      @endforeach
                    @endif
                  </td>
                  <td>{{$application->organisationName}}</td>
                  <td>
                      @if ($application->applicationStatus == 0)
                        <span style="display: none;">4.</span><small class="label label-primary" title="Applicant has started the applications but has not finished it yet."><i class="fa fa-clock-o"></i> Incomplete</small>
                      @elseif ($application->applicationStatus == 1)
                        <span style="display: none;">1.</span><small class="label label-warning" title="Applicant has submitted their DBS application and is awaiting to be reviewed by an administrator and to be submitted to DBS">Pending Review</small>
                      @elseif ($application->applicationStatus == 2)
                        <span style="display: none;">2.</span><small class="label label-danger" title="The administrator considers the information submitted by teh applicant is wrong / not enough and it is being sent back to the applicant to update and re-submit">Review Fail</small>
                      @elseif ($application->applicationStatus == 3)
                        <span style="display: none;">3.</span><small class="label label-warning" title="The administrator has approved the application and it is now pending submission to DBS">Pending Submission</small>
                      @elseif ($application->applicationStatus == 4)
                        <span style="display: none;">7.</span><small class="label label-success" title="Application has been submitted to DBS">Submitted to DBS</small>@if(strlen($application->applicationCompleted) > 1) <br /><small class="label label-success">{{date('d/m/Y @ H:i:s', strtotime($application->admin_submission_date))}}</small> @endif
                      @elseif ($application->applicationStatus == 5)
                        <span style="display: none;">5.</span><small class="label label-success" title="DBS has accepted the application for processing">DBS Submission Success</small>
                      @elseif ($application->applicationStatus == 6)
                        <span style="display: none;">6.</span><small class="label label-danger" title="DBS has returned the application with errors (check DBS email for details)">DBS Fail</small>
                      @elseif ($application->applicationStatus == 8)
                        <span style="display: none;">8.</span><small class="label label-success" title="Application has been processed by DBS. If 'Certificate contains no information' then DBS has found no information/restrictions to include on the certificate. If 'Please wait to view applicant certificate' then DBS has found information/restrictions and has included them on the printed certificate">{{$application->dbsResponse_int023_DisclosureStatus}}</small>
                      @endif
                  </td>
                  <td>S: {{date('d/m/Y @ H:i:s', strtotime($application->createdOn))}} @if(strlen($application->applicationCompleted) > 1) <br />C: {{date('d/m/Y @ H:i:s', strtotime($application->applicationCompleted))}} @endif</td>
                  <td>
                    @if ($application->bpssApplication == 1)
                      @if ($application->BPSSApplicationStatus == 1)
                      <i class="fa fa-check-circle" style="color:green" alt="Complete" title="Complete"></i>
                      @elseif ($application->BPSSApplicationStatus == 0)
                      <i class="fa fa-clock-o" style="color:#FFA500;" alt="Pending" title="Pending"></i>
                      @endif
                    @else
                      <i class="fa fa-times-circle" style="color:red" alt="Not Required" title="Not Required"></i>
                    @endif
                  </td>
                  <td>
                    @if ($application->admin_confirm_valid_passport)&nbsp;<i class="fa fa-book" title="Passport"></i>&nbsp;@endif
                    @if ($application->admin_confirm_valid_dln)&nbsp;<i class="fa fa-car" title="Driver Licence"></i>&nbsp;@endif
                    @if ($application->admin_confirm_valid_nino)&nbsp;<i class="fa fa-user" title="National Insurance Number"></i>@endif
                  </td>
                  <td>
                    @if ($application->applicationStatus == 1 || $application->applicationStatus == 2 || $application->applicationStatus == 3)
                    <input type="checkbox" id="admin_gdpr_check_{{$application->id}}" name="admin_gdpr_check" value="1" application-id="{{$application->id}}" @if($application->admin_gdpr_checked == 1) checked="checked" @endif>
                    @else
                      <input type="checkbox" disabled="disabled" @if($application->admin_gdpr_checked == 1) checked="checked" @endif>
                    @endif
                    <!-- @if ($application->dbs_consent == 1)
                      <i class="fa fa-check-circle" style="color:green" alt="Consent Granted" title="Consent Granted on {{date('D jS M Y @ H:i:s', strtotime($application->dbs_consent_date))}}"></i>
                    @else
                      <i class="fa fa-times-circle" style="color:red" alt="Consent Not Granted" title="Consent Not Granted"></i>
                    @endif -->
                  </td>
                  <td>
                    <a href="{{ env('APP_URL') }}applications/viewApplicationDetails/{{$application->id}}"><button type="button" class="btn btn-success" title="View DBS"><i class="fa fa-search"></i> </button></a>
                    @if ($application->bpssApplication == 1 && $application->BPSSApplicationStatus == 1)
                      <a href="{{ env('APP_URL') }}applicant/downloadBPSSPDF/{{$application->BPSSApplicationID}}" target="_blank"><button type="button" class="btn btn-danger" title="Download BPSS"><i class="fa fa-file-pdf-o"></i></button></a>
                    @endif
                  </td>
                </tr>
                @endforeach
              </tbody>
            </table>
          @else
            There are no started or completed applications in the database.
          @endif
        </div><!-- /.box-body -->
      </div><!-- /.box -->
    </div>


  </div>

  </section>
</div>

@endsection


@section('pageCSS')
  <link href="{{ env('APP_URL') }}plugins/datatables/dataTables.bootstrap.css" rel="stylesheet" type="text/css" />
@endsection

@section('pageJavascript')
<script src="{{ env('APP_URL') }}plugins/datatables/jquery.dataTables.js" type="text/javascript"></script>
<script src="{{ env('APP_URL') }}plugins/datatables/dataTables.bootstrap.js" type="text/javascript"></script>
<script type="text/javascript">

  $(function () {
    $('#searchApplications').dataTable({
      "bPaginate": true,
      "bLengthChange": false,
      "bFilter": true,
      "bSort": true,
      "bInfo": true,
      "bAutoWidth": false
    });
  });

$('[id^="admin_gdpr_check_"]').click(function() {
  if($(this).is(':checked')){
      var admin_gdpr_checked = true;
  } else{
      var admin_gdpr_checked = false;
  }

  var applicationID = $(this).attr('application-id');
  update_gdpr_status(applicationID, admin_gdpr_checked)
});

function update_gdpr_status(applicationID, admin_gdpr_checked){
  $.ajax({
    url: "<?php echo env('APP_URL'); ?>" + "applications/updateGDPRStatus", 
        method: "POST",
        data: {"_token":"{{ csrf_token() }}", "applicationID":applicationID, "admin_gdpr_checked":admin_gdpr_checked},
        success: function(result){
        },
        error: function(result){
          alert('Error: Please try again!');
        },
  });
}


  

</script>
@endsection
