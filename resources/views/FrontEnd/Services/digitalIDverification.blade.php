@extends('layout.frontEnd')

@section('title', 'Digital Identity Verification | ClassifIeD')
@section('meta_keywords', 'pre-employment screening UK, Get ClassifIeD screening platform, screening software for HR, onboarding security checks, ID verification software, digital ID check UK, identity verification for employees, online ID verification, Yoti integration screening')
@section('meta_description', 'With our integrated IDSP which is government approved, there’s no longer a need for in-person identity verification.')


@section('content')
<div class="container py-5">
    <div class="servicesPages">
        <!-- Top Section -->
        <div class="servicesTop">
            <h1>Digital Identity Verification</h1>
            <p>
                With our integrated IDSP which is government approved, there’s no longer a need for in-person identity verification. The process is quick and seamless—completed in just moments on a mobile device—with results uploaded to our system instantly for your convenience. 
            </p>

            <div class="servicesCTA">
                <a href="{{ env('APP_URL') }}ContactUs" class="btn primaryBtn">Apply for ID Verification</a>
            </div>
        </div>

        <!-- Information Sections -->
        <div class="servicesInfo">
            <div class="infoBox">
                <i class="fas fa-user-check"></i>
                <h3>Who Needs Identity Verification?</h3>
                <p>It's a requirement for all criminal record checks and recommended before any employment.</p>
            </div>
            <div class="infoBox">
                <i class="fas fa-clock"></i>
                <h3>Processing Time</h3>
                <p><strong>Instant</strong> - fully digital with automated verification.</p>
            </div>
            <div class="infoBox">
                <i class="fas fa-file-alt"></i>
                <h3>What's Included?</h3>
                <p>ID Document Authentication, Face Comparison, Liveness check, Third Party identity, Watchlist screening</p>
            </div>
        </div>

        <!-- Glossary Section -->
        <div class="glossary">
            <h3>Glossary</h3>
            <p><strong>Liveness Check</strong> A biometric test ensuring a real person is present during authentication.</p>
            <p><strong>Watchlist Screening</strong> Cross-referencing individuals against global sanctions and watchlists to flag potential risks.</p>
        </div>

        <!-- Related Services -->
        <div class="relatedServices">
            <h3>Related Background Checks</h3>
            <div class="relatedCards">
                <div class="serviceCard">
                    <i class="fas fa-user-shield"></i>
                    <h4>BPSS Check</h4>
                    <p>Right to work, Identity Verification, Criminal Record (Basic Disclosure), and Employment history check.</p>
                    <a href="{{ env('APP_URL') }}Services/BPSS" class="btn smallBtn">Learn More</a>
                </div>
                <div class="serviceCard">
                    <i class="fas fa-user-shield"></i>
                    <h4>UK Criminal Record Basic DBS</h4>
                    <p>The Basic level check, from the Disclosure & Barring Service, provides details of unspent convictions.</p>
                    <a href="{{ env('APP_URL') }}Services/DBS-Basic" class="btn smallBtn">Learn More</a>
                </div>
                <div class="serviceCard">
                    <i class="fas fa-passport"></i>
                    <h4>Right to Work</h4>
                    <p>Ensure candidates have the lawful authority to work in the United Kingdom.</p>
                    <a href="{{ env('APP_URL') }}Services/right-to-work" class="btn smallBtn">Learn More</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('pageCSS')
<style>
/* General Styling */
.servicesPages {
    max-width: 900px;
    margin: auto;
    text-align: center;
}

/* Top Section */
.servicesTop {
    background: #f9f9f9;
    padding: 30px;
    border-radius: 8px;
    box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
    margin-bottom: 40px;
}

.servicesTop h1 {
    color: #2C3C64;
    margin-bottom: 10px;
    border-bottom: 0 !important;
    padding-bottom: 0 !important;
}

.servicesCTA {
    margin-top: 20px;
}

.primaryBtn {
    display: inline-block;
    padding: 12px 20px;
    text-decoration: none;
    font-weight: bold;
    border-radius: 5px;
    transition: 0.3s;
    background: #C55359;
    color: white;
}

.primaryBtn:hover {
    background: #9e424b;
    color: white;
}

/* Information Boxes */
.servicesInfo {
    display: flex;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 20px;
    margin-bottom: 40px;
}

.infoBox {
    flex: 1;
    min-width: 250px;
    background: white;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
    text-align: center;
}

.infoBox i {
    font-size: 40px;
    color: #C55359;
    margin-bottom: 10px;
}

/* Pricing Table */
.pricingTitle {
    margin-top: 40px;
    font-size: 22px;
    color: #2C3C64;
}

.pricing-table {
    width: 100%;
    max-width: 600px;
    margin: 20px auto;
    border-collapse: collapse;
}

.pricing-table td {
    padding: 12px;
    border: 1px solid #ddd;
    text-align: left;
}

.pricing-table tr:last-child {
    font-weight: bold;
}

/* Related Services */
.relatedServices {
    margin-top: 50px;
}

.relatedCards {
    display: flex;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 20px;
}

.serviceCard {
    flex: 1;
    min-width: 250px;
    background: white;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
    text-align: center;
}

.serviceCard i {
    font-size: 35px;
    color: #C55359;
    margin-bottom: 10px;
}

.smallBtn {
    display: inline-block;
    margin-top: 10px;
    padding: 8px 15px;
    background: #2C3C64;
    color: white;
    text-decoration: none;
    font-weight: bold;
    border-radius: 5px;
}

.smallBtn:hover {
    background: #1f2b48;
    color: white;
}

/* Glossary */
.glossary {
    margin-top: 50px;
    text-align: left;
    padding: 20px;
    background: #f9f9f9;
    border-radius: 8px;
    box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
}

.glossary h3 {
    color: #2C3C64;
}

.glossary p {
    font-size: 16px;
    margin-bottom: 5px;
}

/* Responsive */
@media (max-width: 768px) {
    .servicesInfo, .relatedCards {
        flex-direction: column;
        align-items: center;
    }
}
</style>
@endsection
