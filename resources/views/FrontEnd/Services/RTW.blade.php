@extends('layout.frontEnd')

@section('title', 'Right To Work | ClassifIeD')
@section('meta_keywords', 'Right to Work check, Get ClassifIeD screening solution, UK Right to Work, United Kingdom employment law')
@section('meta_description', 'Under UK employment law, all employers are legally required to conduct Right to Work checks on their employees to ensure they have the lawful authority to work in the United Kingdom.')


@section('content')
<div class="container py-5">
    <div class="servicesPages">
        <!-- Top Section -->
        <div class="servicesTop">
            <h1>Right to Work Check</h1>
            <p>
                Under UK employment law, all employers are legally required to conduct Right to Work checks on their employees to ensure they have the lawful authority to work in the United Kingdom. We’ll aim to validate UK right to work status digitally wherever possible. When digital verification is not successful, we will request proof of right to work in the UK from the candidate.
            </p>

            <div class="servicesCTA">
                <a href="{{ env('APP_URL') }}ContactUs" class="btn primaryBtn">Apply for RTW</a>
            </div>
        </div>

        <!-- Information Sections -->
        <div class="servicesInfo">
            <div class="infoBox">
                <i class="fas fa-user-check"></i>
                <h3>Who Needs a RTW Check?</h3>
                <p>Anyone employed in the UK.</p>
            </div>
            <div class="infoBox">
                <i class="fas fa-clock"></i>
                <h3>Processing Time</h3>
                <p>Typically ranging between, Instant and 1 working day.</p>
            </div>
            <div class="infoBox">
                <i class="fas fa-file-alt"></i>
                <h3>What's Included?</h3>
                <p>Digital Right to Work confirmation with manual fallback.</p>
            </div>
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
                    <h4>UK Criminal Record Basic DBS</h4>
                    <p>The Basic level check, from the Disclosure & Barring Service, provides details of unspent convictions.</p>
                    <a href="{{ env('APP_URL') }}Services/DBS-Basic" class="btn smallBtn">Learn More</a>
                </div>
                <div class="serviceCard">
                    <i class="fas fa-user-shield"></i>
                    <h4>BPSS Check</h4>
                    <p>Right to work, Identity Verification, Criminal Record (Basic Disclosure), and Employment history check.</p>
                    <a href="{{ env('APP_URL') }}Services/BPSS" class="btn smallBtn">Learn More</a>
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
