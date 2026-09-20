@extends('layout.admin')

@section('title', 'Manage Applications')

@section('content')

<?php //echo'<pre>';print_r($allusers);echo'</pre>'; ?>

<div class="container-fluid">

<div class="modal modal-warning" id="deleteModal" user-id="0">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">×</span></button>
        <h4 class="modal-title">WARNING</h4>
      </div>
      <div class="modal-body">
        <p>Are you sure you want to delete this applicant and all the information they have entered<br />including and DBS details?</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline pull-left" id="confirmDelete">YES</button>
        <button type="button" class="btn btn-outline pull-left" data-dismiss="modal">No</button>
        
      </div>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>

<div class="modal" id="deletePendingModal" applicant-id="0">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">×</span></button>
        <h4 class="modal-title">WARNING</h4>
      </div>
      <div class="modal-body">
        <p>Are you sure you want to cancel this DBS application request?</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn forceBgClassified pull-left" id="confirmPendingDelete">YES</button>
        <button type="button" class="btn btn-secondary pull-left" data-dismiss="modal">No</button>
        
      </div>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>

  <div class="box">
    <div class="box-header">
      <h3 class="box-title">Manage Applications</h3>
    </div><!-- /.box-header -->
    <div class="box-body">
      
      <div>
        <a href="{{ env('APP_URL') }}adminoperator/applicants/"><button type="button" class="btn @if(empty($dbsStatus)) btn-success @endif" title="Show All">All</button></a>&nbsp;&nbsp;&nbsp;
        <a href="{{ env('APP_URL') }}adminoperator/applicants/-1"><button type="button" class="btn @if(!empty($dbsStatus) && $dbsStatus == -1) btn-success @endif" title="Show All">Pending Registration</button></a>&nbsp;&nbsp;&nbsp;
        <a href="{{ env('APP_URL') }}adminoperator/applicants/1"><button type="button" class="btn @if(!empty($dbsStatus) && $dbsStatus == 1) btn-success @endif" title="Show All">Pending Application Review</button></a>&nbsp;&nbsp;&nbsp;
        <a href="{{ env('APP_URL') }}adminoperator/applicants/4"><button type="button" class="btn @if(!empty($dbsStatus) && $dbsStatus == 4) btn-success @endif" title="Show All">Submitted to DBS</button></a>&nbsp;&nbsp;&nbsp;
        <a href="{{ env('APP_URL') }}adminoperator/applicants/8"><button type="button" class="btn @if(!empty($dbsStatus) && $dbsStatus == 8) btn-success @endif" title="Show All">Application Processed</button></a>&nbsp;&nbsp;&nbsp;
      </div>
      <table id="allusers" class="table table-bordered table-striped">
        <thead>
          <tr>
            <th>Email</th>
            <th>Name</th>
            <th>Organisation</th>
            <th>DBS Status</th>
            <th>BPSS Status</th>
            <th>IDs Verified</th>
            <th>GDPR</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          @if(isset($allusers) && count($allusers)>0)
          @foreach ($allusers as $user)
          <?php //echo'<pre>';print_r($user);echo'</pre>'; ?>
          <tr id="user_block_{{ $user->id }}">
            <td>
              {{$user->email }}
              @if(!empty($user->lastLogin))<br />Last Login: {{date('d/m/Y @ H:i:s', strtotime($user->lastLogin))}}@endif
            </td>
            <td>
                @if($user->applicationStatus > 0)
                  <a href="{{ env('APP_URL') }}applications/viewApplicationDetails/{{$user->DBSApplicationID}}">{{$user->applicationDetails_presentSurname}}, {{$user->applicationDetails_forename}} {{$user->applicationDetails_middlename}}</a>
                  @if(count($user->otherNames)>0)
                    @foreach ($user->otherNames as $otherName)
                      <br />{{$otherName->other_surname}}, {{$otherName->other_forename}} {{$otherName->other_middlename}}
                    @endforeach
                  @endif
                @else
                  {{$user->lastName}}, {{$user->title}} {{$user->firstName}}
                @endif

            </td>
            <td>{{$user->organisationName }}</td>
            <td>
              @if ($user->dbsApplication == 1)
                @if ($user->applicationStatus == -1)
                  <span style="display: none;">9.</span><small class="label label-info" title="Applicant has not started their DBS application yet.">Not Started</small>
                @elseif ($user->applicationStatus == 0)
                  <span style="display: none;">4.</span><small class="label label-primary" title="Applicant has started the applications but has not finished it yet."><i class="fa fa-clock-o"></i> Incomplete</small>
                @elseif ($user->applicationStatus == 1)
                  <span style="display: none;">1.</span><small class="label label-warning" title="Applicant has submitted their DBS application and is awaiting to be reviewed by an administrator and to be submitted to DBS">Pending Review</small>
                @elseif ($user->applicationStatus == 2)
                  <span style="display: none;">2.</span><small class="label label-danger" title="The administrator considers the information submitted by teh applicant is wrong / not enough and it is being sent back to the applicant to update and re-submit">Review Fail</small>
                @elseif ($user->applicationStatus == 3)
                  <span style="display: none;">3.</span><small class="label label-warning" title="The administrator has approved the application and it is now pending submission to DBS">Pending Submission</small>
                @elseif ($user->applicationStatus == 4)
                  <span style="display: none;">7.</span><small class="label label-success" title="Application has been submitted to DBS">Submitted to DBS</small>
                @elseif ($user->applicationStatus == 5)
                  <span style="display: none;">5.</span><small class="label label-success" title="DBS has accepted the application for processing">DBS Submission Success</small>
                @elseif ($user->applicationStatus == 6)
                  <span style="display: none;">6.</span><small class="label label-danger" title="DBS has returned the application with errors (check DBS email for details)">DBS Fail</small>
                @elseif ($user->applicationStatus == 8)
                  <span style="display: none;">8.</span><small class="label label-success" title="Application has been processed by DBS. If 'Certificate contains no information' then DBS has found no information/restrictions to include on the certificate. If 'Please wait to view applicant certificate' then DBS has found information/restrictions and has included them on the printed certificate">{{$user->applicationDetails_dbsResponse_int023_DisclosureStatus}}</small>
                @endif
                @if($user->applicationStatus > 0)
                  <br />S: {{date('d/m/Y @ H:i:s', strtotime($user->applicationDetails_applicationCreatedOn))}} @if(strlen($user->applicationCompleted) > 1) <br />C: {{date('d/m/Y @ H:i:s', strtotime($user->applicationCompleted))}} @endif
                @endif
              @else 
                <span style="display: none;">91.</span><small class="label label-default" title="DBS application not required">Not Required</small>
              @endif
            </td>
            <td>
                @if ($user->bpssApplication == 1 && isset($user->bpss_applicationStatus))
                  @if ($user->bpss_applicationStatus == 1)
                    <small class="label label-success" title="Complete">Complete</small><br />
                    Start Date: @if (date('Y-m-d', strtotime($user->bpss_start_date)) != '1970-01-01'){{date('d/m/Y', strtotime($user->bpss_start_date))}}@else{{'Not Set'}}@endif
                    @elseif ($user->bpss_applicationStatus == 0)
                    <small class="label label-warning" title="Pending">Pending</small>
                  @endif
                @else
                  <small class="label label-default" title="Not Required">Not Required</small>
                @endif
            </td>
            <td>
              @if($user->applicationStatus > 0)
                @if ($user->admin_confirm_valid_passport)&nbsp;<i class="fa fa-book" title="Passport"></i>&nbsp;@endif
                @if ($user->admin_confirm_valid_dln)&nbsp;<i class="fa fa-car" title="Driver Licence"></i>&nbsp;@endif
                @if ($user->admin_confirm_valid_nino)&nbsp;<i class="fa fa-user" title="National Insurance Number"></i>@endif
              @else
                &nbsp;
              @endif
            </td>
            <td>
              @if($user->dbsApplication)
                @if($user->applicationStatus > 0)
                  @if ($user->applicationStatus == 1 || $user->applicationStatus == 2 || $user->applicationStatus == 3)
                  <input type="checkbox" id="admin_gdpr_check_{{$user->DBSApplicationID}}" name="admin_gdpr_check" value="1" application-id="{{$user->DBSApplicationID}}" @if($user->admin_gdpr_checked == 1) checked="checked" @endif>
                  @else
                    <input type="checkbox" disabled="disabled" @if($user->admin_gdpr_checked == 1) checked="checked" @endif>
                  @endif
                  {{-- @if ($user->dbs_consent == 1)
                    <i class="fa fa-check-circle" style="color:green" alt="Consent Granted" title="Consent Granted on {{date('D jS M Y @ H:i:s', strtotime($user->dbs_consent_date))}}"></i>
                  @else
                    <i class="fa fa-times-circle" style="color:red" alt="Consent Not Granted" title="Consent Not Granted"></i>
                  @endif --}}
                @else
                  <input type="checkbox" disabled="disabled">
                @endif
              @else
                {{'&nbsp;'}}
              @endif
            </td>
            <td>
              <a href="{{ env('APP_URL') }}users/viewProfile/{{ $user->id }}"><button type="button" class="btn btn-success" title="View Applicant"><i class="fa fa-search"></i></button></a>
              
              <a href="{{ env('APP_URL') }}adminoperator/editApplicant/{{$user->id}}"><button type="button" class="btn btn-info" title="View attached files" style="padding-left:5px; padding-right:5px;"><span style="font-weight: bold;">Files</span></button></a>
              
              @if ($user->applicationStatus > 0 && $user->bpssApplication == 1 && $user->bpss_applicationStatus && $user->bpssvr_formStatus)
                <a href="{{ env('APP_URL') }}"><button type="button" class="btn btn-info" title="BPSS VR" style="padding-left:5px; padding-right:5px;"><span style="font-weight: bold;">BPSS VR</span></button></a>
              @endif
              
            </td>
          </tr>
          @endforeach
          @endif

          @if(isset($pendingApplicants) && count($pendingApplicants)>0)
          @foreach ($pendingApplicants as $pendingApplicant)
          <tr id="applicant_block_{{ $pendingApplicant->id }}">
            <td>{{$pendingApplicant->surname}}, {{$pendingApplicant->forename}}</td>
            <td>{{$pendingApplicant->email }}</td>
            <td>{{$pendingApplicant->organisationName }}</td>
            <td><span style="display: none;">90.</span><small class="label label-default" title="User has not registered yet on the platform.">
              @if(date("Y-m-d", strtotime($pendingApplicant->lastEmailSent)) == '1970-01-01')
                Invitation not yet sent
              @else
                Pending User Registration. Invitation sent: {{date("Y-m-d H:i:s", strtotime($pendingApplicant->lastEmailSent))}}
              @endif
            </small></td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
          @endforeach
          @endif
        </tbody>
        <tfoot>
          <tr>
            <th>Email</th>
            <th>Name</th>
            <th>Organisation</th>
            <th>DBS Status</th>
            <th>BPSS Status</th>
            <th>IDs Verified</th>
            <th>GDPR</th>
            <th>Actions</th>
          </tr>
        </tfoot>
      </table>

      
      

    </div><!-- /.box-body -->
  </div><!-- /.box -->

