@extends('layout.admin')

@section('title', 'Manage Organisations')

@section('content')

<div class="container-fluid">
  <section class="content">
  
  <div class="row">
    <div class="col-md-12">
      <div class="box">
        <div class="box-header">
          <h3 class="box-title">DBS Submission Report</h3>
        </div><!-- /.box-header -->
        <div class="box-body">

          <div class="row">
            <div class="col-md-8">
              <!-- form start -->
              <form id="dbsReport" method="POST" action="{{ env('APP_URL') }}reports/generateDbsSubmissionReport">
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


          @if(isset($organisationsReport) && count($organisationsReport)>0)
            <table id="dbsReportTable" class="table table-bordered table-striped">
              <thead>
                <tr>
                  <th>Organisation</th>
                  <th>Number Of Applications</th>
                  <th>Total cost for the selected period</th>
                </tr>
              </thead>
              <tbody>
                @foreach ($organisationsReport as $organisationData)
                <tr>
                  <td>{{$organisationData['organisationName']}}</td>
                  <td>{{$organisationData['numberOfApplications']}}</td>
                  <td>&pound; {{$organisationData['totalCost']}}</td>
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



<!--
  $(function () {
    $('#dbsReportTable').dataTable({
      "bPaginate": true,
      
      "bFilter": false,
      "bSort": true,
      "bInfo": true,
      "bAutoWidth": false,
      dom: 'Bfrtip',
      "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
      buttons: [
            'print', 'csv'
        ]
    });
  });
-->
  

</script>
@endsection
