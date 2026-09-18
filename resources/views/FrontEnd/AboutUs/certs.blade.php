@extends('layout.frontEnd')

@section('title', 'Certifications and Memberships | ClassifIeD')
@section('meta_keywords', 'ISO Certified screening, IASME Certified Screening, Cyber Essentials certified screening, accredited screening provider')
@section('meta_description', 'Find out about Get ClassifIeDs accreditations and memberships. Including ISO, IASME and Cyber Essentials Plus')

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
            <h1 class="display-5 fw-bold mb-4">Certifications and Memberships</h1>
            <p class="lead mb-4">Find out more about Get ClassifIeD's accreditations and memberships.</p>
        </div>
        <!-- Image Section -->
        <div class="col-lg-6 text-end pe-0">
            <img rel="preload" as="image" src="{{ env('APP_URL') }}images/FrontEnd/banner-spotty-lady.webp" alt="Lady folded arms smiling" class="img-fluid" loading="eager" fetchpriority="high">
        </div>
    </div>

    <div class="Title-TextBlock">
        <h2>Quality <br><span>and assurance</span></h2>
        <p>We take pride in meeting the highest industry standards through recognised certifications and frameworks. Our accreditations reflect our ongoing commitment to trust, compliance, and professionalism in pre-employment screening.</p>
    </div>

    <div class="CertificationsMemberships">
        <h3 class="mb-4">Certifications</h3>
        <div class="CertItem">
            <img src="{{ env('APP_URL') }}images/FrontEnd/essentialsPlus.webp" alt="Cyber Essentials plus logo">
            <p><span>Cyber Essentials plus</span><br />
            BluescreenIT Ltd (Get ClassifIeD) has been awarded Cyber Essentials Plus.
            </p>
        </div>
        <div class="CertItem">
            <img src="{{ env('APP_URL') }}images/FrontEnd/CyberAssurance.webp" alt="IASME Cyber Assurance logo">
            <p><span>IASME Cyber Assurance</span><br />
            BluescreenIT Ltd (Get ClassifIeD) has been awarded IASME Cyber Assurance.
            </p>
        </div>
        <div class="CertItem">
            <img src="{{ env('APP_URL') }}images/FrontEnd/ISO9001.webp" alt="ISO 9001 logo" style="width: 150px; margin-inline:25px;">
            <p><span>ISO 9001</span><br />
            BluescreenIT Ltd (Get ClassifIeD) has been awarded ISO 9001 for quality management.
            </p>
        </div>
        <div class="CertItem">
            <img src="{{ env('APP_URL') }}images/FrontEnd/ISO27001.webp" alt="ISO 27001 Certification logo" style="width: 150px; margin-inline:25px;">
            <p><span>ISO 27001</span><br />
            BluescreenIT Ltd (Get ClassifIeD) has been awarded ISO 27001.
            </p>
        </div>
        <div class="CertItem">
            <img src="{{ env('APP_URL') }}images/FrontEnd/ERS_Gold_Banner_2025.webp" alt="Gold Armed Forces Covenant Award">
            <p><span>Gold Armed Forces Covenant Award</span><br />
            BluescreenIT Ltd (Get ClassifIeD) has been awarded Gold Armed Forces Covenant Award.
            </p>
        </div>
        <h3 class="mt-5 mb-4">Memberships</h3>
        <div class="CertItem">
            <img src="{{ env('APP_URL') }}images/FrontEnd/DBSlogo.webp" alt="Disclosure and Barring Service logo">
            <p><span>Disclosure and Barring Service</span><br />
            BluescreenIT Ltd (Get ClassifIeD) is an Registered Organisation of the Disclosure and Barring Service in England and Wales (Reg. No 89045885006).
            </p>
        </div>
        <h3 class="mt-5 mb-4">Frameworks</h3>
        <div class="CertItem">
            <img src="{{ env('APP_URL') }}images/FrontEnd/CCSlogo.webp" alt="Crown Commercial Services logo">
            <p><span>Crown Commercial Services</span><br />
            BluescreenIT Ltd (Get ClassifIeD) is lsited on G-Cloud 14, a government-approved channel, operated by Crown Commercial Services.<br />
            <a href="https://redirect.contractawardservice.crowncommercial.gov.uk/agreement/RM1557.14/lot/All" alt="G-Cloud 14 listing" class="certsLink">View Listing</a>
            </p>
        </div>
    </div>

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
.CertificationsMemberships {
    margin-top: 60px;
}

.CertificationsMemberships h3 {
    color: #2C3C64;
    font-weight: 700;
    font-size: 1.75rem;
    border-bottom: 4px solid #C55359;
    padding-bottom: 6px;
    display: inline-block;
}

.CertItem {
    display: flex;
    align-items: center;
    gap: 30px;
    padding: 25px 0;
    border-bottom: 1px solid #ccc;
}

.CertItem:last-of-type {
    border-bottom: none;
}

.CertItem img {
    width: 200px;
    height: auto;
    object-fit: contain;
}

.CertItem p {
    margin: 0;
    color: #444;
    font-size: 1rem;
    line-height: 1.5;
}

.CertItem p span {
    font-size: 1.1rem;
    color: #C55359;
    font-weight: 600;
}

.certsLink {
    color: #C55359;
}

/* Responsive layout for small screens */
@media (max-width: 768px) {
    .CertItem {
        flex-direction: column;
        align-items: flex-start;
        gap: 15px;
    }

    .CertItem img {
        width: 70px;
    }
}

</style>
@endsection