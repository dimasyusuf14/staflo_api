<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staflo — Task Assigner</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link
        href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600;700;800&family=DM+Sans:ital,wght@0,300;0,400;0,500;0,600;1,400&display=swap"
        rel="stylesheet">
    <style>
        :root {
            --primary: #00A89E;
            --primary-dark: #007D75;
            --primary-light: #E0F5F4;
            --primary-mid: #00C9BD;
            --secondary: #003D66;
            --secondary-light: #E6EFF5;
            --secondary-mid: #1A5A82;
            --accent: #FF6B35;
            --white: #FFFFFF;
            --off-white: #F7FAFA;
            --gray-50: #F4F6F8;
            --gray-100: #E8ECF0;
            --gray-200: #C9D0D8;
            --gray-400: #8A96A3;
            --gray-600: #4A5568;
            --gray-900: #1A202C;
            --text-primary: #0D1B2A;
            --text-secondary: #4A5568;
            --text-muted: #8A96A3;
            --radius-sm: 8px;
            --radius-md: 14px;
            --radius-lg: 22px;
            --radius-xl: 32px;
            --shadow-soft: 0 2px 20px rgba(0, 168, 158, 0.10);
            --shadow-card: 0 4px 32px rgba(0, 61, 102, 0.10);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DM Sans', sans-serif;
            background: var(--off-white);
            color: var(--text-primary);
            overflow-x: hidden;
        }

        h1,
        h2,
        h3,
        h4 {
            font-family: 'Sora', sans-serif;
        }

        /* ── NAV ── */
        nav {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 100;
            background: rgba(255, 255, 255, 0.92);
            backdrop-filter: blur(16px);
            border-bottom: 1px solid var(--gray-100);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 5%;
            height: 64px;
        }

        .nav-logo {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .logo-mark {
            width: 36px;
            height: 36px;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-family: 'Sora', sans-serif;
            font-weight: 800;
            font-size: 15px;
            letter-spacing: -0.5px;
        }

        .nav-logo-text {
            font-family: 'Sora', sans-serif;
            font-weight: 700;
            font-size: 18px;
            color: var(--primary);
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 32px;
            list-style: none;
        }

        .nav-links a {
            text-decoration: none;
            color: var(--text-secondary);
            font-size: 14px;
            font-weight: 500;
            transition: color 0.2s;
        }

        .nav-links a:hover {
            color: var(--primary);
        }

        .nav-cta {
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 10px;
            padding: 10px 22px;
            font-family: 'DM Sans', sans-serif;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .nav-cta:hover {
            background: var(--primary-dark);
            transform: translateY(-1px);
        }

        /* ── HERO ── */
        .hero {
            min-height: 100vh;
            padding: 100px 5% 80px;
            display: flex;
            align-items: center;
            background: linear-gradient(160deg, var(--off-white) 55%, var(--primary-light) 100%);
            position: relative;
            overflow: hidden;
        }

        .hero::before {
            content: '';
            position: absolute;
            top: -120px;
            right: -80px;
            width: 560px;
            height: 560px;
            background: radial-gradient(circle, rgba(0, 168, 158, 0.12) 0%, transparent 70%);
            border-radius: 50%;
        }

        .hero::after {
            content: '';
            position: absolute;
            bottom: -60px;
            left: 10%;
            width: 300px;
            height: 300px;
            background: radial-gradient(circle, rgba(0, 61, 102, 0.07) 0%, transparent 70%);
            border-radius: 50%;
        }

        .hero-inner {
            max-width: 1500px;
            margin: 0 auto;
            width: 100%;
            display: grid;
            grid-template-columns: 1fr 1fr;
            align-items: center;
            gap: 80px;
            position: relative;
            z-index: 1;
        }

        .hero-tag {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: var(--primary-light);
            border: 1px solid rgba(0, 168, 158, 0.25);
            color: var(--primary-dark);
            font-size: 12px;
            font-weight: 600;
            padding: 6px 14px;
            border-radius: 999px;
            margin-bottom: 22px;
            text-transform: uppercase;
            letter-spacing: 0.8px;
        }

        .hero-tag::before {
            content: '';
            width: 7px;
            height: 7px;
            background: var(--primary);
            border-radius: 50%;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                opacity: 1;
                transform: scale(1);
            }

            50% {
                opacity: 0.5;
                transform: scale(1.4);
            }
        }

        .hero-title {
            font-size: clamp(28px, 3.5vw, 46px);
            font-weight: 800;
            line-height: 1.1;
            color: var(--secondary);
            margin-bottom: 22px;
            letter-spacing: -1.5px;
        }

        .hero-title .accent {
            color: var(--primary);
        }

        .hero-subtitle {
            font-size: 18px;
            line-height: 1.7;
            color: var(--text-secondary);
            margin-bottom: 40px;
            max-width: 480px;
        }

        .hero-btns {
            display: flex;
            gap: 14px;
            align-items: center;
            flex-wrap: wrap;
        }

        .btn-primary {
            background: var(--primary);
            color: white;
            border: none;
            padding: 15px 30px;
            border-radius: 12px;
            font-family: 'DM Sans', sans-serif;
            font-weight: 600;
            font-size: 15px;
            cursor: pointer;
            transition: all 0.25s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 4px 20px rgba(0, 168, 158, 0.35);
        }

        .btn-primary:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 8px 28px rgba(0, 168, 158, 0.4);
        }

        .btn-secondary {
            background: transparent;
            color: var(--secondary);
            border: 1.5px solid var(--gray-200);
            padding: 15px 30px;
            border-radius: 12px;
            font-family: 'DM Sans', sans-serif;
            font-weight: 600;
            font-size: 15px;
            cursor: pointer;
            transition: all 0.25s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-secondary:hover {
            border-color: var(--primary);
            color: var(--primary);
            background: var(--primary-light);
        }

        .hero-stats {
            display: flex;
            gap: 32px;
            align-items: center;
            margin-bottom: 24px
        }

        .stat {
            display: flex;
            flex-direction: column;
        }

        .stat-num {
            font-family: 'Sora', sans-serif;
            font-size: 26px;
            font-weight: 800;
            color: var(--secondary);
            line-height: 1;
        }

        .stat-label {
            font-size: 12px;
            color: var(--text-muted);
            margin-top: 4px;
            font-weight: 500;
        }

        .stat-divider {
            width: 1px;
            height: 40px;
            background: var(--gray-200);
        }

        /* Hero App Preview */
        .hero-visual {
            display: flex;
            justify-content: center;
            position: relative;
        }

        .phone-frame {
            width: 260px;
            height: 520px;
            background: var(--secondary);
            border-radius: 38px;
            padding: 12px;
            box-shadow: 0 40px 80px rgba(0, 61, 102, 0.35), 0 0 0 1px rgba(255, 255, 255, 0.1);
            position: relative;
            z-index: 2;
        }

        .phone-screen {
            width: 100%;
            height: 100%;
            background: #F0F4F8;
            border-radius: 28px;
            overflow: hidden;
            position: relative;
        }

        .phone-notch {
            height: 28px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--secondary);
        }

        .phone-notch-pill {
            width: 70px;
            height: 6px;
            background: rgba(255, 255, 255, 0.15);
            border-radius: 99px;
        }

        .app-header {
            background: var(--secondary);
            padding: 10px 14px 16px;
            color: white;
        }

        .app-header-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
        }

        .app-header-greeting {
            font-size: 10px;
            opacity: 0.65;
        }

        .app-header-name {
            font-family: 'Sora', sans-serif;
            font-size: 13px;
            font-weight: 700;
        }

        .app-avatar {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background: var(--primary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            font-weight: 700;
            color: white;
        }

        .app-summary-row {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 6px;
        }

        .app-summary-card {
            background: rgba(255, 255, 255, 0.12);
            border-radius: 10px;
            padding: 8px 8px;
            text-align: center;
        }

        .app-summary-num {
            font-family: 'Sora', sans-serif;
            font-size: 18px;
            font-weight: 800;
            color: white;
        }

        .app-summary-label {
            font-size: 8px;
            opacity: 0.7;
            color: white;
            margin-top: 2px;
        }

        .app-body {
            padding: 12px;
        }

        .app-section-title {
            font-family: 'Sora', sans-serif;
            font-size: 10px;
            font-weight: 700;
            color: var(--text-secondary);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
        }

        .task-card {
            background: white;
            border-radius: 10px;
            padding: 10px;
            margin-bottom: 7px;
            box-shadow: 0 1px 6px rgba(0, 0, 0, 0.06);
        }

        .task-card-top {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 5px;
        }

        .task-title {
            font-size: 10px;
            font-weight: 600;
            color: var(--text-primary);
            line-height: 1.3;
        }

        .task-badge {
            font-size: 7px;
            font-weight: 700;
            padding: 2px 7px;
            border-radius: 99px;
            white-space: nowrap;
        }

        .badge-green {
            background: #E0F5F0;
            color: #006B5A;
        }

        .badge-amber {
            background: #FFF3E0;
            color: #8C5A00;
        }

        .badge-red {
            background: #FDECEA;
            color: #8C2020;
        }

        .task-assignee {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .task-avatar {
            width: 16px;
            height: 16px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 7px;
            font-weight: 700;
            color: white;
        }

        .task-assignee-name {
            font-size: 8px;
            color: var(--text-muted);
        }

        .task-bar {
            height: 3px;
            background: var(--gray-100);
            border-radius: 99px;
            margin-top: 6px;
        }

        .task-bar-fill {
            height: 100%;
            border-radius: 99px;
        }

        .floating-card {
            position: absolute;
            background: white;
            border-radius: 14px;
            padding: 10px 14px;
            box-shadow: 0 8px 28px rgba(0, 61, 102, 0.18);
            z-index: 3;
        }

        .float-right {
            right: -50px;
            top: 100px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .float-left {
            left: -55px;
            bottom: 110px;
            min-width: 130px;
        }

        .float-icon {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
        }

        .float-title {
            font-family: 'Sora', sans-serif;
            font-size: 12px;
            font-weight: 700;
            white-space: nowrap;
        }

        .float-sub {
            font-size: 10px;
            color: var(--text-muted);
        }

        /* ── STORE LAYOUT SECTION ── */
        .store-section {
            background: white;
            padding: 80px 5%;
        }

        .store-section-inner {
            max-width: 1200px;
            margin: 0 auto;
        }

        .section-eyebrow {
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--primary);
            margin-bottom: 10px;
        }

        .section-title {
            font-size: clamp(28px, 4vw, 42px);
            font-weight: 800;
            color: var(--secondary);
            letter-spacing: -1px;
            line-height: 1.15;
            margin-bottom: 14px;
        }

        .section-subtitle {
            font-size: 17px;
            color: var(--text-secondary);
            line-height: 1.7;
            max-width: 540px;
        }

        /* ── APP STORE-STYLE BLOCK ── */
        .app-store-block {
            display: grid;
            grid-template-columns: 1fr 340px;
            gap: 48px;
            align-items: start;
        }

        .app-main-info {
            display: flex;
            flex-direction: column;
        }

        .app-identity {
            display: flex;
            align-items: center;
            gap: 20px;
            margin-bottom: 24px;
        }

        .app-icon-large {
            width: 84px;
            height: 84px;
            border-radius: 20px;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 8px 24px rgba(0, 168, 158, 0.3);
            flex-shrink: 0;
        }

        .app-icon-large svg {
            width: 46px;
            height: 46px;
        }

        .app-identity-text h2 {
            font-size: 28px;
            font-weight: 800;
            color: var(--primary);
            margin-bottom: 3px;
        }

        .app-identity-text .app-category {
            font-size: 14px;
            color: var(--secondary);
            font-weight: 600;
            margin-bottom: 6px;
        }

        .app-meta-row {
            display: flex;
            gap: 5px;
            align-items: center;
            flex-wrap: wrap;
        }

        .app-meta-chip {
            display: flex;
            align-items: center;
            gap: 5px;
            background: var(--gray-50);
            border-radius: 99px;
            padding: 4px 12px;
            font-size: 13px;
            color: var(--text-secondary);
        }

        .app-meta-chip .dot {
            width: 4px;
            height: 4px;
            background: var(--gray-200);
            border-radius: 50%;
        }

        .stars {
            color: #F5A623;
            letter-spacing: 1px;
        }

        .app-action-row {
            display: flex;
            gap: 12px;
            align-items: center;
            margin-bottom: 28px;
        }

        .btn-install {
            background: var(--primary);
            color: white;
            border: none;
            padding: 12px 36px;
            border-radius: 99px;
            font-family: 'DM Sans', sans-serif;
            font-weight: 700;
            font-size: 15px;
            cursor: pointer;
            transition: all 0.2s;
            box-shadow: 0 4px 16px rgba(0, 168, 158, 0.3);
        }

        .btn-install:hover {
            background: var(--primary-dark);
            transform: translateY(-1px);
        }

        .btn-share-icon {
            background: var(--gray-50);
            color: var(--secondary);
            border: 1.5px solid var(--gray-200);
            padding: 11px 20px;
            border-radius: 99px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 6px;
            transition: all 0.2s;
        }

        .btn-share-icon:hover {
            border-color: var(--primary);
            color: var(--primary);
        }

        .app-available-note {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 12px;
            color: var(--text-muted);
            margin-bottom: 32px;
        }

        .app-available-note svg {
            width: 16px;
            height: 16px;
            flex-shrink: 0;
        }

        /* Screenshots carousel */
        .screenshots {
            display: flex;
            gap: 14px;
            overflow-x: auto;
            padding-bottom: 4px;
            scrollbar-width: none;
        }

        .screenshots::-webkit-scrollbar {
            display: none;
        }

        .screenshot {
            flex-shrink: 0;
            width: 170px;
            height: 380px;
            background: linear-gradient(160deg, var(--secondary) 0%, #005080 100%);
            border-radius: 4px;
            overflow: hidden;
            position: relative;
            cursor: pointer;
            transition: transform 0.2s;
        }

        .screenshot:hover {
            transform: scale(1.02);
        }

        .screenshot-label {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            padding: 14px 12px;
            color: white;
            font-size: 11px;
            font-weight: 600;
            line-height: 1.4;
        }

        .screenshot-label .headline {
            font-family: 'Sora', sans-serif;
            font-size: 13px;
            font-weight: 800;
            margin-bottom: 3px;
        }

        .screenshot-ui {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 200px;
        }

        .ss-card {
            background: rgba(255, 255, 255, 0.95);
            margin: 0 8px 8px;
            border-radius: 10px;
            padding: 10px;
        }

        .ss-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 5px;
        }

        .ss-label {
            font-size: 8px;
            font-weight: 600;
            color: var(--text-primary);
        }

        .ss-badge {
            font-size: 7px;
            font-weight: 700;
            padding: 2px 8px;
            border-radius: 99px;
        }

        .ss-bar {
            height: 4px;
            background: var(--gray-100);
            border-radius: 99px;
            margin: 4px 0 2px;
        }

        .ss-bar-fill {
            height: 100%;
            border-radius: 99px;
            background: var(--primary);
        }

        .ss-mini-row {
            display: flex;
            gap: 4px;
            margin-bottom: 5px;
        }

        .ss-mini-num {
            font-family: 'Sora', sans-serif;
            font-size: 16px;
            font-weight: 800;
            color: var(--secondary);
        }

        .ss-mini-label {
            font-size: 7px;
            color: var(--text-muted);
        }

        .ss-list {}

        .ss-item {
            display: flex;
            align-items: center;
            gap: 6px;
            margin-bottom: 6px;
        }

        .ss-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            flex-shrink: 0;
        }

        .ss-text {
            font-size: 8px;
            color: var(--text-primary);
        }

        .ss-check {
            width: 14px;
            height: 14px;
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 8px;
            color: white;
            margin-left: auto;
        }

        /* Sidebar logo */
        .app-sidebar {
            display: flex;
            align-items: flex-start;
            justify-content: center;
            padding-top: 8px;
        }

        .app-sidebar-logo {
            width: 200px;
            height: 200px;
            border-radius: 40px;
            object-fit: cover;
            box-shadow: 0 12px 40px rgba(0, 61, 102, 0.20);
        }

        .sidebar-section {
            margin-bottom: 24px;
        }

        .sidebar-title {
            font-family: 'Sora', sans-serif;
            font-size: 14px;
            font-weight: 700;
            color: var(--secondary);
            margin-bottom: 14px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .sidebar-title a {
            font-size: 12px;
            color: var(--primary);
            font-family: 'DM Sans', sans-serif;
            font-weight: 500;
            text-decoration: none;
        }

        .rating-big {
            text-align: center;
            margin-bottom: 14px;
        }

        .rating-big-num {
            font-family: 'Sora', sans-serif;
            font-size: 48px;
            font-weight: 800;
            color: var(--secondary);
            line-height: 1;
        }

        .rating-stars {
            font-size: 20px;
            color: #F5A623;
            margin: 6px 0;
        }

        .rating-count {
            font-size: 12px;
            color: var(--text-muted);
        }

        .rating-bars {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .rating-bar-row {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .rating-bar-label {
            font-size: 11px;
            color: var(--text-muted);
            width: 10px;
            text-align: right;
        }

        .rating-bar-track {
            flex: 1;
            height: 5px;
            background: var(--gray-200);
            border-radius: 99px;
        }

        .rating-bar-fill {
            height: 100%;
            border-radius: 99px;
            background: var(--primary);
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid var(--gray-100);
            font-size: 13px;
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .info-key {
            color: var(--text-muted);
        }

        .info-val {
            color: var(--text-primary);
            font-weight: 600;
        }

        .related-app {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 0;
            border-bottom: 1px solid var(--gray-100);
        }

        .related-app:last-child {
            border-bottom: none;
        }

        .related-icon {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
        }

        .related-name {
            font-size: 13px;
            font-weight: 600;
            color: var(--text-primary);
        }

        .related-maker {
            font-size: 11px;
            color: var(--text-muted);
            margin-bottom: 2px;
        }

        .related-stars {
            font-size: 11px;
            color: #F5A623;
        }

        /* ── FEATURES SECTION ── */
        .features-section {
            padding: 80px 5%;
            background: linear-gradient(180deg, var(--off-white) 0%, var(--secondary-light) 100%);
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 24px;
            margin-top: 48px;
        }

        .feature-card {
            background: white;
            border-radius: 20px;
            padding: 28px 24px;
            border: 1px solid var(--gray-100);
            transition: all 0.25s;
            position: relative;
            overflow: hidden;
        }

        .feature-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 16px 40px rgba(0, 61, 102, 0.12);
        }

        .feature-card::before {
            content: '';
            position: absolute;
            bottom: 0;
            right: 0;
            width: 80px;
            height: 80px;
            background: var(--primary-light);
            border-radius: 50%;
            transform: translate(30px, 30px);
        }

        .feature-icon {
            width: 50px;
            height: 50px;
            border-radius: 14px;
            background: var(--primary-light);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 18px;
            color: var(--primary);
        }

        .feature-icon svg {
            width: 26px;
            height: 26px;
        }

        .feature-title {
            font-family: 'Sora', sans-serif;
            font-size: 17px;
            font-weight: 700;
            color: var(--secondary);
            margin-bottom: 10px;
        }

        .feature-desc {
            font-size: 14px;
            color: var(--text-secondary);
            line-height: 1.65;
        }

        /* ── TEAM SECTION ── */
        .team-section {
            padding: 80px 5%;
            background: var(--secondary);
            color: white;
            text-align: center;
        }

        .team-title {
            font-size: clamp(28px, 4vw, 42px);
            font-weight: 800;
            color: white;
            margin-bottom: 14px;
            letter-spacing: -1px;
        }

        .team-subtitle {
            font-size: 17px;
            color: rgba(255, 255, 255, 0.65);
            line-height: 1.7;
            max-width: 520px;
            margin: 0 auto 48px;
        }

        .team-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            max-width: 900px;
            margin: 0 auto;
        }

        .team-card {
            background: rgba(255, 255, 255, 0.07);
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 18px;
            padding: 24px 20px;
            text-align: center;
            transition: all 0.2s;
        }

        .team-card:hover {
            background: rgba(255, 255, 255, 0.12);
            transform: translateY(-3px);
        }

        .team-avatar {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            margin: 0 auto 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Sora', sans-serif;
            font-weight: 800;
            font-size: 20px;
            color: white;
        }

        .team-member-name {
            font-weight: 700;
            font-size: 15px;
            margin-bottom: 4px;
        }

        .team-member-role {
            font-size: 12px;
            color: rgba(255, 255, 255, 0.55);
        }

        /* ── CTA ── */
        .cta-section {
            padding: 80px 5%;
            background: var(--primary-light);
            text-align: center;
        }

        .cta-title {
            font-size: clamp(30px, 4vw, 46px);
            font-weight: 800;
            color: var(--secondary);
            margin-bottom: 16px;
            letter-spacing: -1px;
        }

        .cta-sub {
            font-size: 17px;
            color: var(--text-secondary);
            margin-bottom: 36px;
            line-height: 1.7;
        }

        .cta-btns {
            display: flex;
            gap: 14px;
            justify-content: center;
            flex-wrap: wrap;
        }

        /* ── FOOTER ── */
        footer {
            background: var(--secondary);
            padding: 40px 5%;
            color: rgba(255, 255, 255, 0.5);
            font-size: 13px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .footer-logo-text {
            font-family: 'Sora', sans-serif;
            font-weight: 700;
            font-size: 15px;
            color: white;
        }

        .footer-logo-text span {
            color: var(--primary);
        }

        .footer-links {
            display: flex;
            gap: 24px;
        }

        .footer-links a {
            color: rgba(255, 255, 255, 0.5);
            text-decoration: none;
            transition: color 0.2s;
        }

        .footer-links a:hover {
            color: var(--primary);
        }

        html {
            scroll-behavior: smooth;
        }

        .screenshots-label {
            font-size: 12px;
            font-weight: 700;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.8px;
            margin-bottom: 8px;
        }

        .section-subtitle {
            margin-bottom: 40px;
        }

        .app-store-block {
            margin-top: 40px;
        }
    </style>
</head>

<body>

    <!-- DOWNLOAD -->
    <section id="download" class="store-section">
        <div class="store-section-inner">
            <div class="section-eyebrow">Download</div>
            <h2 class="section-title"><span style="color:var(--primary);">Staflo:</span> Task Assigner</h2>
            <p class="section-subtitle">Tersedia untuk Android, gratis selamanya. Mulai kelola tim Anda hari ini.</p>

            <div class="app-store-block">
                <!-- Main info -->
                <div class="app-main-info">
                    <div class="app-identity">
                        <div class="app-icon-large" style="overflow:hidden;padding:0;">
                            <img src="{{ asset('images/logo_staflo.png') }}" alt="Logo"
                                style="width:100%;height:100%;object-fit:cover;">
                        </div>
                        <div class="app-identity-text">
                            <h2>Staflo</h2>
                            <div class="app-category">Task Assigner &amp; Team Manager</div>
                            <div class="app-meta-row">
                                <div class="app-meta-chip"><span class="stars">★★★★★</span></div>
                                <div class="app-meta-chip"><span>User Friendly</span></div>
                                <div class="app-meta-chip"><span>Realtime</span></div>
                                <div class="app-meta-chip"><span>Free</span></div>
                            </div>
                        </div>
                    </div>

                    <div class="app-action-row">
                        <button class="btn-install">Instal Sekarang</button>
                    </div>

                    <div class="hero-stats">
                        <div class="stat">
                            <span class="stat-num">100%</span>
                            <span class="stat-label">Gratis</span>
                        </div>
                        <div class="stat-divider"></div>
                        <div class="stat">
                            <span class="stat-num">Realtime</span>
                            <span class="stat-label">Notifikasi</span>
                        </div>
                        <div class="stat-divider"></div>
                        <div class="stat">
                            <span class="stat-num">Android</span>
                            <span class="stat-label">Platform</span>
                        </div>
                    </div>

                    <div class="screenshots-label">Tangkapan Layar</div>
                    <div class="screenshots">
                        <div class="screenshot">
                            <img src="{{ asset('images/ui.jpeg') }}" alt="Staflo UI 1"
                                style="width:100%;height:100%;object-fit:cover;display:block;">
                        </div>
                        <div class="screenshot">
                            <img src="{{ asset('images/ui.jpeg') }}" alt="Staflo UI 2"
                                style="width:100%;height:100%;object-fit:cover;display:block;">
                        </div>
                        <div class="screenshot">
                            <img src="{{ asset('images/ui.jpeg') }}" alt="Staflo UI 3"
                                style="width:100%;height:100%;object-fit:cover;display:block;">
                        </div>
                        <div class="screenshot">
                            <img src="{{ asset('images/ui.jpeg') }}" alt="Staflo UI 4"
                                style="width:100%;height:100%;object-fit:cover;display:block;">
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="app-sidebar">
                    <img src="{{ asset('images/logo_staflo.png') }}" alt="Staflo" class="app-sidebar-logo">
                </div>
            </div>
        </div>
    </section>

    <!-- CTA -->
    <section class="cta-section">
        <h2 class="cta-title">Siap Memulai?</h2>
        <p class="cta-sub">Assign tugas, pantau progress, dan tingkatkan kolaborasi tim dalam satu platform.</p>

    </section>

    <!-- FOOTER -->
    <footer>
        <div>
            <div class="footer-logo-text">Sta<span>flo</span></div>
            <div style="margin-top:6px;">© 2026 Staflo. Semua hak dilindungi.</div>
        </div>
    </footer>

</body>

</html>
