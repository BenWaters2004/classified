@extends('layout.admin')

@section('title', 'All Applications')

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
        <p>Are you sure you want to delete this candidate and all the information they have entered?</p>
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

<!-- Delete Confirmation Modal -->
<div class="modal" id="deleteRequestModal" applicant-id="0">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">×</span></button>
        <h4 class="modal-title">WARNING</h4>
      </div>
      <div class="modal-body">
        <p>Are you sure you want to cancel this application request?</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary pull-left" id="confirmDelete">YES</button>
        <button type="button" class="btn btn-secondary pull-left" data-dismiss="modal">No</button>
      </div>
    </div>
  </div>
</div>

<!-- View Registration Link Modal -->
<div class="modal" id="viewLinkModal">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">×</span>
        </button>
        <h4 class="modal-title">Registration Link</h4>
      </div>
      <div class="modal-body">
        <a id="registrationLink"></a>
        </br>
        <button type="button" class="btn btn-primary copy-link-btn" id="copyLink" data-toggle="tooltip" data-original-title="Copy link">
          Copy Link <i class="fa fa-copy"></i>
        </button>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>


<div class="modal modal-warning" id="returnModal" user-id="0">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">×</span>
        </button>
        <h4 class="modal-title">Revoke VR</h4>
      </div>

      <div class="modal-body">
        <p>
          Are you sure you want to revoke this applicant's VR?<br>
          This will mark the applicant as incomplete and move them out of Completed Applications.
        </p>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-outline pull-left" id="confirmReturnVR">YES</button>
        <button type="button" class="btn btn-outline pull-left" data-dismiss="modal">No</button>
      </div>
    </div>
  </div>
