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
            min-height: 100%;
            margin: 0;
            background: var(--garage-dark);
            color: #fff;
        }

        .hero-bg {
            position: fixed;
            inset: 0;
            z-index: -2;
            background: linear-gradient(90deg, rgba(5, 16, 28, .94) 0%, rgba(5, 17, 30, .78) 48%, rgba(5, 17, 30, .42) 100%), url('https://images.unsplash.com/photo-1487754180451-c456f719a1fc?auto=format&fit=crop&q=85&w=2000') center/cover;
        }

        .hero-bg::after {
            content: '';
            position: absolute;
            inset: 0;
            z-index: -1;
            opacity: .38;
            background-image: linear-gradient(rgba(255, 255, 255, .08) 1px, transparent 1px), linear-gradient(90deg, rgba(255, 255, 255, .08) 1px, transparent 1px);
            background-size: 42px 42px;
            mask-image: linear-gradient(90deg, #000, transparent 70%);
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

        .login-drawer {
            position: fixed;
            inset: 0 0 0 auto;
            width: 430px;
            z-index: 10;
            padding: 53px 40px;
            background: rgba(7, 21, 34, .96);
            border-left: 1px solid rgba(255, 255, 255, .11);
            backdrop-filter: blur(35px);
            transform: translateX(100%);
            transition: .55s cubic-bezier(.7, 0, .3, 1);
        }

        .login-drawer.active {
            transform: translateX(0);
        }

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
            background: #0a1c2e;
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
    <div class="overlay" id="overlay" onclick="toggleDrawer()"></div>
    <main class="main-content">
        <header class="d-flex justify-content-between align-items-center">
            <div class="brand"><span class="brand-mark"><i class="bi bi-wrench-adjustable"></i></span><span>JIN LONG GARAGE<small>金龙汽车维修 · WORKSHOP OPERATIONS</small></span></div>
            <button class="btn-access" onclick="toggleDrawer()">STAFF LOGIN <i class="bi bi-shield-lock ms-2"></i></button>
        </header>
        <section class="hero-copy">
            <div class="eyebrow animate__animated animate__fadeInDown">REPAIR JOBS · PAYMENTS · RECEIPTS</div>
            <h1 class="hero-title animate__animated animate__fadeInLeft">BUILT FOR THE<br><span>BUSY WORKSHOP.</span></h1>
            <img src= "workshop/images/logo.png">
            
            <p class="hero-text">A simple place for your cashier to register vehicles, record repair jobs, collect payments and issue receipts—while you stay in control of the day.</p>
            <div class="service-tags animate__animated animate__fadeInUp"><span class="service-tag"><i class="bi bi-car-front-fill"></i>Vehicle records</span><span class="service-tag"><i class="bi bi-clipboard2-check"></i>Repair jobs</span><span class="service-tag"><i class="bi bi-cash-stack"></i>Payments</span><span class="service-tag"><i class="bi bi-receipt"></i>Receipts</span></div>
        </section>
        <footer class="dock animate__animated animate__fadeInUp">
            <div><strong><span class="status-dot"></span>Workshop system online</strong><small>Secure access for the owner and cashier</small></div><small>© <?= date('Y') ?> Jin Long Garage. All rights reserved.</small>
        </footer>
    </main>
    <aside class="login-drawer" id="loginDrawer">
        <div class="d-flex justify-content-between align-items-start mb-5">
            <div>
                <div class="eyebrow" style="font-size:9px">STAFF PORTAL</div>
                <h4 class="fw-bold mt-2 mb-0">Welcome back</h4><small style="color:#9db0c5">Sign in to access workshop operations.</small>
            </div><button class="btn text-white p-0" aria-label="Close login" onclick="toggleDrawer()"><i class="bi bi-x-lg fs-5"></i></button>
        </div>
        <form id="loginform">
            <div class="form-group"><input type="text" name="username" id="username" required placeholder=" " autocomplete="username"><label for="username">Username</label></div>
            <div class="form-group"><input type="password" name="password" id="password" required placeholder=" " autocomplete="current-password"><label for="password">Password</label><i class="bi bi-eye toggle-pass" id="togglePassword" onclick="togglePassword()"></i></div>
            <button class="btn-login w-100" type="submit">LOGIN TO SYSTEM <i class="bi bi-arrow-right ms-1"></i></button>
            <div class="security-note"><i class="bi bi-shield-check me-1"></i> Your session is secure and protected</div>
            <input name="deviceId" type="hidden" id="deviceId">
            <div class="text-center mt-4"><a href="../forgot-password" class="small text-decoration-none" style="color:#c2d4e6">Forgot password?</a></div>
        </form>
    </aside>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script>
        function toggleDrawer() {
            document.getElementById('loginDrawer').classList.toggle('active');
            document.getElementById('overlay').classList.toggle('active');
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
            const button = $(this).find('button[type="submit"]');
            button.prop('disabled', true).text('SIGNING IN...');
            $.ajax({
                url: '../classes/Login.php?f=login',
                method: 'POST',
                dataType: 'json',
                data: $(this).serialize(),
                success: function(resp) {
                    if (resp.status === 'success') {
                        toastr.success('Login successful');
                        window.location.href = '../public/';
                    } else {
                        toastr.error(resp.msg || 'Invalid username or password.');
                    }
                },
                error: function() {
                    toastr.error('Login service is not available yet. Connect the local Login endpoint to enable sign-in.');
                },
                complete: function() {
                    button.prop('disabled', false).html('LOGIN TO SYSTEM <i class="bi bi-arrow-right ms-1"></i>');
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