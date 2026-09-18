@extends('layout.frontEnd')

@section('title', 'Security & Compliance | ClassifIeD')
@section('meta_keywords', 'Secure background checks, certified employment screening, nist compliant employee screening platform, ISO 27001 screening solution')
@section('meta_description', 'Cyber Security is paramount to our solution, with the service being run by BIT Security a Cyber Security SOC.')


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
    <div class="securityWrapper">
        <!-- Lottie Script -->
        <script src="https://unpkg.com/@lottiefiles/dotlottie-wc@0.6.2/dist/dotlottie-wc.js" type="module"></script>

        <!-- Hero Section -->
        <div class="securityHero d-flex flex-column flex-md-row align-items-center justify-content-between" data-aos="fade-up">
            <div class="securityText mb-4 mb-md-0">
                <h1 class="securityTitle display-5 fw-bold mb-4" style="font-size: 48px;">Security & Compliance</h1>
                <p class="securitySubtitle" style="font-size: 20px;">Protecting data with cutting-edge security measures.</p>
            </div>
            <dotlottie-wc src="https://lottie.host/71001b21-0d20-446f-827f-689f4cc681a1/IOTKqBWg8k.lottie" 
                        style="width: 350px; height: 350px;" 
                        speed="1" autoplay loop>
            </dotlottie-wc>
        </div>

        <div style="max-width: 1100px; margin-inline: auto;">
            <!-- Security Overview -->
            <div class="securityOverview fade-in">
                <p>
                    Security is at the core of our pre-employment screening application. As a cybersecurity company, we ensure your data is protected, compliant, and accessible with state-of-the-art security measures. Our approach covers physical, personnel, and digital security to provide end-to-end protection.
                </p>
            </div>
    
            <!-- Security Categories -->
            <div class="securitySections">
                <div class="securityCard fade-in">
                    <div class="iconContainer"><i class="fas fa-building"></i></div>
                    <h3>Physical Security</h3>
                    <ul>
                        <li>24/7 security staff & CCTV</li>
                        <li>Double-door systems & biometric access</li>
                        <li>Intrusion detection & alarm monitoring</li>
                        <li>Restricted access with card readers</li>
                    </ul>
                </div>
                <div class="securityCard fade-in">
                    <div class="iconContainer"><i class="fas fa-server"></i></div>
                    <h3>Availability</h3>
                    <ul>
                        <li>99%+ system uptime</li>
                        <li>Redundant infrastructure</li>
                        <li>Continuous monitoring & failover</li>
                    </ul>
                </div>
                <div class="securityCard fade-in">
                    <div class="iconContainer"><i class="fas fa-user-shield"></i></div>
                    <h3>Personnel Security</h3>
                    <ul>
                        <li>All employees hold BPSS clearance</li>
                        <li>Software oversight by UK SC-cleared personnel</li>
                    </ul>
                </div>
                <div class="securityCard fade-in">
                    <div class="iconContainer"><i class="fas fa-lock"></i></div>
                    <h3>Access Control</h3>
                    <ul>
                        <li>Multi-Factor Authentication (MFA)</li>
                        <li>Role-based & organisation-based access control</li>
                        <li>Administrative delegation & separation</li>
                    </ul>
                </div>
                <div class="securityCard fade-in">
                    <div class="iconContainer"><i class="fas fa-check-circle"></i></div>
                    <h3>Certifications & Compliance</h3>
                    <ul>
                        <li>ISO 9001 Certified</li>
                        <li>Cyber Essentials Plus</li>
                        <li>IASME Cyber Assurance</li>
                        <li>ISO 27001 Certified</li>
                        <li>Aligned with NIST Standards</li>
                    </ul>
                </div>
            </div>

            <!-- Certification Section -->
            <div class="certificationSection fade-in">
                <h3>Industry Certifications</h3>
                <div class="certificationGrid">
                    <img src="{{ env('APP_URL') }}images/FrontEnd/essentialsPlus.webp" alt="Cyber Essentials Plus">
                    <img src="{{ env('APP_URL') }}images/FrontEnd/ISO9001.webp" alt="ISO 9001">
                    <img src="{{ env('APP_URL') }}images/FrontEnd/CyberAssurance.webp" alt="IASME Cyber Assurance">
                    <img src="{{ env('APP_URL') }}images/FrontEnd/ISO27001.webp" alt="ISO 27001 Certification logo">
                </div>
                <br />
                <a href="{{ env('APP_URL') }}AboutUs/Certifications" class="viewMore">View More <i class="fa-solid fa-arrow-right"></i></a>
            </div>
        </div>

        <!-- Contact Section -->
        <div class="securityContact fade-in">
            <h3>Have Questions About Security?</h3>
            <p>Get in touch with our security team to learn more about how we protect your data.</p>
            <a href="{{ env('APP_URL') }}ContactUs" class="btn btnSecurity">Contact Us</a>
        </div>
    </div>
