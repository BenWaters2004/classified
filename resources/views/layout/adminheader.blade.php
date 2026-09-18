<!-- Logo -->

<style>
  @media (max-width: 767px) {
    .logo {
      display: none !important;
    }
  }
  @media (max-width: 922px) {
    .titleRemoval {
      display: none !important;
    }
  }
  @media (max-width: 397px) {
    .titleRemoval2 {
      display: none !important;
    }
  }
  @media (max-width: 700px) {
      .invoice {
          margin: 10px 0px;
      }
  }
</style>

@inject('notifications', 'App\Http\Controllers\Notifications')

@php  
$userNotifications = $notifications->getHeaderNotifications();
@endphp
        <a href="{{ env('APP_URL') }}login" class="logo">
          @if (env('APP_ENV') == 'production')
            <img class="img-responsive logo" src="{{ env('APP_URL') }}images/logo_{{ str_replace(' ', '', strtolower(env('APP_NAME'))) }}.webp"/>
          @else
            <img class="img-responsive logo" src="{{ env('APP_URL') }}images/logo_demo.png"/>
          @endif
        </a>
        <!-- Header Navbar: style can be found in header.less -->
        <nav class="navbar navbar-static-top" role="navigation">
          <!-- Sidebar toggle button-->
          <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
            <span class="sr-only">Toggle navigation</span>
            <i class="fa-solid fa-bars"></i>
          </a>
          <div style="float: left; margin: 5px 0 0 20px;"><span class="titleRemoval2" style="font-size: 28px; color: #FFF; font-weight: bold;">Get ClassifIeD <span class="titleRemoval">Admin Dashboard</span></span></div>
          <div class="navbar-custom-menu">
            <ul class="nav navbar-nav">
              @if(isset($userNotifications) && count($userNotifications)>0)
              <li class="dropdown notifications-menu">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                  <i class="fa-regular fa-bell"></i> Notifications
                  <span class="label label-warning">{{count($userNotifications)}}</span>
                </a>

                <ul class="dropdown-menu">
                  <li class="header">You have {{count($userNotifications)}} notification(s)</li>
                  <li>
                    <ul class="menu">
                      @foreach ($userNotifications as $notification)
                      <li>
                        <a href="{{ env('APP_URL') }}notifications/viewNotification/{{$notification->id}}">
                            @if(isset($notification->category))
                              @if(empty($notification->category)) 
                               <i class="fa fa-users text-aqua"></i>
                              @elseif($notification->category == 1) 
                               <i class="fa fa-warning text-yellow"></i>
                              @elseif($notification->category == 2) 
                               <i class="fa fa-users text-red"></i>
                              @endif
                            @endif
                           @if(isset($notification->title) && strlen($notification->title) > 0) {{$notification->title}} @endif
                        </a>
                      </li>
                      @endforeach
                    </ul>
                  </li>
                  <li class="footer"><a href="{{ env('APP_URL') }}notifications/">View all</a></li>
                </ul>
              </li>
              @else
              <li class="dropdown notifications-menu">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                  <i class="fa fa-bell-o"></i> Notifications
                  <span class="label label-success">0</span>
                </a>
              </li>
              @endif

              <!-- User Account: style can be found in dropdown.less -->
              <li class="dropdown user user-menu">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                  <span class="hidden-xs"><i class="fa-regular fa-user"></i> {{ \Auth::user()->firstName }} {{\Auth::user()->lastName}}</span>
                </a>
                <ul class="dropdown-menu">
                  <!-- Menu Footer-->
                  <li class="user-footer">
                    <div class="pull-left">
                      <a href="{{ env('APP_URL') }}users/viewProfile/" class="btn btn-default btn-flat">My Account</a>
                    </div>
                    <div class="pull-right">
                      <a href="{{ env('APP_URL') }}logout" class="btn btn-default btn-flat">Sign out</a>
                    </div>
                  </li>
                </ul>
              </li>
            </ul>
          </div>
        </nav>
