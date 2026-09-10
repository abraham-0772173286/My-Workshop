<?php
header('Content-Type: application/json');

$slot = $_POST['slot'] ?? '';
if (!in_array($slot, ['customer', 'advisor', 'cashier'], true)) {
    echo json_encode(['ok' => false, 'error' => 'Invalid signature slot']);
    exit;
}

$sigDir = __DIR__ . '/signatures';

if (($_POST['action'] ?? '') === 'clear') {
    foreach (['png', 'jpg', 'jpeg', 'webp', 'gif'] as $ext) {
        $f = $sigDir . '/' . $slot . '.' . $ext;
        if (is_file($f)) @unlink($f);
    }
    echo json_encode(['ok' => true, 'message' => 'Signature removed']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_FILES['signature_file'])) {
    echo json_encode(['ok' => false, 'error' => 'No file received']);
    exit;
}

$file = $_FILES['signature_file'];

if ($file['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['ok' => false, 'error' => 'Upload error: ' . $file['error']]);
    exit;
}

if ($file['size'] > 3 * 1024 * 1024) {
    echo json_encode(['ok' => false, 'error' => 'Image must be 3 MB or smaller']);
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
    if ($oldExt !== $ext && is_file($sigDir . '/' . $slot . '.' . $oldExt)) {
        @unlink($sigDir . '/' . $slot . '.' . $oldExt);
    }
}

if (!is_dir($sigDir)) @mkdir($sigDir, 0777, true);

$target = $sigDir . '/' . $slot . '.' . $ext;
if (!move_uploaded_file($file['tmp_name'], $target)) {
    echo json_encode(['ok' => false, 'error' => 'Could not save the image']);
    exit;
}

echo json_encode(['ok' => true, 'message' => 'Signature saved successfully']);