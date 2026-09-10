<?php
declare(strict_types=1);

/**
 * Google Translate API wrapper for workshop translation system.
 * Translates from English to any target language using the Google Cloud Translation API v2.
 * 
 * Usage:
 *   $translator = new GoogleTranslate('YOUR_API_KEY');
 *   $result = $translator->translate('Hello', 'zh-CN');
 *   $results = $translator->translateBatch(['Hello', 'World'], 'zh-CN');
 */
class GoogleTranslate
{
    private string $apiKey;
    private string $baseUrl = 'https://translation.googleapis.com/language/translate/v2';
    private string $cacheDir;
    private int $cacheExpiry = 86400 * 30; // 30 days

    /** Supported languages with their Google Translate codes */
    public static array $supportedLanguages = [
        'en' => ['code' => 'en',    'name' => 'English',    'native' => 'English',    'flag' => '🇺🇸'],
        'zh' => ['code' => 'zh-CN', 'name' => 'Chinese',    'native' => '中文',       'flag' => '🇨🇳'],
        'es' => ['code' => 'es',    'name' => 'Spanish',    'native' => 'Español',    'flag' => '🇪🇸'],
        'fr' => ['code' => 'fr',    'name' => 'French',     'native' => 'Français',   'flag' => '🇫🇷'],
        'ar' => ['code' => 'ar',    'name' => 'Arabic',     'native' => 'العربية',    'flag' => '🇸🇦'],
        'de' => ['code' => 'de',    'name' => 'German',     'native' => 'Deutsch',    'flag' => '🇩🇪'],
        'pt' => ['code' => 'pt',    'name' => 'Portuguese', 'native' => 'Português',  'flag' => '🇧🇷'],
        'ja' => ['code' => 'ja',    'name' => 'Japanese',   'native' => '日本語',     'flag' => '🇯🇵'],
        'ko' => ['code' => 'ko',    'name' => 'Korean',     'native' => '한국어',     'flag' => '🇰🇷'],
        'hi' => ['code' => 'hi',    'name' => 'Hindi',      'native' => 'हिन्दी',     'flag' => '🇮🇳'],
        'sw' => ['code' => 'sw',    'name' => 'Swahili',    'native' => 'Kiswahili',  'flag' => '🇰🇪'],
        'ru' => ['code' => 'ru',    'name' => 'Russian',    'native' => 'Русский',    'flag' => '🇷🇺'],
        'tr' => ['code' => 'tr',    'name' => 'Turkish',    'native' => 'Türkçe',     'flag' => '🇹🇷'],
        'id' => ['code' => 'id',    'name' => 'Indonesian', 'native' => 'Bahasa',     'flag' => '🇮🇩'],
        'th' => ['code' => 'th',    'name' => 'Thai',       'native' => 'ไทย',        'flag' => '🇹🇭'],
        'vi' => ['code' => 'vi',    'name' => 'Vietnamese', 'native' => 'Tiếng Việt', 'flag' => '🇻🇳'],
        'it' => ['code' => 'it',    'name' => 'Italian',    'native' => 'Italiano',   'flag' => '🇮🇹'],
        'nl' => ['code' => 'nl',    'name' => 'Dutch',      'native' => 'Nederlands', 'flag' => '🇳🇱'],
        'pl' => ['code' => 'pl',    'name' => 'Polish',     'native' => 'Polski',     'flag' => '🇵🇱'],
        'sv' => ['code' => 'sv',    'name' => 'Swedish',    'native' => 'Svenska',    'flag' => '🇸🇪'],
    ];

    public function __construct(string $apiKey, ?string $cacheDir = null)
    {
        $this->apiKey = $apiKey;
        $this->cacheDir = $cacheDir ?? __DIR__ . '/../assets/lang/.cache';
        if (!is_dir($this->cacheDir)) {
            mkdir($this->cacheDir, 0755, true);
        }
    }

    /**
     * Translate a single string from English to the target language.
     */
    public function translate(string $text, string $targetLang): string
    {
        if (empty(trim($text)) || $targetLang === 'en') {
            return $text;
        }

        $cacheKey = $this->getCacheKey($text, $targetLang);
        $cached = $this->getFromCache($cacheKey);
        if ($cached !== null) {
            return $cached;
        }

        $response = $this->apiCall([
            'q'     => $text,
            'source' => 'en',
            'target' => $targetLang,
            'format' => 'text',
        ]);

        $translated = $response['data']['translations'][0]['translatedText'] ?? $text;
        $this->putInCache($cacheKey, $translated);

        return $translated;
    }

