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

    const CACHE_VERSION = 2;

    /**
     * Get the cache file if it exists and is valid.
     * 
     * If the cache file is invalid, then this method will
     * return false, in order to allow assignment within an
     * if statement.
     */
    public static function getCache(): object|false
    {
        if (FS::fileExists(self::CACHE_FILE))
        {
            try
            {
                $object = self::readCacheFile();

                $expiration = @$object->expire ?? 0;
                $version = @$object->version ?? 0;

                if ($version != self::CACHE_VERSION)
                    return false;

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

        return false;
    }

    /**
     * Read and parse the cache file, if possible.
     * 
     * If the data is invalid, then this function will
     * return null.
     */
    protected static function readCacheFile(): ?object
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
    public static function writeCache(array $responses, bool $noCheck = false): void
    {
        if (!FS::fileExists(self::CACHE_FILE) || $noCheck)
        {
            $session = AuthManager::getUniqueSessionCookie();

            $data = (object)[
                "expire" => time() + (1 * WEEKS),
                "version" => self::CACHE_VERSION,
                "responseCache" => (object)[
                    $session => (object)$responses
                ]
            ];

            FS::writeFile(self::CACHE_FILE, json_encode($data));
        }
        else
        {
            self::updateCache($responses);
        }
    }

    /**
     * Update the cache in order to add new data.
     * 
     * @param string[] $addedResponses
     */
    public static function updateCache(array $addedResponses): void
    {
        $data = self::readCacheFile();
        
        if ((null == $data) || (@$data->version != self::CACHE_VERSION))
        {
            self::writeCache($addedResponses, true);
            return;
        }

        $session = AuthManager::getUniqueSessionCookie();

        @$data->expire += (1 * DAYS);
        @$data->responseCache->{$session} = $addedResponses;

        // Rewrite the file now
        FS::writeFile(self::CACHE_FILE, json_encode($data));
    }
}