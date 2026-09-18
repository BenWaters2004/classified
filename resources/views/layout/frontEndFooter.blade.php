<footer class="bg-primary text-white">
    <div class="container footerTop">
        <div class="row">
            <!-- Left Section -->
            <div class="col-md-5">
                <a class="logo text-decoration-none" href="{{ env('APP_URL') }}">
                    @if (env('APP_ENV') == 'production')
                        <img loading="lazy" class="img-responsive logo" src="{{ env('APP_URL') }}images/whitewithcolourstrap.png" alt="Logo"/>
                    @else
                        <img loading="lazy" class="img-responsive logo" src="{{ env('APP_URL') }}images/logo_demo.png" alt="Demo Logo"/>
                    @endif
                </a>
                <p class="logoDescFooter">Get ClassifIeD provides DBS and other pre-employment screening for business. ClassifIeD is an application created by BluescreenIT LTD.</p>
                <p class="mb-0">
                    <strong><a href="{{ env('APP_URL') }}privacypolicy.pdf" target="_blank" class="aHoverColour">Privacy Policy</a> | <a href="{{ env('APP_URL') }}modernslaverypolicy.pdf" target="_blank" class="aHoverColour">Modern Slavery Policy</a> | <a href="{{ env('APP_URL') }}sitemap" class="aHoverColour">Sitemap</a></strong>
                </p>
            </div>

            <!-- Middle Section -->
            <div class="col-md-3">
                <h5 class="fw-bold">Departments</h5>
                <ul class="list-unstyled">
                    <li><a href="https://www.bluescreenit.co.uk/" target="_blank" class="aHoverColour">BIT Group</a></li>
                    <li><a href="https://thinkbitsecurity.co.uk/" target="_blank" class="aHoverColour">BIT Security</a></li>
                    <li><a href="https://thinkbittraining.co.uk/" target="_blank" class="aHoverColour">BIT Training</a></li>
                </ul>
            </div>

            <!-- Right Section -->
            <div class="col-md-4">
                <h5 class="fw-bold">Contact</h5>
                <ul class="list-unstyled">
                    <li><a href="Tel: +44 (0)1752 724 000" class="aHoverColour">Tel: +44 (0)1752 724 000</a></li>
                    <li><a href="mailto: screening@thinkbitgroup.co.uk" class="aHoverColour">Email: screening@thinkbitgroup.co.uk</a></li>
                    <li>
                        HQ: Plymouth Science Park, 1 Davy Rd,<br>
                              Plymouth, Devon PL6 8BX
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="text-center border-top pt-3 copyrightBottom">
        <p>
            <strong>Copyright &copy; {{date('Y')}} <a href="https://thinkbitsecurity.co.uk/" target="_blank">BluescreenIT LTD.</a></strong> All rights reserved. All trademarks acknowledged
        </p>
    </div>
</footer>

<style>
    @media (max-width: 768px) {
        footer h5 {
            margin-top: 30px;
        }
    }

    .bg-primary {
        background-color: #2C3C64 !important;
    }
    /* Custom Footer Styling */
    .footerTop, footer {
        background-color: #2C3C64 !important; /* Primary dark blue */
        color: white;
    }

    footer h5 {
        border-bottom: 8px solid #C55359; /* Red underline */
        display: inline-block;
        padding-bottom: 6px;
        margin-bottom: 20px;
    }

    .footerTop a, .aHoverColour {
        text-decoration: none;
        color: white;
        transition: 300ms;
    }
    .aHoverColour:hover {
        color: #C55359;
        text-decoration: underline;
    }

    footer ul {
        padding-left: 0;
        list-style: none;
    }

    footer ul li {
        margin-bottom: 12px;
    }

    .copyrightBottom {
        background-color: white !important;
        color: black;
        margin-top: 45px;
    }

    /* Logo Styling */
    .logo img {
        max-height: 50px;
    }

    footer {
        padding-top: 45px
    }

    .logoDescFooter {
        margin-top: 15px;
        margin-bottom: 15px;
    }
</style>
