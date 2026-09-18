<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Candidate Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">    
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

    @php
        $brand = $brandColor ?? '#C55359';
    @endphp

    <style>
        :root{
            --brand: {{ $brand }};
            --brand-contrast: #fff;
            --ink: #2C3C64;
        }
        body{ background:#fff; color:#333; }
        .brand-bar{ position:fixed; top:0; left:0; right:0; height:10px; background:var(--brand); z-index:10; }

        .wrap{ padding-top:40px; padding-bottom:40px; }
        .card{
            background:#f8f9fa; border:1px solid #e3e3e3; border-radius:6px; padding:25px; margin-bottom:20px;
        }
        .muted{ color:#6b7480; }
        .btn-brand{ background:var(--brand); color:var(--brand-contrast) !important; border-color:var(--brand); }
        .btn-brand:hover, .btn-brand:focus{ opacity:.9; color:#fff; }

        /* Header row */
        .dash-header{ display:flex; align-items:center; justify-content:space-between; gap:15px; margin-bottom:20px; }
        .org-left{ display:flex; align-items:center; gap:15px; }
        .org-logo img{ max-height:48px; max-width:220px; }
        .org-name{ font-weight:700; color:var(--ink); }

        /* Status chip */
        .status-chip{
            display:inline-block; padding:6px 12px; border-radius:20px; font-weight:600; border:1px solid #ddd; background:#fff;
        }
        .status-chip.review{ border-color:#ffd699; background:#fff7e6; color:#8a5a00; }
        .status-chip.done{ border-color:#b7eb8f; background:#f6ffed; color:#135200; }
        .status-chip.info{ border-color:#91d5ff; background:#e6f7ff; color:#003a8c; }

        /* Animated check */
        .check-wrap{
            width:140px; height:140px; border-radius:50%; background: #e6f7ee; display:flex; align-items:center; justify-content:center;
            margin:10px auto 20px; border:3px solid #c7f5d6; position:relative;
        }
        .check{
            width:64px; height:32px; border-left:6px solid #28a745; border-bottom:6px solid #28a745;
            transform: rotate(-45deg); transform-origin:center;
            animation: draw .7s ease-out forwards;
            opacity:0;
        }
        @keyframes draw{
            0%{ opacity:0; clip-path: inset(0 100% 0 0); }
            10%{ opacity:1; }
            100%{ opacity:1; clip-path: inset(0 0 0 0); }
        }

        /* Footer actions row */
        .actions{ display:flex; flex-wrap:wrap; gap:10px; justify-content: center; }
        .actions .btn{ min-width:150px; }

        @media (max-width:768px){
            .dash-header{ flex-direction:column; align-items:flex-start; }
            .org-left{ width:100%; justify-content:space-between; }
        }
    </style>
</head>
<body class="is-portal">
    <div class="brand-bar"></div>

    <div class="container wrap">
        {{-- COMPLETE: admin finished --}}
        @if($status === 'complete')
            <div class="text-center">
                <div class="check-wrap">
                    <div class="check"></div>
                </div>
                <h2 style="color:var(--ink); font-weight:700;">Screening complete</h2>
                <p class="lead muted" style="max-width:720px; margin:10px auto 20px;">
                    Your screening process is complete. Your employer has been sent the final report.
                    There’s nothing else you need to do.
                </p>

                <div class="card" style="display:inline-block; text-align:left; max-width:680px;">
                    <span class="status-chip done"><i class="fa fa-check"></i> Completed</span>
                    <div style="height:10px"></div>
                    <ul class="list-unstyled" style="margin-bottom:0">
                        <li><i class="fa fa-check text-success"></i> Application submitted</li>
                        <li><i class="fa fa-check text-success"></i> Verification checks performed</li>
                        <li><i class="fa fa-check text-success"></i> Report delivered to employer</li>
                    </ul>
                </div>

                <div style="height:15px"></div>
                <div class="actions">
                    <a class="btn btn-default" href="{{ route('candidate.help') }}">
                        <i class="fa fa-life-ring"></i> Help &amp; Support
                    </a>
                    <a class="btn btn-danger" href="{{ url('/logout') }}">
                        <i class="fa fa-power-off"></i> Logout
                    </a>
                </div>
            </div>
        @elseif($status === 'under_review')
            {{-- UNDER REVIEW: submitted but not yet admin-complete --}}
            <div class="dash-header">
                <div class="org-left">
                    <div class="org-logo">
                        @if (!empty($userDetails->logo))
                            <img src="{{ url('/admin/logo/' . $userDetails->logo) }}" alt="Company Logo">
                        @else
                            <img src="{{ asset('images/logo_' . str_replace(' ', '', strtolower(config('app.name'))) . '.webp') }}" alt="Get ClassifIeD Logo">
                        @endif
                    </div>
                    <div class="org-name">{{ $userDetails->organisationName ?? 'Candidate Portal' }}</div>
                </div>
                <div class="actions">
                    <a class="btn btn-default" href="{{ route('candidate.help') }}"><i class="fa fa-life-ring"></i> Help &amp; Support</a>
                    <a class="btn btn-danger" href="{{ url('/logout') }}"><i class="fa fa-power-off"></i> Logout</a>
                </div>
            </div>

            <div class="card">
                <h3 style="color:var(--ink); margin-top:0;">Application status</h3>
                <p class="muted">Thanks — we’ve received your application. Our team is reviewing it now.</p>
                <div style="margin-bottom:10px;">
                    <span class="status-chip review"><i class="fa fa-hourglass-half"></i> Under review</span>
                </div>

                <div class="row">
                    <div class="col-sm-6">
                        <ul class="list-unstyled">
                            <li><i class="fa fa-check text-success"></i> Submitted by you</li>
                            <li><i class="fa fa-refresh fa-spin"></i> Verification checks in progress</li>
                            <li><i class="fa fa-clock-o"></i> Awaiting admin completion</li>
                        </ul>
                    </div>
                    <div class="col-sm-6">
                        {{-- Optional: surface high-level check flags if you want --}}
                        <ul class="list-unstyled muted">
                            <li>Personal details: <strong>{{ (int)($RequiredChecks->aboutYou ?? 0) === 2 ? 'Submitted' : 'Incomplete' }}</strong></li>
                            @if(($RequiredChecks->basicDBS ?? 0) > 0)
                                <li>Criminal record check: <strong>{{ (int)($RequiredChecks->basicDBS ?? 1) === 2 ? 'Submitted' : 'Incomplete' }}</strong></li>
                            @endif
                            @if(($RequiredChecks->YotiVerifcation ?? 0) > 0)
                                <li>Digital identity: <strong>{{ (int)($RequiredChecks->YotiVerifcation ?? 1) === 2 ? 'Submitted' : 'Incomplete' }}</strong></li>
                            @endif
                            @if(($RequiredChecks->employmentRef ?? 0) > 0)
                                <li>Employment history: <strong>{{ (int)($RequiredChecks->employmentRef ?? 1) === 2 ? 'Submitted' : 'Incomplete' }}</strong></li>
                            @endif
                            @if(($RequiredChecks->academicRef ?? 0) > 0)
                                <li>Academic history: <strong>{{ (int)($RequiredChecks->academicRef ?? 1) === 2 ? 'Submitted' : 'Incomplete' }}</strong></li>
                            @endif
                            @if(($RequiredChecks->personalRef ?? 0) > 0)
                                <li>Personal references: <strong>{{ (int)($RequiredChecks->personalRef ?? 1) === 2 ? 'Submitted' : 'Incomplete' }}</strong></li>
                            @endif
                            <li>Supporting documents: <strong>{{ (int)($RequiredChecks->supportingDocs ?? 1) === 2 ? 'Submitted' : 'Incomplete' }}</strong></li>
                        </ul>
                    </div>
                </div>

                <div class="alert alert-info" style="margin-top:15px;">
                    <i class="fa fa-info-circle"></i>
                    Need to correct something? You can reopen your application and update your answers.
                    You will need to submit the application again when you have finished editing.
                </div>

                <div class="actions" style="margin-top:20px;">
                    <form method="POST" action="{{ route('candidate.application.edit') }}" style="display:inline-block; margin:0;">
                        @csrf
                        <button type="submit" class="btn btn-brand">
                            <i class="fa fa-pencil"></i> Edit application
                        </button>
                    </form>
                    <a class="btn btn-default" href="{{ route('candidate.help') }}">
                        <i class="fa fa-life-ring"></i> Help &amp; Support
                    </a>
                </div>
            </div>
        @else
            {{-- NOT SUBMITTED (guard) --}}
            <div class="text-center">
                <h3 style="color:var(--ink); font-weight:700;">Finish your application</h3>
                <p class="muted">It looks like you haven’t submitted yet.</p>
                <a class="btn btn-brand" href="{{ route('candidate.welcome') }}"><i class="fa fa-arrow-right"></i> Go to application</a>
                <div style="height:10px"></div>
                <a class="btn btn-default" href="{{ route('candidate.help') }}"><i class="fa fa-life-ring"></i> Help &amp; Support</a>
                <a class="btn btn-danger" href="{{ url('/logout') }}"><i class="fa fa-power-off"></i> Logout</a>
            </div>
        @endif
    </div>

    {{-- Scripts --}}
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>

    <div class="container" style="height: 60px;"><p>&nbsp;</p></div>
        <footer> @include('layout.footer') </footer>
    </div>
    @include('layout.accessibility')
</body>
</html>