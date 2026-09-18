@extends('layout.admin')

@section('title', 'Notification details')

@section('content')

<div class="modal" id="deleteModal" organisation-id="0">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">×</span></button>
        <h4 class="modal-title">WARNING</h4>
      </div>
      <div class="modal-body">
        <p>Are you sure you want to remove this notification?</p>
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

<div class="container-fluid">
  <section class="content">
  
  <div class="row">
    <div class="col-md-7">
      <div class="Newbox">
        <div class="box-header">
          <h3 class="box-title">Notification details</h3>
        </div><!-- /.box-header -->
        <div class="box-body">
          <table class="table table-bordered">
            <tbody>
              @if(!empty($notificationDetails->category))
              <tr>
                <th>Category</th>
                <td>
                  @if(isset($notificationDetails->categoryName) && !empty($notificationDetails->categoryName)){{$notificationDetails->categoryName}}@endif
                </td>
              </tr>
              @endif
              <tr>
                <th>Title</th>
                <td>
                  {{$notificationDetails->title}}
                </td>
              </tr>
              <tr>
                <th>Details</th>
                <td>{!!$notificationDetails->body!!}</td>
              </tr>
              @if(!empty($notificationDetails->relatedUserId))
              <tr>
                <th>Related User</th>
                <td>
                  @if(isset($notificationDetails->relatedUserFullName) && !empty($notificationDetails->relatedUserFullName)){{$notificationDetails->relatedUserFullName}}@endif
                </td>
              </tr>
              @endif
              @if(!empty($notificationDetails->relatedAction))
              <tr>
                <th>Related Action</th>
                <td>
                  @if(isset($notificationDetails->relatedAction) && !empty($notificationDetails->relatedAction)){{$notificationDetails->relatedAction}}@endif
                </td>
              </tr>
              @endif
              <tr>
                <th>Created By</th>
                <td>
                  @if(isset($notificationDetails->createdByFullName) && !empty($notificationDetails->createdByFullName)){{$notificationDetails->createdByFullName}}@endif
                </td>
              </tr>
              <tr>
                <th>Created On</th>
                <td>{{date("d/m/Y H:i:s", strtotime($notificationDetails->createdOn))}}</td>
              </tr>
              <tr>
                <td colspane ="2">
                  <button type="button" class="btn btn-success" title="Resolve"><i class="fa fa-check"></i> Resolve</button>&nbsp;
                  <button type="button" class="btn btn-warning" data-toggle="modal" data-target="#deleteModal" data-notificationid="{{ $notificationDetails->id }}" title="Dismiss"><i class="fa fa-times" ></i> Dismiss</button>
                </td>
              </tr>

            </tbody>
          </table>
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
  </style>
@endsection

@section('pageJavascript')
<script src="{{ env('APP_URL') }}plugins/datatables/jquery.dataTables.js" type="text/javascript"></script>
<script src="{{ env('APP_URL') }}plugins/datatables/dataTables.bootstrap.js" type="text/javascript"></script>
<script type="text/javascript">

  $('#deleteModal').on('show.bs.modal', function(e) {
    var organisationID = e.relatedTarget.dataset.organisationid;
    $('#deleteModal').attr("organisation-id", organisationID);
  });
  $(document).on("click", "#confirmDelete", function() {
    var organisationID = $("#deleteModal").attr("organisation-id");
    deleteOrganisation(organisationID);
  });

  function deleteOrganisation(organisationID){
    $.ajax({
        url: "<?php echo env('APP_URL'); ?>" + "admin/deleteOrganisation", 
        method: "POST",
        data: {"_token":"{{ csrf_token() }}", "organisationID":organisationID},
        success: function(result){
          if (result == 1){
            $("#organisation_block_"+organisationID).remove();
            $('#deleteModal').modal('toggle');
          }
        },
        error: function(result){
          $('#deleteModal').modal('toggle');
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
