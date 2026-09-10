<?php
header('Content-Type: application/json');

$logoDir = __DIR__ . '/logo';

if (($_POST['action'] ?? '') === 'clear') {
    foreach (['png', 'jpg', 'jpeg', 'webp', 'gif'] as $ext) {
        $f = $logoDir . '/company_logo.' . $ext;
        if (is_file($f)) @unlink($f);
    }
    echo json_encode(['ok' => true, 'message' => 'Logo removed']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_FILES['logo_file'])) {
    echo json_encode(['ok' => false, 'error' => 'No file received']);
    exit;
}

$file = $_FILES['logo_file'];

if ($file['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['ok' => false, 'error' => 'Upload error: ' . $file['error']]);
    exit;
}

if ($file['size'] > 5 * 1024 * 1024) {
    echo json_encode(['ok' => false, 'error' => 'Image must be 5 MB or smaller']);
    exit;
}

$info = @getimagesize($file['tmp_name']);
if ($info === false) {
    echo json_encode(['ok' => false, 'error' => 'Not a valid image file']);
    exit;
}

$map = [
    'image/png'  => 'png',
    'image/jpeg' => 'jpg',
    'image/gif'  => 'gif',
    'image/webp' => 'webp',
];

$ext = $map[$info['mime']] ?? null;
if ($ext === null) {
    echo json_encode(['ok' => false, 'error' => 'Only PNG, JPG, GIF or WEBP images are allowed']);
    exit;
}

foreach (['png', 'jpg', 'jpeg', 'webp', 'gif'] as $oldExt) {
    if ($oldExt !== $ext && is_file($logoDir . '/company_logo.' . $oldExt)) {
        @unlink($logoDir . '/company_logo.' . $oldExt);
    }
}

if (!is_dir($logoDir)) @mkdir($logoDir, 0777, true);

$target = $logoDir . '/company_logo.' . $ext;
if (!move_uploaded_file($file['tmp_name'], $target)) {
    echo json_encode(['ok' => false, 'error' => 'Could not save the image']);
    exit;
}

echo json_encode(['ok' => true, 'message' => 'Logo saved successfully']);