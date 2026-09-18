@extends('layout.frontEnd')

@section('title', 'Glossary | ClassifIeD')
@section('meta_keywords', 'screening terms explained, employment screening glossary, pre-employment definitions, DBS terms UK, BPSS terminology guide')
@section('meta_description', 'Screening can be a confusing world, break through the jargon with our glossary.')


@section('content')
<div class="container py-5 px-3 px-md-5 glossaryPage">
    <h1 class="fw-bold mb-4 text-center">Glossary of Terms</h1>
    <p class="text-center">Screening can be a confusing world, break through the jargon with our glossary.</p>

    <input type="text" class="form-control mb-4 search-bar" placeholder="Search for a term..." />

    @php
        $glossaryItems = [
            ['term' => 'Access NI', 'definition' => 'AccessNI is the body that performs criminal record checks for individuals in Northern Ireland.'],
            ['term' => 'Admin Delegation', 'definition' => 'The ability to assign different roles and responsibilities to system administrators within the application.'],
            ['term' => 'Applicant Tracking System (ATS)', 'definition' => 'An ATS is a system that businesses can use to help automate elements of their recruitment process. This can include sorting through CVs as well as monitoring the progress of candidates through their application and background screening process.'],
            ['term' => 'Application fraud', 'definition' => 'When a job applicant makes a false claim about anything from their academic or professional qualifications during the application process to improve their chances of being hired.'],
            ['term' => 'Approved Access Number', 'definition' => 'A unique identifier assigned to an individual who has been granted access following a successful screening and approval process.'],
            ['term' => 'Baseline Personnel Security Standard (BPSS)', 'definition' => 'Baseline Personnel Security Standard – a pre-employment screening standard for government and  individuals working with or on behalf of a government department in the UK. A BPSS check includes multiple verification steps, covering 4 parts (RICE): R - ight to work, I - dentity Verification, C - riminal Record (Basic Disclosure), E - mployment history check. In addition, candidates are required to disclose any significant periods spent abroad (6 months or more in the past 3 years)'],
            ['term' => 'BS7858 Screening', 'definition' => 'A British Standard for screening individuals in secure environments, particularly in the security industry. It includes identity verification, employment history (up to 5 years), criminal record checks (standard/enhanced DBS where applicable), credit checks, and right to work verification. It ensures that personnel are suitable and trustworthy for roles in security-sensitive positions.'],
            ['term' => 'Clearance Revalidation', 'definition' => 'A process to review and confirm a person’s continued suitability for a security clearance over time.'],
            ['term' => 'CRB check', 'definition' => 'The Criminal Records Bureau of England and Wales (CRB) merged with the Independent Safeguarding Authority (ISA)  in December 2012, to become the Disclosure and Barring Service (DBS). If someone talks about a CRB check they are inncorrectly refering to a DBS Check.'],
            ['term' => 'DBS', 'definition' => 'Disclosure and Barring Service – checks an individual’s criminal record to help employers make safer recruitment decisions.'],
            ['term' => 'Digital Identity Verification', 'definition' => 'The process of verifying a person’s identity through secure online methods, such as biometric facial recognition, document upload, and real-time validation.'],
            ['term' => 'Disclosure', 'definition' => 'Disclosure is the official term for a UK criminal record certificate.'],
            ['term' => 'Disclosure Scotland', 'definition' => 'Disclosure Scotland is the body that performs criminal record check for individuals in Scotland.'],
            ['term' => 'Diploma mill', 'definition' => 'A diploma mill or degree mill is a business that sells illegitimate diplomas or academic degrees, respectively. Often these entities will grant a “degree” based on the submission of a résumé detailing life experience and will even let the applicant choose their own subject and year of graduation. Diploma mills sometimes also provide verification services for their fake degrees, making it even more important to check that the institution is suitably accredited.'],
            ['term' => 'E-result', 'definition' => 'An electronically issued result or certificate indicating the outcome of a screening check.'],
            ['term' => 'Exempted Position', 'definition' => 'A job role that is exempt from the Rehabilitation of Offenders Act 1974. This means employers can ask about spent convictions when conducting background checks. These roles typically involve working with children, vulnerable adults, or in positions of public trust and require a standard/enhanced DBS check.'],
            ['term' => 'Identity Verification', 'definition' => 'The process of confirming a person is who they claim to be, typically via digital document and facial checks but can be done in person.'],
            ['term' => 'Known Consignor', 'definition' => 'An entity approved by the Civil Aviation Authority (CAA) to dispatch air cargo on aircraft without further screening. Must meet rigorous security controls.'],
            ['term' => 'MFA', 'definition' => 'Multi-Factor Authentication – an added layer of login security requiring a secondary method of verification (e.g., phone, app).'],
            ['term' => 'Overseas Criminal Record Check', 'definition' => 'An overseas criminal record check is the process used to verify whether a candidate has a criminal record in another country. It is typically required when hiring individuals who have lived or worked abroad.'],
            ['term' => 'Personal Identifiable Information (PII)', 'definition' => 'Information that can be used to identify an individual, such as name, address, date of birth, or national insurance number.'],
            ['term' => 'PESA', 'definition' => 'Pre-Employment Screening Application – the original name for Get ClassifIeD which was originally developed exclusively for the aerospace sector.'],
            ['term' => 'Pre-employment Screening', 'definition' => 'The process of verifying a candidate’s background before they are hired, including identity, criminal records, and employment history.'],
            ['term' => 'Regulated Agent', 'definition' => 'An entity that screens air cargo on behalf of others and complies with aviation security regulations. Recognised and approved by the Department for Transport (DfT).'],
            ['term' => 'Responsible Body/Organisation', 'definition' => 'The organisation or individual authorised to initiate screening checks on behalf of the applicant or employer.'],
            ['term' => 'Right to Work', 'definition' => 'The legal right for an individual to work in the UK. Employers must verify this before employment begins, typically using passport, visa, or share code documentation.'],
            ['term' => 'SC Cleared', 'definition' => 'Security Check (SC) is a UK government clearance level required for access to sensitive information.'],
            ['term' => 'Screening', 'definition' => 'A general term for the process of checking an individual’s background to assess suitability for employment or access.'],
            ['term' => 'Spent Convictions', 'definition' => 'Convictions that have passed their rehabilitation period under the Rehabilitation of Offenders Act 1974. They generally do not need to be disclosed to employers unless applying for roles that are exempt from the Act (e.g., those requiring standard/enhanced DBS checks).'],
            ['term' => 'Unspent Convictions', 'definition' => 'Convictions that have not yet reached the end of their rehabilitation period and must be disclosed when requested, especially for roles involving security clearance or regulated activity.'],
            ['term' => 'Verification', 'definition' => 'The process of confirming that the information provided by a candidate is accurate and genuine. This can include checking identity, employment history, qualifications, address history, and right to work. It is a core part of all screening and vetting processes.'],
            ['term' => 'Vetting', 'definition' => 'A thorough background assessment of an individual to determine their trustworthiness and suitability for a role, especially those involving access to sensitive information or secure environments. Vetting can include criminal checks, financial screening, employment verification, and references.'],
        ];

        $grouped = collect($glossaryItems)->sortBy('term')->groupBy(fn($item) => strtoupper(substr($item['term'], 0, 1)));
    @endphp

    <div class="alphabetList d-flex flex-wrap justify-content-center mb-4">
        @foreach ($grouped as $letter => $items)
            <a href="#letter-{{ $letter }}" class="mx-2 text-decoration-none text-primary fw-bold alphabet-link">{{ $letter }}</a>
        @endforeach
    </div>

    <div class="glossaryList">
        @foreach ($grouped as $letter => $items)
            <h4 class="fw-bold mt-5 mb-3" id="letter-{{ $letter }}">{{ $letter }}</h4>
            @foreach ($items as $item)
                <div class="glossaryItem" data-term="{{ strtolower($item['term']) }}">
                    <button class="toggleTerm d-md-none">
                        <h5 class="mb-0">{{ $item['term'] }}</h5>
                        <i class="fas fa-chevron-down"></i>
                    </button>
                    <div class="termBody">
                        <h5 class="d-none d-md-block">{{ $item['term'] }}</h5>
                        <p>{{ $item['definition'] }}</p>
                    </div>
                </div>
            @endforeach
        @endforeach
    </div>
