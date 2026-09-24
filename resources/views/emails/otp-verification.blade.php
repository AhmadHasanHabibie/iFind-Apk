<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kode Verifikasi Akun i-Find</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: #f8fafc;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            color: #1e293b;
        }
        .container {
            max-width: 540px;
            margin: 40px auto;
            background: #ffffff;
            border-radius: 20px;
            border: 1px solid #e2e8f0;
            overflow: hidden;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
        }
        .header {
            background: #2563eb;
            padding: 32px 24px;
            text-align: center;
            color: #ffffff;
        }
        .logo {
            display: inline-block;
            width: 44px;
            height: 44px;
            line-height: 44px;
            background: #ffffff;
            color: #2563eb;
            font-size: 24px;
            font-weight: 900;
            border-radius: 14px;
            margin-bottom: 12px;
        }
        .header h1 {
            margin: 0;
            font-size: 20px;
            font-weight: 800;
            letter-spacing: -0.5px;
        }
        .body {
            padding: 36px 32px;
            text-align: center;
        }
        .greeting {
            font-size: 16px;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 8px;
        }
        .desc {
            font-size: 14px;
            color: #64748b;
            line-height: 1.6;
            margin-bottom: 28px;
        }
        .otp-box {
            display: inline-block;
            background: #eff6ff;
            border: 2px dashed #93c5fd;
            border-radius: 16px;
            padding: 16px 32px;
            margin: 0 auto 28px auto;
        }
        .otp-code {
            font-family: 'Courier New', Courier, monospace;
            font-size: 34px;
            font-weight: 900;
            letter-spacing: 8px;
            color: #1d4ed8;
            margin: 0;
        }
        .note {
            font-size: 12px;
            color: #94a3b8;
            line-height: 1.5;
        }
        .footer {
            background: #f8fafc;
            border-top: 1px solid #f1f5f9;
            padding: 20px;
            text-align: center;
            font-size: 11px;
            color: #94a3b8;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">i</div>
            <h1>Verifikasi Akun i-Find</h1>
        </div>
        <div class="body">
            <div class="greeting">Halo, {{ $user->name }}! 👋</div>
            <div class="desc">
                Terima kasih telah mendaftar di <strong>i-Find</strong> (Platform Spot Nongkrong Hemat & Booking Meja Pelajar).<br>
                Gunakan kode OTP berikut untuk memverifikasi akun Anda:
            </div>

            <div class="otp-box">
                <p class="otp-code">{{ $otp }}</p>
            </div>

            <div class="note">
                ⏱️ Kode OTP ini berlaku selama <strong>10 menit</strong>.<br>
                Demi keamanan akun Anda, jangan pernah membagikan kode ini kepada siapapun termasuk pihak i-Find.
            </div>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} i-Find Platform. Dikirim secara otomatis, mohon tidak membalas email ini.
        </div>
    </div>
</body>
</html>
