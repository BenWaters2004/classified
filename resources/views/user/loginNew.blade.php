@extends('layout.default')

@section('title', "Login | Get ClassifIeD")

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

    .text-link a {
        color: #2C3C64;
        font-size: 14px;
    }

    .text-red {
        color: #C55359;
        font-weight: 600;
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

<div class="split-container">
    <!-- Left: Login Form -->
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

        <form action="{{ env('APP_URL') }}login" method="post">
            {{ csrf_field() }}
            <h3 class="mb-3" style="color:#2C3C64;">Sign in</h3>
            <p class="text-muted mb-4">Use your email and password to access your account</p>

            <div class="form-group">
                <input type="email" name="emailAddressUKpesa" id="emailAddressUKpesa" class="form-control" placeholder="Email address" required autofocus>
            </div>

            <div class="form-group mt-3">
                <input type="password" name="password" id="password" class="form-control" placeholder="Password" autocomplete="off" required>
            </div>

            @if ($errors->any() && $errors->has('message'))
                <div class="text-red text-center mt-2 mb-2">
                    {{ $errors->first('message') }}
                </div>
            @endif

            <button class="btn btn-login btn-block mt-3" type="submit">Sign in</button>

            <div class="text-center text-link mt-3">
                <a href="{{ env('APP_URL') }}forgotPassword" title="Send a password reset link">Forgot your password?</a>
            </div>
        </form>
    </div>

    <!-- Right: Visual Panel (Desktop Only) -->
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
</script>
@endsection