</div>


  <section class="content">
    <div class="row">
      <div class="col-md-12">
        <div class="Newbox">
          <div class="box-header">
            <h3 class="box-title">All Applications</h3>
          </div><!-- /.box-header -->
          <div class="box-body">
            <div>
                <a href="{{ env('APP_URL') }}applications/allApplications"><button type="button" class="btn btn-success" title="Show All">All</button></a>&nbsp;&nbsp;&nbsp;
                <a href="{{ env('APP_URL') }}applications/pendingRequests"><button type="button" class="btn btnSelector" title="Applications pending registeration">Pending Registration</button></a>&nbsp;&nbsp;&nbsp;
                <a href="{{ env('APP_URL') }}applications/inprogressApplications"><button type="button" class="btn btnSelector" title="In progress applications">In Progress Applications</button></a>&nbsp;&nbsp;&nbsp;
                <a href="{{ env('APP_URL') }}applications/completedApplications"><button type="button" class="btn btnSelector" title="Completed Applications">Completed Applications</button></a>&nbsp;&nbsp;&nbsp;
            </div>
            <div class="table-responsive">
              <table id="searchApplications" class="table table-bordered table-striped table-responsive">
                <thead> 
                  <tr>
                    <th>Email</th>
                    <th>Name</th>
                    <th>Organisation</th>
                    <th>Checks ordered</th>
                    <th>IDs Verified</th>
                    <th>Status</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody></tbody>
              </table>
            </div>
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

    .copy-contact, .copy-link-btn i {
      color: #2C3C64;
      transition: color 0.2s ease;
    }
    .copy-contact:hover, .copy-link-btn:hover i {
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

$(document).ready(function() {
    $('#searchApplications').dataTable({
        "bProcessing": true,
        "bServerSide": true,
        "sAjaxSource": "{{ url('/applications/allApplicationsData') }}",
        "sServerMethod": "POST",

        "fnServerParams": function (aoData) {
            aoData.push({ "name": "_token", "value": "{{ csrf_token() }}" });
        },

        "fnServerData": function (sSource, aoData, fnCallback, oSettings) {
            oSettings.jqXHR = $.ajax({
                "dataType": 'json',
                "type": "POST",
                "url": sSource,
                "data": aoData,
                "success": function (json) {
                    fnCallback(json);
                },
                "error": function (xhr, status, error) {
                    console.error('❌ AJAX ERROR:', status, error);
                }
            });
        },

        "aoColumns": [
            // 1. Email + phones
            { "mData": "email", "mRender": function(data, type, row) {
                let html = '';
                if (row.email) {
                    html += `<i class="fa fa-copy copy-contact" data-toggle="tooltip" data-original-title="Copy email" data-value="${row.email}" style="cursor:pointer; margin-right:5px;"></i>${row.email}`;
                }
                if (row.searchStatus != 1) {
                    if (row.contact_number) {
                        let tel = '+' + (row.contact_number_country_code || '') + ' ' + (row.contact_number || '');
                        html += `<br><i class="fa fa-copy copy-contact" data-toggle="tooltip" data-original-title="Copy telephone" data-value="${tel}" style="cursor:pointer; margin-right:5px;"></i>Tel: ${tel}`;
                    }
                    if (row.mobile_number) {
                        let mob = '+' + (row.mobile_number_country_code || '') + ' ' + (row.mobile_number || '');
                        html += `<br><i class="fa fa-copy copy-contact" data-toggle="tooltip" data-original-title="Copy mobile" data-value="${mob}" style="cursor:pointer; margin-right:5px;"></i>Mob: ${mob}`;
                    }
                } else if (row.mobileNumberMain) {
                    html += `<br><i class="fa fa-copy copy-contact" data-toggle="tooltip" data-original-title="Copy mobile" data-value="${row.mobileNumberMain}" style="cursor:pointer; margin-right:5px;"></i>Mob: ${row.mobileNumberMain}`;
                }
                return html || '';
            }},
            // 2. Name
            { "mData": "firstName", "mRender": function(data, type, row) {
                if (row.searchStatus != 1) {
                    return (row.firstName || '') + ' ' + (row.lastName || '');
                } else {
                    return (row.forename || '') + ' ' + (row.surname || '');
                }
            }},
            // 3. Organisation
            { "mData": "organisationName" },
            // 4. Checks ordered
            { 
                "mData": "searchStatus", 
                "mRender": function(data, type, row) {
                    let html = '';
                    if (row.searchStatus == 2) {
                        // DBS - Basic
                        if (row.dbsApplication == 1) {
                            if (row.applicationStatus == -1) html += `<small class="label label-info">DBS - Basic | Not Started</small><br>`;
                            else if (row.applicationStatus == 0) html += `<small class="label label-primary">DBS - Basic | Incomplete</small><br>`;
                            else if (row.applicationStatus == 1) html += `<small class="label label-warning">DBS - Basic | Pending Review</small><br>`;
                            else if (row.applicationStatus == 2) html += `<small class="label label-danger">DBS - Basic | Review Fail</small><br>`;
                            else if (row.applicationStatus == 3) html += `<small class='label label-warning'>DBS - Basic | Pending Submission</small><br>`;
                            else if (row.applicationStatus == 4) html += `<small class="label label-success">DBS - Basic | Submitted to DBS</small><br>`;
                            else if (row.applicationStatus == 5) html += `<small class="label label-success">DBS - Basic | DBS Processing</small><br>`;
                            else if (row.applicationStatus == 6) html += `<small class="label label-danger">DBS - Basic | DBS Fail</small><br>`;
                            else if (row.applicationStatus == 8) html += `<small class='label label-success'>DBS - Basic | ${row.dbsResponse_int023_DisclosureStatus || ''}</small><br>`;
                            else html += `<small class="label label-primary">DBS - Basic | Incomplete</small><br>`;
                        }
                        // BPSS
                        if (row.bpssApplication == 1) {
                            html += (row.BPSSapplicationStatus == 1) 
                                ? `<small class="label label-success">BPSS | Complete</small><br>` 
                                : `<small class="label label-primary">BPSS | Incomplete</small><br>`;
                        }
                        // Digital ID Verification
                        if (row.useYoti == 1) html += `<small class="label label-primary"><i class="fa fa-clock-o" aria-hidden="true"></i> Digital ID | Incomplete</small>`;
                        else if (row.useYoti == 2) html += `<small class="label label-success">Digital ID | Complete</small>`;
                        else if (row.useYoti == 3) html += `<small class="label label-danger">Digital ID | Failed</small>`;
                    } 
                    else if (row.searchStatus == 1) {
                        html = `<small class="label label-danger">Not Registered</small>`;
                    } 
                    else if (row.searchStatus == 3) {
                        html = `<small class="label label-success">Complete</small>`;
                    }
                    return html;
                }
            },
            // 5. IDs Verified
            { "mData": "searchStatus", "mRender": function(data, type, row) {
                if (row.searchStatus != 1 && row.applicationStatus > 0) {
                    let icons = '';
                    if (row.admin_confirm_valid_passport) icons += `<i class="fa fa-book" title="Passport"></i>&nbsp;`;
                    if (row.admin_confirm_valid_dln) icons += `<i class="fa fa-car" title="Driver Licence"></i>&nbsp;`;
                    if (row.admin_confirm_valid_nino) icons += `<i class="fa fa-user" title="National Insurance Number"></i>`;
                    return icons;
                }
                return '';
            }},
            // 6. Status
            { "mData": "searchStatus", "mRender": function(data, type, row) {
                if (row.searchStatus == 1) {
                    return `<small class="label label-danger">Not Registered</small>`;
                } else if (row.searchStatus == 2) {
                    return `<small class="label label-warning">Inprogress</small><br>Started: ${row.createdOn ? new Date(row.createdOn).toLocaleString() : ''}`;
                } else if (row.searchStatus == 3) {
                    return `<small class="label label-success">Completed</small><br>${row.timeSinceCompletion} ago`;
                }
                return '';
            }},
            // 7. Actions
            { "mData": "id", "mRender": function(data, type, row) {
                let html = '';
                let id = row.id || row.applicantID || data;
                let appUrl = '{{ env('APP_URL') }}';

                if (row.searchStatus == 1) {
                    html += `<button type="button" class="btn btn-danger" data-toggle="modal" data-target="#deleteRequestModal" data-applicantid="${id}" title="Cancel Request" style="margin: 1px;"> <i class="fa fa-remove"></i></button>`;
                    html += `<button id="resendRequest_${id}" data-applicantid="${id}" type="button" class="btn btn-success" title="Resend Email" style="margin: 1px;"><i class="fa fa-envelope"></i></button>`;
                    html += `<span id="resendRequestMessage_${id}" class="text-red" style="display:none;">Email sent!</span>`;
                    html += `<button type="button" class="btn btn-info view-link-btn" data-applicantid="${id}" data-link="${appUrl}register/${row.accessUrlCode || ''}" title="View Registration Link" style="margin: 1px;"><i class="fa fa-eye"></i></button>`;
                    html += `<a href="${appUrl}applications/pendingRequests/${id}/edit" class="btn btn-warning" title="Edit Applicant" style="margin: 1px;"><i class="fa fa-edit"></i></a>`;
                } else if (row.searchStatus == 2) {
                    html += `<button type="button" class="btn btn-danger" data-toggle="modal" data-target="#deleteModal" data-userid="${id}" title="Remove Applicant" style="margin: 1px;"><i class="fa fa-times"></i></button>`;
                    html += `<a href="${appUrl}users/editProfile/${id}" style="margin: 1px;"><button type="button" class="btn btn-warning" title="Edit Applicant"><i class="fa fa-edit"></i></button></a>`;
                    html += `<a href="${appUrl}users/viewProfile/${id}" style="margin: 1px;"><button type="button" class="btn btn-success" title="View Applicant"><i class="fa fa-search"></i></button></a>`;
                    html += `<a href="${appUrl}adminoperator/editApplicant/${id}" style="margin: 1px;"><button type="button" class="btn btn-info"><span style="font-weight:bold;">Files</span></button></a>`;
                    if (row.dbsApplication && row.applicationStatus > 0) html += `<a href="${appUrl}applications/viewApplicationDetails/${row.applicationID}" style="margin: 1px;"><button type="button" class="btn btn-info" ><span style="font-weight:bold;">DBS</span></button></a>`;
                    if (row.useYoti == 2) html += `<a href="${appUrl}yoti-report/${id}" style="margin: 1px;"><button type="button" class="btn btn-success"><span style="font-weight:bold;">YOTI</span></button></a>`;
                    else if (row.useYoti == 3) html += `<a href="${appUrl}yoti/sendBack/${id}" style="margin: 1px;"><button type="button" class="btn btn-danger"><span style="font-weight:bold;">Send back</span></button></a>`;
                    html += `<a href="${appUrl}finalReport/review/${id}" style="margin: 1px;"><button type="button" class="btn btn-danger"><span style="font-weight:bold;">Review</span></button></a>`;
                } else {
                    html += `<button type="button" class="btn btn-danger" data-toggle="modal" data-target="#deleteModal" data-userid="${id}" title="Remove Applicant" style="margin: 1px;"><i class="fa fa-times"></i></button>`;
                    html += `<a href="${appUrl}adminoperator/editApplicant/${id}" style="margin: 1px;"><button type="button" class="btn btn-info"><span style="font-weight:bold;">Files</span></button></a>`;
                    html += `<a href="{{ env('APP_URL') }}users/editProfile/${id}"><button type="button" class="btn btn-warning" title="Edit Applicant"><i class="fa fa-edit"></i></button></a>`;
                    if (row.dbsApplication && row.applicationStatus > 0) html += `<a href="${appUrl}applications/viewApplicationDetails/${row.applicationID}" style="margin: 1px;"><button type="button" class="btn btn-info"><span style="font-weight:bold;">DBS</span></button></a>`;
                    if (row.useYoti == 2) html += `<a href="${appUrl}yoti-report/${id}" style="margin: 1px;"><button type="button" class="btn btn-success"><span style="font-weight:bold;">YOTI</span></button></a>`;
                    html += `<a href="https://classified.getclassified.co.uk/finalReport/generate/${id}" target="_blank" style="margin: 1px;"><button type="button" class="btn btn-success"><span style="font-weight:bold;">Final Report</span></button></a>`;
                    html += `<button type="button" class="btn btn-danger" data-toggle="modal" data-target="#returnModal" data-userid="${id}" title="Revoke VR" style="margin: 1px;">Revoke</button>`;
                }
                return html;
            }}
        ],

        "bPaginate": true,
        "bLengthChange": false,
        "bFilter": true,
        "bSort": true,
        "bInfo": true,
        "bAutoWidth": false
    });
});


  $(document).on("click", ".view-link-btn", function() {
    var link = $(this).data("link");
    $("#registrationLink").text(link);
    $("#copyLink").data("value", link);
    $("#viewLinkModal").modal("show");
  });

  $(document).on("click", "#copyLink", function() {
    var value = $(this).data("value");
    var $button = $(this);
    var originalTitle = $button.attr("data-original-title");

    navigator.clipboard.writeText(value).then(function() {
      $button.attr("data-original-title", "Copied!").tooltip("show");

      setTimeout(function() {
        $button.tooltip("hide")
          .attr("data-original-title", originalTitle);
      }, 1000);
    }).catch(function(err) {
      console.error("Failed to copy: ", err);
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

  $('#deleteRequestModal').on('show.bs.modal', function(e) {
    var applicantID = e.relatedTarget.dataset.applicantid;
    $('#deleteRequestModal').attr("applicant-id", applicantID);
  });

  $(document).on("click", "#confirmDelete", function() {
    var applicantID = $("#deleteRequestModal").attr("applicant-id");
    deleteApplicationRequest(applicantID);
  });

  function deleteApplicationRequest(applicantID) {
    $.ajax({
        url: "{{ env('APP_URL') }}applications/removeRequest", 
        method: "POST",
        data: {"_token": "{{ csrf_token() }}", "applicantID": applicantID},
        success: function(result) {
          if (result == 1) {
            $("#applicant_block_" + applicantID).remove();
            $('#deleteRequestModal').modal('toggle');
          }
        }
      });
  }

  $(document).on("click", "[id^='resendRequest_']", function() {
    var applicantID = $(this).attr("data-applicantid");
    resendApplicationRequest(applicantID);
  });

  function resendApplicationRequest(applicantID) {
    $.ajax({
        url: "{{ env('APP_URL') }}applications/resendRequest", 
        method: "POST",
        data: {"_token": "{{ csrf_token() }}", "applicantID": applicantID},
        success: function(result) {
          if (result == 1) {
            $("#resendRequestMessage_" + applicantID).text('Email sent!').show();
          }
        }
      });
  }

  $('#returnModal').on('show.bs.modal', function(e) {
      var userID = e.relatedTarget.dataset.userid;
      $('#returnModal').attr("user-id", userID);
  });

  $(document).on("click", "#confirmReturnVR", function() {
      var userID = $("#returnModal").attr("user-id");
      returnVR(userID);
  });

  function returnVR(userID) {
    $.ajax({
        url: "{{ route('applications.returnVR') }}",
        method: "POST",
        dataType: "json",
        data: {
            "_token": "{{ csrf_token() }}",
            "userID": userID
        },
        success: function(response) {
            if (response.success) {
                $('#returnModal').modal('hide');

                // Refresh DataTable without refreshing whole page
                $('#searchApplications').dataTable().fnDraw(false);
            } else {
                alert(response.message || "Unable to revoke VR.");
            }
        },
        error: function(xhr) {
            console.error(xhr.responseText);
            alert("There was an error returning the VR.");
            $('#returnModal').modal('hide');
        }
    });
  }

  $(document).ready(function() {
    // Enable tooltips
    $('[data-toggle="tooltip"]').tooltip();
});

// Copy click handler
$(document).on('click', '.copy-contact', function() {
    var value = $(this).data('value');
    var $icon = $(this);
    var originalTitle = $icon.attr('data-original-title');

    navigator.clipboard.writeText(value).then(function() {
        $icon.attr('data-original-title', 'Copied!').tooltip('show');

        setTimeout(function() {
            $icon.tooltip('hide')
                 .attr('data-original-title', originalTitle);
        }, 1000);
    }).catch(function(err) {
        console.error('Failed to copy: ', err);
    });
});

</script>
@endsection