    /**
     * Translate multiple strings in a single API call (batch).
     * Returns an array of translated strings in the same order.
     */
    public function translateBatch(array $strings, string $targetLang): array
    {
        if ($targetLang === 'en') {
            return $strings;
        }

        $results = [];
        $toTranslate = [];
        $toTranslateIndices = [];

        foreach ($strings as $i => $text) {
            if (empty(trim($text))) {
                $results[$i] = $text;
                continue;
            }
            $cacheKey = $this->getCacheKey($text, $targetLang);
            $cached = $this->getFromCache($cacheKey);
            if ($cached !== null) {
                $results[$i] = $cached;
            } else {
                $toTranslate[] = $text;
                $toTranslateIndices[] = $i;
            }
        }

        if (!empty($toTranslate)) {
            // Google Translate API accepts multiple q parameters for batch
            $params = [
                'source' => 'en',
                'target' => $targetLang,
                'format' => 'text',
            ];
            foreach ($toTranslate as $text) {
                $params['q'][] = $text;
            }

            $response = $this->apiCall($params);
            $translations = $response['data']['translations'] ?? [];

            foreach ($translations as $j => $item) {
                $idx = $toTranslateIndices[$j];
                $translated = $item['translatedText'] ?? $toTranslate[$j];
                $results[$idx] = $translated;

                $cacheKey = $this->getCacheKey($toTranslate[$j], $targetLang);
                $this->putInCache($cacheKey, $translated);
            }
        }

        ksort($results);
        return array_values($results);
    }

    /**
     * Translate a full translation dictionary (key-value object) to the target language.
     * This is the main method used by the workshop translation system.
     * Returns an associative array of key => translated_value.
     */
    public function translateDictionary(array $sourceDict, string $targetLang): array
    {
        if ($targetLang === 'en') {
            return $sourceDict;
        }

        $keys = array_keys($sourceDict);
        $values = array_values($sourceDict);

        $translatedValues = $this->translateBatch($values, $targetLang);

        $result = [];
        foreach ($keys as $i => $key) {
            $result[$key] = $translatedValues[$i];
        }

        return $result;
    }

    /**
     * Translate a JSON file and save the result to the cache.
     */
    public function translateJsonFile(string $sourcePath, string $targetLang): array
    {
        $sourceContent = file_get_contents($sourcePath);
        if ($sourceContent === false) {
            throw new RuntimeException("Cannot read source file: {$sourcePath}");
        }

        $sourceDict = json_decode($sourceContent, true);
        if (!is_array($sourceDict)) {
            throw new RuntimeException("Invalid JSON in source file: {$sourcePath}");
        }

        // Check if cached version exists and is fresh
        $cacheFile = $this->getCacheFilePath($targetLang);
        if (is_file($cacheFile) && (time() - filemtime($cacheFile)) < $this->cacheExpiry) {
            $cached = json_decode(file_get_contents($cacheFile), true);
            if (is_array($cached)) {
                return $cached;
            }
        }

        $translated = $this->translateDictionary($sourceDict, $targetLang);

        // Save to cache
        file_put_contents($cacheFile, json_encode($translated, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));

        return $translated;
    }

    /**
     * Get list of all supported languages.
     */
    public static function getSupportedLanguages(): array
    {
        return self::$supportedLanguages;
    }

    /**
     * Check if a language code is supported.
     */
    public static function isLanguageSupported(string $langCode): bool
    {
        return isset(self::$supportedLanguages[$langCode]);
    }

    /**
     * Get the Google API language code for a workshop language code.
     */
    public static function getGoogleLangCode(string $langCode): string
    {
        return self::$supportedLanguages[$langCode]['code'] ?? $langCode;
    }

    // ─── Private helpers ───────────────────────────────────────────────

    private function apiCall(array $params): array
    {
        $url = $this->baseUrl . '?key=' . $this->apiKey;

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => http_build_query($params, '', '&', PHP_QUERY_RFC3986),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_HTTPHEADER     => ['Content-Type: application/x-www-form-urlencoded'],
            CURLOPT_SSL_VERIFYPEER => true,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($response === false) {
            throw new RuntimeException("cURL error: {$error}");
        }

        $data = json_decode($response, true);
        if ($httpCode !== 200 || !isset($data['data'])) {
            $errorMsg = $data['error']['message'] ?? "HTTP {$httpCode}: Unknown error";
            throw new RuntimeException("Google Translate API error: {$errorMsg}");
        }

        return $data;
    }

    private function getCacheKey(string $text, string $targetLang): string
    {
        return md5($targetLang . '|' . $text);
    }

    private function getCacheFilePath(string $targetLang): string
    {
        return $this->cacheDir . '/' . $targetLang . '.json';
    }

    private function getFromCache(string $cacheKey): ?string
    {
        $indexFile = $this->cacheDir . '/_index.json';
        if (!is_file($indexFile)) {
            return null;
        }

        $index = json_decode(file_get_contents($indexFile), true);
        if (!is_array($index) || !isset($index[$cacheKey])) {
            return null;
        }

        $entry = $index[$cacheKey];
        if ((time() - ($entry['ts'] ?? 0)) > $this->cacheExpiry) {
            unset($index[$cacheKey]);
            file_put_contents($indexFile, json_encode($index));
            return null;
        }

        return $entry['val'] ?? null;
    }

    private function putInCache(string $cacheKey, string $value): void
    {
        $indexFile = $this->cacheDir . '/_index.json';
        $index = [];
        if (is_file($indexFile)) {
            $index = json_decode(file_get_contents($indexFile), true) ?? [];
        }

        $index[$cacheKey] = ['val' => $value, 'ts' => time()];

        // Prune if too large (keep last 10000 entries)
        if (count($index) > 10000) {
            uasort($index, fn($a, $b) => ($b['ts'] ?? 0) - ($a['ts'] ?? 0));
            $index = array_slice($index, 0, 10000, true);
        }

        file_put_contents($indexFile, json_encode($index, JSON_UNESCAPED_UNICODE));
    }
}
