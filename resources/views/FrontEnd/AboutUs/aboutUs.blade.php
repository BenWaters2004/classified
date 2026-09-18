@extends('layout.frontEnd')

@section('title', 'About Us | ClassifIeD')
@section('meta_keywords', 'Get ClassifIeD, Get ClassifIeD screening solution, bluescreenit, BIT Group, BIT Training, BIT Security, pre-employment screening, security clearance, cyber security')
@section('meta_description', 'Get ClassifIeD is a screening service offered by BIT Group. BIT Group was established in 2004, offering cyber security services.')


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
            <h1 class="display-5 fw-bold mb-4">About Get ClassifIeD</h1>
            <p class="lead mb-4">Helping you make informed recruitment decisions.</p>
        </div>
        <!-- Image Section -->
        <div class="col-lg-6 text-end pe-0">
            <img rel="preload" as="image" src="{{ env('APP_URL') }}images/FrontEnd/banner-smile-laptop.webp" alt="Woman smiling with laptop" class="img-fluid" loading="eager" fetchpriority="high">
        </div>
    </div>

    <div class="Title-TextBlock">
        <h2>Cyber Security<br><span>is at the heart of Get ClassifIeD</span></h2>
        <p>Get ClassifIeD is a screening service offered by <a href="https://www.bluescreenit.co.uk/" class="noLinkShow" target="_blank">BIT Group</a> a trading name of BluescreenIT Ltd. Established in 2004, BIT Group continues to grow its innovative service delivery, from our head office in the South West. We have a proud heritage of working with the MoD, winning a Gold Armed Forces Covenants Award in 2025. As the official service provider to the CyberHub Trust, we continue to pursue our passion for delivering community-driven security and training services to the South West and the whole of the United Kingdom.
        <br /><br />
        BIT Group operates three specialist business arms: <a href="https://thinkbittraining.co.uk/" class="noLinkShow" target="_blank">BIT Training</a>, <a href="https://thinkbitsecurity.co.uk/" class="noLinkShow" target="_blank">BIT Security</a> and BIT Research. Brought together, the group provides organisations with a bespoke, innovative ecosystem to protect and enhance the most important assets of their business … the people, the processes and the technology.</p>
    </div>

    <div class="info-section">
        <div class="info-box blue" data-aos="fade-right">
            <div class="info-content">
                <img src="{{ env('APP_URL') }}images/FrontEnd/BIT_Training.png" alt="BIT Training logo">
            </div>
        </div>
        <div class="info-box white" data-aos="fade-left">
            <div class="info-content">
                <div class="info-text">
                    <h3>BIT Training</h3>
                    <p>A leading training provider in commercial online, instructor-led and bespoke IT, Cyber Security, Project Management, Data and IT Service Management training, digital apprenticeships and digital Skills Bootcamps for many of today’s leading industry vendors. Delivered by our qualified in-house training team and through our growing network of learning partners. It allows us to provide customised and cost-effective training solutions to all our customers. As part of our training division, we are still immensely proud of our 19 years of delivering ELCAS Resettlement vendor-funded training programmes to support UK MoD personnel transition into commercial operations.</p>
                    <a href="https://thinkbittraining.co.uk/" class="read-more" target="_blank">Read more <i class="fa-solid fa-arrow-right"></i></a>
                </div>
            </div>
        </div>
    </div>

    <div class="info-section">
        <div class="info-box white" data-aos="fade-right">
            <div class="info-content">
                <div class="info-text">
                    <h3>BIT Security</h3>
                    <p>Created to bring affordable Managed Security Services to the market through the creation of the UK’s first Community Security Operation Centre (SOC). Using market-leading technology combined with fully trained analysts and consultants, our tailor-made services support the most demanding budgets. In 2019, our Cyber Security Centre won the WMN Apprenticeship Development Award. It was shortlisted for the South West Tech Awards for our Commitment to Diversity for our positive stance to recruit individuals from hard-to-reach and diverse backgrounds, including neurodiverse, ex hackers and MoD service leavers, as part of our cyber apprenticeship programmes.</p>
                    <a href="https://thinkbitsecurity.co.uk/" class="read-more" target="_blank">Read more <i class="fa-solid fa-arrow-right"></i></a>
                </div>
            </div>
        </div>
        <div class="info-box blue" data-aos="fade-left">
            <div class="info-content">
                <img src="{{ env('APP_URL') }}images/FrontEnd/bit-security.png" alt="BIT Security logo">
            </div>
        </div>
    </div>

    <div class="info-section">
        <div class="info-box blue" data-aos="fade-right">
            <div class="info-content">
                <img src="{{ env('APP_URL') }}images/whiteWithStrap.png" alt="ClassifIeD logo">
            </div>
        </div>
        <div class="info-box white" data-aos="fade-left">
            <div class="info-content">
                <div class="info-text">
                    <h3>BIT Screening</h3>
                    <p>Created in 2018, PESA (Pre-Employment Screening Application) was purpose-built to meet the stringent personnel vetting needs of the aerospace industry. The platform offered a bespoke BPSS clearance system, designed to streamline background screening while maintaining compliance with government and industry standards. Following several successful years of deployment within aerospace, we expanded our vision with the launch of Get ClassifIeD - a modern, scalable screening solution for all industries. Built on a foundation of cybersecurity excellence, Get ClassifIeD ensures every check is secure, efficient, and fully compliant with regulatory standards - empowering businesses to make safer, faster hiring decisions across the UK.</p>
                </div>
            </div>
        </div>
    </div>

    <div class="position-relative text-white mt-5" data-aos="fade-up">
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
/* General Styling */
.noLinkShow {
    color: #2C3C64;
}

/* Info Section */
.info-section {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 0;
    width: 100%;
}

.info-box {
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 40px;
    min-height: 300px;
}

.blue {
    background: #2C3C64;
    color: #fff;
}

.white {
    background: #fff;
    padding-top: 40px !important;
    padding-bottom: 40px !important;
    padding-left: 20px !important;
    padding-right: 20px !important;
}

.info-content {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 20px;
    max-width: 80%;
    text-align: left;
}

.info-content img {
    max-width: 250px;
    height: 200px;
    object-fit: contain;
    display: block;
}


.info-text {
    flex: 1;
}

.info-text h3 {
    color: #2C3C64;
    margin-bottom: 10px;
    font-size: 24px;
    border-bottom: 6px solid #C55359;
    font-family: "Obvia Narrow", sans-serif;
    font-weight: 600;
}

.info-text p {
    font-size: 16px;
    font-family: "Obvia Narrow", sans-serif;
    font-weight: 400;
    line-height: 22px;
    color: #828282;
}

.read-more {
    font-size: 16px;
    color: #C55359;
    font-weight: bold;
    text-decoration: none;
}

.read-more:hover {
    text-decoration: underline;
}

/* Responsive Layout */
@media (max-width: 768px) {
    .info-section {
        grid-template-columns: 1fr;
    }
    .info-content {
        flex-direction: column;
        text-align: center;
    }
    .info-content img {
        margin-bottom: 10px;
    }
}
</style>
@endsection
