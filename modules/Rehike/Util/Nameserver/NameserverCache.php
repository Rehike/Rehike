<?php
namespace Rehike\Util\Nameserver;

use Rehike\Exception\FileSystem\FsFileDoesNotExistException;
use Rehike\Exception\FileSystem\FsFileReadFailureException;
use Rehike\FileSystem;
use Rehike\Logging\DebugLogger;

/**
 * Provides utilities for caching nameserver lookups.
 * 
 * @author Pumpkin <pumpkinpielemon@gmail.com>
 */
class NameserverCache
{
    /**
     * The path to the file used to store the cache.
     * 
     * @var string
     */
    public const CACHE_FILE = "cache/nameserver_cache.json";

    /**
     * The amount of time for which a cache entry is valid.
     * 
     * @var int
     */
    public const VALID_TIME = 18000; // 5 hours

    /**
     * Attempt to get nameserver information from the cache.
     * 
     * @return ?NameserverInfo Null if failed or the cache file doesn't exist.
     */
    public static function get(string $domain): ?NameserverInfo
    {
        if (!FileSystem::fileExists(self::CACHE_FILE))
        {
            return null;
        }

        try
        {
            $jsonStr = FileSystem::getFileContents(self::CACHE_FILE);
        }
        catch (FsFileReadFailureException $e)
        {
            DebugLogger::print(
                "[NameserverCache] Failed to read nameserver cache file " .
                "with exception: %s", $e->getMessage()
            );
            return null;
        }
        catch (FsFileDoesNotExistException $e)
        {
            DebugLogger::print(
                "[NameserverCache] Nameserver cache file somehow " .
                "managed to stop existing after the proper check."
            );
            return null;
        }

        if (!is_string($jsonStr))
        {
            DebugLogger::print("Failed to read nameserver cache file.");
            return null;
        }

        $data = json_decode($jsonStr);

        if (!is_object($data))
        {
            DebugLogger::print("Nameserver cache file contains invalid data. The file will be removed.");
            unlink(self::CACHE_FILE);
            return null;
        }

        $entry = $data->{$domain};

        if (isset($entry)
            && isset($entry->domain)
            && isset($entry->expire)
            && $entry->domain == $domain
            && $entry->expire > time()
        )
        {
            return new NameserverInfo($domain, $entry->ip);
        }

        DebugLogger::print(
            "Nameserver cache file is invalid.\n" .
            " - Entry is set: %s\n" .
            " - Entry domain is set: %s\n" .
            " - Entry expire is set: %s\n" .
            " - Entry domain is same: %s\n" .
            " - Entry expire time is less than current time: %s\n",
            isset($entry) ? "true" : "false",
            isset($entry->domain) ? "true" : "false",
            isset($entry->expire) ? "true" : "false",
            @$entry->domain == $domain ? "true" : "false",
            @$entry->expire > time() ? "true" : "false",
        );

        return null;
    }

    /**
     * Write new nameserver information to the cache.
     * 
     * @return bool True on success, false on failure.
     */
    public static function write(NameserverInfo $info): bool
    {
        $data = (object)[];

        if (FileSystem::fileExists(self::CACHE_FILE))
        {
            try
            {
                $jsonStr = FileSystem::getFileContents(self::CACHE_FILE);
            }
            catch (FsFileDoesNotExistException|FsFileReadFailureException $e)
            {
                DebugLogger::print(
                    "Failed to read file contents while attempting" .
                    "to write nameserver cache contents."
                );
                $jsonStr = null;
            }

            if ($jsonStr)
            {
                $data = json_decode($jsonStr);

                if (!is_object($data))
                {
                    DebugLogger::print("Failed to parse nameserver cache file.");
                }
            }
        }

        $serializedInfo = (object)[];
        $serializedInfo->domain = $info->domain;
        $serializedInfo->ip = $info->ipAddress;
        $serializedInfo->expire = time() + self::VALID_TIME;

        $data->{$info->domain} = $serializedInfo;

        FileSystem::writeFile(self::CACHE_FILE, json_encode($data));
        return FileSystem::fileExists(self::CACHE_FILE);
    }
}