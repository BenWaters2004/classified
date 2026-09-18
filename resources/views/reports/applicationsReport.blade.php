@extends('layout.admin')

@section('title', 'Applications Report')

@section('content')

<?php //echo'<pre>';print_r($allusers);echo'</pre>'; ?>

<div class="container-fluid">
  <section class="content">
  
  <div class="row">
    <div class="col-md-12">
      <div class="box">
        <div class="box-header">
          <h3 class="box-title">Applications Report</h3>
        </div><!-- /.box-header -->
        <div class="box-body">

          <div class="row">
            <div class="col-md-8">
              <!-- form start -->
              <form id="applicationsReport" method="POST" action="{{ env('APP_URL') }}reports/generateApplicationsReport">
                {{ csrf_field() }}


                <div class="row">
                  <div class="col-md-4">
                    <div class="form-group">
                      <label>Date From:</label>
                      <div class="input-group">
                        <div class="input-group-addon">
                          <i class="fa fa-calendar"></i>
                        </div>
                        <input type="text" class="form-control pull-right" id="reportDateRangeFrom" name="reportDateRangeFrom" @if(isset($reportDateRangeFrom) && !empty($reportDateRangeFrom)) value="{{date('d/m/Y', strtotime($reportDateRangeFrom))}}" @endif>
                      </div>
                    </div>
                  </div>

                  <div class="col-md-4">
                    <div class="form-group">
                      <label>Date To:</label>
                      <div class="input-group">
                        <div class="input-group-addon">
                          <i class="fa fa-calendar"></i>
                        </div>
                        <input type="text" class="form-control pull-right" id="reportDateRangeTo" name="reportDateRangeTo" @if(isset($reportDateRangeTo) && !empty($reportDateRangeTo)) value="{{date('d/m/Y', strtotime($reportDateRangeTo))}}" @endif>
                      </div>
                    </div>
                  </div>

                  <div class="col-md-4">
                    <div class="form-group">
                      <label>Organisation:</label>
                      <div class="input-group">
                        <div class="input-group-addon">
                          <i class="fa fa-folder"></i>
                        </div>
                        <select  id="organisationID" name="organisationID" class="form-control">
                          <option value="0">All</option>
                          @foreach ($availableOrganisations as $organisation)
                            <option value="{{ $organisation->id }}" @if (isset($organisationID) && !empty($organisationID) && $organisationID == $organisation->id) selected="selected" @endif>{{ $organisation->organisationName }}</option>
                          @endforeach
                        </select>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="row">
                  <div class="col-md-12">
                    <div class="form-group">
                        <button type="submit" class="forceBgClassified btn btn-lg pull-right" style="margin-bottom: 10px; margin-top:10px;">Generate Report</button>
                    </div>
                  </div>
                </div>

              </form>
            </div>
          </div>


          @if(isset($applications) && count($applications)>0)
            <table id="searchApplications" class="table table-bordered table-striped">
              <thead>
                <tr>
                  <th>Applicant Name</th>
                  <th>Application Started</th>
                  <th>Organisation</th>
                  <th>Admin Responsible</th>
                </tr>
              </thead>
              <tbody>
                @foreach ($applications as $application)
                <tr id="user_block_{{ $application->id }}">
                  <td>
                    {{$application->presentSurname}}, {{$application->forename}} {{$application->middlename}}
                  </td>
                  <td>{{date('d/m/Y @ H:i:s', strtotime($application->createdOn))}}</td>
                  <td>{{$application->organisationName}}</td>
                  <td>
                    @if(isset($application->createdByAdmin->lastName) && !empty($application->createdByAdmin->lastName)){{$application->createdByAdmin->lastName}} @endif
                    @if(isset($application->createdByAdmin->firstName) && !empty($application->createdByAdmin->firstName)){{$application->createdByAdmin->firstName}}@endif
                  </td>
                </tr>
                @endforeach
              </tbody>
            </table>
          @endif
        </div><!-- /.box-body -->
      </div><!-- /.box -->
    </div>


  </div>

  </section>
</div>

