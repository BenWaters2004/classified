@extends('layout.frontEnd')

@section('title', 'Clients | ClassifIeD')
@section('meta_keywords', 'HR screening software, employer background checks, complete DBS application, manage DBS and BPSS applications, employer portal for background checks, hiring compliance platform')
@section('meta_description', 'Learn how our screening services can help optimise your HR solutions and increase your companies security.')

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
    <div class="row align-items-center pageBanner" data-aos="fade-up">
        <!-- Text Section -->
        <div class="col-lg-6 text-white textBannerSection">
            <h1 class="display-5 fw-bold mb-4">Made for you</h1>
            <p class="lead mb-4">Our packages are made for you, because every company is unique.</p>
        </div>
        <!-- Image Section -->
        <div class="col-lg-6 text-end pe-0">
            <img rel="preload" as="image" src="{{ env('APP_URL') }}images/FrontEnd/banner-lady-mug.webp" alt="Lady smiling holding a mug" class="img-fluid" loading="eager" fetchpriority="high">
        </div>
    </div>

    <div class="Title-TextBlock">
        <h2>Overview <br><span>Why Us?</span></h2>
        <p><ul>
            <li>Faster clearance rate and easy to use portal</li>
            <li>Expertise in the screening industry clearing over 1000+ applicants over 3+ years</li>
            <li>Monitor the progress at every stage on the dashboard and provide downloadable reports</li>
            <li>Dedicated customer service for query resolution and reporting</li>
            <li>Automate tasks and processes across a clearance process business and throughout the information lifecycle on reports</li>
            <li>Unlock workforce potential and retain the best talent with our revalidation service.</li>
        </ul></p>
    </div>

    <div class="row text-center mt-5 resources-grid" data-aos="fade-up">
        <div class="col-md-4 mb-4">
            <div onclick="openIframe()" class="resource-tile">
                <i class="fas fa-rss fa-3x mb-3"></i>
                <h5>Interactive Demo</h5>
                <p>Go to Demo <i class="fa-solid fa-arrow-right"></i></p>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <a href="{{ env('APP_URL') }}Resources/glossary" class="resource-tile">
                <i class="fas fa-book fa-3x mb-3"></i>
                <h5>Glossary</h5>
                <p>Go to Glossary <i class="fa-solid fa-arrow-right"></i></p>
            </a>
        </div>
        <div class="col-md-4 mb-4">
            <a href="{{ env('APP_URL') }}sample-report.pdf" target="_blank" class="resource-tile">
                <i class="fas fa-file-alt fa-3x mb-3"></i>
                <h5>Sample Report</h5>
                <p>Go to Sample Report <i class="fa-solid fa-arrow-right"></i></p>
            </a>
        </div>
    </div>


    <div class="row contact-section mt-5">
        <div class="col-lg-7 col-md-12" data-aos="fade-right">
            <!-- FAQ Section -->
            <div class="faq-container">
                <h2>Frequently Asked Questions</h2>
                <div class="faq-item">
                    <button class="faq-question">What is pre-employment screening? <i class="fa fa-chevron-down"></i></button>
                    <div class="faq-answer">
                        <p>Pre-employment screening is the process of verifying a candidate's background, qualifications, and work history before hiring.</p>
                    </div>
                </div>

                <div class="faq-item">
                    <button class="faq-question">How long does the screening process take? <i class="fa fa-chevron-down"></i></button>
                    <div class="faq-answer">
                        <p>The process can take anywhere from a few hours to a few weeks depending on the depth of the checks required.</p>
                    </div>
                </div>

                <div class="faq-item">
                    <button class="faq-question">Will I be notified when the screening is complete? <i class="fa fa-chevron-down"></i></button>
                    <div class="faq-answer">
                        <p>Yes, you will receive an update once the screening process has been completed. Your dashboard will also display the current status of all your applications.</p>
                    </div>
                </div>

                <div class="faq-item">
                    <button class="faq-question">What happens if a candidate don't pass their clearance? <i class="fa fa-chevron-down"></i></button>
                    <div class="faq-answer">
                        <p>This will come down to your company and their own HR policies and procedures. Unfortunately, we are not in a position to answer this on their behalf. BIT Group/Get ClassifIeD will never state whether or not you should hire a candidate but will provide all the information for you to make your own descion.</p>
                    </div>
                </div>

                <div class="faq-item">
                    <button class="faq-question">What do I do if I need help or have questions? <i class="fa fa-chevron-down"></i></button>
                    <div class="faq-answer">
                        <p>Someone from Get ClassifIeD/BIT Group is always avaliable durring business hours at Tel: <a href="tel:+441752724000">+44 (0)1752 724 000</a> or Email: <a href="mailto:screening@thinkbitgroup.co.uk">screening@thinkbitgroup.co.uk</a>.</p>
                    </div>
                </div>

                <div class="faq-item">
                    <button class="faq-question">What does ClassifIeD do to keep my organisations information secure? <i class="fa fa-chevron-down"></i></button>
                    <div class="faq-answer">
                        <p>Your data's security is at the forefront of our application, for more infomation on our security visit <a href="{{ env('APP_URL') }}AboutUs/security-at-classified">Security at ClassifIeD</a> or our <a href="{{ env('APP_URL') }}privacypolicy.pdf">Privacy Policy</a>.</p>
                    </div>
                </div>
            </div>
        </div>
        <!-- Text Section -->
        <div class="col-lg-5 col-md-12 contacts-info mb-4" data-aos="fade-left">
            <div class="ContactBox">
                <h3>Data Security</h3>
                <p>How we protect data: <a href="{{ env('APP_URL') }}AboutUs/security-at-classified">Security at ClassifIeD</a></p>
                <p>Our Privacy Policy: <a href="{{ env('APP_URL') }}privacypolicy.pdf">Privacy Policy</a></p>
            </div>
            <div class="ContactBox">
                <h3>Client Support</h3>
                <p>Call: <a href="Tel: +44 (0)1752 724 000">+44 (0)1752 724 000</a></p>
                <p>Email: <a href="mailto:screening@thinkbitgroup.co.uk">screening@thinkbitgroup.co.uk</a></p>
            </div>
            <div class="ContactBox">
                <h3>Technical Support</h3>
                <p>Call: <a href="Tel: +44 (0)1752 270139">+44 (0)1752 270139</a></p>
                <p>Email: <a href="mailto:ben.waters@thinkbitgroup.co.uk">ben.waters@thinkbitgroup.co.uk</a></p>
            </div>
            <div class="ContactBox">
                <h3>Address</h3>
                <p>HQ: Plymouth Science Park, 1 Davy Rd, Plymouth, Devon PL6 8BX</p>
                <iframe width="100%" height="auto" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade" src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2542.271461755169!2d-4.1085706!3d50.417413700000004!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x486ced666312f649%3A0x51421885656f8b6c!2sGet%20ClassifIeD!5e0!3m2!1sen!2suk!4v1764270872781!5m2!1sen!2suk"></iframe>
            </div>
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

