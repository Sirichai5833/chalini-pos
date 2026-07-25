<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset OTP</title>
</head>

<body style="font-family: Arial, sans-serif; background:#f7f7f7; padding:20px;">
    <div
        style="max-width:600px;margin:0 auto;background:#ffffff;padding:32px;border-radius:16px;box-shadow:0 2px 12px rgba(0,0,0,.08);">
        <h2 style="margin-top:0;color:#111;">รหัส OTP สำหรับรีเซ็ตรหัสผ่าน</h2>
        <p style="color:#333;font-size:15px;line-height:1.6;">ใช้รหัสด้านล่างนี้เพื่อยืนยันตัวตนของคุณบนเว็บไซต์ของเรา
            รหัสจะหมดอายุภายใน 10 นาที</p>
        <p style="font-size:28px;font-weight:700;letter-spacing:3px;color:#0a0a0a;">{{ $otp }}</p>
        <p style="color:#777;font-size:14px;line-height:1.6;">หากคุณไม่ได้ร้องขอรหัสนี้ โปรดเพิกเฉยต่ออีเมลฉบับนี้</p>
        <p style="color:#777;font-size:13px;">ขอบคุณ,<br>ทีมงาน</p>
    </div>
</body>

</html>
