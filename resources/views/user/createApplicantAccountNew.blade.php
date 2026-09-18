@extends('layout.default')

@section('title', "Applicant Registration")

@section('content')
<style>
    body {
        background-color: #f4f6f9;
        margin: 0;
    }

    #main-content {
      margin-bottom: 0px;
    }

    .main-header {
      display: none;
    }

    .split-container {
        display: flex;
        min-height: 85vh;
        margin-top: 3vh;
    }

    .login-left {
        flex: 1;
        background: #fff;
        padding: 60px 40px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        max-width: 100%;
    }

    .login-right {
        flex: 1;
        background-color: #2C3C64;
        color: #fff;
        padding: 60px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        background-image: url('{{ env('APP_URL') }}images/BITBars.png');
        background-size: auto;
        background-position: right bottom;
        background-repeat: no-repeat;
        position: relative;
    }

    .login-right h1, .login-right h2 {
        font-size: 36px;
        font-weight: 700;
        color: white;
        text-align: center;
    }

    .login-right p {
        font-size: 16px;
        text-align: center;
    }

    .form-control {
        border-radius: 6px;
    }

    .form-control:focus {
      border-color: #C55359 !important;
    }

    .login-logo {
        max-height: 60px;
        margin-bottom: 30px;
    }

    .btn-login {
        background-color: #C55359;
        color: #fff;
        border: none;
        font-weight: 600;
        margin-bottom: 10px;
    }

    .btn-login:hover {
        background-color: #a94247;
        color: white;
    }

    .text-red {
        color: #C55359;
        font-weight: 600;
    }

    .requirement-list {
        list-style: none;
        padding-left: 0;
    }

    .requirement-list li {
        margin: 6px 0;
        font-size: 14px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .requirement-list .icon {
        font-size: 16px;
    }

    .icon-red {
        color: #C55359;
    }

    .icon-green {
        color: #28a745;
    }

    @media (max-width: 768px) {
        .split-container {
            flex-direction: column;
        }

        .login-right {
            display: none;
        }

        .login-left {
            padding: 40px 20px;
        }
    }

    .slider {
        position: relative;
        width: 100%;
        height: auto;
        text-align: center;
    }

    .slide {
        display: none;
        animation: fadeIn 1s ease-in-out;
    }

    .slide.active {
        display: block;
    }

    .dots {
        position: absolute;
        bottom: 30px;
        left: 0;
        right: 0;
        display: flex;
        justify-content: center;
        gap: 10px;
    }

    .dot {
        height: 12px;
        width: 12px;
        background-color: rgba(255, 255, 255, 0.4);
        border-radius: 50%;
        display: inline-block;
        cursor: pointer;
        transition: background-color 0.3s;
    }

    .dot.active {
        background-color: #fff;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<div class="split-container">
    <!-- Left: Registration Form -->
    <div class="login-left">
        <div class="text-center">
            @if (env('APP_ENV') == 'production')
                <a href="/">
                    <img src="{{ env('APP_URL') }}images/logo_{{ str_replace(' ', '', strtolower(env('APP_NAME'))) }}.webp" class="login-logo" alt="Logo">
                </a>
            @else
                <a href="/">
                    <img src="{{ env('APP_URL') }}images/logo_demo.png" class="login-logo" alt="Demo Logo">
                </a>
            @endif
        </div>

        <form action="{{ env('APP_URL') }}register/createAccount" method="post">
            {{ csrf_field() }}
            <h3 class="mb-3" style="color:#2C3C64;">Account Setup</h3>
            <p class="text-muted mb-4">Please enter a password for your account</p>

            <div class="form-group">
                <input type="password" name="password" id="password" class="form-control" placeholder="Password" autocomplete="off" required autofocus>
            </div>

            <div class="form-group mt-3">
                <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" placeholder="Repeat Password" autocomplete="off" required>
            </div>

            <input type="hidden" name="accessUrlCode" value="{{$accessUrlCode}}">

            @if ($errors->any() && $errors->has('message'))
                <div class="text-red text-center mt-2 mb-2">
                    {{$errors->first('message')}}
                </div>
            @endif

            <div class="mt-3 mb-3" style="font-size: 14px;">
                Your password must:
                <ul class="requirement-list">
                    <li id="length"><i class="fa-solid fa-xmark icon icon-red"></i> Be at least 14 characters long</li>
                    <li id="uppercase"><i class="fa-solid fa-xmark icon icon-red"></i> Contain at least 1 uppercase letter</li>
                    <li id="lowercase"><i class="fa-solid fa-xmark icon icon-red"></i> Contain at least 1 lowercase letter</li>
                    <li id="number"><i class="fa-solid fa-xmark icon icon-red"></i> Contain at least 1 number</li>
                    <li id="special"><i class="fa-solid fa-xmark icon icon-red"></i> Contain at least 1 special character from @$!%*#?</li>
                </ul>
            </div>

            <button class="btn btn-login btn-block" type="submit">Register Account</button>
        </form>
    </div>

    <!-- Right: Visual Panel -->
    <div class="login-right">
        <img src="{{ env('APP_URL') }}images/BITBars.png" alt="Decorative background with BIT Bars" style="display: none;">
        <div class="slider">
            <div class="slide active">
                <h1>Welcome to {{ env('APP_NAME') }}</h1>
                <p>Secure and professional pre-employment screening, trusted by organisations nationwide.</p>
            </div>
            <div class="slide">
                <h2>Fast & Reliable</h2>
                <p>Get your candidates verified faster with our streamlined digital process.</p>
            </div>
            <div class="slide">
                <h2>Compliance Made Simple</h2>
                <p>Stay fully compliant with industry and government regulations effortlessly.</p>
            </div>
        </div>
        <div class="dots">
            <span class="dot active" onclick="showSlide(0)"></span>
            <span class="dot" onclick="showSlide(1)"></span>
            <span class="dot" onclick="showSlide(2)"></span>
        </div>
    </div>
</div>

<script>
    let currentSlide = 0;
    const slides = document.querySelectorAll('.slide');
    const dots = document.querySelectorAll('.dot');

    function showSlide(index) {
        slides.forEach((slide, i) => {
            slide.classList.toggle('active', i === index);
            dots[i].classList.toggle('active', i === index);
        });
        currentSlide = index;
    }

    function nextSlide() {
        currentSlide = (currentSlide + 1) % slides.length;
        showSlide(currentSlide);
    }

    setInterval(nextSlide, 5000);

    // Password requirement validation
    const passwordInput = document.getElementById('password');
    const checks = {
        length: /.{14,}/,
        uppercase: /[A-Z]/,
        lowercase: /[a-z]/,
        number: /[0-9]/,
        special: /[@$!%*#?]/
    };

    passwordInput.addEventListener('input', () => {
        const value = passwordInput.value;
        for (const key in checks) {
            const el = document.getElementById(key).querySelector('i');
            if (checks[key].test(value)) {
                el.classList.remove('fa-xmark', 'icon-red');
                el.classList.add('fa-check', 'icon-green');
            } else {
                el.classList.add('fa-xmark', 'icon-red');
                el.classList.remove('fa-check', 'icon-green');
            }
        }
    });
</script>
@endsection
