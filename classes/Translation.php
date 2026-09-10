<?php
declare(strict_types=1);

/**
 * Translation API endpoint.
 * Serves translated JSON files to the frontend.
 * Uses Google Translate API for dynamic translation of any language.
 *
 * GET /classes/Translation.php?lang=zh
 * GET /classes/Translation.php?lang=ar
 * GET /classes/Translation.php?action=languages
 */
require_once __DIR__ . '/GoogleTranslate.php';

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: public, max-age=3600');
header('Access-Control-Allow-Origin: *');

$lang = trim($_GET['lang'] ?? '');
$action = trim($_GET['action'] ?? '');

if ($action === 'languages') {
    $languages = GoogleTranslate::getSupportedLanguages();
    $available = [];
    foreach ($languages as $code => $info) {
        $langFile = __DIR__ . '/../assets/lang/' . $code . '.json';
        $available[] = [
            'code'    => $code,
            'name'    => $info['name'],
            'native'  => $info['native'],
            'flag'    => $info['flag'],
            'bundled' => is_file($langFile),
        ];
    }
    echo json_encode(['languages' => $available]);
    exit;
}

if (empty($lang)) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing lang parameter.']);
    exit;
}

$validLangs = array_keys(GoogleTranslate::$supportedLanguages);
if (!in_array($lang, $validLangs, true)) {
    http_response_code(400);
    echo json_encode(['error' => 'Unsupported language code: ' . $lang]);
    exit;
}

// 1. Try bundled static file first (fastest)
$staticFile = __DIR__ . '/../assets/lang/' . $lang . '.json';
if (is_file($staticFile)) {
    $content = file_get_contents($staticFile);
    if ($content !== false) {
        $data = json_decode($content, true);
        if (is_array($data) && count($data) > 10) {
            echo $content;
            exit;
        }
    }
}

// 2. Load English source dictionary
$enFile = __DIR__ . '/../assets/lang/en.json';
if (!is_file($enFile)) {
    http_response_code(500);
    echo json_encode(['error' => 'English source file not found.']);
    exit;
}

$enDict = json_decode(file_get_contents($enFile), true);
if (!is_array($enDict)) {
    http_response_code(500);
    echo json_encode(['error' => 'Invalid English source file.']);
    exit;
}

// 3. Get Google API key
$apiKey = '';
$envKey = $_ENV['GOOGLE_TRANSLATE_API_KEY'] ?? getenv('GOOGLE_TRANSLATE_API_KEY') ?? '';
if (!empty($envKey)) {
    $apiKey = $envKey;
} else {
    $configFile = __DIR__ . '/../configs/translate_api.php';
    if (is_file($configFile)) {
        $config = require $configFile;
        $apiKey = $config['api_key'] ?? '';
    }
}

if (empty($apiKey)) {
    // No API key configured - return English with header indicating fallback
    header('X-Translation-Source: fallback-english-no-key');
    echo json_encode($enDict, JSON_UNESCAPED_UNICODE);
    exit;
}

// 4. Translate using Google Translate API
$googleLang = GoogleTranslate::getGoogleLangCode($lang);
try {
    $translator = new GoogleTranslate($apiKey);
    $translated = $translator->translateDictionary($enDict, $googleLang);

    // Cache to static file for future fast loads
    file_put_contents($staticFile, json_encode($translated, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT), LOCK_EX);

    header('X-Translation-Source: google-translate');
    echo json_encode($translated, JSON_UNESCAPED_UNICODE);
} catch (Exception $e) {
    header('X-Translation-Source: fallback-english-error');
    header('X-Translation-Error: ' . $e->getMessage());
    echo json_encode($enDict, JSON_UNESCAPED_UNICODE);
}
