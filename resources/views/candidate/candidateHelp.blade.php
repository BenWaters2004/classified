@extends('layout.candidate')

@section('form-content')
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
        color: var(--brand);
    }
    .faq-item.active .faq-question {
        color: var(--brand);
    }
    .faq-question:focus-visible {
        outline: 2px solid var(--brand);
        outline-offset: 2px;
        border-radius: 4px;
    }

    .faq-answer {
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.4s ease-in-out, padding 0.4s ease-in-out;
        background: #f9f9f9;
        padding: 0 15px;
        border-left: 4px solid var(--brand);
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
        color: var(--brand);
        font-size: 2rem;
        margin-bottom: 0.5rem !important;
    }
    .contacts-info a {
        color: var(--brand);
        text-decoration: none;
    }
    .contacts-info p {
        margin-bottom: 0.5rem !important;
        font-size: 1.5rem;
    }

    .ContactBox {
        background-color: rgb(228, 228, 228);
        padding: 15px;
        margin-bottom: 20px;
        width: auto;
        /* Optional: give the boxes a subtle brand accent on the left */
        /* border-left: 4px solid var(--brand); */
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
</style>
    <div class="p-5 bg-light rounded">
        <div class="row contact-section mt-5">
            <div class="col-lg-7 col-md-12">
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
                        <button class="faq-question">What information do I need to provide? <i class="fa fa-chevron-down"></i></button>
                        <div class="faq-answer">
                            <p>You may be asked to provide identification, proof of employment history, references, and qualifications. The information requested is deapendant on the checks your employer has requested.</p>
                        </div>
                    </div>

                    <div class="faq-item">
                        <button class="faq-question">Will I be notified when the screening is complete? <i class="fa fa-chevron-down"></i></button>
                        <div class="faq-answer">
                            <p>Yes, you will receive an update once your screening process has been completed. This portal will also display the current status of your application.</p>
                        </div>
                    </div>

                    <div class="faq-item">
                        <button class="faq-question">Why am I being asked to do this again? (if you have already) <i class="fa fa-chevron-down"></i></button>
                        <div class="faq-answer">
                            <p>If you have been recruited through an agency, it is likely they will do their own checks on you as an individual. Our BPPS screening is required by your company and is irrespective of what clearance you have done with any other organisation. Even if you have recently done a DBS or BPSS check, we will still need to complete our own. Depending on your employers policies, you may be asked to complete this again every 3-10 years.</p>
                        </div>
                    </div>

                    <div class="faq-item">
                        <button class="faq-question">What happens if I don't pass my clearance? <i class="fa fa-chevron-down"></i></button>
                        <div class="faq-answer">
                            <p>This will come down to your employer and their own HR policies and procedures. Unfortunately, we are not in a position to answer this on their behalf. BIT Group/Get ClassifIeD will never state whether or not the employer should hire a candidate but will provide all the information for them to make their own descion.</p>
                        </div>
                    </div>

                    <div class="faq-item">
                        <button class="faq-question">What do I do if I need help or have questions? <i class="fa fa-chevron-down"></i></button>
                        <div class="faq-answer">
                            <p>Someone from Get ClassifIeD/BIT Group is always avaliable during business hours at Tel: <a href="tel:+441752724000">+44 (0)1752 724 000</a> or Email: <a href="mailto:screening@thinkbitgroup.co.uk">screening@thinkbitgroup.co.uk</a>.</p>
                        </div>
                    </div>

                    <div class="faq-item">
                        <button class="faq-question">What does ClassifIeD do to keep my information secure? <i class="fa fa-chevron-down"></i></button>
                        <div class="faq-answer">
                            <p>Your data's security is at the forefront of our application, for more infomation on our security visit <a href="{{ env('APP_URL') }}AboutUs/security-at-classified">Security at ClassifIeD</a> or our <a href="{{ env('APP_URL') }}privacypolicy.pdf">Privacy Policy</a>.</p>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Text Section -->
            <div class="col-lg-5 col-md-12 contacts-info mb-4">
                <div class="ContactBox">
                    <h3>Data Security</h3>
                    <p>How we protect data: <a href="{{ env('APP_URL') }}AboutUs/security-at-classified">Security at ClassifIeD</a></p>
                    <p>Our Privacy Policy: <a href="{{ env('APP_URL') }}privacypolicy.pdf">Privacy Policy</a></p>
                </div>
                <div class="ContactBox">
                    <h3>Candidate Support</h3>
                    <p>Call: <a href="Tel: +44 (0)1752 724 000">+44 (0)1752 724 000</a></p>
                    <p>Email: <a href="mailto:screening@thinkbitgroup.co.uk">screening@thinkbitgroup.co.uk</a></p>
                </div>
                <div class="ContactBox">
                    <h3>Address</h3>
                    <p>HQ: Plymouth Science Park, 1 Davy Rd, Plymouth, Devon PL6 8BX</p>
                </div>
            </div>
        </div>
    </div>

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
    </script>
@endsection