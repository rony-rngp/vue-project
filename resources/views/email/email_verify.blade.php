{{-- resources/views/emails/otp.blade.php --}}
    <!DOCTYPE html>
<html>
<head>
    <style>
        .container {
            max-width: 480px;
            margin: auto;
            padding: 20px;
            font-family: Arial, sans-serif;
            border: 1px solid #eee;
            border-radius: 8px;
            background: #f9f9f9;
        }
        .otp-box {
            background: #ffffff;
            padding: 20px;
            font-size: 24px;
            text-align: center;
            border: 2px dashed #007bff;
            color: #007bff;
            margin: 20px 0;
            border-radius: 6px;
        }
        .footer {
            font-size: 12px;
            color: #888;
            text-align: center;
            margin-top: 30px;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Verify Your Email</h2>
    <p>Hello {{ $user->name }},</p>
    <p>Your One-Time Password (OTP) is:</p>
    <div class="otp-box">{{ $otp }}</div>
    <p>This code will expire in 10 minutes. Please do not share it with anyone.</p>
    <p>If you didn’t request this, you can safely ignore it.</p>
    <div class="footer">
        &copy; {{ date('Y') }} {{ get_settings('website_name') }}. All rights reserved.
    </div>
</div>
</body>
</html>
