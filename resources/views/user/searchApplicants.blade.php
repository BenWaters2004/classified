@extends('layout.admin')

@section('title', 'Manage Users')

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

  <div class="box">
    <div class="box-header">
      <h3 class="box-title">Manage Applicants</h3>
    </div><!-- /.box-header -->
    <div class="box-body">
      <a href="{{ env('APP_URL') }}applications/newApplicationRequest"><button type="button" class="btn btn-primary"><i class="fa fa-plus"></i> Send New Application Request</button></a>
      @if(isset($allusers) && count($allusers)>0)
      <table id="allusers" class="table table-bordered table-striped">
        <thead>
          <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Organisation</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($allusers as $user)
          <tr id="user_block_{{ $user->id }}">
            <td>{{$user->lastName}}, {{$user->title}} {{$user->firstName}}</td>
            <td>{{$user->email }}</td>
            <td>{{$user->organisationName }}</td>
            <td>
              <a href="{{ env('APP_URL') }}users/viewProfile/{{ $user->id }}"><button type="button" class="btn btn-success" title="View Applicant"><i class="fa fa-search"></i></button></a>
              <a href="{{ env('APP_URL') }}users/editProfile/{{ $user->id }}"><button type="button" class="btn btn-warning" title="Edit Applicant"><i class="fa fa-edit"></i></button></a>
              @if(\Auth::user()->id != $user->id)
              <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#deleteModal" data-userid="{{ $user->id }}" title="Remove Applicant"><i class="fa fa-times"></i></button>
              @endif
            </td>
          </tr>
          @endforeach
        </tbody>
        <tfoot>
          <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Organisation</th>
            <th>User Type</th>
            <th>Actions</th>
          </tr>
        </tfoot>
      </table>
      @else
      <br /><p>There are no applicants in the database</p>
      @endif
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
      "bAutoWidth": false
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

</script>
@endsection
