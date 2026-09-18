@extends('layout.frontEnd')

@section('title', 'Get ClassifIeD | Background screening')
@section('meta_keywords', 'Get ClassifIeD, background screening, employee screening, online DBS check, BPSS clearance, ID verification software, security clearance, pre-employment screening, HR screening solutions')


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
    <!-- Hero Section -->
    <div class="row align-items-center pageBanner" data-aos="fade-up">
        <div class="col-lg-6 text-white textBannerSection">
            <h1 class="display-5 fw-bold mb-4">Let’s, Get ClassifIeD</h1>
            <p class="lead mb-4">Automated pre-employment screening,<br />in hours, not days</p>
            <a href="{{ env('APP_URL') }}ContactUs" class="btn expertBtnFront" style="margin-right: 10px;">Speak to an expert</a>
            <a href="{{ env('APP_URL') }}login" class="btn loginBtnFront">Login</a>
        </div>
        <div class="col-lg-6 text-end pe-0">
            <img rel="preload" as="image" src="{{ env('APP_URL') }}images/FrontEnd/banner-lady-ipad.webp" alt="Woman holding an Ipad" class="img-fluid" loading="eager" fetchpriority="high">
        </div>
    </div>

    <!-- Service Description Section -->
    <div class="Title-TextBlock" data-aos="fade-in">
        <h2>Choose the services that<br><span>meet your requirements</span></h2>
        <p>
            Integrate our screening service and extend the capabilities of your HR management and hiring teams.
            <br /><br />
            Our pre-employment screening services give you the freedom to make hiring faster. Choose from a selection of checks that suit your requirements.
        </p>
    </div>
</div>

<div class="scrolling-carousel-container">
    <h2 class="text-center">Explore Our Screening Services</h2>
    <div class="scrolling-carousel">
        <div class="scrolling-carousel-track">
            <!-- Duplicate items to create a seamless loop -->
            <div class="scrolling-item">
                <i class="fas fa-user-shield"></i>
                <h3>Criminal Record Checks</h3>
                <p>Ensure candidates have no criminal records affecting their suitability.</p>
            </div>
            <div class="scrolling-item">
                <i class="fas fa-id-badge"></i>
                <h3>Digital Identity Verification</h3>
                <p>Confirm candidate identity to reduce fraud risk.</p>
            </div>
            <div class="scrolling-item">
                <i class="fas fa-briefcase"></i>
                <h3>Employment History</h3>
                <p>Validate previous employment details and references.</p>
            </div>
            <div class="scrolling-item">
                <i class="fas fa-graduation-cap"></i>
                <h3>Academic History</h3>
                <p>Verify the candidate's educational background.</p>
            </div>
            <div class="scrolling-item">
                <i class="fas fa-user-friends"></i>
                <h3>Personal References</h3>
                <p>Check credibility through personal references.</p>
            </div>
            <div class="scrolling-item">
                <i class="fas fa-passport"></i>
                <h3>Right to Work</h3>
                <p>Ensure candidates have the lawful authority to work in the United Kingdom.</p>
            </div>

            <!-- Duplicate set for seamless loop -->
            <div class="scrolling-item">
                <i class="fas fa-user-shield"></i>
                <h3>Criminal Record Checks</h3>
                <p>Ensure candidates have no criminal records affecting their suitability.</p>
            </div>
            <div class="scrolling-item">
                <i class="fas fa-id-badge"></i>
                <h3>Digital Identity Verification</h3>
                <p>Confirm candidate identity to reduce fraud risk.</p>
            </div>
            <div class="scrolling-item">
                <i class="fas fa-briefcase"></i>
                <h3>Employment History</h3>
                <p>Validate previous employment details and references.</p>
            </div>
            <div class="scrolling-item">
                <i class="fas fa-graduation-cap"></i>
                <h3>Academic History</h3>
                <p>Verify the candidate's educational background.</p>
            </div>
            <div class="scrolling-item">
                <i class="fas fa-user-friends"></i>
                <h3>Personal References</h3>
                <p>Check credibility through personal references.</p>
            </div>
            <div class="scrolling-item">
                <i class="fas fa-passport"></i>
                <h3>Right to Work</h3>
                <p>Ensure candidates have the lawful authority to work in the United Kingdom.</p>
            </div>

            <!-- Duplicate set for seamless loop -->
            <div class="scrolling-item">
                <i class="fas fa-user-shield"></i>
                <h3>Criminal Record Checks</h3>
                <p>Ensure candidates have no criminal records affecting their suitability.</p>
            </div>
            <div class="scrolling-item">
                <i class="fas fa-id-badge"></i>
                <h3>Digital Identity Verification</h3>
                <p>Confirm candidate identity to reduce fraud risk.</p>
            </div>
            <div class="scrolling-item">
                <i class="fas fa-briefcase"></i>
                <h3>Employment History</h3>
                <p>Validate previous employment details and references.</p>
            </div>
            <div class="scrolling-item">
                <i class="fas fa-graduation-cap"></i>
                <h3>Academic History</h3>
                <p>Verify the candidate's educational background.</p>
            </div>
            <div class="scrolling-item">
                <i class="fas fa-user-friends"></i>
                <h3>Personal References</h3>
                <p>Check credibility through personal references.</p>
            </div>
            <div class="scrolling-item">
                <i class="fas fa-passport"></i>
                <h3>Right to Work</h3>
                <p>Ensure candidates have the lawful authority to work in the United Kingdom.</p>
            </div>

            <!-- Duplicate set for seamless loop -->
            <div class="scrolling-item">
                <i class="fas fa-user-shield"></i>
                <h3>Criminal Record Checks</h3>
                <p>Ensure candidates have no criminal records affecting their suitability.</p>
            </div>
            <div class="scrolling-item">
                <i class="fas fa-id-badge"></i>
                <h3>Digital Identity Verification</h3>
                <p>Confirm candidate identity to reduce fraud risk.</p>
            </div>
            <div class="scrolling-item">
                <i class="fas fa-briefcase"></i>
                <h3>Employment History</h3>
                <p>Validate previous employment details and references.</p>
            </div>
            <div class="scrolling-item">
                <i class="fas fa-graduation-cap"></i>
                <h3>Academic History</h3>
                <p>Verify the candidate's educational background.</p>
            </div>
            <div class="scrolling-item">
                <i class="fas fa-user-friends"></i>
                <h3>Personal References</h3>
                <p>Check credibility through personal references.</p>
            </div>
            <div class="scrolling-item">
                <i class="fas fa-passport"></i>
                <h3>Right to Work</h3>
                <p>Ensure candidates have the lawful authority to work in the United Kingdom.</p>
            </div>
        </div>
    </div>
