<?php
require_once __DIR__ . '/../inc/app.php';
workshop_redirect_if_logged_in();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SHENGCHI | Workshop Operations</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700;800&family=Noto+Sans+SC:wght@500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <style>
        :root {
            --garage-gold: #f5a623;
            --garage-blue: #0e3158;
            --garage-dark: #071522;
            --glass: rgba(255, 255, 255, .09);
            --border-soft: rgba(255, 255, 255, .17);
        }

        * {
            box-sizing: border-box;
            font-family: 'DM Sans', 'Noto Sans SC', sans-serif;
        }

        html,
        body {
            min-height: 100vh;
            margin: 0;
            background: var(--garage-dark);
            color: #fff;
            overflow-x: hidden;
        }

        /* Ensure smooth scrolling and proper background attachment */
        html {
            scroll-behavior: smooth;
        }

        /* Additional background layers for depth */
        .hero-bg-secondary {
            position: fixed;
            inset: 0;
            z-index: -1;
            background: 
                radial-gradient(ellipse at top left, rgba(245, 166, 35, 0.03) 0%, transparent 50%),
                radial-gradient(ellipse at bottom right, rgba(14, 49, 88, 0.05) 0%, transparent 50%);
            animation: secondaryFloat 50s ease-in-out infinite alternate;
            pointer-events: none;
        }

        @keyframes secondaryFloat {
            0% {
                transform: translateX(0) translateY(0) scale(1);
                opacity: 0.6;
            }
            33% {
                transform: translateX(30px) translateY(-20px) scale(1.05);
                opacity: 0.8;
            }
            66% {
                transform: translateX(-20px) translateY(30px) scale(0.98);
                opacity: 0.7;
            }
            100% {
                transform: translateX(0) translateY(0) scale(1);
                opacity: 0.6;
            }
        }

        /* Background loading and fade-in effect */
        .hero-bg {
            position: fixed;
            inset: 0;
            z-index: -2;
            background: linear-gradient(90deg, rgba(5, 16, 28, .94) 0%, rgba(5, 17, 30, .78) 48%, rgba(5, 17, 30, .42) 100%), url('/workshop/assets/images/hero.png') center/cover no-repeat;
            background-attachment: fixed;
            will-change: transform;
            opacity: 0;
            animation: 
                smoothZoom 25s ease-in-out infinite alternate, 
                backgroundShift 40s linear infinite,
                fadeInBackground 2s ease-out forwards;
        }

        /* Background loading states */
        body:not(.bg-loaded) .hero-bg {
            background-image: linear-gradient(90deg, rgba(5, 16, 28, .94) 0%, rgba(5, 17, 30, .78) 48%, rgba(5, 17, 30, .42) 100%);
            animation-delay: 0.5s;
        }

        body.bg-loaded .hero-bg {
            animation-delay: 0s;
        }

        /* Smooth transition when image loads */
        .hero-bg {
            transition: background-image 0.5s ease-in-out;
        }

        @keyframes fadeInBackground {
            0% {
                opacity: 0;
                transform: scale(1.1);
            }
            100% {
                opacity: 1;
                transform: scale(1);
            }
        }

        /* Preload background image */
        .hero-bg::before {
            content: '';
            position: absolute;
            inset: 0;
            z-index: 1;
            background: 
                radial-gradient(circle at 20% 30%, rgba(245, 166, 35, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 80% 70%, rgba(14, 49, 88, 0.15) 0%, transparent 50%),
                radial-gradient(circle at 40% 80%, rgba(245, 166, 35, 0.08) 0%, transparent 50%),
                linear-gradient(
                    45deg,
                    rgba(5, 16, 28, 0.95) 0%,
                    rgba(5, 17, 30, 0.85) 25%,
                    rgba(5, 17, 30, 0.75) 50%,
                    rgba(5, 17, 30, 0.85) 75%,
                    rgba(5, 16, 28, 0.95) 100%
                );
            animation: overlayPulse 30s ease-in-out infinite, particleFloat 45s linear infinite;
        }

        @keyframes smoothZoom {
            0% {
                transform: scale(1) rotate(0deg);
            }
            50% {
                transform: scale(1.08) rotate(0.5deg);
            }
            100% {
                transform: scale(1.05) rotate(-0.3deg);
            }
        }

        @keyframes backgroundShift {
            0% {
                background-position: 0% 50%;
            }
            25% {
                background-position: 100% 50%;
            }
            50% {
                background-position: 100% 100%;
            }
            75% {
                background-position: 0% 100%;
            }
            100% {
                background-position: 0% 50%;
            }
        }

        .hero-bg::before {
            content: '';
            position: absolute;
            inset: 0;
            z-index: 1;
            background: linear-gradient(
                45deg,
                rgba(5, 16, 28, 0.95) 0%,
                rgba(5, 17, 30, 0.85) 25%,
                rgba(5, 17, 30, 0.75) 50%,
                rgba(5, 17, 30, 0.85) 75%,
                rgba(5, 16, 28, 0.95) 100%
            );
            animation: overlayPulse 30s ease-in-out infinite;
        }

        @keyframes overlayPulse {
            0%, 100% {
                opacity: 1;
            }
            50% {
                opacity: 0.8;
            }
        }

        .hero-bg::after {
            content: '';
            position: absolute;
            inset: 0;
            z-index: 2;
            opacity: .25;
            background-image: 
                linear-gradient(rgba(255, 255, 255, .06) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255, 255, 255, .06) 1px, transparent 1px);
            background-size: 50px 50px;
            mask-image: linear-gradient(90deg, #000, transparent 70%);
            animation: gridMove 60s linear infinite;
        }

        @keyframes gridMove {
            0% {
                transform: translate(0, 0);
            }
            25% {
                transform: translate(-25px, -25px);
            }
            50% {
                transform: translate(-50px, 0);
            }
            75% {
                transform: translate(-25px, 25px);
            }
            100% {
                transform: translate(0, 0);
            }
        }

        /* Responsive background adjustments */
        @media (max-width: 768px) {
            .hero-bg {
                background-size: cover;
                background-position: center;
                animation: smoothZoomMobile 20s ease-in-out infinite alternate, backgroundShiftMobile 35s linear infinite;
            }

            @keyframes smoothZoomMobile {
                0% {
                    transform: scale(1.1);
                }
                100% {
                    transform: scale(1.2);
                }
            }

            @keyframes backgroundShiftMobile {
                0% {
                    background-position: 20% 20%;
                }
                33% {
                    background-position: 80% 40%;
                }
                66% {
                    background-position: 40% 80%;
                }
                100% {
                    background-position: 20% 20%;
                }
            }

            .hero-bg::after {
                background-size: 30px 30px;
                opacity: .15;
            }
        }

        @media (max-width: 480px) {
            .hero-bg {
                animation: smoothZoomSmall 15s ease-in-out infinite alternate;
            }

            @keyframes smoothZoomSmall {
                0% {
                    transform: scale(1.15);
                }
                100% {
                    transform: scale(1.25);
                }
            }

            .hero-bg::after {
                background-size: 25px 25px;
                opacity: .1;
            }
        }

        /* Performance optimizations */
        @media (prefers-reduced-motion: reduce) {
            .hero-bg,
            .hero-bg::before,
            .hero-bg::after,
            .hero-bg-secondary {
                animation: none !important;
            }
        }

        /* Hardware acceleration for smooth animations */
        .hero-bg,
        .hero-bg::before,
        .hero-bg::after,
        .hero-bg-secondary {
            transform: translateZ(0);
            backface-visibility: hidden;
            perspective: 1000px;
        }

        /* Ensure content stays above background */
        .main-content {
            position: relative;
            z-index: 10;
        }

        .login-drawer {
            z-index: 1000;
        }

        .overlay {
            z-index: 999;
        }

        /* Additional mobile optimizations */
        @media (max-width: 768px) {
            .hero-bg {
                background-attachment: scroll; /* Better performance on mobile */
            }
            
            .hero-bg-secondary {
                animation-duration: 30s; /* Faster on mobile for battery life */
            }
        }

        /* High contrast mode support */
        @media (prefers-contrast: high) {
            .hero-bg::before {
                background: rgba(0, 0, 0, 0.9);
            }
        }

        /* Enhanced floating particles effect */
        .hero-bg::before {
            background: 
                radial-gradient(circle at 20% 30%, rgba(245, 166, 35, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 80% 70%, rgba(14, 49, 88, 0.15) 0%, transparent 50%),
                radial-gradient(circle at 40% 80%, rgba(245, 166, 35, 0.08) 0%, transparent 50%),
                linear-gradient(
                    45deg,
                    rgba(5, 16, 28, 0.95) 0%,
                    rgba(5, 17, 30, 0.85) 25%,
                    rgba(5, 17, 30, 0.75) 50%,
                    rgba(5, 17, 30, 0.85) 75%,
                    rgba(5, 16, 28, 0.95) 100%
                );
            animation: overlayPulse 30s ease-in-out infinite, particleFloat 45s linear infinite;
        }

        @keyframes particleFloat {
            0% {
                background-position: 0% 0%, 0% 0%, 0% 0%, 0% 0%;
            }
            25% {
                background-position: 100% 25%, 25% 100%, 50% 50%, 0% 0%;
            }
            50% {
                background-position: 75% 75%, 75% 25%, 100% 0%, 0% 0%;
            }
            75% {
                background-position: 25% 100%, 100% 75%, 25% 75%, 0% 0%;
            }
            100% {
                background-position: 0% 0%, 0% 0%, 0% 0%, 0% 0%;
            }
        }

        .main-content {
            min-height: 100vh;
            padding: 34px 6%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            gap: 50px;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 11px;
            font-weight: 800;
            letter-spacing: .4px;
            font-size: 18px;
        }

        .brand-mark {
            display: grid;
            place-items: center;
            width: 38px;
            height: 38px;
            border-radius: 10px;
            color: #0e3158;
            background: var(--garage-gold);
            font-size: 20px;
        }

        .brand small {
            display: block;
            margin-top: 1px;
            color: #b4c4d5;
            font-size: 9px;
            letter-spacing: 1.4px;
            font-weight: 600;
        }

        .btn-access {
            background: var(--garage-gold);
            color: #112b49;
            padding: 12px 23px;
            border-radius: 9px;
            font-size: 13px;
            font-weight: 800;
            border: 0;
            transition: .25s;
        }

        .btn-access:hover {
            background: #ffc45a;
            transform: translateY(-2px);
            box-shadow: 0 12px 34px rgba(245, 166, 35, .28);
        }

        .btn-role-admin {
            background: #dc3545;
            color: #fff;
        }

        .btn-role-admin:hover {
            background: #e85563;
            box-shadow: 0 12px 34px rgba(220, 53, 69, .35);
        }

        .btn-role-owner {
            background: #0d6efd;
            color: #fff;
        }

        .btn-role-owner:hover {
            background: #3d8bfd;
            box-shadow: 0 12px 34px rgba(13, 110, 253, .35);
        }

        .btn-role-cashier {
            background: var(--garage-gold);
            color: #112b49;
        }

        @media(max-width:700px) {
            .btn-access {
                padding: 10px 16px;
                font-size: 11px;
            }
        }

        .eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: #ffe1a8;
            font-weight: 700;
            font-size: 11px;
            letter-spacing: 1.1px;
        }

        .eyebrow::before {
            content: '';
            width: 30px;
            height: 2px;
            background: var(--garage-gold);
        }

        .hero-copy {
            max-width: 760px;
        }

        .hero-title {
            margin: 14px 0 17px;
            font-size: clamp(3.2rem, 7vw, 6.4rem);
            font-weight: 800;
            letter-spacing: -3px;
            line-height: .92;
        }

        .hero-title span {
            color: var(--garage-gold);
        }

        .hero-text {
            color: #d5e0ec;
            font-size: 17px;
            line-height: 1.65;
            max-width: 530px;
        }

        .service-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 9px;
            margin-top: 30px;
        }

        .service-tag {
            border: 1px solid var(--border-soft);
            background: var(--glass);
            backdrop-filter: blur(10px);
            padding: 8px 13px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 600;
            color: #e9f1f8;
        }

        .service-tag i {
            color: var(--garage-gold);
            margin-right: 5px;
        }

        .dock {
            width: min(100%, 1080px);
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 24px;
            padding: 15px 19px;
            background: rgba(6, 21, 36, .7);
            border: 1px solid var(--border-soft);
            border-radius: 13px 13px 0 0;
            backdrop-filter: blur(16px);
        }

        .dock strong {
            font-size: 12px;
        }

        .dock small {
            display: block;
            color: #aabed0;
            font-size: 10px;
            margin-top: 2px;
        }

        .status-dot {
            display: inline-block;
            width: 7px;
            height: 7px;
            background: #58d5a0;
            border-radius: 50%;
            margin-right: 6px;
            box-shadow: 0 0 10px #58d5a0;
        }

        /* ==========================================================
           LOGIN DRAWER - GLASSY (FROSTED) EFFECT
           The panel background is now semi-transparent and uses
           backdrop-filter blur + saturate, so the hero background
           and overlay shine through behind it like frosted glass.
           ========================================================== */
        .login-drawer {
            position: fixed;
            inset: 0 0 0 auto;
            width: 430px;
            z-index: 10;
            padding: 53px 40px;
            /* semi-transparent so the blurred background shows = glassy look */
            background: rgba(10, 25, 45, .35);
            /* frost (blur + boost colours of) whatever sits behind the panel */
            backdrop-filter: blur(35px) saturate(160%);
            -webkit-backdrop-filter: blur(35px) saturate(160%);
            border-left: 1px solid rgba(255, 255, 255, .14);
            /* soft shadow to lift the panel off the page */
            box-shadow: -18px 0 45px rgba(2, 9, 16, .45);
            transform: translateX(100%);
            transition: .55s cubic-bezier(.7, 0, .3, 1);
        }

        .login-drawer.active {
            transform: translateX(0);
        }

        /* ==========================================================
           CANCEL BUTTON at the top of the login popup.
           A small frosted pill so it is always visible and clearly
           shows the user how to close the drawer.
           ========================================================== */
        .btn-close-drawer {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 8px 15px;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: .3px;
            color: #d5e0ec;
            background: rgba(255, 255, 255, .06);
            border: 1px solid rgba(255, 255, 255, .18);
            border-radius: 999px;
            cursor: pointer;
            transition: .25s;
        }

        .btn-close-drawer:hover {
            color: #fff;
            background: rgba(245, 166, 35, .18);
            border-color: rgba(245, 166, 35, .5);
            transform: translateY(-1px);
        }

        /* Decorative gold glow in the top corner.
           pointer-events: none keeps it from covering the Cancel
           button (the circle overlaps the top-right of the drawer). */
        .login-drawer::before {
            content: '';
            position: absolute;
            width: 220px;
            height: 220px;
            right: -90px;
            top: -80px;
            border-radius: 50%;
            background: #f5a6231c;
            filter: blur(5px);
            pointer-events: none;
        }

        .form-group {
            position: relative;
            margin-bottom: 28px;
        }

        .form-group input {
            width: 100%;
            padding: 16px 14px;
            color: #fff;
            background: transparent;
            border: 1px solid var(--border-soft);
            border-radius: 9px;
            outline: 0;
        }

        .form-group input:focus {
            border-color: var(--garage-gold);
            box-shadow: 0 0 0 3px rgba(245, 166, 35, .12);
        }

        .form-group label {
            position: absolute;
            left: 11px;
            top: 50%;
            transform: translateY(-50%);
            padding: 0 5px;
            color: #9db0c5;
            /* translucent so the label blends with the glassy panel */
            background: rgba(10, 28, 46, .85);
            pointer-events: none;
            font-size: 12px;
            transition: .2s;
        }

        .form-group input:focus+label,
        .form-group input:not(:placeholder-shown)+label {
            top: 0;
            color: #ffd889;
            font-size: 10px;
        }

        .toggle-pass {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #aabed0;
            cursor: pointer;
        }

        .btn-login {
            background: var(--garage-gold);
            color: #112b49;
            border: 0;
            border-radius: 9px;
            padding: 14px;
            font-weight: 800;
            transition: .2s;
        }

        .btn-login:hover {
            background: #ffc45a;
        }

        .security-note {
            color: #93a9c0;
            text-align: center;
            font-size: 11px;
            margin-top: 15px;
        }

        .security-note i {
            color: #58d5a0;
        }

        /* ── Language switcher (dark panel) ── */
        #landingLangDropdown {
            max-height: 320px;
            overflow-y: auto;
            min-width: 170px;
            background: rgba(10, 25, 45, .97);
            border: 1px solid rgba(255, 255, 255, .15);
        }

        #landingLangDropdown .dropdown-item {
            color: #d5e0ec;
            font-size: 12px;
            padding: 6px 12px;
        }

        #landingLangDropdown .dropdown-item:hover,
        #landingLangDropdown .dropdown-item.active {
            background: rgba(245, 166, 35, .15);
            color: #fff;
        }

        @media(max-width:700px) {
            /* icon-only globe on small screens so the header fits */
            #langPickerBtn #landingLangLabel {
                display: none;
            }
        }

        .overlay {
            position: fixed;
            inset: 0;
            z-index: 9;
            background: rgba(2, 9, 16, .48);
            opacity: 0;
            pointer-events: none;
            transition: .3s;
        }

        .overlay.active {
            opacity: 1;
            pointer-events: auto;
        }

        @media(max-width:700px) {
            .main-content {
                padding: 23px 22px;
            }

            .hero-title {
                letter-spacing: -2px;
            }

            .hero-text {
                font-size: 15px;
            }

            .dock {
                align-items: flex-start;
                flex-direction: column;
                gap: 8px;
            }

            .login-drawer {
                width: 100%;
                padding: 40px 25px;
            }

            .brand {
                font-size: 15px;
            }
        }
    </style>
