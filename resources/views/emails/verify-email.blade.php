<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Your Email - OJT360</title>
    <style>
        body {
            font-family: 'Inter', Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f8f9fa;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .header {
            background: linear-gradient(135deg, #8B0000, #A0522D);
            padding: 30px;
            text-align: center;
        }
        .logo {
            width: 60px;
            height: 60px;
            background: white;
            border-radius: 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
        }
        .logo-text {
            color: #8B0000;
            font-size: 24px;
            font-weight: bold;
        }
        .header h1 {
            color: white;
            margin: 0;
            font-size: 28px;
            font-weight: 600;
        }
        .content {
            padding: 40px 30px;
        }
        .welcome-text {
            font-size: 18px;
            color: #343A40;
            margin-bottom: 20px;
            line-height: 1.6;
        }
        .verify-button {
            display: inline-block;
            background: linear-gradient(135deg, #8B0000, #A0522D);
            color: white;
            padding: 15px 30px;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 16px;
            margin: 20px 0;
            transition: transform 0.2s;
        }
        .verify-button:hover {
            transform: translateY(-2px);
        }
        .instructions {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            border-left: 4px solid #8B0000;
        }
        .footer {
            background-color: #343A40;
            color: white;
            padding: 20px;
            text-align: center;
            font-size: 14px;
        }
        .footer a {
            color: #FFD700;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">
                <span class="logo-text">OJT</span>
            </div>
            <h1>OJT360</h1>
        </div>
        
        <div class="content">
            <h2 style="color: #8B0000; margin-bottom: 20px;">Welcome to OJT360!</h2>
            
            <p class="welcome-text">
                Hi {{ $user->name }},<br><br>
                Thank you for registering with OJT360! We're excited to have you join our internship monitoring platform.
            </p>
            
            <p class="welcome-text">
                To complete your registration and start using OJT360, please verify your email address by clicking the button below:
            </p>
            
            <div style="text-align: center;">
                <a href="{{ $verificationUrl }}" class="verify-button">
                    Verify Email Address
                </a>
            </div>
            
            <div class="instructions">
                <h3 style="color: #8B0000; margin-top: 0;">What happens next?</h3>
                <ul style="color: #343A40; line-height: 1.6;">
                    <li>Click the verification button above</li>
                    <li>Your account will be activated</li>
                    <li>You can access your OJT360 dashboard</li>
                    <li>Start monitoring your internship progress</li>
                </ul>
            </div>
            
            <p style="color: #6c757d; font-size: 14px; margin-top: 30px;">
                If you didn't create an account with OJT360, you can safely ignore this email.
            </p>
        </div>
        
        <div class="footer">
            <p>© {{ date('Y') }} OJT360. All rights reserved.</p>
            <p>This email was sent to {{ $user->email }}</p>
        </div>
    </div>
</body>
</html>