</div>


<div class="container py-5">

    <div class="container py-5">
        <div class="row align-items-center">
            <div class="col-lg-4" data-aos="fade-right">
                <h2 class="contactUnderline">Your Digital Partner</h2>
                <ul class="list-unstyled">
                    <li class="d-flex align-items-center">
                        <i class="bi bi-check-circle" style="color: #C55359; margin-right: 8px;"></i> Making hiring safer and more reliable
                    </li>
                    <li class="d-flex align-items-center">
                        <i class="bi bi-check-circle" style="color: #C55359; margin-right: 8px;"></i> Strengthen your customer and client trust
                    </li>
                    <li class="d-flex align-items-center">
                        <i class="bi bi-check-circle" style="color: #C55359; margin-right: 8px;"></i> Reduce workplace fraud and theft
                    </li>
                    <li class="d-flex align-items-center">
                        <i class="bi bi-check-circle" style="color: #C55359; margin-right: 8px;"></i> Ensure integrity by knowing who you hire
                    </li>
                </ul>
            </div>
            <div class="col-lg-8 text-end" data-aos="fade-left">
                <img src="{{ env('APP_URL') }}images/FrontEnd/PSP.webp" alt="Image of a building" class="img-fluid rounded" style="max-height: 350px; width: 100%; object-fit: cover;">
            </div>
        </div>
        <div class="row text-center mt-5">
            <div class="col-md-3 col-6">
                <div class="p-3 border rounded shadow-sm d-flex align-items-center justify-content-center" style="height: 150px;">
                    <img src="{{ env('APP_URL') }}images/FrontEnd/essentialsPlus.webp" alt="Cyber Essentials Plus Certification logo" class="img-fluid" style="max-width: 175px;">
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="p-3 border rounded shadow-sm d-flex align-items-center justify-content-center" style="height: 150px;">
                    <img src="{{ env('APP_URL') }}images/FrontEnd/ISO9001.webp" alt="ISO 9001 Certification logo" class="img-fluid" style="max-width: 120px;">
                </div>
            </div>
            <div class="col-md-3 col-6 mt-3 mt-md-0">
                <div class="p-3 border rounded shadow-sm d-flex align-items-center justify-content-center" style="height: 150px;">
                    <img src="{{ env('APP_URL') }}images/FrontEnd/CyberAssurance.webp" alt="IASME Cyber Assurance logo" class="img-fluid" style="max-width: 175px;">
                </div>
            </div>
            <div class="col-md-3 col-6 mt-3 mt-md-0">
                <div class="p-3 border rounded shadow-sm d-flex align-items-center justify-content-center" style="height: 150px;">
                    <img src="{{ env('APP_URL') }}images/FrontEnd/ISO27001.webp" alt="ISO 27001 Certification logo" class="img-fluid" style="max-width: 120px;">
                </div>
            </div>
        </div>
    </div>

    <div class="py-5 demoSection">
        <h2 class="text-center">Interactive Demo</h2>
        <div class="demo-toggle">
            <div class="toggle-container">
                <div class="toggle-background"></div>
                <button class="toggle-btn active" id="clientPortal">Client Portal</button>
                <button class="toggle-btn" id="candidatePortal">Candidate Portal</button>
            </div>
        </div>
        <script async src="https://js.storylane.io/js/v2/storylane.js"></script>
        <div class="sl-embed" style="position:relative;padding-bottom:calc(48.45% + 25px);width:90%;margin-left:5%;height:0;transform:scale(1)">
            <iframe title="Interactive Demonstration" loading="lazy" class="sl-demo" id="demoFrame" src="https://getclassified.storylane.io/demo/k7ulia3eklut?embed=inline" name="sl-embed" allow="fullscreen" allowfullscreen style="position:absolute;top:0;left:0;width:100%!important;height:92%!important;border:1px solid rgba(63,95,172,0.35);box-shadow: 0px 0px 18px rgba(26, 19, 72, 0.15);border-radius:10px;box-sizing:border-box;"></iframe>
        </div>
    </div>

    <div class="Title-TextBlock"  data-aos="fade-in">
        <h2>Comprehensive <span>Managed Service</span></h2>
        <p>Concentrate on what matters most while we handle the intricacies of screening on your behalf. If you or your candidates require assistance at any point, our committed team is ready to provide unwavering support.</p>
    </div>

    <div class="py-4 mb-4" data-aos="fade-up">
        <h2 class="text-center mb-4" style="color: #2C3C64;">Latest ClassifIeD News</h2>
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4 mt-2">
            @foreach ($latestPosts as $post)
                <div class="col">
                    <div class="card h-100 shadow-sm border-0">
                        <img loading="lazy" src="{{ asset($post->image_path) }}" class="card-img-top" alt="{{ $post->title }}" style="object-fit: cover; height: 200px;">
                        <div class="card-body d-flex flex-column">
                            <p class="text-muted text-end mb-2">{{ \Carbon\Carbon::parse($post->published_at)->format('d M Y') }}</p>
                            <h5 class="card-title" style="color: #2C3C64;">{{ $post->title }}</h5>
                            <p class="card-text">{!! $post->description !!}</p>
                            <div class="mt-auto">
                                <a href="{{ url('blog/' . $post->slug) }}" class="btn btn-outline-primary btn-sm mt-3">Read more <i class="fa-solid fa-arrow-right"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="text-center">
            <a href="{{ env('APP_URL') }}Blog" class="btn btn-outline-secondary btn-md mt-3">See more News <i class="fa-solid fa-arrow-right"></i></a>
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

