@extends('layout.frontEnd')

@section('title', 'Sitemap | ClassifIeD')
@section('meta_description', 'A quick overview of all the pages you can find information on.')


@section('content')
<div class="container py-5 px-5">
    <h2 class="fw-bold">Site Map</h2>
    <br />
    <ul class="sitemap">
        <li><a href="{{ env('APP_URL') }}login">Get ClassifIeD login</a></li>
        <li><a href="{{ env('APP_URL') }}">Home Page</a></li>
        <li><a href="{{ env('APP_URL') }}Services">Services</a>
            <ul>
                <li><a href="{{ env('APP_URL') }}Services/DBS-Basic">DBS - Basic</a></li>
                <li><a href="{{ env('APP_URL') }}Services/BPSS">BPSS Check</a></li>
                <li><a href="{{ env('APP_URL') }}Services/Digital-ID-Verification">Digital ID Verification</a></li>
                <li><a href="{{ env('APP_URL') }}Services/right-to-work">Right to Work (RTW)</a></li>
                <li><a href="{{ env('APP_URL') }}Services/academic-history">Academic History</a></li>
                <li><a href="{{ env('APP_URL') }}Services/employment-history">Employment History</a></li>
                <li><a href="{{ env('APP_URL') }}Services/personal-references">Personal References</a></li>
            </ul>
        </li>
        <li><a href="{{ env('APP_URL') }}Resources">Resources</a>
            <ul>
                <li><a href="{{ env('APP_URL') }}Resources/glossary">Glossary</a></li>
                <li><a href="{{ env('APP_URL') }}sample-report.pdf">Sample Report</a></li>
            </ul>
        </li>
        <li><a href="{{ env('APP_URL') }}Candidates">Candidate Information</a></li>
        <li><a href="{{ env('APP_URL') }}Clients">Client Information</a></li>
        <li><a href="{{ env('APP_URL') }}AboutUs">About Us</a>
            <ul>
                <li><a href="{{ env('APP_URL') }}AboutUs/security-at-classified">Security & Compliance</a></li>
                <li><a href="{{ env('APP_URL') }}AboutUs/Certifications">Certifications and Memberships</a></li>
            </ul>
        </li>
        <li><a href="{{ env('APP_URL') }}ContactUs">Contact Us</a></li>
        <li><a href="{{ env('APP_URL') }}Blog">Blog</a>
            <ul>
                @foreach ($blogPosts as $post)
                    <li><a href="{{ url('blog/' . $post->slug) }}">{{ $post->title }}</a></li>
                @endforeach
            </ul>
        </li>
        <li><a href="{{ env('APP_URL') }}Contact/accessibility-feedback">Accessibility Feedback</a></li>
        <li><a href="{{ env('APP_URL') }}privacypolicy.pdf" target="_blank">Privacy Policy</a></li>
    </ul>
</div>

<style>
    .sitemap {
        list-style: none;
        padding-left: 0;
    }

    .sitemap li {
        position: relative;
        padding-left: 20px;
        margin-bottom: 10px; /* Increased spacing */
    }

    /* Horizontal line connecting item to the tree */
    .sitemap li::before {
        content: "";
        position: absolute;
        left: -10px;
        top: 13px;
        width: 15px;
        height: 1px;
        background-color: lightgray;
        transform: translateY(-50%);
    }

    /* Vertical line extending downward */
    .sitemap li::after {
        content: "";
        position: absolute;
        left: -10px;
        top: 0;
        bottom: -10px;
        width: 1px;
        background-color: lightgray;
    }

    /* Adjust spacing for sub-lists */
    .sitemap ul {
        list-style: none;
        padding-left: 20px;
        margin-top: 12px;
        margin-bottom: 12px;
    }

    .sitemap li:last-child::after {
        bottom: 13px;
    }

    /* Styling links */
    .sitemap a {
        text-decoration: none;
        color: #C55359;
        font-size: 1.1rem;
        transition: 250ms ease;
    }

    .sitemap a:hover {
        color: rgb(149, 56, 59);
        padding-left: 7px;
    }


</style>
@endsection
