<?php

class YtSigninCore
{
    public bool   $isSignedIn = false;
    public bool   $cacheExists = false;
    public string $sapisid = '';
    public string $username = '';
    public string $ucid = '';
    public string $googBrandAccId = '';
    public string $avatarUrl = '';
    public object $serverResponse;
    public array  $httpCookies = [];

    public const testUrl = 'https://www.youtube.com/user/jawed/about?pbj=1';
    public const cacheFile = 'cache/sc2.json';

    public static function getCookies(): array {
        if (isset($_SERVER['COOKIE'])) {
            return $_SERVER['COOKIE'];
        } else {
            return [];
        }
    }

    public static function generateSapisidHash(string $sapisid, string $origin = 'https://www.youtube.com'): string {
        $curtime = time();
        $hash = sha1($curtime . ' ' . $sapisid . ' ' . $origin);
        return 'SAPISIDHASH ' . $curtime . '_' . $hash;
    }

    public function getCache(): object {
        $cache = file_get_contents(self::cacheFile);

        try {
            $json = json_decode($cache);
        } catch (Exception $e) {
            return (object) ['error' => 'Invalid cache.'];
        }
        
        return $json;
    }

    public static function cacheFileExists(): bool {
        return file_exists(self::cacheFile);
    }

    public function __construct() {
        $this->serverResponse = (object) [];
        $this->httpCookies = self::getCookies();
    }
}
