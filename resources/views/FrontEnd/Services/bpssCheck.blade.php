@extends('layout.frontEnd')

@section('title', 'BPSS | ClassifIeD')
@section('meta_keywords', 'pre-employment screening UK, Get ClassifIeD screening platform, BPSS clearance process, baseline personnel security standard, BPSS screening provider, government security vetting')
@section('meta_description', 'The BPSS check plays a pivotal role in ensuring personnel security across organisations. This process helps to prevent unauthorised disclosure of classifIeD data and reduces the likelihood of espionage or sabotage.')


@section('content')
<div class="container py-5">
    <div class="servicesPages">
        <!-- Top Section -->
        <div class="servicesTop">
            <h1>Baseline Personnel Security Standard (BPSS) Check</h1>
            <p>
                The BPSS check plays a pivotal role in ensuring personnel security across organisations. Our ClassifIeD system can efficiently conduct comprehensive background checks to mitigate insider threats, safeguard sensitive data, and uphold your organisation's reputation.
            </p>
            <p>
                Government organisations and defence agencies rely on BPSS checks performed through our ClassifIeD system to ensure that individuals with access to sensitive information or systems are trustworthy and pose no risk to national security. This process helps to prevent unauthorised disclosure of classifIeD data and reduces the likelihood of espionage or sabotage. 
            </p>
            <p>
                The importance of BPSS checks extends beyond national security and corporate interests. They also protect individuals' privacy and rights by ensuring that only those who meet the required security standards are granted access to personal data and confidential information. With our ClassifIeD system, organisations can streamline this critical process while maintaining the highest levels of security and compliance. 
            </p>
            <p>
                A BPSS check includes multiple verification steps, covering 4 parts (RICE): R - ight to work, I - dentity Verification, C - riminal Record (Basic Disclosure), E - mployment history check. In addition, candidates are required to disclose any significant periods spent abroad (6 months or more in the past 3 years).
            </p>

            <div class="servicesCTA">
                <a href="{{ env('APP_URL') }}ContactUs" class="btn primaryBtn">Apply for BPSS</a>
            </div>
        </div>

        <!-- Information Sections -->
        <div class="servicesInfo">
            <div class="infoBox">
                <i class="fas fa-user-check"></i>
                <h3>Who Needs a BPSS Check?</h3>
                <p>Employees handling government data, defence agencies, law enforcement, businesses handling sensitive commercial information or operating within regulated industries such as finance, healthcare, or energy.</p>
            </div>
            <div class="infoBox">
                <i class="fas fa-clock"></i>
                <h3>Processing Time</h3>
                <p>BPSS checks typically take 5-10 working days, depending on verification complexity.</p>
            </div>
            <div class="infoBox">
                <i class="fas fa-file-alt"></i>
                <h3>What's Included?</h3>
                <p>The check covers multiple verification steps, covering 4 parts (RICE): <strong>R</strong>ight to Work, <strong>I</strong>dentity Verification, <strong>C</strong>riminal Record (Basic DBS), and <strong>E</strong>mployment History.</p>
            </div>
        </div>

        <!-- Glossary Section -->
        <div class="glossary">
            <h3>Glossary</h3>
            <p><strong>BPSS (Baseline Personnel Security Standard):</strong> A UK government standard for background screening before employment in sensitive roles.</p>
            <p><strong>Right to Work:</strong> Nationality and Immigration Status (including an entitlement to undertake the work in question).</p>
            <p><strong>Identity Verification</strong> ID Data check (electronic identity authentication- name, address, aliases, links, accounts, etc.).</p>
            <p><strong>Crimincal Record</strong> Provides information on unspent convictions and conditional cautions or confirms the absence of such convictions, in line with filtering rules.</p>
            <p><strong>Employment history check</strong> Confirmation of past 3 years employment (minimum) history / activity.</p>
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
