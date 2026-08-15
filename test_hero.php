<?php
require_once __DIR__ . '/inc/app.php';
$workshopBase = workshop_base_path();
$heroImagePath = $workshopBase . 'assets/images/hero.png';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Hero Image Test</title>
    <style>
        body { margin: 0; font-family: Arial; }
        .test-bg {
            width: 100vw;
            height: 100vh;
            background: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)), url('<?= $heroImagePath ?>') center/cover;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 2rem;
            animation: zoom 5s ease-in-out infinite alternate;
        }
        @keyframes zoom {
            0% { transform: scale(1); }
            100% { transform: scale(1.1); }
        }
    </style>
</head>
<body>
    <div class="test-bg">
        <div style="text-align: center; background: rgba(0,0,0,0.7); padding: 2rem; border-radius: 10px;">
            <h1>Hero Image Test</h1>
            <p>Image path: <?= $heroImagePath ?></p>
            <p><a href="<?= $heroImagePath ?>" target="_blank" style="color: #f5a623;">Open Image Directly</a></p>
            <p><a href="inc/landing.php" style="color: white;">Back to Landing Page</a></p>
        </div>
    </div>
</body>
</html>
