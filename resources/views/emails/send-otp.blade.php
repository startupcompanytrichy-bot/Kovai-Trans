<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset OTP - Transport ERP</title>
    <style>
        body { font-family: 'Segoe UI', Roboto, Arial, sans-serif; background: #f1f5f9; margin: 0; padding: 0; }
        .container { max-width: 520px; margin: 30px auto; background: #fff; border-radius: 20px; overflow: hidden; box-shadow: 0 8px 32px rgba(15,23,42,0.08); }
        .header { background: linear-gradient(135deg, #0f172a, #1e293b); padding: 36px 32px 28px; text-align: center; }
        .header h1 { color: #fff; margin: 0; font-size: 24px; font-weight: 700; letter-spacing: 0.3px; }
        .header p { color: #94a3b8; font-size: 13px; margin: 8px 0 0; }
        .body { padding: 36px 32px 28px; }
        .greeting { font-size: 15px; color: #0f172a; font-weight: 600; margin: 0 0 6px; }
        .body p { color: #475569; font-size: 14px; line-height: 1.7; margin: 0 0 16px; }
        .otp-label { text-align: center; font-size: 13px; color: #64748b; margin: 20px 0 6px; text-transform: uppercase; letter-spacing: 1px; font-weight: 600; }
        .otp { text-align: center; font-size: 40px; font-weight: 800; letter-spacing: 10px; color: #2563eb; background: #eff6ff; padding: 18px 16px; border-radius: 14px; margin: 0 0 20px; border: 1px solid #dbeafe; }
        .divider { height: 1px; background: #e2e8f0; margin: 20px 0; }
        .note { background: #f8fafc; border-left: 4px solid #3b82f6; padding: 14px 16px; border-radius: 10px; margin: 16px 0; }
        .note strong { display: block; font-size: 13px; color: #0f172a; margin-bottom: 4px; }
        .note p { font-size: 13px; color: #64748b; margin: 0; line-height: 1.6; }
        .warning { background: #fef2f2; border-left: 4px solid #ef4444; padding: 14px 16px; border-radius: 10px; margin: 16px 0; }
        .warning strong { display: block; font-size: 13px; color: #991b1b; margin-bottom: 4px; }
        .warning p { font-size: 13px; color: #b91c1c; margin: 0; line-height: 1.6; }
        .footer { background: #f8fafc; padding: 24px 32px; text-align: center; }
        .footer p { font-size: 12px; color: #94a3b8; margin: 0 0 4px; line-height: 1.6; }
        .footer .company { font-weight: 600; color: #64748b; font-size: 13px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Password Reset Request</h1>
            <p>Transport ERP &mdash; Secure Account Recovery</p>
        </div>
        <div class="body">
            <p class="greeting">Hi {{ $recipientName }},</p>
            <p>We received a request to reset the password for <strong>{{ $recipientEmail }}</strong>. Use the code below to complete the process.</p>

            <div class="otp-label">Verification Code</div>
            <div class="otp">{{ $otp }}</div>

            <div class="note">
                <strong>Note:</strong>
                <p>This code expires in <strong>2 minutes</strong>. Please use it promptly. After expiration, you will need to request a new code.</p>
            </div>

            <div class="warning">
                <strong>Security:</strong>
                <p>Do not share this code. Our team will never ask for your password or verification code. If you did not make this request, please ignore this email.</p>
            </div>

            <div class="divider"></div>

            <p style="font-size:13px; color:#94a3b8; text-align:center; margin:0;">
                Need help? Contact your system administrator.
            </p>
        </div>
        <div class="footer">
            <p class="company">Transport ERP</p>
            <p>This is an automated message.</p>
            <p>&copy; {{ date('Y') }} Transport ERP. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
