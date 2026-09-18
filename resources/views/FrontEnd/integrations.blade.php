@extends('layout.frontEnd')

@section('title', 'Integrations | ClassifIeD')
@section('meta_keywords', 'screening platform API, ATS integration, HR system integration, background check API, applicant tracking integration, Get ClassifIeD integrations')
@section('meta_description', 'Discover how to integrate Get ClassifIeD’s screening platform with your existing systems. Learn about our pre-built ATS integrations and flexible API for custom workflows.')


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
            <h1 class="display-5 fw-bold mb-4">Meet our integration partners</h1>
            <p class="lead mb-4">We’ve partnered with leading HR solutions and applicant tracking systems to offer pre-built integrations, making it easy to use our platform and services directly within your recruitment workflow.</p>
        </div>
        <!-- Image Section -->
        <div class="col-lg-6 text-end pe-0">
            <img rel="preload" as="image" src="{{ env('APP_URL') }}images/FrontEnd/banner-spotty-lady.webp" alt="Lady folded arms smiling" class="img-fluid" loading="eager" fetchpriority="high">
        </div>
    </div>

    <div>
        <h2>Why integrate with us?</h2>
    </div>

    <div class="Title-TextBlock">
        <h2>Custom API <br><span>integration</span></h2>
        <p>Our developer-friendly API gives you complete control over how you connect with our platform. Integrate our full suite of screening services directly into your own systems, automate background checks, and build a solution that fits your exact requirements.
        You can find our comprehensive <a href="#CommingSoon" class="link">developer guide here.</a></p>
    </div>

    <h2 class="mt-5">Our integration partners</h2>
    <!--Grid of partners (add when available)-->

    <div class="container py-5 text-white" data-aos="fade-up">
        <div class="overlayContact">
            <div class="py-5">
                <div class="d-flex flex-column flex-md-row align-items-start justify-content-between">
                    <div class="contactText">
                        <h2 class="fw-bold mb-3 contactUnderline">Let's get in touch</h2>
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
.link {
    color: #C55359;
    text-decoration: none;
}
.link:hover {
    color: #b1474cff;
    text-decoration: underline;
}
</style>
@endsection