</div>

@endsection


@section('pageCSS')
  <link href="{{ env('APP_URL') }}plugins/datatables/dataTables.bootstrap.css" rel="stylesheet" type="text/css" />
@endsection

@section('pageJavascript')
<script src="{{ env('APP_URL') }}plugins/datatables/jquery.dataTables.js" type="text/javascript"></script>
<script src="{{ env('APP_URL') }}plugins/datatables/dataTables.bootstrap.js" type="text/javascript"></script>
<script type="text/javascript">
  $('#deleteModal').on('show.bs.modal', function(e) {
    var userID = e.relatedTarget.dataset.userid;
    $('#deleteModal').attr("user-id", userID);
  });
  $(document).on("click", "#confirmDelete", function() {
    var userID = $("#deleteModal").attr("user-id");
    deleteUser(userID);
  });

  function deleteUser(userID){
    $.ajax({
        url: "<?php echo env('APP_URL'); ?>" + "users/deleteUser", 
        method: "POST",
        data: {"_token":"{{ csrf_token() }}", "userID":userID},
        success: function(result){
          if (result == 1){
            $("#user_block_"+userID).remove();
            $('#deleteModal').modal('toggle');
          }
        },
        error: function(result){
          $('#deleteModal').modal('toggle');
        },
      });
  }

  $(function () {
    $('#allusers').dataTable({
      "bPaginate": true,
      "bLengthChange": false,
      "bFilter": true,
      "bSort": true,
      "bInfo": true,
      "bAutoWidth": false,
      "aaSorting": [[3, 'asc']]
    });
  });

  $(document).on("click", "[id^='userRole_']", function() {
    var roleID = $(this).attr("role-id");
    var userID = $(this).attr("user-id");
      $("#label_userRole_"+userID+"_"+roleID).html('Updating');
      $("#label_userRole_"+userID+"_"+roleID).show();
      if( $(this).is(':checked')) {
          updateUserRole(userID, roleID, 'addRole');
      } else {
          updateUserRole(userID, roleID, 'removeRole');
      }
  });

  function updateUserRole(userID, roleID, updateType){
    $.ajax({
        url: "<?php echo env('APP_URL'); ?>" + "users/updateUserRole", 
        method: "POST",
        data: {"_token":"{{ csrf_token() }}", "userID":userID, "roleID":roleID, "updateType":updateType},
        success: function(result){
          $("#label_userRole_"+userID+"_"+roleID).html('Updated');
          $("#label_userRole_"+userID+"_"+roleID).show();
          setTimeout(function() {$("#label_userRole_"+userID+"_"+roleID).hide()}, 2000);
        },
        error: function(result){
          $("#label_userRole_"+userID+"_"+roleID).html('Error');
          $("#label_userRole_"+userID+"_"+roleID).show();
          setTimeout(function() {$("#label_userRole_"+userID+"_"+roleID).hide()}, 2000);
        },
      });
  }


  $('#deletePendingModal').on('show.bs.modal', function(e) {
    var applicantID = e.relatedTarget.dataset.applicantid;
    $('#deletePendingModal').attr("applicant-id", applicantID);
  });
  $(document).on("click", "#confirmPendingDelete", function() {
    var applicantID = $("#deletePendingModal").attr("applicant-id");
    $("[id^='resendRequestMessage_']").hide();
    deleteApplicationRequest(applicantID);
  });

  function deleteApplicationRequest(applicantID){
    $.ajax({
        url: "<?php echo env('APP_URL'); ?>" + "applications/removeRequest", 
        method: "POST",
        data: {"_token":"{{ csrf_token() }}", "applicantID":applicantID},
        success: function(result){
          if (result == 1){
            $("#applicant_block_"+applicantID).remove();
            $('#deletePendingModal').modal('toggle');
          }
        },
        error: function(result){
          $('#deletePendingModal').modal('toggle');
        },
      });
  }

  $(document).on("click", "[id^='resendRequest_']", function() {
    var applicantID = $(this).attr("data-applicantid");
    $("[id^='resendRequestMessage_']").hide();
    resendApplicationRequest(applicantID);
  });

  function resendApplicationRequest(applicantID){
    $.ajax({
        url: "<?php echo env('APP_URL'); ?>" + "applications/resendRequest", 
        method: "POST",
        data: {"_token":"{{ csrf_token() }}", "applicantID":applicantID},
        success: function(result){
          if (result == 1){
            $("#resendRequestMessage_"+applicantID).text('Email sent!');
            $("#resendRequestMessage_"+applicantID).show();
          }
        },
        error: function(result){
          $("#resendRequestMessage_"+applicantID).text('Error: Please try again!');
            $("#resendRequestMessage_"+applicantID).show();
        },
      });
  }

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