</head>

<body>
    <div class="hero-bg"></div>
    <div class="hero-bg-secondary"></div>
    <div class="overlay" id="overlay" onclick="toggleDrawer()"></div>
    <main class="main-content">
        <header class="d-flex justify-content-between align-items-center">
            <div class="brand"><span class="brand-mark"><i class="bi bi-wrench-adjustable"></i></span><span>SHENG CHI GARAGE<small>盛驰汽修 · WORKSHOP OPERATIONS</small></span></div>
            <div class="d-flex gap-2 align-items-center">
                <div class="dropdown">
                    <button class="btn btn-link dropdown-toggle nav-link" type="button" id="langPickerBtn" data-bs-toggle="dropdown" aria-label="Change language" style="text-decoration:none;color:#d5e0ec;font-size:13px;font-weight:600;">
                        <i class="bi bi-globe me-1"></i><span id="landingLangLabel">EN</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" id="landingLangDropdown"></ul>
                </div>
                <button class="btn-access btn-role-admin" onclick="toggleDrawer('admin')" title="Full system access - All permissions">
                    <i class="bi bi-shield-lock me-1"></i><span data-landing-i18n="adminBtn">ADMIN</span>
                </button>
                <button class="btn-access btn-role-owner" onclick="toggleDrawer('owner')" title="Business owner dashboard - Financial oversight">
                    <i class="bi bi-person-badge me-1"></i><span data-landing-i18n="ownerBtn">OWNER</span>
                </button>
                <button class="btn-access btn-role-cashier" onclick="toggleDrawer('cashier')" title="Cashier operations - Daily transactions">
                    <i class="bi bi-cash-stack me-1"></i><span data-landing-i18n="cashierBtn">CASHIER</span>
                </button>
            </div>
        </header>
        <section class="hero-copy">
            <div class="eyebrow animate__animated animate__fadeInDown" data-landing-i18n="eyebrow">REPAIR JOBS · PAYMENTS · RECEIPTS</div>
            <h1 class="hero-title animate__animated animate__fadeInLeft"><span data-landing-i18n="heroLine1">BUILT FOR THE</span><br><span data-landing-i18n="heroLine2">BUSY WORKSHOP.</span></h1>
            
            <p class="hero-text" data-landing-i18n="heroDesc">A comprehensive workshop management system with role-based access control. Administrators have full system access, owners can oversee all operations and finances, while cashiers focus on daily transactions and customer service.</p>
            
            <div class="service-tags animate__animated animate__fadeInUp">
                <span class="service-tag"><i class="bi bi-car-front-fill"></i><span data-landing-i18n="vehicleRecords">Vehicle records</span></span>
                <span class="service-tag"><i class="bi bi-clipboard2-check"></i><span data-landing-i18n="repairJobs">Repair jobs</span></span>
                <span class="service-tag"><i class="bi bi-cash-stack"></i><span data-landing-i18n="payments">Payments</span></span>
                <span class="service-tag"><i class="bi bi-receipt"></i><span data-landing-i18n="receipts">Receipts</span></span>
                <span class="service-tag"><i class="bi bi-shield-check"></i><span data-landing-i18n="roleBased">Role-based access</span></span>
            </div>
        </section>
        <footer class="dock animate__animated animate__fadeInUp">
            <div><strong><span class="status-dot"></span><span data-landing-i18n="systemOnline">Workshop system online</span></strong><small data-landing-i18n="secureAccess">Secure access for the owner and cashier</small></div><small>© <?= date('Y') ?> Jin Long Garage. <span data-landing-i18n="allRights">All rights reserved.</span></small>
        </footer>
    </main>
        <aside class="login-drawer" id="loginDrawer">
        <div class="d-flex justify-content-between align-items-start mb-5">
            <div>
                <div class="eyebrow" style="font-size:9px" id="roleLabel" data-landing-i18n="staffPortal">STAFF PORTAL</div>
                <h4 class="fw-bold mt-2 mb-0" data-landing-i18n="welcomeBack">Welcome back</h4>
                <small style="color:#9db0c5" id="roleDescription" data-landing-i18n="signInDesc">Sign in to access workshop operations.</small>
            </div>
            <!-- Cancel button (top-right) closes the login popup.
                 It calls toggleDrawer() with NO argument, which removes
                 the .active class and slides the drawer back out. -->
            <button type="button" class="btn-close-drawer" aria-label="Close login" onclick="toggleDrawer()">
                <i class="bi bi-x-lg"></i> Cancel
            </button>
        </div>
        <form id="loginform">
            <div class="form-group">
                <input type="text" name="username" id="username" required placeholder=" " autocomplete="username">
                <label for="username" data-landing-i18n="username">Username</label>
            </div>
            <div class="form-group">
                <input type="password" name="password" id="password" required placeholder=" " autocomplete="current-password">
                <label for="password" data-landing-i18n="password">Password</label>
                <i class="bi bi-eye toggle-pass" id="togglePassword" onclick="togglePassword()"></i>
            </div>
            <button class="btn-login w-100" type="submit"><span data-landing-i18n="loginBtn">LOGIN TO SYSTEM</span> <i class="bi bi-arrow-right ms-1"></i></button>
            <div class="security-note">
                <i class="bi bi-shield-check me-1"></i> <span data-landing-i18n="secureSession">Your session is secure and protected</span>
            </div>
            <input name="deviceId" type="hidden" id="deviceId">
            <input name="selectedRole" type="hidden" id="selectedRole">
            <div class="text-center mt-4">
                <a href="#" class="small text-decoration-none" style="color:#c2d4e6" onclick="showForgotPassword()">
                    <span data-landing-i18n="forgotPassword">Forgot password?</span>
                </a>
            </div>
        </form>
    </aside>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script>
    /* ── Landing Page Translation System ─────────────────── */
    (function() {
      'use strict';
      var LANGUAGES = [
        { code: 'en', name: 'English',    native: 'English',    flag: '🇺🇸' },
        { code: 'zh', name: 'Chinese',    native: '中文',       flag: '🇨🇳' },
        { code: 'es', name: 'Spanish',    native: 'Español',    flag: '🇪🇸' },
        { code: 'fr', name: 'French',     native: 'Français',   flag: '🇫🇷' },
        { code: 'ar', name: 'Arabic',     native: 'العربية',    flag: '🇸🇦' },
        { code: 'de', name: 'German',     native: 'Deutsch',    flag: '🇩🇪' },
        { code: 'pt', name: 'Portuguese', native: 'Português',  flag: '🇧🇷' },
        { code: 'ja', name: 'Japanese',   native: '日本語',     flag: '🇯🇵' },
        { code: 'ko', name: 'Korean',     native: '한국어',     flag: '🇰🇷' },
        { code: 'hi', name: 'Hindi',      native: 'हिन्दी',     flag: '🇮🇳' },
        { code: 'sw', name: 'Swahili',    native: 'Kiswahili',  flag: '🇰🇪' },
        { code: 'ru', name: 'Russian',    native: 'Русский',    flag: '🇷🇺' },
        { code: 'tr', name: 'Turkish',    native: 'Türkçe',     flag: '🇹🇷' },
        { code: 'id', name: 'Indonesian', native: 'Bahasa',     flag: '🇮🇩' },
        { code: 'th', name: 'Thai',       native: 'ไทย',        flag: '🇹🇭' },
        { code: 'vi', name: 'Vietnamese', native: 'Tiếng Việt', flag: '🇻🇳' },
        { code: 'it', name: 'Italian',    native: 'Italiano',   flag: '🇮🇹' },
        { code: 'nl', name: 'Dutch',      native: 'Nederlands', flag: '🇳🇱' },
        { code: 'pl', name: 'Polish',     native: 'Polski',     flag: '🇵🇱' },
        { code: 'sv', name: 'Swedish',    native: 'Svenska',    flag: '🇸🇪' }
      ];

      var _translations = {};
      var _base = '/workshop/';
      var LANG_KEYS = {
        eyebrow: 'landingEyebrow', heroLine1: 'landingHeroLine1', heroLine2: 'landingHeroLine2',
        heroDesc: 'landingHeroDesc', vehicleRecords: 'vehicleRecordsTag', repairJobs: 'repairJobsTag',
        payments: 'paymentsTag', receipts: 'receiptsTag', roleBased: 'roleBasedAccessTag',
        systemOnline: 'systemOnline', secureAccess: 'secureAccess', allRights: 'allRightsReserved',
        staffPortal: 'staffPortal', welcomeBack: 'welcomeBack', signInDesc: 'signInDesc',
        username: 'username', password: 'password', loginBtn: 'loginBtn',
        secureSession: 'secureSession', forgotPassword: 'forgotPassword',
        adminBtn: 'adminBtn', ownerBtn: 'ownerBtn', cashierBtn: 'cashierBtn',
        adminPortal: 'adminPortal', adminPortalDesc: 'adminPortalDesc',
        ownerPortal: 'ownerPortal', ownerPortalDesc: 'ownerPortalDesc',
        cashierPortal: 'cashierPortal', cashierPortalDesc: 'cashierPortalDesc'
      };

      function buildDropdown() {
        var dd = document.getElementById('landingLangDropdown');
        if (!dd) return;
        var html = '';
        var current = localStorage.getItem('appLanguage') || 'en';
        LANGUAGES.forEach(function(l) {
          var active = l.code === current ? ' active' : '';
          html += '<li><a class="dropdown-item' + active + '" href="#" onclick="window._landingLang(\'' + l.code + '\'); return false;" style="color:#d5e0ec;font-size:12px;padding:6px 12px;">'
            + '<span class="me-1">' + l.flag + '</span> ' + l.native + ' <small class="text-muted">' + l.name + '</small></a></li>';
        });
        dd.innerHTML = html;
      }

      function applyLanding() {
        Object.keys(LANG_KEYS).forEach(function(key) {
          var i18nKey = LANG_KEYS[key];
          if (!_translations[i18nKey]) return;
          var els = document.querySelectorAll('[data-landing-i18n="' + key + '"]');
          els.forEach(function(el) { el.textContent = _translations[i18nKey]; });
        });
      }

      function fetchTranslations(lang, cb) {
        // Static JSON file first (fast, reliable), API endpoint as fallback
        fetch(_base + 'assets/lang/' + lang + '.json')
          .then(function(r) { if (!r.ok) throw new Error('HTTP ' + r.status); return r.json(); })
          .then(function(d) { cb(null, d); })
          .catch(function() {
            fetch(_base + 'classes/Translation.php?lang=' + lang)
              .then(function(r) { if (!r.ok) throw new Error('HTTP ' + r.status); return r.json(); })
              .then(function(d) { cb(null, d); })
              .catch(function(e) { cb(e); });
          });
      }

      function setLang(lang, data) {
        _translations = data;
        localStorage.setItem('appLanguage', lang);
        var info = LANGUAGES.find(function(l){ return l.code === lang; });
        var label = document.getElementById('landingLangLabel');
        if (label && info) label.textContent = info.flag + ' ' + info.code.toUpperCase();
        buildDropdown();
        applyLanding();
      }

      function loadLandingLang(lang) {
        fetchTranslations(lang, function(err, data) {
          if (err) {
            console.warn('Language not loaded for ' + lang + ':', err);
            if (lang !== 'en') loadLandingLang('en');
            return;
          }
          setLang(lang, data);
        });
      }

      window._landingLang = loadLandingLang;
      /* lookup helper for other scripts: window._landingT('loginBtn') */
      window._landingT = function(key) {
        var k = LANG_KEYS[key] || key;
        return _translations[k] || '';
      };

      document.addEventListener('DOMContentLoaded', function() {
        buildDropdown();
        var saved = localStorage.getItem('appLanguage') || 'en';
        loadLandingLang(saved);
      });
    })();
    </script>
    <script>
        // Preload background image for smooth animation
        function preloadBackgroundImage() {
            const img = new Image();
            img.onload = function() {
                document.body.classList.add('bg-loaded');
            };
            img.onerror = function() {
                console.warn('Hero background image failed to load');
                document.body.classList.add('bg-loaded'); // Continue without image
            };
            img.src = '/workshop/assets/images/hero.png';
        }

        // Initialize background loading
        document.addEventListener('DOMContentLoaded', preloadBackgroundImage);

        function toggleDrawer(role) {
            const drawer = document.getElementById('loginDrawer');
            const overlay = document.getElementById('overlay');
            const roleLabel = document.getElementById('roleLabel');
            const roleDescription = document.getElementById('roleDescription');
            const usernameField = document.getElementById('username');
            const selectedRoleInput = document.getElementById('selectedRole');
            
            if (role) {
                // Opening drawer with specific role
                drawer.classList.add('active');
                overlay.classList.add('active');

                // Update label, description and pre-fill username
                const T = window._landingT || function() { return ''; };
                const roleConfig = {
                    'admin': {
                        label: T('adminPortal') || 'ADMIN PORTAL',
                        description: T('adminPortalDesc') || 'Full system access - Manage all operations, users, and settings',
                        username: 'admin'
                    },
                    'owner': {
                        label: T('ownerPortal') || 'OWNER PORTAL',
                        description: T('ownerPortalDesc') || 'Business oversight - View all data, manage operations and reports',
                        username: 'owner'
                    },
                    'cashier': {
                        label: T('cashierPortal') || 'CASHIER PORTAL',
                        description: T('cashierPortalDesc') || 'Daily operations - Handle transactions, customers, and repair jobs',
                        username: 'cashier'
                    }
                };

                const config = roleConfig[role] || {
                    label: T('staffPortal') || 'STAFF PORTAL',
                    description: T('signInDesc') || 'Sign in to access workshop operations.',
                    username: role
                };
                
                roleLabel.textContent = config.label;
                roleDescription.textContent = config.description;
                usernameField.value = config.username;
                selectedRoleInput.value = role;
                
                // Focus password field
                setTimeout(() => document.getElementById('password').focus(), 100);
            } else {
                // Closing drawer - triggered by the top-right "Cancel" button
                // (or by clicking the dark overlay behind the popup).
                const T = window._landingT || function() { return ''; };
                drawer.classList.remove('active');
                overlay.classList.remove('active');
                roleLabel.textContent = T('staffPortal') || 'STAFF PORTAL';
                roleDescription.textContent = T('signInDesc') || 'Sign in to access workshop operations.';
                usernameField.value = '';
                selectedRoleInput.value = '';
                document.getElementById('password').value = '';
            }
        }

        function showForgotPassword() {
            alert('Please contact your system administrator for password reset assistance.\n\nFor demo purposes, all accounts use password: 2212Aa@0');
        }

        function togglePassword() {
            const field = document.getElementById('password'),
                icon = document.getElementById('togglePassword');
            const visible = field.type === 'text';
            field.type = visible ? 'password' : 'text';
            icon.className = visible ? 'bi bi-eye toggle-pass' : 'bi bi-eye-slash toggle-pass';
        }
        $('#loginform').on('submit', function(e) {
            e.preventDefault();
            const T = window._landingT || function() { return ''; };
            const button = $(this).find('button[type="submit"]');
            const originalText = button.html();
            button.prop('disabled', true).text(T('signingIn') || 'SIGNING IN...');

            $.ajax({
                url: '../classes/Login.php?f=login',
                method: 'POST',
                dataType: 'json',
                data: $(this).serialize(),
                success: function(resp) {
                    if (resp.status === 'success') {
                        toastr.success(T('loginSuccessful') || 'Login successful! Redirecting...', resp.user?.name || 'Welcome');
                        setTimeout(() => {
                            window.location.href = resp.redirect || '../public/';
                        }, 1000);
                    } else {
                        toastr.error(resp.msg || T('loginFailed') || 'Login failed. Please try again.');
                    }
                },
                error: function(xhr) {
                    let errorMsg = T('connectionError') || 'Connection error. Please check your network and try again.';

                    if (xhr.responseJSON?.msg) {
                        errorMsg = xhr.responseJSON.msg;
                    } else if (xhr.status === 401) {
                        errorMsg = T('invalidCredentials') || 'Invalid username or password.';
                    } else if (xhr.status === 423) {
                        errorMsg = T('accountLocked') || 'Account is temporarily locked. Please try again later.';
                    } else if (xhr.status === 403) {
                        errorMsg = T('insufficientPermissions') || 'Insufficient permissions for selected role.';
                    }

                    toastr.error(errorMsg);
                },
                complete: function() {
                    button.prop('disabled', false).html(originalText);
                }
            });
        });

        function generateUUID() {
            return ([1e7] + -1e3 + -4e3 + -8e3 + -1e11).replace(/[018]/g, c => (c ^ crypto.getRandomValues(new Uint8Array(1))[0] & 15 >> c / 4).toString(16));
        }
        (function loadDeviceId() {
            let id = localStorage.getItem('garageDeviceId');
            if (!id) {
                id = generateUUID();
                localStorage.setItem('garageDeviceId', id);
            }
            document.getElementById('deviceId').value = id;
        })();
    </script>
</body>

</html>