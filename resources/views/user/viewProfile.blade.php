@extends('layout.admin')

@section('title', 'View User Details')

@section('content')


<div class="container-fluid">
  <section class="content">
  <div class="row">
    <div class="col-md-6">
      <div class="Newbox">
      <div class="box-header">
        <h3 class="box-title">View User Details</h3>
      </div><!-- /.box-header -->
      <div class="box-body">
        <table class="table table-bordered">
          <tbody>
          <tr>
            <th>Title</th>
            <td>{{ $userDetails->title }}</td>
          </tr>
          <tr>
            <th>First Name</th>
            <td>{{ $userDetails->firstName }}</td>
          </tr>
          <tr>
            <th>Last Name</th>
            <td>{{ $userDetails->lastName }}</td>
          </tr>
          <tr>
            <th>Base Organisation</th>
            <td>{{ $userDetails->organisationName }}</td>
          </tr>
          <tr>
            <th>Email</th>
            <td>{{ $userDetails->email }}</td>
          </tr>
          @if($userDetails->userType == 'siteuser')
          <tr>
            <th>User Type</th>
            <td>
              @if (in_array('user', $userDetails->userRoles)) User<br />@endif
              @if (in_array('stakeholder', $userDetails->userRoles)) Stakeholder<br />@endif
              @if (in_array('manager', $userDetails->userRoles)) Manager<br />@endif
              @if (in_array('admin', $userDetails->userRoles)) Administrator<br />@endif
              @if (in_array('superadmin', $userDetails->userRoles)) Super Admin<br />@endif
              
            </td>
          </tr>
          @endif
          <tr>
            <td>
              @inject('checkAccess', 'App\Http\Controllers\Controller')
              @if ($checkAccess->checkAccess('superuser'))
                @if($userDetails->userType == 'admin')
                <a href="{{ env('APP_URL') }}admin/settings/organisation"><button type="button" class="btn forceBgClassified">List Users</button></a>&nbsp;&nbsp;&nbsp; 
                @elseif ($userDetails->userType == 'applicant')
                <a href="{{ env('APP_URL') }}applications/allApplications"><button type="button" class="btn forceBgClassified">List Applicants</button></a>&nbsp;&nbsp;&nbsp; 
                @endif
              @endif
              <a href="{{ env('APP_URL') }}users/editProfile/{{ $userDetails->id }}"><button type="button" class="btn btn-warning">Edit Details</button></a></td>
            <td>&nbsp;</td>
          </tr>
        </tbody>
      </table>

      </div><!-- /.box-body -->
    </div><!-- /.box -->
    </div>
  </div>
  </section>
</div>

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
<script type="text/javascript">
 
</script>
@endsection
