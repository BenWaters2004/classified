@extends('layout.admin')

@section('title', 'Pending Applications')

@section('content')

<!-- Delete Confirmation Modal -->
<div class="modal" id="deleteModal" applicant-id="0">
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

<!-- Send SMS Confirmation Modal 
<div class="modal" id="sendSmsModal" applicant-id="0">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">×</span>
        </button>
        <h4 class="modal-title">Send Registration SMS</h4>
      </div>
      <div class="modal-body">
        <p>Are you sure you want to send this applicant a registration link by text message?</p>
        <p id="smsPreviewNumber" class="text-muted" style="margin-bottom:0;"></p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary pull-left" id="confirmSendSms">Yes, send SMS</button>
        <button type="button" class="btn btn-secondary pull-left" data-dismiss="modal">No</button>
      </div>
    </div>
  </div>
</div> -->

@if(session('success'))
    <div class="alert alert-success alert-dismissible">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">
            ×
        </button>

        <i class="fa fa-check"></i>
        {{ session('success') }}
    </div>
@endif

<div class="container-fluid">
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
              <a href="{{ env('APP_URL') }}applications/pendingRequests"><button type="button" class="btn btn-success" title="Applications pending registeration">Pending Registration</button></a>&nbsp;&nbsp;&nbsp;
              <a href="{{ env('APP_URL') }}applications/inprogressApplications"><button type="button" class="btn btnSelector" title="In progress applications">In Progress Applications</button></a>&nbsp;&nbsp;&nbsp;
              <a href="{{ env('APP_URL') }}applications/completedApplications"><button type="button" class="btn btnSelector" title="Completed Applications">Completed Applications</button></a>&nbsp;&nbsp;&nbsp;
            </div>
            @if(isset($applicants) && count($applicants) > 0)
              <div class="table-responsive">
                <table id="searchApplications" class="table table-bordered table-striped">
                  <thead>
                    <tr>
                      <th>Email</th>
                      <th>Name</th>
                      <th>Organisation</th>
                      <th>Status</th>
                      <th>Date Submitted</th>
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

                        @if ($applicant->mobileNumberMain)
                          <br>
                          <i class="fa fa-copy copy-contact"
                            data-toggle="tooltip"
                            data-original-title="Copy mobile"
                            data-value="{{ $applicant->mobileNumberMain }}"
                            style="cursor:pointer; margin-right:5px;"></i>
                          Mob: {{ $applicant->mobileNumberMain }}
                        @endif
                    </td>
                      <td>{{ $applicant->surname }}, {{ $applicant->forename }}</td>
                      <td>{{ $applicant->organisationName }}</td>
                      <td>Not Registered</td>
                      <td>
                        @if (date("Y-m-d", strtotime($applicant->lastEmailSent)) != '1970-01-01') 
                          {{date("D jS M Y @ H:i:s", strtotime($applicant->lastEmailSent))}} 
                        @else 
                          <small class="label label-danger" title="User is awaiting for the invitation to be sent">Awaiting invitation to be sent</small>
                        @endif
                      </td>
                      <td>
                        <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#deleteModal" data-applicantid="{{ $applicant->id }}" title="Cancel Request"><i class="fa fa-remove"></i></button>
                        <button id="resendRequest_{{ $applicant->id }}" data-applicantid="{{ $applicant->id }}" type="button" class="btn btn-success" title="Resend Email"><i class="fa fa-envelope"></i></button>
                        <span id="resendRequestMessage_{{ $applicant->id }}" class="text-red" style="display:none;">Email sent!</span>
                        <!--<button type="button"
                                class="btn btn-primary open-sms-modal-btn"
                                data-applicantid="{{ $applicant->id }}"
                                data-mobile="{{ $applicant->mobileNumberMain }}"
                                title="Send Registration SMS">
                          <i class="fa fa-comment"></i>
                        </button>
                        <span id="sendSmsMessage_{{ $applicant->id }}" class="text-green" style="display:none;">SMS sent!</span>-->
                        <button type="button" class="btn btn-info view-link-btn"
                                data-applicantid="{{ $applicant->id }}" 
                                data-link="{{ env('APP_URL') }}register/{{ $applicant->accessUrlCode }}"
                                title="View Registration Link">
                          <i class="fa fa-eye"></i>
                        </button>
                        <a href="{{ route('applications.pending.edit', $applicant->id) }}"
                          class="btn btn-warning"
                          title="Edit Applicant">
                            <i class="fa fa-edit"></i>
                        </a>
                      </td>
                    </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
            @else
              There are no applicants pending registration.
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
  $('#deleteModal').on('show.bs.modal', function(e) {
    var applicantID = e.relatedTarget.dataset.applicantid;
    $('#deleteModal').attr("applicant-id", applicantID);
  });

  $(document).on("click", "#confirmDelete", function() {
    var applicantID = $("#deleteModal").attr("applicant-id");
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
            $('#deleteModal').modal('toggle');
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

  $(document).ready(function() {
    $('[data-toggle="tooltip"]').tooltip();
  });

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

  /* Open SMS confirm modal
  $(document).on("click", ".open-sms-modal-btn", function () {
    var applicantID = $(this).data("applicantid");
    var mobile = $(this).data("mobile");

    $("#sendSmsModal").attr("applicant-id", applicantID);
    $("#smsPreviewNumber").text(mobile ? ("Number: " + mobile) : "No mobile number on file.");
    $("#sendSmsModal").modal("show");
  });

  // Confirm SMS send
  $(document).on("click", "#confirmSendSms", function () {
    var applicantID = $("#sendSmsModal").attr("applicant-id");
    sendRegistrationSms(applicantID);
  });

  function sendRegistrationSms(applicantID) {
    $.ajax({
      url: "{{ env('APP_URL') }}applications/sendRegistrationSms",
      method: "POST",
      data: {
        "_token": "{{ csrf_token() }}",
        "applicantID": applicantID
      },
      success: function (response) {
        try {
          const res = typeof response === "string" ? JSON.parse(response) : response;

          if (res.success) {
            $("#sendSmsMessage_" + applicantID).text("SMS sent!").show();
            $("#sendSmsModal").modal("hide");
          } else {
            alert(res.message || "Failed to send SMS.");
          }
        } catch (e) {
          console.error("Invalid response from server", response);
          alert("Unexpected server response.");
        }
      },
      error: function (xhr) {
        let msg = "Failed to send SMS.";
        if (xhr.responseJSON && xhr.responseJSON.message) {
          msg = xhr.responseJSON.message;
        }
        alert(msg);
      }
    });
  }*/
</script>
@endsection