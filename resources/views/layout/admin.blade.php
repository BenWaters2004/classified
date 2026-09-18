<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
  <head>
    <meta charset="UTF-8">
    <title>@yield('title')</title>
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="manifest" href="/site.webmanifest">
    
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
    <!-- Bootstrap 3.3.2 -->
    <link href="{{ env('APP_URL') }}bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />    
    <!-- FontAwesome 7.3.1 -->
    <link rel="stylesheet" href="{{ asset('fontawesome/css/fontawesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('fontawesome/css/solid.min.css') }}">
    <link rel="stylesheet" href="{{ asset('fontawesome/css/regular.min.css') }}">
    <link rel="stylesheet" href="{{ asset('fontawesome/css/brands.min.css') }}">

    <!-- Ionicons 2.0.0 -->
    <link href="https://code.ionicframework.com/ionicons/2.0.0/css/ionicons.min.css" rel="stylesheet" type="text/css" />    
    <!-- Theme style -->
    <link href="{{ env('APP_URL') }}dist/css/AdminLTE.css" rel="stylesheet" type="text/css" />
    <!-- AdminLTE Skins. Choose a skin from the css/skins 
         folder instead of downloading all of them to reduce the load. -->
    <link href="{{ env('APP_URL') }}dist/css/skins/_all-skins.min.css" rel="stylesheet" type="text/css" />
    <!-- iCheck -->
    <link href="{{ env('APP_URL') }}plugins/iCheck/flat/blue.css" rel="stylesheet" type="text/css" />
    <!-- Morris chart -->
    <link href="{{ env('APP_URL') }}plugins/morris/morris.css" rel="stylesheet" type="text/css" />
    <!-- jvectormap -->
    <link href="{{ env('APP_URL') }}plugins/jvectormap/jquery-jvectormap-1.2.2.css" rel="stylesheet" type="text/css" />
    <!-- Date Picker -->
    <link href="{{ env('APP_URL') }}plugins/datepicker/datepicker3.css" rel="stylesheet" type="text/css" />
    <!-- Daterange picker -->
    <link href="{{ env('APP_URL') }}plugins/daterangepicker/daterangepicker-bs3.css" rel="stylesheet" type="text/css" />
    <!-- bootstrap wysihtml5 - text editor -->
    <link href="{{ env('APP_URL') }}plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css" rel="stylesheet" type="text/css" />

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->

    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-534VMZSN5B"></script>
    <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());

    gtag('config', 'G-534VMZSN5B');
    </script>
    <style type="text/css">
    .skin-blue .main-header .logo {
        background-color: #fff;
        color: #7FA2CE;
        border-bottom: 0 solid transparent;
    }
    </style>
    @yield('pageCSS', '')
  </head>
  <body class="skin-blue is-portal">
    <div class="wrapper">
      
      <header class="main-header"> @include('layout.adminheader') </header>
      <!-- Left side column. contains the logo and sidebar -->
      <aside class="main-sidebar"> @include('layout.adminsidebar') </aside>
      

      <!-- Right side column. Contains the navbar and content of the page -->
      <div class="content-wrapper"> 
        @if(Session::has('error_handler'))
                <div class="alert alert-danger" alert-dismissible fade show" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                    {{ Session::get('error_handler') }}
                </div>
            @endif
        @yield('content') 
    </div>

      <footer class="main-footer"> @include('layout.adminfooter') </footer>
    </div><!-- ./wrapper -->

    <!-- jQuery 2.1.3 -->
    <script src="{{ env('APP_URL') }}plugins/jQuery/jQuery-2.1.3.min.js" type="text/javascript"></script>
    <!-- jQuery UI 1.11.2 -->
    <script src="https://code.jquery.com/ui/1.11.2/jquery-ui.min.js" type="text/javascript" integrity="sha384-IvbGQHn8kzhI5WJ08ahOTFvlAv0Tm7ELkg+2XkIaylUDhvHKQ7y55N7B0ruleN86"></script>
    <!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
    <script>
      $.widget.bridge('uibutton', $.ui.button);
    </script>
    <!-- Bootstrap 3.3.2 JS -->
    <script src="{{ env('APP_URL') }}bootstrap/js/bootstrap.min.js" type="text/javascript"></script>    
    <!-- Morris.js charts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/raphael/2.1.0/raphael-min.js" type="text/javascript" integrity="sha384-8XEfPUNnHyHtKtNgJfkQXsUtG2EfGr/HG4+bFNBMuBn/Cyr+oNDNEWM2m62MS2Ma"></script>
    <script src="{{ env('APP_URL') }}plugins/morris/morris.min.js" type="text/javascript"></script>
    <!-- Sparkline -->
    <script src="{{ env('APP_URL') }}plugins/sparkline/jquery.sparkline.min.js" type="text/javascript"></script>
    <!-- jvectormap -->
    <script src="{{ env('APP_URL') }}plugins/jvectormap/jquery-jvectormap-1.2.2.min.js" type="text/javascript"></script>
    <script src="{{ env('APP_URL') }}plugins/jvectormap/jquery-jvectormap-world-mill-en.js" type="text/javascript"></script>
    <!-- jQuery Knob Chart -->
    <script src="{{ env('APP_URL') }}plugins/knob/jquery.knob.js" type="text/javascript"></script>
    <!-- daterangepicker -->
    <script src="{{ env('APP_URL') }}plugins/daterangepicker/daterangepicker.js" type="text/javascript"></script>
    <!-- datepicker -->
    <script src="{{ env('APP_URL') }}plugins/datepicker/bootstrap-datepicker.js" type="text/javascript"></script>
    <!-- Bootstrap WYSIHTML5 -->
    <script src="{{ env('APP_URL') }}plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js" type="text/javascript"></script>
    <!-- iCheck -->
    <script src="{{ env('APP_URL') }}plugins/iCheck/icheck.min.js" type="text/javascript"></script>
    <!-- Slimscroll -->
    <script src="{{ env('APP_URL') }}plugins/slimScroll/jquery.slimscroll.min.js" type="text/javascript"></script>
    <!-- FastClick -->
    <script src="{{ env('APP_URL') }}plugins/fastclick/fastclick.min.js"></script>
    <!-- AdminLTE App -->
    <script src="{{ env('APP_URL') }}dist/js/app.min.js" type="text/javascript"></script>

    @yield('pageJavascript')
    @include('layout.accessibility')
    <script src="https://classified.statuspage.io/embed/script.js"></script>
  </body>
</html>