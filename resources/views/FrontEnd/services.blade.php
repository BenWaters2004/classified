@extends('layout.frontEnd')

@section('title', 'Services | ClassifIeD')
@section('meta_keywords', 'pre-employment checks UK, basic DBS checks, digital right to work, academic verification, employment verification, personal references, character references, bpss clearance')
@section('meta_description', 'Take a look at all the background screening checks we are able to offer.')


@section('content')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

<div class="container py-5">
    <div class="row align-items-center pageBanner animate">
        <!-- Text Section -->
        <div class="col-lg-6 text-white textBannerSection">
            <h1 class="display-5 fw-bold mb-4">Services</h1>
            <p class="lead mb-4">We provide a wide range of checks for all your needs.</p>
        </div>
        <!-- Image Section -->
        <div class="col-lg-6 text-end pe-0">
            <img rel="preload" as="image" src="{{ env('APP_URL') }}images/FrontEnd/banner-lady-laptop.webp" alt="Lady smiling with laptop" class="img-fluid" loading="eager" fetchpriority="high">
        </div>
    </div>

    <!-- Table Section -->
    <div class="table-responsive mt-5 animate">
        <table class="table">
            <thead style="background-color: #C55359; color: white;">
                <tr>
                    <th style="width: 20%;">Service</th>
                    <th style="width: 60%;">Service Description</th>
                    <th style="width: 20%;">Duration</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><a href="{{ env('APP_URL') }}Services/DBS-Basic">UK Criminal Record (Basic DBS, England & Wales)</a></td>
                    <td>The Basic level check, from the Disclosure & Barring Service, provides details of unspent convictions and conditional cautions. This check was designed for individuals residing or working in England and Wales but is also used in Jersey, Guernsey and the Isle of Man.</td>
                    <td>1 to 10 working days</td>
                </tr>
                <tr>
                    <td><a href="{{ env('APP_URL') }}Services/BPSS">BPSS Check</a></td>
                    <td>A BPSS check consists of verification made up of the following 4 parts (RICE):
                        <b>R</b> - ight to work,
                        <b>I</b> - dentity Verification,
                        <b>C</b> - riminal Record (Basic Disclosure),
                        <b>E</b> - mployment history check.
                        In addition, candidates are required to disclose any significant periods spent abroad (6 months or more in the past 3 years). </td>
                    <td>1 to 10 working days</td>
                </tr>
                <tr>
                    <td><a href="{{ env('APP_URL') }}Services/Digital-ID-Verification">Digital ID Verification</a></td>
                    <td>We will digitally verify an individual's identity to reduce fraud risk. This is also a requirement for criminal record checks. </td>
                    <td>Instant</td>
                </tr>
                <tr>
                    <td><a href="{{ env('APP_URL') }}Services/right-to-work">Right to Work</a></td>
                    <td>Ensure candidates have the lawful authority to work in the United Kingdom.</td>
                    <td>1 working day</td>
                </tr>
                <tr>
                    <td><a href="{{ env('APP_URL') }}Services/academic-history">Academic History</a></td>
                    <td>We will thoroughly substantiate an individual’s academic history, encompassing both secondary and higher education achievements.</td>
                    <td>1-5 working days</td>
                </tr>
                <tr>
                    <td><a href="{{ env('APP_URL') }}Services/employment-history">Employment History</a></td>
                    <td>This check is designed to confirm the details of an individual's employment history accurately.</td>
                    <td>1-5 working days</td>
                </tr>
                <tr>
                    <td><a href="{{ env('APP_URL') }}Services/personal-references">Personal References</a></td>
                    <td>We will conduct a personal reference check to gather insights into an individual’s character through the perspective of a referee.</td>
                    <td>1-5 working days</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="position-relative text-white mt-4 animate">
        <div class="overlayContact">
            <div class="py-5">
                <div class="d-flex flex-column flex-md-row align-items-start justify-content-between">
                    <div class="contactText">
                        <h2 class="fw-bold mb-3 contactUnderline">Create a bespoke package</h2>
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
/* Animation */
.animate {
    opacity: 0;
    transform: translateY(30px);
    transition: all 0.7s ease-out;
}

/* Fade-in Animation */
.animate.visible {
    opacity: 1;
    transform: translateY(0);
}
</style>
@endsection

@section('pageJavascript')
<script>
// Function to detect when elements are in view
function revealOnScroll() {
    let elements = document.querySelectorAll('.animate');
    let windowHeight = window.innerHeight;
    
    elements.forEach(el => {
        let position = el.getBoundingClientRect().top;
        if (position < windowHeight - 100) {
            el.classList.add('visible');
        }
    });
}

// Run on scroll and when the page loads
document.addEventListener("DOMContentLoaded", revealOnScroll);
document.addEventListener("scroll", revealOnScroll);
</script>
@endsection