</div>
@endsection

@section('pageCSS')
<style>
/* General Security Page Styling */
.securityWrapper {
    margin: auto;
    text-align: center;
}

/* Hero Section */
.securityHero {
    background-color: #2C3C64;
    color: white;
    padding: 50px;
    margin-bottom: 40px;
    display: flex;
    flex-direction: column;
    gap: 30px;
    text-align: start;
}

@media (min-width: 768px) {
    .securityHero {
        flex-direction: row;
        align-items: center;
        justify-content: space-between;
    }
}

.securityText {
    max-width: 600px;
}

.securityTitle {
    font-size: 36px;
    font-weight: bold;
    margin-bottom: 10px;
}

.securitySubtitle {
    font-size: 18px;
    opacity: 0.8;
}

/* Security Overview */
.securityOverview {
    font-size: 18px;
    color: #2C3C64;
    margin-bottom: 30px;
}

/* Security Sections */
.securitySections {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
    justify-content: center;
}

.securityCard {
    background: white;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    width: 300px;
    text-align: left;
}

.iconContainer {
    font-size: 40px;
    color: #C55359;
    margin-bottom: 15px;
}

.securityCard h3 {
    font-size: 20px;
    margin-bottom: 10px;
    color: #2C3C64;
}

.securityCard ul {
    padding: 0;
    list-style-type: none;
    font-size: 16px;
    color: #555;
}

.securityCard ul li {
    margin-bottom: 8px;
}

/* Certification Section */
.certificationSection {
    margin-top: 50px;
}

.certificationGrid {
    display: flex;
    justify-content: center;
    gap: 20px;
    margin-top: 20px;
}

.certificationGrid img {
    max-height: 100px;
    width: auto;
    transition: transform 0.3s ease-in-out;
}

.certificationGrid img:hover {
    transform: scale(1.1);
}

/* Contact Section */
.securityContact {
    background: #2C3C64;
    color: white;
    padding: 30px;
    border-radius: 8px;
    margin-top: 50px;
}

.securityContact h3 {
    font-size: 24px;
    margin-bottom: 10px;
}

.securityContact p {
    font-size: 16px;
    margin-bottom: 20px;
}

.btnSecurity {
    background: #C55359;
    color: white;
    padding: 12px 20px;
    text-decoration: none;
    font-weight: bold;
    border-radius: 5px;
    transition: 0.3s;
}

.btnSecurity:hover {
    background: #9e424b;
}

/* Page Animations */
.fade-in {
    opacity: 0;
    transform: translateY(30px);
    transition: opacity 0.6s ease-out, transform 0.6s ease-out;
}

.fade-in.visible {
    opacity: 1;
    transform: translateY(0);
}

.viewMore {
    color: #C55359;
    text-align: center;
}

/* Responsive */
@media (max-width: 768px) {
    .securitySections {
        flex-direction: column;
        align-items: center;
    }

    .certificationGrid {
        flex-direction: column;
        align-items: center;
    }

    .securityCard {
        width: 90%;
    }
}
</style>
@endsection

@section('pageJavascript')
<script>
// Reveal animations when scrolling
document.addEventListener("DOMContentLoaded", function () {
    const elements = document.querySelectorAll('.fade-in');

    function checkScroll() {
        elements.forEach(el => {
            const position = el.getBoundingClientRect().top;
            if (position < window.innerHeight - 50) {
                el.classList.add('visible');
            }
        });
    }

    window.addEventListener('scroll', checkScroll);
    checkScroll();
});
</script>
@endsection
