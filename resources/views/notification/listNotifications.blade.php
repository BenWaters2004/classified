@extends('layout.admin')

@section('title', 'Notifications')

@section('content')

<div class="modal" id="deleteModal" notificationid="0">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">×</span></button>
        <h4 class="modal-title">WARNING</h4>
      </div>
      <div class="modal-body">
        <p>Are you sure you want to dismiss this notification?</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="forceBgClassified btn pull-left" id="confirmDelete">YES</button>
        <button type="button" class="btn btn-secondary pull-left" data-dismiss="modal">No</button>
        
      </div>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>

<div class="modal" id="deleteAllModal" organisation-id="0">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">×</span></button>
        <h4 class="modal-title">WARNING</h4>
      </div>
      <div class="modal-body">
        <p>Are you sure you want to dismiss ALL notification?</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary pull-left" id="confirmAllDelete">YES</button>
        <button type="button" class="btn btn-secondary pull-left" data-dismiss="modal">No</button>
        
      </div>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>

<div class="container-fluid">
  <section class="content">
  
  <div class="row">
    <div class="col-md-12">
      <div class="Newbox">
        <div class="box-header">
          <h3 class="box-title">Notifications</h3>
        </div><!-- /.box-header -->
        <div class="box-body">
          <div style="width: 100%; margin-bottom:10px; height:30px;"><button type="button" style="float:right;" class="btn btn-danger" data-toggle="modal" data-target="#deleteAllModal" title="Dismiss All"><i class="fa fa-times" ></i> Dismiss All</button></div>
          <div style="clear:both;"></div> 
          @if(isset($notifications) && count($notifications)>0)
            <table id="manageOrganisations" class="table table-bordered table-striped">
              <thead>
                <tr>
                  <th>Date</th>
                  <th>Related User</th>
                  <th>Title</th>
                  <th>Status</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                @foreach ($notifications as $notification)
                <tr id="notifications_block_{{ $notification->id }}">
                  <td>{{$notification->createdOn}}</td>
                  <td>{{$notification->relatedUserId}}</td>
                  <td>{{$notification->title}}</td>
                  <td>
                      @if ($notification->status == 0)
                        <small class="label label-primary"><i class="fa fa-clock-o"></i> Resolved</small>
                      @elseif ($notification->status == 1)
                        <small class="label label-success">Active</small>
                      @endif
                  </td>
                  <td>
                    <a href="{{ env('APP_URL') }}notifications/viewNotification/{{$notification->id}}"><button type="button" class="btn btn-info" title="View Notification"><i class="fa fa-search"></i> View</button></a>&nbsp;
                    <a href="{{ env('APP_URL') }}notifications/resolveNotification/{{$notification->id}}"><button type="button" class="btn btn-success" title="Resolve"><i class="fa fa-check"></i> Resolve</button></a>&nbsp;
                    <button type="button" class="btn btn-warning" data-toggle="modal" data-target="#deleteModal" data-notificationid="{{ $notification->id }}" title="Dismiss"><i class="fa fa-times" ></i> Dismiss</button>
                  </td>
                </tr>
                @endforeach
              </tbody>
            </table>
          @else
            There are no active notifications.
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
    var notificationid = e.relatedTarget.dataset.notificationid;
    $('#deleteModal').attr("notificationid", notificationid);
  });
  $(document).on("click", "#confirmDelete", function() {
    var notificationId = $("#deleteModal").attr("notificationid");
    deleteNotification(notificationId);
  });

  $(document).on("click", "#confirmAllDelete", function() {
    deleteNotification(0);
  });

  function deleteNotification(notificationId){
    $.ajax({
        url: "<?php echo env('APP_URL'); ?>" + "notifications/dismissNotification", 
        method: "POST",
        data: {"_token":"{{ csrf_token() }}", "notificationId":notificationId},
        success: function(result){
          if (result == 1 && notificationId != 0){
            $("#notifications_block_"+notificationId).remove();
            $('#deleteModal').modal('toggle');
          } else if (result == 1 && notificationId == 0){
            $('[id^="notifications_block_"]').hide();
            $('#deleteAllModal').modal('toggle');
          }
        },
        error: function(result){alert('notok');
          $('#deleteModal').modal('toggle');
          $('#deleteAllModal').modal('toggle');
        },
      });
  }

  $(function () {
    $('#manageOrganisations').dataTable({
      "bPaginate": true,
      "bLengthChange": false,
      "bFilter": true,
      "bSort": true,
      "bInfo": true,
      "bAutoWidth": false
    });
  });


  

</script>
@endsection
