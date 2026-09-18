<!DOCTYPE html>
<html>
<head>
    <title>{{ env("APP_NAME") }} BPSS and DBS Application</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #F8F8F8;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #FFFFFF;
            border-radius: 8px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        .top-bar {
            background-color: #C55359;
            height: 8px;
            width: 100%;
        }
        .header {
            padding: 20px;
            text-align: right;
        }
        .header img {
            height: 50px;
        }
        .content {
            padding: 30px;
            text-align: left;
            color: #333;
        }
        .cta-button {
            display: block;
            width: 100%;
            max-width: 200px;
            text-align: center;
            background-color: #C55359;
            color: #FFFFFF;
            padding: 12px;
            font-size: 16px;
            font-weight: bold;
            border-radius: 5px;
            text-decoration: none;
            margin: 15px 0;
        }
        .cta-button:hover {
            background-color: #B03D42;
        }
        .footer {
            background-color: #F8F8F8;
            text-align: center;
            padding: 15px;
            font-size: 12px;
            color: #888;
        }
    </style>
</head>
<body>

    <div class="container">
        <!-- Top Bar -->
        <div class="top-bar"></div>

        <!-- Header with Logo -->
        <div class="header">
            <img src="{{ URL::to('/images/logo_getclassifiedBackground.jpg') }}" alt="{{ env('APP_NAME') }} Logo">
        </div>

        <!-- Email Content -->
        <div class="content">
            <h2>Employment Screening Application</h2>
            <p>Dear Applicant,</p>
            <p>You have been invited to complete a standalone DBS check and/or a Baseline Personnel Security Standard (BPSS) check. As part of this process, you will need to complete a DBS ‘Criminal Record Check.’</p>

            <!-- Registration CTA -->
            <p><strong>Please click the button below to register your account:</strong></p>
            <a href="{{ $registrationLink }}" target="_blank" class="cta-button">Register Now</a>

            <p>Ensure that you <strong>fully complete your profile</strong> before leaving the page.</p>

            <p><strong>Note:</strong> This registration link is for first-time access only. If you have already registered and need to log in, please use the link below:</p>

            <!-- Login CTA -->
            <a href="{{ env('APP_URL') }}login" target="_blank" class="cta-button">Log in</a>

            <p>You may edit your forms after initially submitting them.</p>

            <p><strong>Important:</strong> Please complete your application and upload all required ID documentation within <strong>48 hours</strong> of receiving this email. Failure to do so may result in delays in processing your clearance.</p>

            <p>Ensure that all ID documents are current and reflect your current address. P60s can be accepted as proof of employment.</p>

            <p><strong>BIT Group</strong> will be processing your security clearance, and you will receive further emails from our security clearance team regarding your application.</p>

            <p>Best regards,</p>
            <p><strong>The Security Vetting Team</strong></p>

            <hr>

            <p style="font-size: 12px; color: #666;"><strong>Note:</strong> This is an automated email, and replies will not be received. If you need assistance, please contact our vetting team at <a href="mailto:screening@thinkbitgroup.co.uk">screening@thinkbitgroup.co.uk</a>. All calls from our Security Vetting Team will originate from our Plymouth office (area code <strong>01752</strong>).</p>

            <!-- BIT Group Logo -->
            <div style="text-align: center; margin-top: 20px;">
                <img src="{{ URL::to('/images/BIT_LOGO_PESA.png') }}" style="width: 120px; height: auto;" alt="BIT Group Logo">
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            &copy; {{ date('Y') }} Get ClassifIeD. All rights reserved.
        </div>
    </div>

</body>
</html>
