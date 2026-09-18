@extends('layout.frontEnd')

@section('title', 'Academic History | ClassifIeD')
@section('meta_keywords', 'pre-employment screening UK, Get ClassifIeD screening platform, screening software for HR, onboarding security checks, academic verification')
@section('meta_description', 'We will thoroughly substantiate an individual’s academic history, encompassing both secondary and higher education achievements.')



@section('content')
<div class="container py-5">
    <div class="servicesPages">
        <!-- Top Section -->
        <div class="servicesTop">
            <h1>Academic History</h1>
            <p>
                We will thoroughly substantiate an individual’s academic history, encompassing both secondary and higher education achievements. 
            </p>
            <p>
                We will liaise directly with the institutions the individual attended to obtain an Academic Reference. Additionally, we can collect copies of exam certificates and academic transcripts directly from the candidate to further validate their academic credentials. 
            </p>
            <p>
                Please note that many institutions now require a processing fee for these verifications. We will handle these payments on your behalf and add the charges to your final invoice. 
            </p>

            <div class="servicesCTA">
                <a href="{{ env('APP_URL') }}ContactUs" class="btn primaryBtn">Apply for Academic History</a>
            </div>
        </div>

        <!-- Information Sections -->
        <div class="servicesInfo">
            <div class="infoBox">
                <i class="fas fa-user-check"></i>
                <h3>Who Needs an Academic History Check?</h3>
                <p>Any employer who needs certain academic qualifications or wants to ensure the candidates integrity.</p>
            </div>
            <div class="infoBox">
                <i class="fas fa-clock"></i>
                <h3>Processing Time</h3>
                <p>Dependant on the academic institution, typically 1-5 working days.</p>
            </div>
            <div class="infoBox">
                <i class="fas fa-file-alt"></i>
                <h3>What's Included?</h3>
                <p>A full academic history including evidence of attendance.</p>
            </div>
        </div>

        <!-- Related Services -->
        <div class="relatedServices">
            <h3>Related Background Checks</h3>
            <div class="relatedCards">
                <div class="serviceCard">
                    <i class="fas fa-id-card"></i>
                    <h4>Identity Verification</h4>
                    <p>We will digitally verify an individual's identity to reduce fraud risk. A requirement for BPSS checks.</p>
                    <a href="{{ env('APP_URL') }}Services/Digital-ID-Verification" class="btn smallBtn">Learn More</a>
                </div>
                <div class="serviceCard">
                    <i class="fas fa-briefcase"></i>
                    <h4>Employment History</h4>
                    <p>This check is designed to confirm the details of an individual's employment history accurately.</p>
                    <a href="{{ env('APP_URL') }}Services/employment-history" class="btn smallBtn">Learn More</a>
                </div>
                <div class="serviceCard">
                    <i class="fas fa-user-friends"></i>
                    <h4>Personal References</h4>
                    <p>We will conduct a personal reference check to gather insights into an individual’s character through the perspective of a referee.</p>
                    <a href="{{ env('APP_URL') }}Services/personal-references" class="btn smallBtn">Learn More</a>
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
