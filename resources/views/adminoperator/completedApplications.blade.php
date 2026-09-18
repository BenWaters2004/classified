@extends('layout.admin')

@section('title', 'Completed Applications')

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
            <h3 class="box-title">Manage Applications</h3>
          </div><!-- /.box-header -->
          <div class="box-body">
            <div>
                <a href="{{ env('APP_URL') }}applications/allApplications"><button type="button" class="btn btnSelector" title="Show All">All</button></a>&nbsp;&nbsp;&nbsp;
                <a href="{{ env('APP_URL') }}applications/pendingRequests"><button type="button" class="btn btnSelector" title="Applications pending registeration">Pending Registration</button></a>&nbsp;&nbsp;&nbsp;
                <a href="{{ env('APP_URL') }}applications/inprogressApplications"><button type="button" class="btn btnSelector" title="In progress applications">In Progress Applications</button></a>&nbsp;&nbsp;&nbsp;
                <a href="{{ env('APP_URL') }}applications/completedApplications"><button type="button" class="btn btn-success" title="Completed Applications">Completed Applications</button></a>&nbsp;&nbsp;&nbsp;
            </div>
            <div class="table-responsive">
              <table id="searchApplications" class="table table-bordered table-striped table-responsive">
                <thead> 
                  <tr>
                    <th>Email</th>
                    <th>Name</th>
                    <th>Organisation</th>
                    <th>Status</th>
                    <th>Final Report</th>
                    <th>Date</th>
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

    .copy-email {
    color: #2C3C64;
    transition: color 0.2s ease;
  }
  .copy-email:hover {
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
        "sAjaxSource": "{{ url('/applications/completedApplicationsData') }}",
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

        // === EXACT 7 COLUMNS WITH PROPER RENDERING ===
        "aoColumns": [
            // 1. Email + copy icon
            { 
                "mData": "email",
                "mRender": function(data, type, row) {
                    return `<i class="fa fa-copy copy-email" data-toggle="tooltip" data-original-title="Copy email" style="cursor:pointer; margin-right:5px;" data-email="${row.email}"></i>${row.email}`;
                }
            },
            // 2. Name (first + last)
            { 
                "mData": "firstName",
                "mRender": function(data, type, row) {
                    return row.firstName + ' ' + row.lastName;
                }
            },
            // 3. Organisation
            { "mData": "organisationName" },
            // 4. Status
            { 
                "mData": "applicationStatus",
                "mRender": function() {
                    return '<small class="label label-success" title="Complete">Complete</small>';
                }
            },
            // 5. Final Report
            { 
                "mData": "id",
                "mRender": function(data) {
                    return `<a href="https://classified.getclassified.co.uk/finalReport/generate/${data}" target="_blank"><button type="button" class="btn btn-success" style="padding-left:5px; padding-right:5px;"><span style="font-weight: bold;">Final Report</span></button></a>`;
                }
            },
            // 6. Date
            { "mData": "timeSinceCompletion" },
            // 7. Actions
            { 
                "mData": "id",
                "mRender": function(data, type, row) {
                    let html = `
                        <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#deleteModal" data-userid="${row.id}" title="Remove Applicant" style="margin: 1px;"><i class="fa fa-times"></i></button>
                        <a href="{{ env('APP_URL') }}adminoperator/editApplicant/${row.id}" style="margin: 1px;"><button type="button" class="btn btn-info" title="View attached files" style="padding-left:5px; padding-right:5px;"><span style="font-weight: bold;">Files</span></button></a>
                        <a href="{{ env('APP_URL') }}users/editProfile/${row.id}"><button type="button" class="btn btn-warning" title="Edit Applicant"><i class="fa fa-edit"></i></button></a>
                    `;

                    if (row.dbsApplication && row.applicationStatus > 0) {
                        html += `<a href="{{ env('APP_URL') }}applications/viewApplicationDetails/${row.applicationID}" style="margin: 1px;"><button type="button" class="btn btn-info" title="View DBS Application" style="padding-left:5px; padding-right:5px;"><span style="font-weight: bold;">DBS</span></button></a>`;
                    }

                    html += `<button type="button" class="btn btn-danger" data-toggle="modal" data-target="#returnModal" data-userid="${row.id}" title="Revoke VR" style="padding-left:5px; padding-right:5px; margin:1px;"><span style="font-weight: bold;">Revoke</span></button>`;
                    return html;
                }
            }
        ],

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
              $('#searchApplications').DataTable().ajax.reload();  // ← refresh table
              $('#deleteModal').modal('toggle');
          }
      },
        error: function(result){
          $('#deleteModal').modal('toggle');
        },
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
          data: {
              "_token": "{{ csrf_token() }}",
              "userID": userID
          },
          success: function(response) {
              if (response.success) {
                  $('#returnModal').modal('hide');
                  $('#searchApplications').dataTable().fnDraw(false);
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

$(document).on('click', '.copy-email', function() {
  var email = $(this).data('email');
  var $icon = $(this);

  navigator.clipboard.writeText(email).then(function() {
    // Temporarily change tooltip text
    $icon.attr('data-original-title', 'Copied!').tooltip('show');

    // Revert tooltip after 1s
    setTimeout(function() {
      $icon.tooltip('hide')
        .attr('data-original-title', 'Copy email');
    }, 1000);
  }).catch(function(err) {
    console.error('Failed to copy email: ', err);
  });
});

</script>
@endsection
