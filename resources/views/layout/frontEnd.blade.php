<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'ClassifIeD')</title>
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="manifest" href="/site.webmanifest">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <!-- FontAwesome 7.3.1 -->
    <link rel="stylesheet" href="{{ asset('fontawesome/css/fontawesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('fontawesome/css/solid.min.css') }}">
    <link rel="stylesheet" href="{{ asset('fontawesome/css/regular.min.css') }}">
    <link rel="stylesheet" href="{{ asset('fontawesome/css/brands.min.css') }}">

    <meta name="geo.placename" content="Plymouth, Devon, England" />
    <meta name="keywords" content="@yield('meta_keywords', 'DBS Checks for employees, pre employment screening, get employee background checks, DBS check service, get BPSS Clearance, employee identity verification')" />
    <meta name="description" content="@yield('meta_description', 'Get ClassifIeD provides DBS and other pre-employment screening for business. ClassifIeD is an application created by BlueScreen IT Ltd')"/>

    <meta name="twitter:card" content="summary">
    <meta name="twitter:title" content="Get ClassifIeD">
    <meta name="twitter:description" content="Pre-employment screening — powered by BIT Group.">
    <meta name="twitter:image" content="https://classified.getclassified.co.uk/images/logo_getclassified3.jpg">

    <meta property="og:type" content="website">
    <meta property="og:site_name" content="Get ClassifIeD">
    <meta property="og:title" content="Get ClassifIeD">
    <meta property="og:description" content="Pre-employment screening - powered by BIT Group">
    <meta property="og:image" content="{{ env('APP_URL') }}images/logo_getclassified3.jpg">
    <meta property="og:url" content="{{ env('APP_URL') }}">

    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-534VMZSN5B"></script>
    <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());

    gtag('config', 'G-534VMZSN5B');
    </script>

    @yield('meta')

    @stack('structuredData')

    <style>
        .overlayContact {
            position: relative;
            z-index: 1;
            overflow: hidden;
        }

        .overlayContact::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(44, 60, 100, 0.75);
            z-index: 0;
        }

        .overlayContact::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: url('{{ env('APP_URL') }}images/FrontEnd/contactBackground.webp');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            z-index: -1;
        }

        .overlayContact > * {
            position: relative;
            z-index: 1;
        }

        .overlayContact p {
            font-weight: 600;
            font-size: 1.1rem;
        }

        .btnContact {
            background-color: #C55359;
            color: white;
            padding: 7px 70px;
            border-radius: 0px;
            font-weight: 600;
            transition: 300ms;
            font-weight: 600;
            font-size: 1.1rem;
            align-self: flex-end;
            margin-right: 0;
            transition: 300ms;
        }
        .btnContact:hover {
            background-color:rgb(187, 69, 75);
        }

        .contactText {
            padding: 0px 40px
        }

        .d-flex {
            width: 100%;
        }

        .d-flex > div {
            flex: 1;
        }

        .contactUnderline {
            display: inline-block;
            border-bottom: 8px solid #C55359;
            padding-bottom: 5px;
        }

        .pageBanner {
            background-color: #2C3C64;
        }

        @media (max-width: 576px) {
            .pageBanner {
                margin-top: -65px;
            }
        }

        .textBannerSection {
            padding: 40px 50px;
        }

        .pageBanner img {
            max-width: 100%;
            height: auto;
            object-fit: cover;
            border-radius: 0;
        }

        .loginBtnFront {
            background-color: #C55359;
            color: white;
            padding: 10px 35px;
            border-radius: 50px;
            font-weight: 700;
            transition: 300ms;
        }
        .loginBtnFront:hover {
            background-color: #a94448;
        }
        .expertBtnFront {
            background-color: transparent;
            color: white;
            padding: 7px 32px;
            border-radius: 50px;
            border: 3px solid #C55359;
            font-weight: 700;
            transition: 300ms;
        }
        .expertBtnFront:hover {
            background-color: rgba(185, 185, 185, 0.23);
            border: 3px solid #C55359;
        }

        @media only screen and (max-width: 439px) {
            .expertBtnFront {
                margin-bottom: 20px;
            }
        }

        h1 {
            border-bottom: 8px solid #C55359;
            padding-bottom: 4px;
        }

        /*Title and text block*/
        .Title-TextBlock {
            width: 100%;
            height: auto;
            display: flex; 
            justify-content: space-between;
            align-items: center;
            margin-top: 110px;
            margin-bottom: 110px;
        }
        .Title-TextBlock h2 {
            flex: 1;
            font-family: "Obvia Narrow", sans-serif;
            font-weight: 600;
            font-size: 37px;
            line-height: 42px;
            color: #C55359;
            max-width: 35%;
        }
        .Title-TextBlock h2 span {
            color: #2C3C64;
        }
        .Title-TextBlock p {
            flex: 1;
            font-family: "Obvia Narrow", sans-serif;
            font-weight: 400;
            font-size: 18px;
            line-height: 24px;
            color: #828282;
            margin-left: 20px;
        }
        .Title-TextBlock p span {
            color: #C55359;
            font-size: 24px;
            line-height: 30px;
            font-weight: 600;
        }

        @media (min-width: 769px) and (max-width: 1200px) {
            .Title-TextBlock {
                width: 90%;
                margin-left: 5%;
            }
        }
    

        /*Styling changes for phone screens*/
        @media only screen and (max-width: 768px) {
            /*Title Text block - now vertical aligned rather than horizontal*/
            .Title-TextBlock {
                flex-direction: column;
                align-items: center;
                margin-top: 50px;
                margin-bottom: 50px;
            }
            .Title-TextBlock h2 {
                margin-bottom: 20px;
                max-width: 90%;
                margin-left: 5%;
                margin-right: 5%;
                margin-bottom: 50px;
            }
            .Title-TextBlock p {
                margin-bottom: 20px;
                max-width: 90%;
                margin-left: 5%;
                margin-right: 5%;
            }
        }
       
    </style>
    @yield('pageCSS')
</head>
<body class="is-frontend">
    @include('layout.frontEndHeader')

    <main class="py-4">
        @yield('content')
    </main>

    @yield('pageJavascript')
    
    @include('layout.frontEndFooter')
    @include('layout.accessibility')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://classified.statuspage.io/embed/script.js"></script>
</body>
</html>