<div class="modal modal-default" id="deleteModal" selection-start=""  selection-end=""  selection-organisation="">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">×</span></button>
        <h4 class="modal-title">WARNING</h4>
      </div>
      <div class="modal-body">
        <p><span style="color:#FF0000;">Are you sure you want to purge all the selected users?<br />Have you downloaded and saved the CSV file first?</span><br /><br />
          <strong>Start Date:</strong> <span id="reportDateRangeFromDIV"></span><br /><strong>End Date:</strong> <span id="reportDateRangeToDIV"></span><br /><strong>Organisation:</strong> <span id="organisationNameDIV"></span></p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-info pull-left" id="confirmDelete">YES</button>
        <button type="button" class="btn btn-info pull-left" data-dismiss="modal">No</button>
        
      </div>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>

@endsection


@section('pageCSS')
  <link href="{{ env('APP_URL') }}plugins/datatables/dataTables.bootstrap.css" rel="stylesheet" type="text/css" />
  <link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.5.2/css/buttons.dataTables.min.css">
@endsection

@section('pageJavascript')
<script type="text/javascript">

  $('#reportDateRangeFrom, #reportDateRangeTo').datepicker({
      autoclose: true,
      format: "dd/mm/yyyy",

  });

  $('#deleteModal').on('show.bs.modal', function(e) {
    var reportDateRangeFrom = $("#reportDateRangeFrom").val();
    $('#deleteModal').attr("selection-start", reportDateRangeFrom);
    $('#reportDateRangeFromDIV').text(reportDateRangeFrom);

    var reportDateRangeTo   = $("#reportDateRangeTo").val();
    $('#deleteModal').attr("selection-end", reportDateRangeTo);
    $('#reportDateRangeToDIV').text(reportDateRangeTo);

    var organisationID      = $("#organisationID option:selected").val();
    $('#deleteModal').attr("selection-organisation", organisationID);

    var organisationName      = $("#organisationID option:selected").text();
    $('#organisationNameDIV').text(organisationName);
    
  });

  $(document).on("click", "#confirmDelete", function(e) {
    e.preventDefault();
    jQuery.noConflict();

    var reportDateRangeFrom = $("#deleteModal").attr("selection-start");
    var reportDateRangeTo = $("#deleteModal").attr("selection-end");
    var organisationID = $("#deleteModal").attr("selection-organisation");
    markUsersToDelete(reportDateRangeFrom, reportDateRangeTo, organisationID);
  });

  function markUsersToDelete(reportDateRangeFrom, reportDateRangeTo, organisationID){
    $.ajax({
        url: "<?php echo env('APP_URL'); ?>" + "users/markUsersToDelete", 
        method: "POST",
        data: {"_token":"{{ csrf_token() }}", "reportDateRangeFrom":reportDateRangeFrom, "reportDateRangeTo":reportDateRangeTo, "organisationID":organisationID},
        success: function(result){
          if (result == 1){
            //
            $('#deleteModal').modal('toggle');
          }
        },
        error: function(result){
          $('#deleteModal').modal('toggle');
        },
      });
  }

</script>
<script src="{{ env('APP_URL') }}plugins/datatables/jquery.dataTables.js" type="text/javascript"></script>
<script src="{{ env('APP_URL') }}plugins/datatables/dataTables.bootstrap.js" type="text/javascript"></script>

<script type="text/javascript" src="https://code.jquery.com/jquery-3.3.1.js"></script> 
<script type="text/javascript" src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.2/js/dataTables.buttons.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.flash.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.html5.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.print.min.js"></script>
<script type="text/javascript">




  $(function () {
    $('#searchApplications').dataTable({
      "bPaginate": true,
      
      "bFilter": true,
      "bSort": true,
      "bInfo": true,
      "bAutoWidth": false,
      dom: 'Bfrtip',
      "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
      buttons: [
            'print', 'csv'
        ]
    });
    $('.dt-buttons').append('<button class="dt-button" tabindex="0" data-toggle="modal" data-target="#deleteModal" data-start="123" data-end="22" data-org="33" type="button"><span>Purge Selected Users</span></button>');
  });




  

</script>
@endsection
