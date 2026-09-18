@extends('layout.admin')

@section('title', 'Incomplete Applications')

@section('content')
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
        <p>Are you sure you want to delete this candidate and all the information they have entered<br />including and DBS details?</p>
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



  <section class="content">
    <div class="row">
      <div class="col-md-12">
        <div class="Newbox">
          <div class="box-header">
            <h3 class="box-title">Manage Applications</h3>
          </div><!-- /.box-header -->
          <div class="box-body">
            <div>
                <a href="{{ env('APP_URL') }}applications/allApplications"><button type="button" class="btn btnSelector" title="Show All">All</button></a>&nbsp;&nbsp;&nbsp;
                <a href="{{ env('APP_URL') }}applications/pendingRequests"><button type="button" class="btn btnSelector" title="Applications pending registeration">Pending Registration</button></a>&nbsp;&nbsp;&nbsp;
                <a href="{{ env('APP_URL') }}applications/inprogressApplications"><button type="button" class="btn btn-success" title="In progress applications">In Progress Applications</button></a>&nbsp;&nbsp;&nbsp;
                <a href="{{ env('APP_URL') }}applications/completedApplications"><button type="button" class="btn btnSelector" title="Completed Applications">Completed Applications</button></a>&nbsp;&nbsp;&nbsp;
            </div>
            @if(isset($applicants) && count($applicants) > 0)
              <div class="table-responsive">
                <table id="searchApplications" class="table table-bordered table-striped table-responsive">
                  <thead> 
                    <tr>
                      <th>Email</th>
                      <th>Name</th>
                      <th>Organisation</th>
                      <th>Checks ordered</th>
                      <th>IDs Verified</th>
                      <th>Started</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach ($applicants as $applicant)
                      <tr id="applicant_block_{{ $applicant->id }}">
                        <td>
                          @if ($applicant->email)
                              <i class="fa fa-copy copy-contact"
                                data-toggle="tooltip"
                                data-original-title="Copy email"
                                data-value="{{ $applicant->email }}"
                                style="cursor:pointer; margin-right:5px;"></i>
                              {{ $applicant->email }}
                          @endif

                          @if ($applicant->contact_number)
                              <br>
                              <i class="fa fa-copy copy-contact"
                                data-toggle="tooltip"
                                data-original-title="Copy telephone"
                                data-value="+{{ $applicant->contact_number_country_code ?? '' }} {{ $applicant->contact_number }}"
                                style="cursor:pointer; margin-right:5px;"></i>
                              Tel: +{{ $applicant->contact_number_country_code ?? '' }} {{ $applicant->contact_number }}
                          @endif

                          @if ($applicant->mobile_number)
                              <br>
                              <i class="fa fa-copy copy-contact"
                                data-toggle="tooltip"
                                data-original-title="Copy mobile"
                                data-value="+{{ $applicant->mobile_number_country_code ?? '' }} {{ $applicant->mobile_number }}"
                                style="cursor:pointer; margin-right:5px;"></i>
                              Mob: +{{ $applicant->mobile_number_country_code ?? '' }} {{ $applicant->mobile_number }}
                          @endif
                        </td>


                          <td>{{ $applicant->firstName ?? ''}} {{ $applicant->lastName ?? ''}}</td>
                          <td>{{ $applicant->organisationName ?? ''}}</td>
                          <td>
                              <!--DBS - Basic-->
                              @if ($applicant->dbsApplication == 1)
                                  @if ($applicant->applicationStatus == -1)
                                      <small class="label label-info" title="Applicant has not started their DBS application yet.">DBS - Basic | Not Started</small><br>
                                  @elseif ($applicant->applicationStatus == 0)
                                      <small class="label label-primary" title="Applicant has started the applications but has not finished it yet.">DBS - Basic | Incomplete</small><br>
                                  @elseif ($applicant->applicationStatus == 1)
                                      <small class="label label-warning" title="Applicant has submitted their DBS application and is awaiting to be reviewed by an administrator and to be submitted to DBS.">DBS - Basic | Pending Review</small><br>
                                  @elseif ($applicant->applicationStatus == 2)
                                      <small class="label label-danger" title="The administrator considers the information submitted by the applicant is wrong / not enough and it is being sent back to the applicant to update and re-submit.">DBS - Basic | Review Fail</small><br>
                                  @elseif ($applicant->applicationStatus == 3)
                                      <small class='label label-warning' title='The administrator has approved the application and it is now pending submission to DBS.'>DBS - Basic | Pending Submission</small><br>
                                  @elseif ($applicant->applicationStatus == 4)
                                      <small class="label label-success" title="Application has been submitted to DBS.">DBS - Basic | Submitted to DBS</small><br>
                                  @elseif ($applicant->applicationStatus == 5)
                                      <small class="label label-success" title="DBS has accepted the application for processing.">DBS - Basic | DBS Processing</small><br>
                                  @elseif ($applicant->applicationStatus == 6)
                                      <small class="label label-danger" title="DBS has returned the application with errors (check DBS email for details).">DBS - Basic | DBS Fail</small><br>
                                  @elseif ($applicant->applicationStatus == 8)
                                      <small class='label label-success' title='Application has been processed by DBS. If \"Certificate contains no information\" then DBS has found no information/restrictions to include on the certificate. If \"Please wait to view applicant certificate\" then DBS has found information/restrictions and has included them on the printed certificate'>DBS - Basic | {{ $applicant->dbsResponse_int023_DisclosureStatus }}</small><br>
                                  @endif
                              @endif

                              <!--BPSS-->
                              @if ($applicant->bpssApplication == 1)
                                  @if (isset($applicant->BPSSapplicationStatus) && $applicant->BPSSapplicationStatus == 1)
                                      <small class="label label-success" title="User has finished their BPSS check.">BPSS | Complete</small><br>
                                  @else
                                      <small class="label label-primary" title="User has not finished their BPSS check.">BPSS | Incomplete</small><br>
                                  @endif
                              @endif

                              <!--Digital ID Verification-->
                              @if ($applicant->useYoti == 1)
                                  <small class="label label-primary" title="User hasn't started their ID check yet."><i class="fa fa-clock-o" aria-hidden="true"></i> Digital ID | Incomplete</small>
                              @elseif ($applicant->useYoti == 2)
                                  <small class="label label-success" title="User has completed their Digital ID check.">Digital ID | Complete</small>
                              @elseif ($applicant->useYoti == 3)
                                  <small class="label label-danger" title="User has failed their Digital ID check.">Digital ID | Failed</small>
                              @endif
                          </td>
                          <td>
                          @if ($applicant->applicationStatus > 0)
                              @if ($applicant->admin_confirm_valid_passport)
                                  &nbsp;<i class="fa fa-book" title="Passport"></i>&nbsp;
                              @endif
                              @if ($applicant->admin_confirm_valid_dln) 
                                  &nbsp;<i class="fa fa-car" title="Driver Licence"></i>&nbsp;
                              @endif
                              @if ($applicant->admin_confirm_valid_nino)
                                  &nbsp;<i class="fa fa-user" title="National Insurance Number"></i>
                              @endif
                          @endif
                          </td>
                          <td>{{date("D jS M Y @ H:i:s", strtotime( $applicant->createdOn ))}}</td>
                          <td>
                              <a href="{{ env('APP_URL') }}users/viewProfile/{{ $applicant->id }}"><button type="button" class="btn btn-success" title="View Applicant"><i class="fa fa-search"></i></button></a>
                              <a href="{{ env('APP_URL') }}users/editProfile/{{ $applicant->id }}"><button type="button" class="btn btn-warning" title="Edit Applicant"><i class="fa fa-edit"></i></button></a>
                              <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#deleteModal" data-userid="{{ $applicant->id }}" title="Remove Applicant"><i class="fa fa-times"></i></button>
                              @if($applicant->dbsApplication && $applicant->applicationStatus > 0)
                                  <a href="{{ env('APP_URL') }}applications/viewApplicationDetails/{{ $applicant->applicationID }}"><button type="button" class="btn btn-info" title="View DBS Application" style="padding-left:5px; padding-right:5px;"><span style="font-weight: bold;">DBS</span></button></a>
                              @endif
                              @if ($applicant->useYoti == 2)
                                  <a href="{{ env('APP_URL') }}yoti-report/{{ $applicant->id }}"><button type="button" class="btn btn-success" title="View YOTI Report" style="padding-left:5px; padding-right:5px;"><span style="font-weight: bold;">YOTI</span></button></a>
                              @elseif ($applicant->useYoti == 3)
                                    <a href="{{ env('APP_URL') }}yoti/sendBack/{{ $applicant->id }}"><button type="button" class="btn btn-danger" title="View YOTI Report" style="padding-left:5px; padding-right:5px;"><span style="font-weight: bold;">Send back</span></button></a>
                              @endif
                              <a href="{{ env('APP_URL') }}finalReport/review/{{ $applicant->id }}"><button type="button" class="btn btn-danger" title="Review Final Report" style="padding-left:5px; padding-right:5px;"><span style="font-weight: bold;">Review</span></button></a>
                              <a href="{{ env('APP_URL') }}adminoperator/editApplicant/{{ $applicant->id }}"><button type="button" class="btn btn-info" title="View attached files" style="padding-left:5px; padding-right:5px;"><span style="font-weight: bold;">Files</span></button></a>
                          </td>
                      </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
            @else
              <br>
              There are no applicantions in progress.
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
  <style>
    .label {
        font-size: 12px;
        display: inline-block;
        margin-bottom: 5px;
    }

    .Newbox {
      background: white;
      padding: 2rem;
      border-radius: 12px;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.05);
    }

    .box-title {
      color: #2C3C64;
    }

    .btnSelector {
      color: #2C3C64;
    }

    .copy-contact {
      color: #2C3C64;
      transition: color 0.2s ease;
    }
    .copy-contact:hover {
      color: #C55359;
    }

    
    @media (max-width: 768px) {
        .container-fluid {
            padding: 0;
        }
        .content {
            padding: 0;
        }
    }
  </style>
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

  $(document).ready(function() {
      $('[data-toggle="tooltip"]').tooltip();
  });

  $(document).on('click', '.copy-contact', function() {
      var value = $(this).data('value');
      var $icon = $(this);

      navigator.clipboard.writeText(value).then(function() {
          // Show "Copied!" tooltip
          $icon.attr('data-original-title', 'Copied!').tooltip('show');

          setTimeout(function() {
              $icon.tooltip('hide')
                  .attr('data-original-title', $icon.data('original-title') || 'Copy');
          }, 1000);
      }).catch(function(err) {
          console.error('Failed to copy: ', err);
      });
  });

</script>
@endsection
