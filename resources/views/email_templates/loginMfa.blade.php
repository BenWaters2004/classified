<!DOCTYPE html>
<html>
<head>
    <title>Multi-Factor Authentication Code</title>
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
        .mfa-code {
            display: block;
            font-size: 24px;
            font-weight: bold;
            color: #C55359;
            text-align: center;
            padding: 10px;
            background-color: #F0F0F0;
            border-radius: 4px;
            margin: 20px 0;
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
            <img src="{{ URL::to('/images/logo_getclassifiedBackground.jpg') }}" alt="Get ClassifIeD Logo">
        </div>

        <!-- Email Content -->
        <div class="content">
            <h2>Multi-Factor Authentication Code</h2>
            <p>Dear User,</p>
            <p>To ensure the security of your account, we require multi-factor authentication (MFA) when accessing your account. Please use the code below to complete your login:</p>
            
            <!-- MFA Code -->
            <span class="mfa-code">{{ $mfacode }}</span>

            <p><strong>Important:</strong> This code is valid for only <strong>10 minutes</strong>. If you did not request this code, please ignore this email and change your password.</p>

            <p>If you experience any issues, please contact our support team screening@thinkbitgroup.co.uk.</p>
            
            <p>Best regards,</p>
            <p><strong>Get ClassifIeD Security Team</strong></p>
        </div>

        <!-- Footer -->
        <div class="footer">
            &copy; {{ date('Y') }} Get ClassifIeD. All rights reserved.
        </div>
    </div>

</body>
</html>
