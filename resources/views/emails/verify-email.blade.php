<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verification</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            padding: 20px;
            border: 1px solid #dddddd;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .header {
            text-align: center;
            padding: 10px 0;
            border-bottom: 1px solid #dddddd;
        }
        .header h1 {
            margin: 0;
            color: #333333;
        }
        .content {
            padding: 20px;
        }
        .content p {
            font-size: 16px;
            line-height: 1.6;
            color: #555555;
        }
        .content p.verification-link {
            font-size: 20px;
            font-weight: bold;
            color: #333333;
            text-align: center;
            margin: 20px 0;
        }
        .footer {
            text-align: center;
            padding: 10px 0;
            border-top: 1px solid #dddddd;
            font-size: 14px;
            color: #888888;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Email Verification</h1>
        </div>
        <div class="content">
            <p>Dear User,</p>
            <p>Thank you for registering with I-Plus. Please verify your email address by clicking the link below.</p>
            <p class="verification-link"><a href="{{ $verificationUrl }}">Verify Email Address</a></p>
            <p>If you did not create an account, no further action is required.</p>
            <p>Thank you,<br>The I-Plus Team</p>
        </div>
        <div class="footer">
            <p>&copy; 2024 I-Plus. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
