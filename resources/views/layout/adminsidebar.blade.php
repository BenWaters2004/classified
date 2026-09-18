<style>
  @media (max-width: 767px) {
    .sidebar {
      margin-top: -50px;
    }
  }
  @media (min-width: 767px) {
    .onlySmall {
      display: none;
    }
  }
  
</style>

<!-- sidebar: style can be found in sidebar.less -->
        <section class="sidebar">
          <!-- sidebar menu: : style can be found in sidebar.less -->
          <ul class="sidebar-menu">
            <li class="header" style="font-size: 18px; font-weight: bold;">MAIN NAVIGATION</li>
            <li class="onlySmall"><a href="{{ env('APP_URL') }}login"><i class="fa-regular fa-circle"></i> Dashboard</a></li>
            @inject('checkAccess', 'App\Http\Controllers\Controller')
            @if (\Auth::check()) 
       

            <li class="active treeview">
              <a href="#">
                <i class="fa-regular fa-circle text-info"></i> <span>Applications</span> <i class="fa fa-angle-left pull-right"></i>
              </a>
              <ul class="treeview-menu">
                <li><a href="{{ env('APP_URL') }}adminoperator/newApplicationRequest"><i class="fa-solid fa-plus"></i> New Applicant</a></li>
                <li><a href="{{ env('APP_URL') }}applications/allApplications"><i class="fa-regular fa-circle"></i> All Applications</a></li>
                <li><a href="{{ env('APP_URL') }}applications/pendingRequests"><i class="fa-regular fa-circle"></i> Pending Applications</a></li>
                <li><a href="{{ env('APP_URL') }}applications/inprogressApplications"><i class="fa-regular fa-circle"></i> Incomplete Applications</a></li>
                <li><a href="{{ env('APP_URL') }}applications/completedApplications"><i class="fa-regular fa-circle"></i> Completed Applications</a></li>
              </ul>
            </li>


              {{--<li class="active treeview">
                <a href="#">
                  <i class="fa fa-circle-o text-info"></i> <span>Reports</span> <i class="fa fa-angle-left pull-right"></i>
                </a>
                <ul class="treeview-menu">
                  <li><a href="{{ env('APP_URL') }}reports/applicationsReport"><i class="fa-regular fa-circle"></i> Applications Report</a></li>
                  <li><a href="{{ env('APP_URL') }}reports/trackerReport"><i class="fa-regular fa-circle"></i> Tracker Report</a></li>
                </ul>
              </li>--}}

              @if ($checkAccess->checkAccess('superuser'))
              <li class="active treeview">
                <a href="#">
                  <i class="fa-regular fa-circle text-info"></i> <span>Tools</span> <i class="fa fa-angle-left pull-right"></i>
                </a>
                <ul class="treeview-menu">
                  <li><a href="{{ env('APP_URL') }}superuser/candidate-messaging"><i class="fa-regular fa-circle"></i> Candidate Messaging</a></li>
                  <li><a href="{{ env('APP_URL') }}applications/getApplicationResultForm"><i class="fa-regular fa-circle"></i> Check DBS by REF</a></li>
                  <li><a href="{{ env('APP_URL') }}admin/blog/manage"><i class="fa-regular fa-circle"></i> Manage Blog</a></li>
                </ul>
              </li>
              @endif
            

              <li class="active treeview">
                <a href="#">
                  <i class="fa-regular fa-circle text-info"></i> <span>Settings</span> <i class="fa fa-angle-left pull-right"></i>
                </a>
                <ul class="treeview-menu">
                  <li><a href="{{ env('APP_URL') }}admin/settings/organisation"><i class="fa-regular fa-circle"></i> Organisation Settings</a></li>
                  @if ($checkAccess->checkAccess('superuser'))
                    {{--<li><a href="{{ env('APP_URL') }}admin/dbsReport"><i class="fa-regular fa-circle"></i> DBS Submission Report</a></li>--}}
                  @endif
                  <li><a href="{{ env('APP_URL') }}help/helpCentre"><i class="fa-regular fa-circle"></i> Help Centre</a></li>
                </ul>
              </li>
            
            <li class="header">&nbsp;</li>

            <li><a href="{{ env('APP_URL') }}logout"><i class="fa-regular fa-circle text-danger"></i> Logout</a></li>
            @else
            <li><a href="{{ env('APP_URL') }}login"><i class="fa-regular fa-circle text-danger"></i> Login</a></li>
            @endif
          </ul>
        </section>
        <!-- /.sidebar -->
