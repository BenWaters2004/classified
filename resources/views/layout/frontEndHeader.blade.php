<style>
    /* General Styling */
    body {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    /* Logo Styling */
    .logo img {
        max-height: 50px;
    }

    .logo span {
        font-size: 0.9rem;
        color: #2C3C64;
        font-weight: 600;
    }

    /* Navbar Styling */
    .navbar {
        background-color: #fff;
        padding: 15px 0;
    }

    .navbar .textMain {
        color: #2C3C64;
        font-weight: 600;
        font-size: 18px;
        text-decoration: none;
    }

    .navbar .textMain:hover {
        color: #C55359;
        transition: 300ms;
    }

    /* Bottom Navigation Bar */
    .bottomNav {
        background-color: #2C3C64;
        padding: 8px 0;
        position: sticky;
        top: 0;
        z-index: 1000;
    }

    /* Navigation Links */
    .nav {
        display: flex;
        justify-content: center;
        align-items: center;
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .nav-item {
        margin: 0 15px;
    }

    .nav-link {
        font-size: 0.9rem;
        font-weight: bold;
        color: white !important;
        position: relative;
        text-decoration: none;
    }

    .nav-link:not(.btn)::after {
        content: "";
        position: absolute;
        left: 0;
        bottom: -2px;
        width: 0;
        height: 8px;
        background-color: #C55359;
        transition: width 0.3s ease;
    }

    .nav-link:not(.btn):hover::after {
        width: 100%;
    }

    .nav-link.btn {
        background-color: #C55359;
        padding: 5px 23px;
        border-radius: 20px;
    }
    .nav-link.btn:hover {
        background-color: #a94448;
        border-radius: 10px;
    }

    /* Adjust top navbar styling */
    .navbar .container {
        display: flex;
        justify-content: space-between;
        align-items: center;
        position: relative;
    }

    .navbar .contact-info {
        margin-left: auto; /* Push the contact info to the right */
        display: flex;
        align-items: center;
    }

    .navbar .contact-info a {
        margin-left: 15px; /* Add spacing between items */
    }


    /* Burger Menu Styling */
    .burger {
        display: none;
        flex-direction: column;
        justify-content: space-between;
        align-items: center;
        width: 25px;
        height: 20px;
        cursor: pointer;
        margin: 0 auto; /* Center the burger icon */
    }

    .burger span {
        display: block;
        width: 100%;
        height: 3px;
        background: white;
        transition: all 0.3s ease;
    }

    /* Burger Animation */
    .burger.open span:nth-child(1) {
        transform: translateY(8px) rotate(45deg);
    }

    .burger.open span:nth-child(2) {
        opacity: 0;
    }

    .burger.open span:nth-child(3) {
        transform: translateY(-8px) rotate(-45deg);
    }

    /* Mobile Navigation */
    .nav.mobile {
        flex-direction: column;
        background-color: #2C3C64;
        position: absolute;
        top: 100%; /* Adjust to the bottom */
        right: 0;
        left: 0;
        height: 0; /* Start with no height */
        overflow: hidden;
        transition: height 0.3s ease-in-out;
        padding: 0 15px;
    }

    .nav.mobile.active {
        height: auto; /* Allow the menu to grow with content */
    }

    .nav.mobile .nav-item {
        margin: 15px 0; /* Vertical spacing for each link */
        text-align: left; /* Align text to the left */
    }

    @media (max-width: 992px) {
        .nav {
            display: none;
        }

        .nav.mobile {
            display: flex;
        }

        .burger {
            display: flex;
        }

        .bottomNav .container {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
    }

    @media (max-width: 768px) {
        .mobileHide {
            display: none;
        }
    }

    @media (max-width: 458px) {
        .mobileSmallHide {
            display: none;
        }
    }

    /* Dropdown container */
    .nav-item.dropdown {
        position: relative;
    }

    /* Hide by default */
    .nav-item .dropdown-menu {
        display: none;
        position: absolute;
        top: 100%;
        left: 0;
        background-color: #2C3C64;
        padding: 10px 0;
        min-width: 200px;
        z-index: 999;
        box-shadow: 0px 8px 16px rgba(0, 0, 0, 0.2);
    }

    /* Show on hover */
    .nav-item.dropdown:hover .dropdown-menu {
        display: block;
    }

    /* Links inside dropdown */
    .dropdown-menu a {
        display: block;
        padding: 8px 20px;
        color: white;
        text-decoration: none;
        font-size: 0.9rem;
        font-weight: 500;
        white-space: nowrap;
    }

    .dropdown-menu a:hover {
        background-color: #C55359;
        color: white;
    }

    /* Dropdown container must be positioned */
    .nav-item.dropdown {
        position: relative;
    }

    /* Style the submenu */
    .nav-item .dropdown-menu {
        display: none;
        position: absolute;
        top: 100%;
        left: 0;
        background-color: #2C3C64;
        opacity: 0.95;
        padding: 10px 0;
        min-width: 200px;
        z-index: 1000;
        box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.15);
    }

    /* Show dropdown on hover */
    .nav-item.dropdown:hover .dropdown-menu {
        display: block;
    }

    /* Dropdown link styling */
    .dropdown-menu li a {
        display: block;
        padding: 8px 20px;
        color: white;
        text-decoration: none;
        font-size: 0.9rem;
        font-weight: 500;
        white-space: nowrap;
    }

    .dropdown-menu li a:hover {
        background-color: #C55359;
    }

    .submenu {
        display: none;
        padding-left: 20px;
        margin-top: 5px;
    }

    .nav-item.expandable.active .submenu {
        display: block;
    }

    .toggle-submenu {
        background: none;
        border: none;
        color: white;
        font-weight: bold;
        font-size: 0.9rem;
        text-align: center;
        padding: 0;
        width: 100%;
        cursor: pointer;
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 10px;
        padding-bottom: 8px;
    }

    .toggle-submenu:focus {
        outline: none;
    }

    .submenu a {
        font-size: 0.85rem;
        display: block;
        padding: 8px 0;
        color: #ccc;
        text-decoration: none;
    }

    .toggle-submenu .fa-chevron-down {
        transition: transform 0.3s ease;
    }

    .nav-item.expandable.active .toggle-submenu .fa-chevron-down {
        transform: rotate(180deg);
    }
</style>


<nav class="navbar" role="navigation">
    <div class="container">
        <!-- Logo -->
        <a class="logo text-decoration-none" href="{{ env('APP_URL') }}">
            @if (env('APP_ENV') == 'production')
                <img rel="preload" as="image" class="img-responsive logo" src="{{ env('APP_URL') }}images/logo_{{ str_replace(' ', '', strtolower(env('APP_NAME'))) }}.webp" alt="Logo" loading="eager" fetchpriority="high"/>
            @else
                <img class="img-responsive logo" src="{{ env('APP_URL') }}images/logo_demo.png" alt="Demo Logo"/>
            @endif
            <span class="mobileHide">Personal Clearance. Done.</span>
        </a>

        <!-- Contact Info -->
        <div class="contact-info">
            <a class="textMain me-3 mobileSmallHide" href="Tel: +44 (0)1752 724 000">Tel: +44 (0)1752 724 000</a>
            <a href="https://www.linkedin.com/showcase/bluescreenit-cyber-security-services/" target="_blank" class="textMain mobileHide" title="Linkedin">
                <i class="bi bi-linkedin"></i>
                <span class="sr-only">Linkedin</span>
            </a>
        </div>
    </div>
</nav>


<div class="bottomNav">
    <div class="container">
        <!-- Burger Icon -->
        <div class="burger" id="burgerMenu" onclick="toggleMenu()">
            <span></span>
            <span></span>
            <span></span>
        </div>

        <!-- Desktop Navigation -->
        <ul class="nav justify-content-center" id="desktopNav">
            <li class="nav-item">
                <a class="nav-link" href="{{ env('APP_URL') }}">Home</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ env('APP_URL') }}Services">Services</a>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link" href="{{ env('APP_URL') }}Resources">Resources</a>
                <ul class="dropdown-menu">
                    <li><a href="{{ env('APP_URL') }}Blog">Blog</a></li>
                    <li><a href="{{ env('APP_URL') }}Resources/glossary">Glossary</a></li>
                    <li><a href="{{ env('APP_URL') }}sample-report.pdf" target="_blank">Sample Report</a></li>
                </ul>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ env('APP_URL') }}Candidates">Candidates</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ env('APP_URL') }}Clients">Clients</a>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link" href="{{ env('APP_URL') }}AboutUs">About Us</a>
                <ul class="dropdown-menu">
                    <li><a href="{{ env('APP_URL') }}AboutUs">Who are we</a></li>
                    <li><a href="{{ env('APP_URL') }}AboutUs/security-at-classified">Security at Classified</a></li>
                    <li><a href="{{ env('APP_URL') }}AboutUs/Certifications">Certifications and Memberships</a></li>
                </ul>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ env('APP_URL') }}ContactUs">Contact Us</a>
            </li>
            <li class="nav-item">
                <a class="nav-link btn" href="{{ env('APP_URL') }}login">Login</a>
            </li>
        </ul>

        <!-- Mobile Navigation -->
        <ul class="nav mobile" id="mobileNav">
            <li class="nav-item">
                <a class="nav-link" href="{{ env('APP_URL') }}">Home</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ env('APP_URL') }}Services">Services</a>
            </li>

            <li class="nav-item expandable">
                <button class="nav-link toggle-submenu" type="button">
                    Resources <i class="fa fa-chevron-down"></i>
                </button>
                <ul class="submenu">
                    <li><a href="{{ env('APP_URL') }}Blog">Blog</a></li>
                    <li><a href="{{ env('APP_URL') }}Resources/glossary">Glossary</a></li>
                    <li><a href="{{ env('APP_URL') }}sample-report.pdf" target="_blank">Sample Report</a></li>
                </ul>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="{{ env('APP_URL') }}Candidates">Candidates</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ env('APP_URL') }}Clients">Clients</a>
            </li>

            <li class="nav-item expandable">
                <button class="nav-link toggle-submenu" type="button">
                    About Us <i class="fa fa-chevron-down"></i>
                </button>
                <ul class="submenu">
                    <li><a href="{{ env('APP_URL') }}AboutUs">Who are we</a></li>
                    <li><a href="{{ env('APP_URL') }}AboutUs/security-at-classified">Security at Classified</a></li>
                    <li><a href="{{ env('APP_URL') }}AboutUs/Certifications">Certifications and Memberships</a></li>
                </ul>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="{{ env('APP_URL') }}ContactUs">Contact Us</a>
            </li>
            <li class="nav-item">
                <a class="nav-link btn" href="{{ env('APP_URL') }}login">Login</a>
            </li>
        </ul>
    </div>
</div>

<script>
    function toggleMenu() {
        const mobileNav = document.getElementById("mobileNav");
        const burgerMenu = document.getElementById("burgerMenu");

        mobileNav.classList.toggle("active");
        burgerMenu.classList.toggle("open");
    }

    // Toggle submenus and rotate chevron
    document.addEventListener("DOMContentLoaded", function () {
        document.querySelectorAll(".toggle-submenu").forEach(function (btn) {
            btn.addEventListener("click", function () {
                const parent = btn.closest(".expandable");
                parent.classList.toggle("active");
            });
        });
    });
</script>
