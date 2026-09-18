<!DOCTYPE html>
<html>
<head>
    <title>{{ env('APP_COMPANY_NAME') }} Password Reset</title>
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
        .header {
            background-color: #C55359;
            padding: 20px;
            text-align: center;
            color: #FFFFFF;
            font-size: 20px;
            font-weight: bold;
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
            margin: 20px auto;
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
        <!-- Header -->
        <div class="header">
            Password Reset Request
        </div>

        <!-- Email Content -->
        <div class="content">
            <p>Dear User,</p>
            <p>We received a request to reset your password for **Get ClassifIeD**.</p>

            <!-- Reset Password CTA -->
            <p><strong>Please click the button below to reset your password:</strong></p>
            <a href="{{ $resetLink }}" target="_blank" class="cta-button">Reset Password</a>

            <p><strong>Important:</strong> This link is valid until <strong>{{ date('d M Y H:i:s',strtotime($resetLinkExpire)) }}</strong>. After this time, you will need to request a new password reset.</p>

            <p>If you did not request this password reset, please ignore this email.</p>

            <p>Best regards,</p>
            <p><strong>The Get ClassifIeD Team</strong></p>
        </div>

        <!-- Footer -->
        <div class="footer">
            &copy; {{ date('Y') }} Get ClassifIeD. All rights reserved.
        </div>
    </div>

</body>
</html>
