<?php
namespace Rehike\Signin;

use Rehike\FileSystem as FS;

/**
 * Time constants, relative to time() output which is
 * UNIX timestamp in seconds.
 */
const SECONDS = 1;
const MINUTES = 60;
const HOURS = 60 * MINUTES;
const DAYS = 24 * HOURS;
const WEEKS = 7 * DAYS;

/**
 * Implements the cache manager for the Rehike Signin component.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */
class Cacher
{
    /**
     * Stores the path to the signin cache file.
     * 
     * @var string
     */
    const CACHE_FILE = "cache/signin_cache.json";

    /**
     * Get the cache file if it exists and is valid.
     * 
     * If the cache file is invalid, then this method will
     * return false, in order to allow assignment within an
     * if statement.
     * 
     * @return object|false
     */
    public static function getCache()
    {
        if (FS::fileExists(self::CACHE_FILE))
        {
            try
            {
                $object = self::readCacheFile();

                $expiration = @$object->expire ?? 0;

                if (time() > $expiration)
                    return false;

                if (null != $object)
                    return $object;
                else
                    return false;
            }
            catch (\Throwable $e)
            {
                return false;
            }
        }
    }

    /**
     * Read and parse the cache file, if possible.
     * 
     * If the data is invalid, then this function will
     * return null.
     * 
     * @return object|null
     */
    protected static function readCacheFile()
    {
        $content = FS::getFileContents(self::CACHE_FILE);
        $object = json_decode($content);

        // Validate
        if (false == $object) return null;

        return $object;
    }

    /**
     * Write a new cache file from a provided responses array.
     * 
     * @param string[] $responses
     * @param bool $noCheck
     */
    public static function writeCache($responses, $noCheck = false)
    {
        if (!FS::fileExists(self::CACHE_FILE) || $noCheck)
        {
            $session = AuthManager::getUniqueSessionCookie();

            $data = (object)[
                "expire" => time() + (1 * WEEKS),
                "responseCache" => (object)[
                    $session => (object)$responses
                ]
            ];

            FS::writeFile(self::CACHE_FILE, json_encode($data));
        }
        else
        {
            return self::updateCache($responses);
        }
    }

    /**
     * Update the cache in order to add new data.
     * 
     * @param string[] $addedResponses
     * @return void
     */
    public static function updateCache($addedResponses)
    {
        $data = self::readCacheFile();
        
        if (null == $data)
            return self::writeCache($addedResponses, true);

        $session = AuthManager::getUniqueSessionCookie();

        @$data->expire += (1 * DAYS);
        @$data->responseCache->{$session} = $addedResponses;

        // Rewrite the file now
        FS::writeFile(self::CACHE_FILE, json_encode($data));
    }
}