</div>
@endsection

@section('pageCSS')
<style>
.glossaryPage {
    max-width: 900px;
    margin: auto;
}

.search-bar {
    max-width: 400px;
    transition: 0.3s ease;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    margin: 0 auto;
}

.search-bar:focus {
    box-shadow: 0 0 8px #C55359;
    border-color: #C55359;
}

.alphabetList a {
    color: #C55359;
}

.glossaryList {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.glossaryItem {
    background: #f9f9f9;
    border-left: 6px solid #C55359;
    border-radius: 6px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.06);
    overflow: hidden;
    transition: all 0.3s ease;
}

.toggleTerm {
    background: none;
    border: none;
    width: 100%;
    padding: 15px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    cursor: pointer;
    font-weight: 700;
    color: #2C3C64;
}

.toggleTerm i {
    transition: transform 0.3s;
}

.termBody {
    padding: 0 15px 15px 15px;
    animation: fadeIn 0.4s ease;
}

.termBody p {
    margin: 0;
    font-size: 15px;
    color: #555;
    line-height: 1.6;
}

h1 {
    border-bottom: 0 !important;
    padding-bottom: 0 !important;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

@media (max-width: 768px) {
    .termBody {
        display: none;
    }

    .glossaryItem.open .termBody {
        display: block;
    }

    .glossaryItem.open .toggleTerm i {
        transform: rotate(180deg);
    }
}
</style>
@endsection

@section('pageJavascript')
<script>
    // Search
    document.querySelector('.search-bar').addEventListener('input', function () {
        const searchTerm = this.value.toLowerCase();
        const items = document.querySelectorAll('.glossaryItem');
        const headers = document.querySelectorAll('.glossaryList h4');

        let visibleLetters = new Set();

        items.forEach(item => {
            const term = item.getAttribute('data-term');
            if (term.includes(searchTerm)) {
                item.style.display = 'block';
                visibleLetters.add(term[0]);
            } else {
                item.style.display = 'none';
            }
        });

        headers.forEach(header => {
            const letter = header.id.replace('letter-', '').toLowerCase();
            const hasVisible = [...items].some(item => 
                item.getAttribute('data-term').startsWith(letter) && item.style.display === 'block'
            );
            header.style.display = hasVisible ? 'block' : 'none';
        });
    });

    // Mobile Expand/Collapse
    document.querySelectorAll('.toggleTerm').forEach(btn => {
        btn.addEventListener('click', function () {
            const parent = this.closest('.glossaryItem');
            parent.classList.toggle('open');
        });
    });
</script>
@endsection
