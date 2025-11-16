<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #4a5568;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f7fafc;
        }
        .header {
            background-color: #1a202c;
            color: white;
            padding: 30px 20px;
            text-align: left;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: normal;
        }
        .content {
            background-color: white;
            padding: 30px 25px;
        }
        .content p {
            margin: 0 0 12px 0;
            color: #4a5568;
            font-size: 14px;
        }
        .info-box {
            background-color: #f0f9ff;
            border-left: 4px solid #0ea5e9;
            padding: 12px 15px;
            margin: 15px 0;
            font-size: 13px;
        }
        .warning-box {
            background-color: #fef3c7;
            border-left: 4px solid #f59e0b;
            padding: 12px 15px;
            margin: 15px 0;
            font-size: 13px;
        }
        .button {
            display: inline-block;
            padding: 12px 24px;
            background-color: #8B0000;
            color: white !important;
            text-decoration: none;
            border-radius: 4px;
            margin: 15px 0;
            font-weight: 500;
            font-size: 14px;
        }
        .steps {
            font-size: 13px;
            margin: 15px 0;
        }
        .steps ol {
            margin: 8px 0;
            padding-left: 18px;
        }
        .steps li {
            margin: 4px 0;
        }
        .link-text {
            font-size: 12px;
            color: #4299e1;
            word-break: break-all;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            padding-top: 15px;
            color: #718096;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>OJT360</h1>
    </div>

    <div class="content">
        <h2 style="margin-top: 0; color: #1a202c; font-size: 18px;">OJT Acceptance Letter Request</h2>

        <p>Dear <strong>{{ $acceptanceRequest->supervisor_name }}</strong>,</p>

        <p><strong>{{ $acceptanceRequest->student->name }}</strong> from Eastern Visayas State University has indicated that you accepted them for an internship at <strong>{{ $acceptanceRequest->company_name }}</strong>.</p>

        <div class="info-box">
            <strong>Student Details:</strong><br>
            {{ $acceptanceRequest->student->name }} • {{ $acceptanceRequest->student->studentProfile->course ?? 'Student' }}<br>
            Position: {{ $acceptanceRequest->position }}<br>
            Email: {{ $acceptanceRequest->student->email }}
        </div>

        <p><strong>What you need to do:</strong></p>
        <p>Click the button below to complete your registration. This is a quick process that takes about 3 minutes.</p>

        <center>
            <a href="{{ route('supervisor.acceptance.show', $acceptanceRequest->token) }}" class="button">
                Complete Registration
            </a>
        </center>

        <div class="steps">
            <strong>How it works:</strong>
            <ol>
                <li><strong>First time?</strong> Create your supervisor account (one-time, 3 minutes)</li>
                <li><strong>Already have an account?</strong> Just log in</li>
                <li>Access your dashboard to manage acceptance letters</li>
                <li>Generate professional acceptance letters for your students</li>
                <li>Letters are automatically sent to students and coordinators</li>
            </ol>
        </div>

        <div class="warning-box">
            <strong>Important Notes:</strong><br>
            • This link is unique and secure<br>
            • Link expires: <strong>{{ $acceptanceRequest->expires_at->format('M d, Y') }}</strong><br>
            • If you have multiple students, you only need to register once<br>
            • After registration, you can generate letters anytime from your dashboard
        </div>

        <p style="margin-top: 15px;">If you have questions or didn't accept this student, please contact them directly at: <strong>{{ $acceptanceRequest->student->email }}</strong></p>

        <p class="link-text" style="margin-top: 15px;">Can't click the button? Copy this link:<br>{{ route('supervisor.acceptance.show', $acceptanceRequest->token) }}</p>
    </div>

    <div class="footer">
        <p>This is an automated message from OJT360.</p>
    </div>
</body>
</html>
