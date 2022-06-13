<?php

class PlayerCore {
    const PLAYER_URL = 'https://www.youtube.com/embed/jNQXAC9IVRw';
    const PLAYER_JS_REGEX = '/(?<=\/s\/player\/)(.*)(?=\/[(w)|(p)])/';
    const CACHE_DIR = 'cache';
    const PLAYER_CACHE_FILE = 'player_cache.php';
    const CACHE_PATH = self::CACHE_DIR . '/' . self::PLAYER_CACHE_FILE;
    const CACHE_MAX_TIME = 18000; // 5 hours
    const STS_REGEX = '/signatureTimestamp:?\s*([0-9]*)/';
    const MAX_CACHE_SAVE_RETRIES = 5;

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

    public static function generatePlayerCache(): object {
        // TODO: migrate to general cache utils
        
        // Patch: Major errors without "cache" folder
        // ~ Coffee
        
        $basepos = self::getPlayerBasePosition();
        $time = time();
        $sts = self::getSignatureTimestamp($basepos);

        try
        {
            self::writeCacheFile($time, $basepos, $sts);
        }
        catch (\Throwable $e)
        {
            // Elevate exception
            throw $e;
        }

        return self::main();
    }

    /**
     * PATCH (kirasicecreamm): Occasionally the stream will fail
     * to open. Without catching, this often results in a fatal
     * error being thrown without any proper indicator as to why.
     * 
     * As such, this behaviour was moved from generatePlayerCache()
     * to this new function to allow reattempts in case of odd FS
     * errors.
     * 
     * In addition: crash seems to be caused by unlink (deleting the file)
     * before fopen, probably a time period where the file is "protected"
     * between then and there. fwrite "w" clears the file anyways, so this is 
     * not needed. (https://stackoverflow.com/q/47621347)
     */
    protected static function writeCacheFile(&$time, &$basepos, &$sts, $recurse = 0)
    {
        // Validate the request
        switch (true)
        {
            // Throw an error if the maximum number of retries
            // has been exceeded.
            case ($recurse > self::MAX_CACHE_SAVE_RETRIES):
                throw new \Exception(
                    "Maximum number of retries exceeded. Is directory writable?"
                );
                break;
        }

        // Create the cache folder if it doesn't exist.
        if (!is_dir(self::CACHE_DIR))
        {
            $status = mkdir(self::CACHE_DIR);

            // Make sure the directory was able to be created, otherwise throw
            // an exception.
            if (false == $status)
            {
                throw new \Exception("Permission denied.");
            }
        }
        
        $newCacheFile = fopen(self::CACHE_PATH, 'w');

        $status = fwrite($newCacheFile, '<?php return (object) [
            \'time\' => ' . $time . ',
            \'basepos\' => \'' . $basepos . '\',
            \'sts\' => ' . self::getSignatureTimestamp($basepos) . '
        ];');

        // Validate that the file write was successful, or retry.
        if (false === $status)
        {
            fclose($newCacheFile);
            return self::writeCacheFile($time, $basepos, $sts, $recurse + 1);
        }

        fclose($newCacheFile);
    }

    public static function main(): object {
        // TODO: proper php validity check for cache
        // so we don't crash everything if it's invalid...
        if (file_exists(self::CACHE_PATH)) {
            $playerCache = include(self::CACHE_PATH);
            $maxTime = $playerCache->time + 18000 ?? 0;

            return (time() < $maxTime) 
                ? $playerCache 
                : self::generatePlayerCache();
        } else {
            return self::generatePlayerCache();
        }
    }
}