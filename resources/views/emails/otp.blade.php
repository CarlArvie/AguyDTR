<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>OTP Verification</title>
  </head>

  <body style="margin:0;padding:0;background:#f4f6fb;font-family:Arial,Helvetica,sans-serif;">
    <!-- Preheader (hidden preview text) -->
    <div style="display:none;max-height:0;overflow:hidden;opacity:0;color:transparent;">
      Your verification code is {{ $otp }}. It expires in 10 minutes.
    </div>

    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#f4f6fb;padding:32px 12px;">
      <tr>
        <td align="center">
          <!-- Card -->
          <table role="presentation" width="100%" cellspacing="0" cellpadding="0"
            style="max-width:560px;background:#ffffff;border-radius:16px;overflow:hidden;box-shadow:0 12px 30px rgba(17,24,39,0.10);">

            <!-- Header strip -->
            <tr>
              <td style="background:linear-gradient(135deg,#4f46e5,#7c3aed);padding:22px 24px;">
                <div style="color:#fff;font-size:16px;opacity:0.95;letter-spacing:0.2px;">
                  Verification
                </div>
                <div style="color:#fff;font-size:22px;font-weight:700;margin-top:6px;">
                  Confirm your sign-in
                </div>
              </td>
            </tr>

            <!-- Body -->
            <tr>
              <td style="padding:24px;">
                <p style="margin:0 0 10px;color:#111827;font-size:16px;line-height:24px;">
                  Hi there 👋
                </p>
                <p style="margin:0 0 18px;color:#4b5563;font-size:14px;line-height:22px;">
                  Use the code below to verify your account. This code expires in <b>10 minutes</b>.
                </p>

                <!-- OTP block -->
                <div style="background:#f3f4f6;border:1px dashed #d1d5db;border-radius:14px;padding:16px;text-align:center;">
                  <div style="color:#6b7280;font-size:12px;letter-spacing:0.08em;text-transform:uppercase;">
                    Verification Code
                  </div>

                  <div
                    style="margin-top:10px;font-size:34px;font-weight:800;letter-spacing:0.18em;color:#111827;">
                    {{ $otp }}
                  </div>

                  <div style="margin-top:10px;color:#9ca3af;font-size:12px;line-height:18px;">
                    Tap and hold (mobile) or highlight and copy (desktop).
                  </div>
                </div>

                <!-- Security note -->
                <div style="margin-top:18px;padding:14px 14px;background:#eef2ff;border:1px solid #e0e7ff;border-radius:14px;">
                  <div style="color:#3730a3;font-weight:700;font-size:13px;margin-bottom:4px;">
                    Didn’t request this?
                  </div>
                  <div style="color:#4b5563;font-size:13px;line-height:19px;">
                    You can safely ignore this email. Don’t share this code with anyone.
                  </div>
                </div>

                <p style="margin:18px 0 0;color:#9ca3af;font-size:12px;line-height:18px;">
                  This is an automated message—please do not reply.
                </p>
              </td>
            </tr>
          </table>
        </td>
      </tr>
    </table>
  </body>
</html>
