<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Selamat Datang di Staflo</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background-color: #F0F2F5;
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            -webkit-font-smoothing: antialiased;
            margin: 0;
            padding: 0;
        }

        .wrapper {
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
        }

        .email-table {
            width: 100%;
            background-color: #F0F2F5;
        }

        .email-table-cell {
            padding: 40px 16px;
        }

        .header {
            text-align: center;
            padding: 0 0 32px;
        }

        .logo {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
        }

        .logo-icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #1E40AF 0%, #3B82F6 100%);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .logo-text {
            font-size: 22px;
            font-weight: 700;
            color: #111827;
            letter-spacing: -0.5px;
        }

        .card {
            background: #FFFFFF;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.06), 0 8px 24px rgba(0, 0, 0, 0.05);
        }

        .card-hero {
            background: linear-gradient(135deg, #1E3A8A 0%, #1D4ED8 50%, #2563EB 100%);
            padding: 40px 40px 48px;
            position: relative;
            overflow: hidden;
        }

        .card-hero::before {
            content: '';
            position: absolute;
            top: -60px;
            right: -60px;
            width: 200px;
            height: 200px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.06);
        }

        .card-hero::after {
            content: '';
            position: absolute;
            bottom: -40px;
            left: -30px;
            width: 160px;
            height: 160px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.04);
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(255, 255, 255, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 100px;
            padding: 6px 14px;
            margin-bottom: 20px;
        }

        .hero-badge span {
            font-size: 12px;
            font-weight: 600;
            color: rgba(255, 255, 255, 0.9);
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        .hero-badge-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: #4ADE80;
        }

        .hero-title {
            font-size: 30px;
            font-weight: 700;
            color: #FFFFFF;
            line-height: 1.25;
            letter-spacing: -0.5px;
            margin-bottom: 10px;
        }

        .hero-subtitle {
            font-size: 15px;
            color: rgba(255, 255, 255, 0.7);
            line-height: 1.6;
        }

        .card-body {
            padding: 36px 40px;
        }

        .greeting {
            font-size: 16px;
            color: #374151;
            line-height: 1.7;
            margin-bottom: 28px;
        }

        .greeting strong {
            color: #111827;
            font-weight: 600;
        }

        .credentials-box {
            background: #F8FAFF;
            border: 1px solid #DBEAFE;
            border-radius: 14px;
            padding: 24px 28px;
            margin-bottom: 28px;
            position: relative;
        }

        .credentials-header {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 20px;
            padding-bottom: 16px;
            border-bottom: 1px solid #DBEAFE;
        }

        .credentials-icon {
            width: 34px;
            height: 34px;
            background: #1D4ED8;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .credentials-title {
            font-size: 14px;
            font-weight: 700;
            color: #1E3A8A;
            letter-spacing: 0.3px;
            text-transform: uppercase;
        }

        .credential-row {
            display: table;
            width: 100%;
            table-layout: fixed;
            padding: 8px 0;
        }

        .credential-row:not(:last-child) {
            border-bottom: 1px solid #EFF6FF;
        }

        .credential-label {
            display: table-cell;
            font-size: 13px;
            color: #6B7280;
            font-weight: 500;
            vertical-align: middle;
            text-align: left;
            width: 38%;
        }

        .credential-colon {
            display: table-cell;
            width: 16px;
            font-size: 13px;
            color: #6B7280;
            font-weight: 500;
            text-align: center;
            vertical-align: middle;
        }

        .credential-content {
            display: table-cell;
            text-align: left;
            vertical-align: middle;
            padding-left: 8px;
        }

        .credential-value {
            display: inline-block;
            font-size: 14px;
            color: #111827;
            font-weight: 600;
            background: #FFFFFF;
            border: 1px solid #E5E7EB;
            border-radius: 7px;
            padding: 5px 12px;
            font-family: 'SF Mono', 'Fira Code', 'Courier New', monospace;
            letter-spacing: 0.3px;
            text-align: center;
        }

        .credential-badge {
            display: inline-block;
            font-size: 13px;
            font-weight: 600;
            color: #1D4ED8;
            background: #DBEAFE;
            border-radius: 7px;
            padding: 5px 12px;
            text-align: center;
        }

        .steps-section {
            margin-bottom: 32px;
        }

        .steps-title {
            font-size: 15px;
            font-weight: 700;
            color: #111827;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .step-item {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            padding: 8px 0;
        }

        .step-item:not(:last-child) {
            border-bottom: 1px solid #F3F4F6;
        }

        .step-number {
            width: 28px;
            height: 28px;
            background: #1D4ED8;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            font-weight: 700;
            color: #FFFFFF;
            flex-shrink: 0;
            margin-top: 1px;
        }

        .step-text {
            font-size: 12px;
            color: #374151;
            line-height: 1.6;
            padding-top: 3px;
        }

        .cta-button {
            display: block;
            background: linear-gradient(135deg, #1E3A8A 0%, #2563EB 100%);
            color: #FFFFFF !important;
            text-decoration: none;
            text-align: center;
            font-size: 15px;
            font-weight: 600;
            padding: 15px 24px;
            border-radius: 12px;
            margin-bottom: 28px;
            letter-spacing: 0.2px;
            -webkit-text-fill-color: #FFFFFF !important;
        }

        .cta-button:link,
        .cta-button:visited,
        .cta-button:hover,
        .cta-button:active {
            color: #FFFFFF !important;
            text-decoration: none !important;
            -webkit-text-fill-color: #FFFFFF !important;
        }

        .help-box {
            background: #FFFBEB;
            border: 1px solid #FDE68A;
            border-radius: 12px;
            padding: 16px 20px;
            display: flex;
            align-items: flex-start;
            gap: 12px;
            margin-bottom: 28px;
        }

        .help-icon {
            width: 20px;
            height: 20px;
            flex-shrink: 0;
            margin-top: 1px;
        }

        .help-text {
            font-size: 13px;
            color: #92400E;
            line-height: 1.6;
        }

        .help-text strong {
            color: #78350F;
        }

        .divider {
            height: 1px;
            background: #F3F4F6;
            margin: 0 0 28px;
        }

        .signature {
            font-size: 14px;
            color: #6B7280;
            line-height: 1.7;
        }

        .signature strong {
            color: #111827;
            font-weight: 600;
        }

        .footer {
            text-align: center;
            padding: 28px 0 0;
        }

        .footer-text {
            font-size: 12px;
            color: #9CA3AF;
            line-height: 1.7;
        }

        .footer-links {
            margin-top: 8px;
        }

        .footer-links a {
            font-size: 12px;
            color: #6B7280;
            text-decoration: none;
            margin: 0 8px;
        }

        @media screen and (max-width: 600px) {
            .email-table-cell {
                padding: 24px 10px !important;
            }

            .wrapper,
            .card {
                border-radius: 14px !important;
            }

            .card-hero {
                padding: 28px 20px 30px !important;
            }

            .hero-badge {
                margin-bottom: 14px !important;
                padding: 5px 12px !important;
            }

            .hero-title {
                font-size: 24px !important;
                line-height: 1.3 !important;
            }

            .hero-subtitle {
                font-size: 14px !important;
                line-height: 1.55 !important;
            }

            .card-body {
                padding: 22px 18px !important;
            }

            .greeting {
                font-size: 14px !important;
                line-height: 1.65 !important;
                margin-bottom: 20px !important;
            }

            .credentials-box {
                padding: 16px 14px !important;
                margin-bottom: 20px !important;
                border-radius: 12px !important;
            }

            .credentials-header {
                margin-bottom: 14px !important;
                padding-bottom: 12px !important;
            }

            .credentials-title {
                font-size: 12px !important;
            }

            .credential-row {
                display: block !important;
                padding: 9px 0 !important;
            }

            .credential-label,
            .credential-colon,
            .credential-content {
                display: block !important;
                width: 100% !important;
                text-align: left !important;
                padding-left: 0 !important;
            }

            .credential-colon {
                display: none !important;
            }

            .credential-label {
                margin-bottom: 6px !important;
                font-size: 12px !important;
            }

            .credential-value,
            .credential-badge {
                display: block !important;
                width: 100% !important;
                text-align: left !important;
                font-size: 13px !important;
                padding: 7px 10px !important;
                word-break: break-word !important;
                overflow-wrap: anywhere !important;
            }

            .steps-section {
                margin-bottom: 20px !important;
            }

            .steps-title {
                font-size: 14px !important;
                margin-bottom: 10px !important;
            }

            .step-item {
                gap: 8px !important;
                padding: 7px 0 !important;
            }

            .step-number {
                width: 24px !important;
                height: 24px !important;
                font-size: 12px !important;
                border-radius: 6px !important;
            }

            .step-text {
                font-size: 12px !important;
                line-height: 1.55 !important;
            }

            .cta-button {
                font-size: 14px !important;
                padding: 13px 16px !important;
                border-radius: 10px !important;
                margin-bottom: 20px !important;
            }

            .help-box {
                padding: 12px 14px !important;
                gap: 8px !important;
                margin-bottom: 20px !important;
            }

            .help-text {
                font-size: 12px !important;
                line-height: 1.55 !important;
            }

            .divider {
                margin-bottom: 20px !important;
            }

            .signature {
                font-size: 13px !important;
                line-height: 1.6 !important;
            }

            .footer {
                padding-top: 18px !important;
            }

            .footer-text {
                font-size: 11px !important;
                line-height: 1.6 !important;
            }

            .footer-links {
                margin-top: 10px !important;
            }

            .footer-links a {
                display: inline-block !important;
                margin: 4px 6px !important;
                font-size: 11px !important;
            }
        }

        @media screen and (max-width: 420px) {
            .card-hero {
                padding: 24px 16px 26px !important;
            }

            .hero-title {
                font-size: 21px !important;
            }

            .hero-subtitle {
                font-size: 13px !important;
            }

            .card-body {
                padding: 18px 14px !important;
            }

            .credentials-box {
                padding: 14px 12px !important;
            }

            .credential-value,
            .credential-badge {
                font-size: 12px !important;
                padding: 6px 8px !important;
            }
        }
    </style>
</head>

<body>
    <table role="presentation" cellpadding="0" cellspacing="0" border="0" class="email-table">
        <tr>
            <td align="center" class="email-table-cell">
                <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="600"
                    style="width:100%;max-width:600px;">
                    <tr>
                        <td>
                            <div class="wrapper">

                                <!-- Main Card -->
                                <div class="card">

                                    <!-- Hero Section -->
                                    <div class="card-hero">
                                        <div class="hero-badge">
                                            <span>Akun Berhasil Dibuat</span>
                                        </div>
                                        <div class="hero-title">Selamat Datang,<br>{{ $userName }}!</div>
                                        <div class="hero-subtitle">Akun Anda di sistem Staflo telah berhasil
                                            disiapkan.<br>Berikut adalah
                                            informasi untuk memulai.</div>
                                    </div>

                                    <!-- Body -->
                                    <div class="card-body">

                                        <p class="greeting">
                                            Halo <strong>{{ $userName }}</strong>, akun Anda telah aktif dan siap
                                            digunakan.
                                            Gunakan kredensial di bawah ini untuk masuk pertama kali ke sistem Staflo.
                                        </p>

                                        <!-- Credentials Box -->
                                        <div class="credentials-box">
                                            <div class="credentials-header">

                                                <span class="credentials-title">Informasi Login</span>
                                            </div>

                                            <div class="credential-row">
                                                <span class="credential-label">Email</span>
                                                <span class="credential-colon">:</span>
                                                <span class="credential-content">
                                                    <span class="credential-value">{{ $userEmail }}</span>
                                                </span>
                                            </div>
                                            <div class="credential-row">
                                                <span class="credential-label">Password Sementara</span>
                                                <span class="credential-colon">:</span>
                                                <span class="credential-content">
                                                    <span class="credential-value">{{ $userPassword }}</span>
                                                </span>
                                            </div>
                                            <div class="credential-row">
                                                <span class="credential-label">Posisi</span>
                                                <span class="credential-colon">:</span>
                                                <span class="credential-content">
                                                    <span class="credential-badge">{{ $userPosition }}</span>
                                                </span>
                                            </div>
                                        </div>

                                        <!-- Steps -->
                                        <div class="steps-section">
                                            <div class="steps-title">

                                                Langkah Setelah Login Pertama
                                            </div>

                                            <div class="step-item">
                                                <div class="step-text">Segera <strong>ubah password</strong>
                                                    sementara Anda ke password yang
                                                    lebih kuat dan personal.</div>
                                            </div>
                                            <div class="step-item">
                                                <div class="step-text"><strong>Simpan password baru</strong> di tempat
                                                    yang aman, seperti
                                                    aplikasi password manager.</div>
                                            </div>
                                            <div class="step-item">
                                                <div class="step-text"><strong>Jangan bagikan</strong> kredensial akun
                                                    kepada siapapun, termasuk
                                                    administrator.</div>
                                            </div>
                                        </div>

                                        <!-- CTA Button -->
                                        <a href="{{ $appDownloadLink }}" class="cta-button"
                                            style="color:#ffffff !important;text-decoration:none !important;-webkit-text-fill-color:#ffffff !important;">Unduh
                                            Aplikasi
                                            Staflo</a>

                                        <!-- Help Notice -->
                                        <div class="help-box">
                                            <svg class="help-icon" viewBox="0 0 20 20" fill="none">
                                                <path
                                                    d="M10 18C14.4183 18 18 14.4183 18 10C18 5.58172 14.4183 2 10 2C5.58172 2 2 5.58172 2 10C2 14.4183 5.58172 18 10 18Z"
                                                    stroke="#B45309" stroke-width="1.5" />
                                                <path d="M10 10V14M10 7V6" stroke="#B45309" stroke-width="1.5"
                                                    stroke-linecap="round" />
                                            </svg>
                                            <div class="help-text">
                                                <strong>Butuh bantuan?</strong> Jika Anda mengalami kesulitan masuk
                                                atau memiliki pertanyaan,
                                                silakan hubungi administrator sistem Anda.
                                            </div>
                                        </div>

                                        <div class="divider"></div>

                                        <!-- Signature -->
                                        <div class="signature">
                                            Salam hangat,<br>
                                            <strong>Tim Staflo</strong>
                                        </div>

                                    </div>
                                </div>

                                <!-- Footer -->
                                <div class="footer">
                                    <p class="footer-text">
                                        Email ini dikirim secara otomatis oleh sistem Staflo.<br>
                                        Harap tidak membalas email ini.
                                    </p>
                                    <div class="footer-links">
                                        <a href="#">Kebijakan Privasi</a>
                                        <a href="#">Bantuan</a>
                                        <a href="#">Hubungi Kami</a>
                                    </div>
                                </div>

                            </div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>
