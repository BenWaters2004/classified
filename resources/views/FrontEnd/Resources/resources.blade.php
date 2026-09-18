@extends('layout.frontEnd')

@section('title', 'Resources | ClassifIeD')
@section('meta_keywords', 'Screening glossary, get classified glossary, get classified final report, get classified blog')
@section('meta_description', 'Welcome to the Get ClassifIeD Resources Hub. Looking for guidance or industry insights? You’re in the right place.')

@section('content')
<!-- AOS Animation Library -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        AOS.init({ duration: 1000, once: true });
    });
</script>


<div class="container py-5">
    <div class="row align-items-center pageBanner" data-aos="fade-up">
        <!-- Text Section -->
        <div class="col-lg-6 text-white textBannerSection">
            <h1 class="display-5 fw-bold mb-4">Resources</h1>
            <p class="lead mb-4"></p>
        </div>
        <!-- Image Section -->
        <div class="col-lg-6 text-end pe-0">
            <img rel="preload" as="image" src="{{ env('APP_URL') }}images/FrontEnd/banner-man-laptop.webp" alt="Man smiling with laptop" class="img-fluid" loading="eager" fetchpriority="high">
        </div>
    </div>

    <div class="Title-TextBlock">
        <h2>Welcome to the Get ClassifIeD <br><span>Resources Hub</span></h2>
        <p>
            Looking for guidance or industry insights? You’re in the right place. Whether you're unsure about the difference between BS7858 screening and the Baseline Personnel Security Standard (BPSS), or puzzled by terms like spent convictions — our industry glossary is here to help. You can also explore a sample screening report to see what a completed check looks like in practice, or dive into our blog for updates, advice, and insights on best practices in security screening.<br /><br />
            Still can’t find what you’re looking for? We’re just a message away — don’t hesitate to get in touch.
        </p>
    </div>

    <div class="row text-center mt-5 resources-grid" data-aos="fade-up">
        <div class="col-md-4 mb-4">
            <a href="{{ env('APP_URL') }}Blog" class="resource-tile">
                <i class="fas fa-rss fa-3x mb-3"></i>
                <h5>Blog</h5>
                <p>Go to Blog <i class="fa-solid fa-arrow-right"></i></p>
            </a>
        </div>
        <div class="col-md-4 mb-4">
            <a href="{{ env('APP_URL') }}Resources/glossary" class="resource-tile">
                <i class="fas fa-book fa-3x mb-3"></i>
                <h5>Glossary</h5>
                <p>Go to Glossary <i class="fa-solid fa-arrow-right"></i></p>
            </a>
        </div>
        <div class="col-md-4 mb-4">
            <a href="{{ env('APP_URL') }}sample-report.pdf" target="_blank" class="resource-tile">
                <i class="fas fa-file-alt fa-3x mb-3"></i>
                <h5>Sample Report</h5>
                <p>Go to Sample Report <i class="fa-solid fa-arrow-right"></i></p>
            </a>
        </div>
    </div>

    <div class="container py-5 text-white" data-aos="fade-up">
        <div class="overlayContact">
            <div class="py-5">
                <div class="d-flex flex-column flex-md-row align-items-start justify-content-between">
                    <div class="contactText">
                        <h2 class="fw-bold mb-3 contactUnderline">Need help?</h2>
                        <p class="mb-2">
                            Tel: <a href="tel:+441752724000" class="text-decoration-none text-light">+44 (0)1752 724 000</a>
                        </p>
                        <p>
                            Email: <a href="mailto:screening@thinkbitgroup.co.uk" class="text-decoration-none text-light">screening@thinkbitgroup.co.uk</a>
                        </p>
                    </div>
                    <a href="{{ env('APP_URL') }}ContactUs" class="btn btnContact mt-6">Speak to an expert</a>
                </div>
            </div>
        </div>
    </div>

</div>

@endsection

@section('pageCSS')
<style>
.resource-tile {
    display: block;
    background-color: #f7f9fc;
    padding: 30px 20px;
    border-radius: 10px;
    text-decoration: none;
    color: #2C3C64;
    transition: all 0.3s ease;
    border: 1px solid transparent;
    height: 100%;
}

.resource-tile:hover {
    background-color: #ffffff;
    border-color: #C55359;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    color: #C55359;
}

.resource-tile i {
    color: #C55359;
    transition: color 0.3s ease;
}

.resource-tile:hover i {
    color: #a94448;
}

.resource-tile h5 {
    margin-top: 10px;
    font-size: 1.2rem;
    font-weight: 600;
}

</style>
@endsection