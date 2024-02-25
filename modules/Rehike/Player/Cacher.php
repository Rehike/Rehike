<?php
namespace Rehike\Player;

require_once "Constants.php";

use Rehike\Player\Exception\CacherException;
use Rehike\ConfigManager\Config;

/**
 * Manage caching and player information storage.
 * 
 * In order to make the player retrival process more
 * efficient, necessary information is stored rather than
 * queried from the server each time it is requested.
 * 
 * However, server-side variables change all the time, so
 * this storage cannot be permanent. Cache should persist
 * for a maximum of 24 hours.
 * 
 * Much of this library is copied from Rehike's FileSystem
 * module in order to retain portability. Unfortunately,
 * that does mean some code redundancy.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */
class Cacher
{
    /**
     * Get the current cached contents.
     */
    public static function get(): object
    {
        $root = PlayerCore::$cacheDestDir;
        $name = PlayerCore::$cacheDestName;
        $path = "$root/$name.json";
        $playerChoice = Config::getConfigProp("appearance.playerChoice");

        if (!DEBUG && file_exists($path))
        {
            $result = json_decode(file_get_contents($path));

            if (
                null != $result &&
                time() < $result->expire &&
                (IS_REHIKE ? ($playerChoice == $result->conditionPlayerChoice) : true)
            )
            {
                return $result->content; // file contents
            }
            else
            {
                throw new CacherException(
                    "Failed to get cached contents from file \"$path\" " .
                    "(or cache has expired)"
                );
            }
        }
        else
        {
            throw new CacherException(
                "Cache file \"$path\" does not exist"
            );
        }
    }

    /**
     * Create or update the cache file with the current
     * information.
     */
    public static function write(object $object): void
    {
        $root = PlayerCore::$cacheDestDir;
        $name = PlayerCore::$cacheDestName;
        $path = "$root/$name.json";

        $expire = PlayerCore::$cacheMaxTime;

        try
        {
            self::writeJson($path, self::expireWrap($expire, $object));
        }
        catch (CacherException $e) { throw $e; }
    }

    /**
     * Wrap an object in an expirable container.
     */
    public static function expireWrap(int $duration, object $object): object
    {
        $expireTime = time() + $duration;

        if (IS_REHIKE)
            $playerChoice = Config::getConfigProp("appearance.playerChoice");

        $result = (object)[
            "expire" => $expireTime,
            "content" => $object
        ];

        if (IS_REHIKE)
        {
            $result->conditionPlayerChoice = $playerChoice;
        }

        return $result;
    }

    /**
     * Serialise an object and write it to the path.
     */
    public static function writeJson(string $path, object|array $object): void
    {
        try
        {
            if (is_array($object))
                $object = (object)$object;

            $object = json_encode($object, JSON_PRETTY_PRINT);

            self::writeRaw($path, $object);
        }
        catch (CacherException $e)
        { 
            throw $e;
        }
    }

    /**
     * Write a raw string to a file path.
     */
    public static function writeRaw(
            string $path, 
            string $contents, 
            bool $recursive = true, 
            bool $append = false
    ): void
    {
        // Make sure all folders leading to the path exist if the
        // recursive option is enabled.
        if ($recursive)
        {
            $folder = self::getFolder($path);

            if (!is_dir($folder))
            {
                self::mkdir($folder, 0777, true);
            }
        }

        // Determine fopen mode from append value
        $fopenMode = $append ? "a" : "w";

        // Use fopen to write the file
        $fh = @\fopen($path, $fopenMode);
        
        $status = @\fwrite($fh, $contents);

        // Validate
        if (false == $fh || false == $status)
        {
            throw new CacherException("Failed to write file \"$path\"");
        }

        \fclose($fh);
    }

    /**
     * Get the containing folder of a file path.
     */
    public static function getFolder(string $path): string
    {
        // Convert the path to account for Windows separation.
        $path = str_replace("\\", "/", $path);

        // Split the path by the separator
        $root = explode("/", $path);

        // Remove the last item (the filename)
        array_splice($root, count($root) - 1, 1);

        // Rejoin
        $root = implode("/", $root);

        return $root;
    }

    /**
     * An error-checked mkdir wrapper.
     */
    public static function mkdir(
            string $dirname, 
            int $mode = 0777, 
            bool $recursive = false
    ): void
    {
        $status = @\mkdir($dirname, $mode, $recursive);

        if (false == $status)
        {
            throw new CacherException(
                "Failed to create directory \"$dirname\""
            );
        }
    }
}