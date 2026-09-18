@extends('layout.frontEnd')

@section('title', 'Contact Us | ClassifIeD')
@section('meta_keywords', 'pre-employment screening UK, Get ClassifIeD, DBS check service, manage DBS and BPSS applications, BPSS screening service, digital identity verification, employment background checks, Get ClassifIeD screening platform, screening software for HR, onboarding security checks')
@section('meta_description', 'Get in touch to see how are screening solution can help you!.')


@section('content')
<div class="container py-5">
    <div class="row align-items-center pageBanner">
        <!-- Text Section -->
        <div class="col-lg-6 text-white textBannerSection">
            <h1 class="display-5 fw-bold mb-4">Let’s, Get in touch</h1>
            <p class="lead mb-4">Start the conversation and see what we can offer.</p>
        </div>
        <!-- Image Section -->
        <div class="col-lg-6 text-end pe-0">
            <img rel="preload" as="image" src="{{ env('APP_URL') }}images/FrontEnd/banner-lady-talking.webp" alt="Lady talking with laptop" class="img-fluid" loading="eager" fetchpriority="high">
        </div>
    </div>

    <!-- Contact Section -->
    <div class="row contact-section mt-5">
        <!-- Text Section -->
        <div class="col-lg-5 col-md-12 contacts-info mb-4">
            <div class="ContactBox">
                <h3>Sales</h3>
                <p>Call: <a href="Tel: +44 (0)1752 977055">+44 (0)1752 977055</a></p>
                <p>Email: <a href="mailto:sales@thinkbitgroup.co.uk">sales@thinkbitgroup.co.uk</a></p>
            </div>
            <div class="ContactBox">
                <h3>Client & Candidate Support</h3>
                <p>Call: <a href="Tel: +44 (0)1752 724 000">+44 (0)1752 724 000</a></p>
                <p>Email: <a href="mailto:screening@thinkbitgroup.co.uk">screening@thinkbitgroup.co.uk</a></p>
            </div>
            <div class="ContactBox">
                <h3>Technical Support</h3>
                <p>Call: <a href="Tel: +44 (0)1752 270139">+44 (0)1752 270139</a></p>
                <p>Email: <a href="mailto:ben.waters@thinkbitgroup.co.uk">ben.waters@thinkbitgroup.co.uk</a></p>
                <p>Service Health Status: <a href="https://classified.statuspage.io/">https://classified.statuspage.io/</a></p>
            </div>
            <div class="ContactBox">
                <h3>Address</h3>
                <p>HQ: Plymouth Science Park, 1 Davy Rd, Plymouth, Devon PL6 8BX</p>
                <iframe width="100%" height="300" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade" src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2542.271461755169!2d-4.1085706!3d50.417413700000004!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x486ced666312f649%3A0x51421885656f8b6c!2sGet%20ClassifIeD!5e0!3m2!1sen!2suk!4v1764270872781!5m2!1sen!2suk"></iframe>
            </div>
        </div>
        <!-- Form Section -->
        <div class="col-lg-7 col-md-12">
            <iframe 
                aria-label="Let's, Get in touch" 
                frameborder="0" 
                class="contact-form" 
                src='https://forms.zohopublic.eu/salesteam1/form/Classified/formperma/pOlkUpsoBqryXiHp1qphSx6TO2bkgMY6nUobypDPXyo'>
            </iframe>
        </div>
    </div>
</div>


<style>

    .contact-section {
        display: flex;
        flex-wrap: wrap;
        align-items: flex-start;
        overflow: hidden;
    }

    .contacts-info {
        margin-top: 60px;
        padding-right: 20px;
    }

    .contacts-info h3 {
        color: #C55359;
        font-size: 1.2rem;
        margin-bottom: 0.5rem !important;
    }
    .contacts-info a {
        color: #C55359;
        text-decoration: none;
    }
    .contacts-info p {
        margin-bottom: 0.5rem !important;
        font-size: 1rem;
    }

    .ContactBox {
        background-color:rgb(228, 228, 228);
        padding: 15px;
        margin-bottom: 20px;
        width: auto;
    }

    .contact-form {
        width: 90%;
        height: 850px;
        margin-left: 5%;
        border: none;
    }

    @media (max-width: 768px) {
        .contact-section {
            flex-direction: column;
        }

        .contact-info {
            text-align: center;
            margin-bottom: 20px;
        }

        .contact-form {
            width: 100%;
        }
    }
</style>

@endsection