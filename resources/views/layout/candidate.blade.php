<!--layout-->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Candidate Portal</title>
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="manifest" href="/site.webmanifest">
    
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <!-- FontAwesome 7.3.1 -->
    <link rel="stylesheet" href="{{ asset('fontawesome/css/fontawesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('fontawesome/css/solid.min.css') }}">
    <link rel="stylesheet" href="{{ asset('fontawesome/css/regular.min.css') }}">
    <link rel="stylesheet" href="{{ asset('fontawesome/css/brands.min.css') }}">
    
    <!-- Ionicons 2.0.0 -->
    <link href="https://code.ionicframework.com/ionicons/2.0.0/css/ionicons.min.css" rel="stylesheet" type="text/css" /> 

    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-534VMZSN5B"></script>
    <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());

    gtag('config', 'G-534VMZSN5B');
    </script>
    {{-- Brand colour variable --}}
    @php
        // Fallback in case the controller didn't set it for some view
        $brandColor = isset($brandColor) ? $brandColor : '#C55359';
    @endphp 
    <style>
        :root{
            --brand: {{ $brandColor }};
            /* Slightly darker mix for hovers and scrollbar hover */
            /* color-mix has wide modern support; if not supported, hover just remains brand */
            --brand-dark: color-mix(in srgb, var(--brand), black 20%);
            --brand-contrast: #fff;
        }

        body { background-color: #fff; }

        #sidebar {
            background-color: #f1f1f1;
            padding: 15px;
            position: fixed;
            top: 10px;
            left: 0;
            width: 250px;
            overflow-y: auto;
            height: calc(100% - 60px);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            z-index: 10;
        }

        .form-Side { margin-left: 250px; }
        .nav-links { flex-grow: 1; }

        .card {
            background: #f8f9fa;
            border: 1px solid #ddd;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        h1 { font-size: 24px; font-weight: bold; }

        #toggleSidebar {
            position: absolute;
            top: -4px;
            right: 15px;
            z-index: 1000;
        }

        .nav-pills>li>a { color: #2C3C64; }

        .nav-pills>li.active>a,
        .nav-pills>li.active>a:focus,
        .nav-pills>li.active>a:hover {
            background-color: var(--brand) !important;
            color: #fff;
        }

        .forceBgClassified {
            background-color: #2c3c64 !important;
            border-color: #2c3c64 !important;
            color: white !important;
            transition: 0.3s;
        }
        .forceBgClassified:hover {
            background-color: #08457e !important;
            color: white;
        }

        #sidebar .logo-container {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 10px;
            margin-top: 10px;
        }
        .logo-container img { max-width: 100%; height: auto; }

        .bottom-links { margin-top: auto; padding-top: 15px; }
        .bottom-links a {
            display: block;
            padding: 10px;
            color: #2C3C64;
            text-decoration: none;
        }
        .bottom-links a:hover { text-decoration: underline; }

        @media (max-width: 768px) {
            #sidebar {
                position: absolute;
                width: 100%;
                top: 50px;
                left: 0;
                bottom: auto;
                display: none;
            }
            .form-Side { margin-left: 0px; }
        }

        .active i { color: white !important; }

        /* Selection + scrollbars themed to brand */
        ::-moz-selection { color: white; background: var(--brand); }
        ::selection { color: white; background: var(--brand); }

        html { scrollbar-color: var(--brand) #f0f0f0; }
        ::-webkit-scrollbar-track { background: #f0f0f0; }
        ::-webkit-scrollbar-thumb { background: var(--brand); }
        ::-webkit-scrollbar-thumb:hover { background: var(--brand-dark); }

        /* Handy utilities if you want to avoid inline styles later */
        .brand-text { color: var(--brand) !important; }
        .brand-bg { background-color: var(--brand) !important; color: var(--brand-contrast) !important; }
        .brand-border { border-color: var(--brand) !important; }
    </style>
</head>
<body class="is-portal">
    <!-- Top Bar -->
    <div class="top-bar" style="background-color: var(--brand); height: 10px; width: 100%; position: fixed; top: 0; z-index: 10;"></div>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav id="sidebar" class="col-sm-3">
                <div class="logo-container">
                    <a href="{{ url('/login') }}" class="logo" style="padding: 0;">
                        @if (!empty($userDetails->logo))
                            <img class="img-responsive logo" src="{{ url('/admin/logo/' . $userDetails->logo) }}" style="width: 200px;" alt="Company Logo"/>
                        @else
                            <img class="img-responsive logo" src="{{ asset('images/logo_' . str_replace(' ', '', strtolower(config('app.name'))) . '.webp') }}" style="width: 200px;" alt="Get ClassifIeD Logo"/>
                        @endif
                    </a>
                </div>
                
                <div style="background-color:rgb(221, 221, 221); height: 2px; margin-top: 15px; margin-bottom: 15px;"></div>

                <ul class="nav nav-pills nav-stacked nav-links">
                    <li class="{{ (isset($formTitle) && $formTitle === 'Welcome') ? 'active' : '' }}">
                        <a href="{{ route('candidate.welcome') }}">
                            <i class="fa-regular fa-circle-check" style="color: var(--brand);"></i> &nbsp;Welcome
                        </a>
                    </li>
                    <li class="{{ (isset($formTitle) && $formTitle === 'Personal Details') ? 'active' : '' }}">
                        <a href="{{ route('candidate.personal.edit') }}">
                            @if ($RequiredChecks->aboutYou > 1)<i class="fa-regular fa-circle-check" style="color: var(--brand);"></i> @else <i class="fa-regular fa-circle"></i> @endif &nbsp;Personal Details
                        </a>
                    </li>
                    @if ($RequiredChecks->basicDBS > 0)
                    <li class="{{ (isset($formTitle) && $formTitle === 'Criminal Record Check') ? 'active' : '' }}">
                        <a href="{{ route('candidate.dbsbasic.edit') }}">
                            @if ($RequiredChecks->basicDBS > 1)<i class="fa-regular fa-circle-check" style="color: var(--brand);"></i> @else <i class="fa-regular fa-circle"></i>  @endif &nbsp;Criminal Record Check
                        </a>
                    </li>
                    @endif
                    @if ($RequiredChecks->YotiVerifcation > 0)
                    <li class="{{ (isset($formTitle) && $formTitle === 'Digital Identity Verification') ? 'active' : '' }}">
                        <a href="{{ route('candidate.identity.edit') }}">
                            @if ($RequiredChecks->YotiVerifcation > 1)<i class="fa-regular fa-circle-check" style="color: var(--brand);"></i> @else <i class="fa-regular fa-circle"></i> @endif &nbsp;Digital Identity Verification
                        </a>
                    </li>
                    @endif
                    @if ($RequiredChecks->employmentRef > 0)
                    <li class="{{ (isset($formTitle) && $formTitle === 'Employment History') ? 'active' : '' }}">
                        <a href="{{ route('candidate.employment.edit') }}">
                            @if ($RequiredChecks->employmentRef > 1)<i class="fa-regular fa-circle-check" style="color: var(--brand);"></i> @else <i class="fa-regular fa-circle"></i> @endif &nbsp;Employment History
                        </a>
                    </li>
                    @endif
                    @if ($RequiredChecks->academicRef > 0)
                    <li class="{{ (isset($formTitle) && $formTitle === 'Academic History') ? 'active' : '' }}">
                        <a href="{{ route('candidate.academic.edit') }}">
                            @if ($RequiredChecks->academicRef > 1)<i class="fa-regular fa-circle-check" style="color: var(--brand);"></i> @else <i class="fa-regular fa-circle"></i> @endif &nbsp;Academic History
                        </a>
                    </li>
                    @endif
                    @if ($RequiredChecks->personalRef > 0)
                    <li class="{{ (isset($formTitle) && $formTitle === 'Personal References') ? 'active' : '' }}">
                        <a href="{{ route('candidate.references.personal.edit') }}">
                            @if ($RequiredChecks->personalRef > 1)<i class="fa-regular fa-circle-check" style="color: var(--brand);"></i> @else <i class="fa-regular fa-circle"></i> @endif &nbsp;Personal References
                        </a>
                    </li>
                    @endif
                    <li class="{{ (isset($formTitle) && $formTitle === 'Supporting Documents') ? 'active' : '' }}">
                        <a href="{{ route('candidate.supporting.edit') }}">
                            @if ($RequiredChecks->supportingDocs > 1)<i class="fa-regular fa-circle-check" style="color: var(--brand);"></i> @else <i class="fa-regular fa-circle"></i> @endif &nbsp;Supporting documents
                        </a>
                    </li>
                    <li class="{{ (isset($formTitle) && $formTitle === 'Save and Submit') ? 'active' : '' }}">
                        <a href="{{ route('candidate.submit.review') }}">
                            <i class="fa-regular fa-circle"></i> &nbsp;Save and Submit
                        </a>
                    </li>
                </ul>


                <div class="bottom-links">
                    <div style="background-color:rgb(221, 221, 221); height: 2px; margin-top: 15px; margin-bottom: 15px;"></div>
                    <a href="{{ route('candidate.help') }}"><i class="fa-regular fa-circle-question"></i>  Help & Support</a>
                    <a href="{{ url('/logout') }}"><i class="fa-solid fa-power-off text-danger"></i>  Logout</a>
                </div>
            </nav>
            
            <!-- Main Content -->
            <main class="col-sm-10 form-Side">
                <h1>{{ $formTitle ?? 'Application Form' }}</h1>
                <!-- Mobile Menu Button -->
                <button class="btn btn-primary visible-xs forceBgClassified" id="toggleSidebar">Menu</button>
                @if(session('success'))
                    <div class="alert alert-success" role="status">{{ session('success') }}</div>
                @endif
                @if(session('info'))
                    <div class="alert alert-info" role="status">{{ session('info') }}</div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger" role="alert">{{ session('error') }}</div>
                @endif
                @if(session('warnings'))
                    <div class="alert alert-warning" role="alert">
                        <ul style="margin-bottom:0;">
                            @foreach((array) session('warnings') as $warning)
                                <li>{{ $warning }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <div class="card">
                    @yield('form-content')
                </div>
            </main>
        </div>
    </div>
    <div class="container" style="height: 60px;"><p>&nbsp;</p></div>
        <footer> @include('layout.footer') </footer>
    </div>
    @include('layout.accessibility')
    <script>
        document.getElementById('toggleSidebar').addEventListener('click', function() {
            var sidebar = document.getElementById('sidebar');
            if (sidebar.style.display === 'none' || sidebar.style.display === '') {
                sidebar.style.display = 'block';
            } else {
                sidebar.style.display = 'none';
            }
        });
    </script>
    <script src="https://classified.statuspage.io/embed/script.js"></script>
</body>
</html>
