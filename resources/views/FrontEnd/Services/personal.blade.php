@extends('layout.frontEnd')

@section('title', 'Personal References | ClassifIeD')
@section('meta_keywords', 'pre employment screening UK, Get ClassifIeD screening, background checks, onboarding security checks, personal references, character references')
@section('meta_description', 'We will conduct a personal reference check to gather insights into an individual’s character through the perspective of a referee.')


@section('content')
<div class="container py-5">
    <div class="servicesPages">
        <!-- Top Section -->
        <div class="servicesTop">
            <h1>Personal References</h1>
            <p>
                We will conduct a personal reference check to gather insights into an individual’s character through the perspective of a referee. 
            </p>
            <p>
                This reference will outline the nature of the relationship between the referee and the individual, the length of time they have known each other, the individual’s strengths, areas for growth, and the referee’s assessment of their suitability for a specific role. 
            </p>
            <p>
                This check is highly recommended as it enables you to collect personal references from individuals who have interacted with the candidate at various stages of their career. These could include colleagues, clients, suppliers, university lecturers, supervisors, or others who can provide valuable insights into the candidate’s integrity, honesty, skills, and overall capabilities. 
            </p>
            <p>
                Using ClassifIeD's online system, the referee will automatically be sent a form to return. 
            </p>

            <div class="servicesCTA">
                <a href="{{ env('APP_URL') }}ContactUs" class="btn primaryBtn">Apply for Personal References</a>
            </div>
        </div>

        <!-- Information Sections -->
        <div class="servicesInfo">
            <div class="infoBox">
                <i class="fas fa-user-check"></i>
                <h3>Who Needs a Personal Reference Check?</h3>
                <p>To be used for any candidate to verify integrity.</p>
            </div>
            <div class="infoBox">
                <i class="fas fa-clock"></i>
                <h3>Processing Time</h3>
                <p>Dependant on the referee's, typically 1-5 working days.</p>
            </div>
            <div class="infoBox">
                <i class="fas fa-file-alt"></i>
                <h3>What's Included?</h3>
                <p>Insights into an individual’s character through the perspective of a referee.</p>
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
                    <i class="fas fa-graduation-cap"></i>
                    <h4>Academic History</h4>
                    <p>We will thoroughly substantiate an individual’s academic history, encompassing both secondary and higher education achievements.</p>
                    <a href="{{ env('APP_URL') }}Services/academic-history" class="btn smallBtn">Learn More</a>
                </div>
                <div class="serviceCard">
                    <i class="fas fa-briefcase"></i>
                    <h4>Employment History</h4>
                    <p>This check is designed to confirm the details of an individual's employment history accurately.</p>
                    <a href="{{ env('APP_URL') }}Services/employment-history" class="btn smallBtn">Learn More</a>
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
