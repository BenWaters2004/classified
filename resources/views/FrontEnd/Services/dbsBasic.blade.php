@extends('layout.frontEnd')

@section('title', 'DBS - Basic | ClassifIeD')
@section('meta_keywords', 'pre-employment screening plymouth, screening software for HR, onboarding security checks, basic DBS check service, criminal record check UK, online DBS application, fast-track DBS check, online DBS check')
@section('meta_description', 'The Basic-level check, conducted through the Disclosure & Barring Service (DBS), provides information on unspent convictions and conditional cautions or confirms the absence of such convictions, in line with filtering rules.')

@section('content')
<div class="container py-5">
    <div class="servicesPages">
        <!-- Top Section -->
        <div class="servicesTop">
            <h1>Criminal Record Check (Basic DBS, England & Wales)</h1>
            <p>
                The Basic-level check, conducted through the Disclosure & Barring Service (DBS), provides information on unspent convictions and conditional cautions or confirms the absence of such convictions, in line with filtering rules. While primarily designed for individuals living or working in England and Wales, it is also applicable in Jersey, Guernsey, and the Isle of Man.
            </p>
            <p>
                With our ClassifIeD system, featuring an integrated IDSP, we ensure seamless identity verification for your candidates before electronically submitting the DBS application. The results are then promptly and securely returned to our system for your convenience. 
            </p>

            <div class="servicesCTA">
                <a href="{{ env('APP_URL') }}ContactUs" class="btn primaryBtn">Apply for Basic DBS</a>
            </div>
        </div>

        <!-- Information Sections -->
        <div class="servicesInfo">
            <div class="infoBox">
                <i class="fas fa-shield-alt"></i>
                <h3>Who Needs a Basic DBS?</h3>
                <p>Individuals working in positions of trust, employers verifying applicants, and individuals seeking personal licenses.</p>
            </div>
            <div class="infoBox">
                <i class="fas fa-clock"></i>
                <h3>Processing Time</h3>
                <p>Basic DBS checks typically take 24 hours to 5 working days, depending on demand.</p>
            </div>
            <div class="infoBox">
                <i class="fas fa-file-alt"></i>
                <h3>What Information is Included?</h3>
                <p>The check includes <span class="glossary-term" data-term="unspent">unspent</span> convictions and conditional cautions, but not spent convictions.</p>
            </div>
        </div>

        <!-- Pricing Table -->
        <h3 class="pricingTitle">Pricing</h3>
        <table class="pricing-table">
            <tr>
                <td>DBS Fee</td>
                <td>£21.50</td>
            </tr>
            <tr>
                <td>Admin Fee (inc VAT)</td>
                <td>£16.90</td>
            </tr>
            <tr>
                <td><strong>Total</strong></td>
                <td><strong>£38.40</strong></td>
            </tr>
        </table>


        <!-- Glossary Section -->
        <div class="glossary">
            <h3>Glossary</h3>
            <p><strong>Unspent:</strong> A conviction that has not yet passed the period of time required for it to be removed from a person's record under the Rehabilitation of Offenders Act 1974.</p>
            <p><strong>Spent Convictions:</strong> Older convictions that are no longer required to be disclosed under most circumstances.</p>
            <p><strong>Conditional caution:</strong> A conditional caution on a basic DBS check will become spent after 3 months if the conditions last 3 months or more, or on the date the conditions end if the conditions last less than 3 months.</p>
        </div>

        <!-- Related Services -->
        <div class="relatedServices">
            <h3>Related Background Checks</h3>
            <div class="relatedCards">
                <div class="serviceCard">
                    <i class="fas fa-id-card"></i>
                    <h4>Identity Verification</h4>
                    <p>We'll digitally verify an individual's identity to reduce fraud risk. This is also a requirement for criminal record checks.</p>
                    <a href="{{ env('APP_URL') }}Services/Digital-ID-Verification" class="btn smallBtn">Learn More</a>
                </div>
                <div class="serviceCard">
                    <i class="fas fa-user-shield"></i>
                    <h4>BPSS Check</h4>
                    <p>Right to work, Identity Verification, Criminal Record (Basic Disclosure), and Employment history check.</p>
                    <a href="{{ env('APP_URL') }}Services/BPSS" class="btn smallBtn">Learn More</a>
                </div>
                <div class="serviceCard">
                    <i class="fas fa-file-signature"></i>
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