<!-- Iframe Modal -->
<div class="modal fade" id="iframeModal" tabindex="-1" aria-labelledby="iframeModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="iframeModalLabel">Client Demonstration</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <script async src="https://js.storylane.io/js/v2/storylane.js"></script>
      <div class="modal-body" style="height: 600px;">
          <iframe title="Interactive Demonstration" loading="lazy" class="sl-demo" id="demoFrame" src="https://getclassified.storylane.io/demo/k7ulia3eklut?embed=inline" name="sl-embed" allow="fullscreen" allowfullscreen style="position:absolute;top:0;left:0;width:100%!important;height:100%!important;border:1px solid rgba(63,95,172,0.35);box-shadow: 0px 0px 18px rgba(26, 19, 72, 0.15);border-radius:10px;box-sizing:border-box;"></iframe>
      </div>
    </div>
  </div>
</div>

@endsection

@section('pageCSS')
<style>
/* FAQ Section */
.faq-container {
    max-width: 800px;
    margin: 50px auto;
    padding: 20px;
}

.faq-item {
    border-bottom: 1px solid #ddd;
    margin-bottom: 10px;
}

.faq-question {
    width: 100%;
    background: none;
    border: none;
    font-size: 18px;
    text-align: left;
    cursor: pointer;
    padding: 15px;
    font-weight: bold;
    display: flex;
    justify-content: space-between;
    align-items: center;
    color: #2C3C64;
    transition: all 0.3s ease-in-out;
}

