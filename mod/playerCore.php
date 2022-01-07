<?php

class PlayerCore {
    const PLAYER_URL = 'https://www.youtube.com/embed/jNQXAC9IVRw';
    const PLAYER_JS_REGEX = '/(?<=\/s\/player\/)(.*)(?=\/[(w)|(p)])/';
    const CACHE_DIR = 'cache';
    const PLAYER_CACHE_FILE = 'player_cache.php';
    const CACHE_PATH = self::CACHE_DIR . '/' . self::PLAYER_CACHE_FILE;
    const CACHE_MAX_TIME = 18000; // 5 hours

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

    public static function generatePlayerCache(bool $cacheExists): string {
        // TODO: migrate to general cache utils
        $basepos = self::getPlayerBasePosition();
        $time = time();

        if ($cacheExists) {
            unlink(self::CACHE_PATH);
        }
        
        $newCacheFile = fopen(self::CACHE_PATH, 'w');
        fwrite($newCacheFile, '<?php return (object) [
            \'time\' => ' . $time . ',
            \'basepos\' => \'' . $basepos . '\'
        ];');
        fclose($newCacheFile);

        return $basepos;
    }

    public static function main(): string {
        // TODO: proper php validity check for cache
        // so we don't crash everything if it's invalid...
        if (file_exists(self::CACHE_PATH)) {
            $playerCache = include(self::CACHE_PATH);
            $maxTime = $playerCache->time + 18000 ?? 0;

            return (time() < $maxTime) 
                ? $playerCache->basepos 
                : self::generatePlayerCache(true);
        } else {
            return self::generatePlayerCache(false);
        }
    }
}