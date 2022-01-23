<?php

class PlayerCore {
    const PLAYER_URL = 'https://www.youtube.com/embed/jNQXAC9IVRw';
    const PLAYER_JS_REGEX = '/(?<=\/s\/player\/)(.*)(?=\/[(w)|(p)])/';
    const CACHE_DIR = 'cache';
    const PLAYER_CACHE_FILE = 'player_cache.php';
    const CACHE_PATH = self::CACHE_DIR . '/' . self::PLAYER_CACHE_FILE;
    const CACHE_MAX_TIME = 18000; // 5 hours
    const STS_REGEX = '/signatureTimestamp:?\s*([0-9]*)/';

    /**
     * Extract the signatureTimestamp embedded within YouTube
     * player source code. 
     * 
     * This is required to optimise streaming, otherwise video 
     * downloads take forever.
     * 
     * @param string $playerCode   String of the YouTube player code
     * @return int signatureTimestamp value
     */
    public static function getSignatureTimestamp(string $basepos): int {
        $playerCode = self::getPlayerSource($basepos);
        preg_match(self::STS_REGEX, $playerCode, $matches);
        
        if (@$matches[1]) {
            return (int) $matches[1];
        }

        return 0;
    }

    public static function getPlayerSource(string $basepos): string {
        $ch = curl_init('https://www.youtube.com/s/player/' . $basepos . '/player_ias.vflset/en_US/base.js');
        curl_setopt_array($ch, [
            CURLOPT_POST => false,
            CURLOPT_RETURNTRANSFER => true
        ]);

        $response = curl_exec($ch);
        curl_close($ch);

        return $response;
    }

    public static function getPlayerBasePosition(): string {
        $ch = curl_init(self::PLAYER_URL);
        curl_setopt_array($ch, [
            CURLOPT_POST => false,
            CURLOPT_RETURNTRANSFER => true
        ]);

        $baseposResponse = curl_exec($ch);
        curl_close($ch);

        preg_match(self::PLAYER_JS_REGEX, $baseposResponse, $baseposExtract);

        return $baseposExtract[0];
    }

    public static function generatePlayerCache(bool $cacheExists): object {
        // TODO: migrate to general cache utils
        $basepos = self::getPlayerBasePosition();
        $time = time();

        if ($cacheExists) {
            unlink(self::CACHE_PATH);
        }
        
        $newCacheFile = fopen(self::CACHE_PATH, 'w');
        fwrite($newCacheFile, '<?php return (object) [
            \'time\' => ' . $time . ',
            \'basepos\' => \'' . $basepos . '\',
            \'sts\' => ' . self::getSignatureTimestamp($basepos) . '
        ];');
        fclose($newCacheFile);

        return self::main();
    }

    public static function main(): object {
        // TODO: proper php validity check for cache
        // so we don't crash everything if it's invalid...
        if (file_exists(self::CACHE_PATH)) {
            $playerCache = include(self::CACHE_PATH);
            $maxTime = $playerCache->time + 18000 ?? 0;

            return (time() < $maxTime) 
                ? $playerCache 
                : self::generatePlayerCache(true);
        } else {
            return self::generatePlayerCache(false);
        }
    }
}