.faq-question:hover {
    color: #C55359;
}

.faq-answer {
    max-height: 0;
    overflow: hidden;
    transition: max-height 0.4s ease-in-out, padding 0.4s ease-in-out;
    background: #f9f9f9;
    padding: 0 15px;
    border-left: 4px solid #C55359;
}

.faq-item.active .faq-answer {
    max-height: 200px; /* Adjust based on content */
    padding: 15px;
}

.faq-question i {
    transition: transform 0.3s ease-in-out;
}

.faq-item.active .faq-question i {
    transform: rotate(180deg);
}


.contact-section {
    display: flex;
    flex-wrap: wrap;
    align-items: flex-start;
    overflow: hidden;
}

.contacts-info {
    margin-top: 60px;
    padding-right: 20px;
}

.contacts-info h3 {
    color: #C55359;
    font-size: 1.2rem;
    margin-bottom: 0.5rem !important;
}
.contacts-info a {
    color: #C55359;
    text-decoration: none;
}
.contacts-info p {
    margin-bottom: 0.5rem !important;
    font-size: 1rem;
}

.ContactBox {
    background-color:rgb(228, 228, 228);
    padding: 15px;
    margin-bottom: 20px;
    width: auto;
}

@media (max-width: 768px) {
    .contact-section {
        flex-direction: column;
    }

    .contact-info {
        text-align: center;
        margin-bottom: 20px;
    }

    .contact-form {
        width: 100%;
    }
}

.resource-tile {
    display: block;
    background-color: #f7f9fc;
    padding: 30px 20px;
    border-radius: 10px;
    text-decoration: none;
    color: #2C3C64;
    transition: all 0.3s ease;
    border: 1px solid transparent;
    height: 100%;
}

.resource-tile:hover {
    background-color: #ffffff;
    border-color: #C55359;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    color: #C55359;
}

.resource-tile i {
    color: #C55359;
    transition: color 0.3s ease;
}

.resource-tile:hover i {
    color: #a94448;
}

.resource-tile h5 {
    margin-top: 10px;
    font-size: 1.2rem;
    font-weight: 600;
}

</style>
@endsection

@section('pageJavascript')
<script>
document.addEventListener("DOMContentLoaded", function() {
    const faqItems = document.querySelectorAll(".faq-item");

    faqItems.forEach(item => {
        const question = item.querySelector(".faq-question");

        question.addEventListener("click", function() {
            // Close all open FAQs
            faqItems.forEach(el => {
                if (el !== item) {
                    el.classList.remove("active");
                }
            });

            // Toggle the clicked question
            item.classList.toggle("active");
        });
    });
});

function openIframe() {
    var myModal = new bootstrap.Modal(document.getElementById('iframeModal'));
    myModal.show();
}

</script>
@endsection

@push('structuredData')
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "FAQPage",
  "mainEntity": [
    {
      "@type": "Question",
      "name": "What is pre-employment screening?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "Pre-employment screening is the process of verifying a candidate's background, qualifications, and work history before hiring."
      }
    },
    {
      "@type": "Question",
      "name": "How long does the screening process take?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "The process can take anywhere from a few hours to a few weeks depending on the depth of the checks required."
      }
    },
    {
      "@type": "Question",
      "name": "Will I be notified when the screening is complete?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "Yes, you will receive an update once the screening process has been completed. Your dashboard will also display the current status of all your applications."
      }
    },
    {
      "@type": "Question",
      "name": "What happens if a candidate doesn't pass their clearance?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "This will depend on your company's HR policies. BIT Group/Get ClassifIeD provides the information needed, but does not influence hiring decisions."
      }
    },
    {
      "@type": "Question",
      "name": "What do I do if I need help or have questions?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "Someone from Get ClassifIeD/BIT Group is available during business hours at +44 (0)1752 724 000 or screening@thinkbitgroup.co.uk."
      }
    },
    {
      "@type": "Question",
      "name": "What does ClassifIeD do to keep my organisation's information secure?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "Your data's security is a priority. Learn more on our Security at ClassifIeD page or view our Privacy Policy."
      }
    }
  ]
}
</script>
@endpush