<style>
    .demoSection {
        background-color: #f9f9f9;
        border-radius: 25px;
    }

    .row.text-center img {
        transition: transform 0.3s ease-in-out;
    }

    .row.text-center img:hover {
        transform: scale(1.2);
    }

    
    .demo-toggle {
        display: flex;
        justify-content: center;
        margin: 20px 0;
    }
    .toggle-container {
        display: flex;
        border: 2px solid #C55359; /* Outline in desired color */
        border-radius: 50px;
        padding: 5px;
        width: 350px;
        position: relative;
    }

    .toggle-background {
        position: absolute;
        top: 5px;
        left: 5px;
        width: calc(50% - 5px); /* Half the toggle width */
        height: calc(100% - 10px); /* Keep it inside the container */
        background: #C55359;;
        border-radius: 50px;
        transition: transform 0.3s ease-in-out;
    }

    .toggle-btn {
        flex: 1;
        background: transparent;
        border: none;
        color: #333;
        font-weight: bold;
        padding: 10px;
        cursor: pointer;
        font-size: 16px;
        border-radius: 50px;
        z-index: 10;
        transition: all 0.3s ease-in-out;
    }
    .toggle-btn:hover {
        color: #C55359;
    }

    .toggle-btn.active {
        color: white; 
    }

   /* Scrolling Carousel Container */
    .scrolling-carousel-container {
        position: relative;
        max-width: 100%;
        margin: 0px auto 50px auto;
        overflow: hidden;
        padding: 40px 0; /* Added more space above and below */
        background-color: #f9f9f9;
        text-align: center;
    }

    /* Title Styling */
    .scrolling-carousel-container h2 {
        margin-bottom: 30px; /* Added more space between title and boxes */
    }

    /* Scrolling Track */
    .scrolling-carousel {
        display: flex;
        overflow: hidden;
        width: 100%;
        padding: 10px 0;
    }

    .scrolling-carousel-track {
        display: flex;
        width: 400%; /* Ensures smooth looping */
        gap: 40px; /* Increased gap between boxes */
        animation: scrollAnimation 50s linear infinite;
    }

    /* Individual Scroll Item */
    .scrolling-item {
        flex: 0 0 auto;
        width: 240px; /* Slightly bigger boxes */
        background: white;
        box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
        padding: 20px;
        text-align: center;
        border-radius: 10px;
        white-space: normal; /* Prevents text overflow */
        word-wrap: break-word;
    }

    /* FontAwesome Icons */
    .scrolling-item i {
        font-size: 45px;
        color: #C55359;
        margin-bottom: 15px;
    }

    /* Text Styling */
    .scrolling-item h3 {
        font-size: 17px;
        color: #2C3C64;
        margin-bottom: 10px;
    }

    .scrolling-item p {
        font-size: 14px;
        color: #666;
        margin: 0;
    }

    .btn-outline-primary {
        color: #C55359 !important;
        border-color: #C55359 !important;
    }
    .btn-outline-primary:hover {
        background-color: #C55359 !important;
        color: white !important;
    }

    .btn-outline-secondary {
        color: #2C3C64 !important;
        border-color: #2C3C64 !important;
    }
    .btn-outline-secondary:hover {
        background-color: #2C3C64 !important;
        color: white !important;
    }

    /* Animation */
    @keyframes scrollAnimation {
        from { transform: translateX(0); }
        to { transform: translateX(-50%); } /* Moves only half to loop seamlessly */
    }

    /* Responsive */
    @media (max-width: 768px) {
        .scrolling-item {
            width: 200px;
        }

        .scrolling-item i {
            font-size: 35px;
        }

        .scrolling-carousel-track {
            gap: 30px; /* Slightly smaller gaps on mobile */
        }
    }

</style>

<script> 
    document.getElementById('clientPortal').addEventListener('click', function() {
        document.querySelector('.toggle-background').style.transform = 'translateX(0)';
        document.getElementById('demoFrame').src = 'https://getclassified.storylane.io/demo/k7ulia3eklut?embed=inline';
    });

    document.getElementById('candidatePortal').addEventListener('click', function() {
        document.querySelector('.toggle-background').style.transform = 'translateX(100%)';
        document.getElementById('demoFrame').src = 'https://getclassified.storylane.io/demo/m9xpokji6czg?embed=inline';
    });

    document.querySelectorAll('.toggle-btn').forEach(button => {
        button.addEventListener('click', function() {
            document.querySelectorAll('.toggle-btn').forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
        });
    });

</script>
@endsection