@extends('layout.admin')

@section('title', 'Reference Logs')

@section('content')

<?php //echo'<pre>';print_r($allusers);echo'</pre>'; ?>

<div class="container-fluid">
  <section class="content">
  
  <div class="row">
    <div class="col-md-12">
      <div class="box">
        <div class="box-header">
          <h3 class="box-title">Reference Logs</h3>
        </div><!-- /.box-header -->
        <div class="box-body">
          @if(isset($referenceRecords) && count($referenceRecords)>0)
            <table id="referenceRecords" class="table table-bordered table-striped">
              <thead>
                <tr>
                  <th>Status</th>
                  <th>Date</th>
                  <th>Requested By</th>
                  <th>Referee Name</th>
                  <th>Referee Relationship</th>
                  <th>Length of Association</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                @foreach ($referenceRecords as $refRecord)
                <tr id="user_block_{{ $refRecord->id }}">
                  <td>
                    @php
                      switch($refRecord->log_referenceStatus){
                          case("1"):
                              echo 'Sent to Ref.';
                              break;
                          case("2"):
                              echo 'Ref Completed';
                              break;
                          case("3"):
                              echo 'Ref Cancelled';
                              break;
                          default:
                              echo 'N/A';
                      }
                      @endphp
                  </td>
                  <td>{{$refRecord->log_createdOn}}</td>
                  <td style="word-break:break-all;">{{$refRecord->requestedBy}}</td>
                  <td  style="word-break:break-all;">
                    @if (strlen($refRecord->referenceDetails_fullName) > 0)
                      {{$refRecord->referenceDetails_fullName}}
                    @else
                      {{$refRecord->referee_name}}
                    @endif
                  </td>
                  <td>{{$refRecord->reference_natureOfAq}}</td>
                  <td  style="word-break:break-all;">
                    @if (strlen($refRecord->reference_preriodKnownFrom) > 0 || strlen($refRecord->reference_periodKnownTo) > 0)
                      {{$refRecord->reference_preriodKnownFrom}} - {{$refRecord->reference_periodKnownTo}}
                    @else
                      {{$refRecord->referee_length_of_association}}
                    @endif
                  </td>
                  <td>
                    @if (isset($refRecord->referenceDetails_logID) && !empty($refRecord->referenceDetails_logID))
                      <a href="{{ env('APP_URL') }}applications/downloadReference/{{$refRecord->reference_form_id}}" target="_blank"><button type="button" class="btn btn-danger" title="Download PDF"><i class="fa fa-file-pdf-o"></i></button></a>
                    @endif
                  </td>
                </tr>
                @endforeach
              </tbody>
            </table>
          @else
            There are no started or completed applications in the database.
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
@endsection

@section('pageJavascript')
<script src="{{ env('APP_URL') }}plugins/datatables/jquery.dataTables.js" type="text/javascript"></script>
<script src="{{ env('APP_URL') }}plugins/datatables/dataTables.bootstrap.js" type="text/javascript"></script>
<script type="text/javascript">

  $(function () {
    $('#referenceRecords').dataTable({
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
