@extends('layout.admin')

@section('title', 'Manage Organisations')

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
        <p>Are you sure you want to remove this organisation?</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary pull-left" id="confirmDelete">YES</button>
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
      <div class="NewBox">
        <div class="box-header">
          <h3 class="box-title">Manage Organisations</h3>
        </div><!-- /.box-header -->
        <div class="box-body">
          @if(isset($organisations) && count($organisations)>0)
            <div class="table-responsive">
              <table id="manageOrganisations" class="table table-bordered table-striped">
                <thead>
                  <tr>
                    <th>Organisation Name</th>
                    <th>Designated Email(s)</th>
                    <th>Status</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach ($organisations as $organisation)
                  <tr id="organisation_block_{{ $organisation->id }}">
                    <td>{{$organisation->organisationName}}</td>
                    <td>{{$organisation->designatedEmailList}}</td>
                    <td>
                        @if ($organisation->organisationStatus == 0)
                          <small class="label label-primary"><i class="fa fa-clock-o"></i> Incomplete</small>
                        @elseif ($organisation->organisationStatus == 1)
                          <small class="label label-success">Active</small>
                        @elseif ($organisation->organisationStatus == 2)
                          <small class="label label-warning">Suspended</small>
                        @elseif ($organisation->organisationStatus == 3)
                          <small class="label label-danger">Deleted</small>
                        @endif
                    </td>
                    <td>
                      <a href="{{ env('APP_URL') }}admin/editOrganisation/{{$organisation->id}}"><button type="button" class="btn btn-warning" title="Edit Organisation"><i class="fa fa-edit"></i></button></a>&nbsp;
                      <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#deleteModal" data-organisationid="{{ $organisation->id }}" title="Remove Organisation"><i class="fa fa-times" ></i></button>
                    </td>
                  </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          @else
            There are no organisations in the database.
          @endif
        </div><!-- /.box-body -->
      </div><!-- /.box -->
    </div>



    <div class="col-md-5">
      <div class="NewBox">
        <div class="box-header">
          <h3 class="box-title">Add New Organisation</h3>
        </div><!-- /.box-header -->
        <div class="box-body">
          <form id="addOrganisation" method="POST" action="{{ env('APP_URL') }}admin/saveOrganisation">
            {{ csrf_field() }}

            <div class="form-group @if ($errors->any() && $errors->has('organisationName')) has-error @endif">
              <label for="organisationName">Organisation Name *</label>
              <input type="text" class="form-control" name="organisationName" id="organisationName" maxlength="30" value="{{ old('organisationName') }}" required>
              <p class="help-block"  id="error_organisationName" @if ($errors->any() && $errors->has('organisationName')) @else style="display:none;" @endif>Please enter the organisation name!</p>
            </div>

            <div class="form-group @if ($errors->any() && $errors->has('organisationEmail')) has-error @endif">
              <label for="organisationEmail">Email</label>
              <input type="email" class="form-control" name="organisationEmail" id="organisationEmail" value="{{ old('organisationEmail') }}" required>
              <p class="help-block"  id="error_organisationEmail" @if ($errors->any() && $errors->has('organisationEmail')) @else style="display:none;" @endif>Please enter a valid email address!</p>
              
            </div>

            <div class="form-group">
              <button type="submit" class="btn forceBgClassified">Save</button>
            </div>

          </form>

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
    @media (max-width: 768px) {
        .container-fluid {
            padding: 0;
        }
        .content {
            padding: 0;
        }
    }

    .NewBox {
      background: white;
      padding: 2rem;
      border-radius: 12px;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.05);
      margin-block: 2rem;
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
