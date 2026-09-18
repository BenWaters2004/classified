@extends('layout.admin')

@section('title', 'View Applicant Details')

@section('content')


<div class="container-fluid">
  <section class="content">
  <div class="row">
    <div class="col-md-6">
      <div class="box">
      <div class="box-header">
        <h3 class="box-title">View Applicant Details</h3>
      </div><!-- /.box-header -->
      <div class="box-body">
        <table class="table table-bordered">
          <tbody>
          <tr>
            <th>First Name</th>
            <td>
              @if (isset($applicantProfile->forename) && strlen($applicantProfile->forename)>0) {{ $applicantProfile->forename }} @else &nbsp; @endif
            </td>
          </tr>
          <tr>
            <th>Last Name</th>
            <td>
              @if (isset($applicantProfile->surname) && strlen($applicantProfile->surname)>0) {{ $applicantProfile->surname }} @else &nbsp; @endif
            </td>
          </tr>
          <tr>
            <th>Organisation</th>
            <td>{{ $applicantProfile->organisationName }}</td>
          </tr>
          <tr>
            <th>Email</th>
            <td>{{ $applicantProfile->email }}</td>
          </tr>
          @if ($applicantProfile->userStatus == 0)
          <tr>
            <th>Unique URL Code</th>
            <td>{{ $applicantProfile->accessUrlCode }}</td>
          </tr>
          @endif
          <tr>
            <th>Applicant Status</th>
            <td>
              @if ($applicantProfile->userStatus == 0)
                <small class="label label-primary"><i class="fa fa-clock-o"></i> Pending Registration</small>
              @elseif ($applicantProfile->userStatus == 1)
                <small class="label label-success">Active</small>
              @elseif ($applicantProfile->userStatus == 2)
                <small class="label label-warning">Suspended</small>
              @elseif ($applicantProfile->userStatus == 3)
                <small class="label label-danger">Deleted</small>
              @endif

              
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

@section('pageJavascript')
<script type="text/javascript">
 
</script>
@endsection
