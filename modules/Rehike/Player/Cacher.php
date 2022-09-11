<?php
namespace Rehike\Player;

use Rehike\Player\Exception\CacherException;

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
     * 
     * @return string
     */
    public static function get()
    {
        $root = PlayerCore::$cacheDestDir;
        $name = PlayerCore::$cacheDestName;
        $path = "$root/$name.json";

        if (file_exists($path))
        {
            $result = json_decode(file_get_contents($path));

            if (null != $result && time() < $result->expire)
                return $result->content; // file contents
            else
                throw new CacherException(
                    "Failed to get cached contents from file \"$path\" " .
                    "(or cache has expired)"
                );
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
     * 
     * @param object $object
     * @return void
     */
    public static function write($object)
    {
        $root = PlayerCore::$cacheDestDir;
        $name = PlayerCore::$cacheDestName;
        $path = "$root/$name.json";

        $expire = PlayerCore::$cacheMaxTime;

        try
        {
            return self::writeJson($path, self::expireWrap($expire, $object));
        }
        catch (CacherException $e) { throw $e; }
    }

    /**
     * Wrap an object in an expirable container.
     * 
     * @param int $duration
     * @param object $object
     * @return object
     */
    public static function expireWrap($duration, $object)
    {
        $expireTime = time() + $duration;

        return (object)[
            "expire" => $expireTime,
            "content" => $object
        ];
    }

    /**
     * Serialise an object and write it to the path.
     * 
     * @param string $path
     * @param object|array $object
     * 
     * @return void
     */
    public static function writeJson($path, $object)
    {
        try
        {
            if (is_array($object))
                $object = (object)$object;

            $object = json_encode($object, JSON_PRETTY_PRINT);

            return self::writeRaw($path, $object);
        }
        catch (CacherException $e) { throw $e; }
    }

    /**
     * Write a raw string to a file path.
     * 
     * @param string $path
     * @param string $contents
     * @param bool $recursive
     * @param bool $append
     * 
     * @return void
     */
    public static function writeRaw($path, $contents, $recursive = true, $append = false)
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
     * 
     * @param string $path
     * @return string
     */
    public static function getFolder($path)
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
     * 
     * @return void
     */
    public static function mkdir($dirname, $mode = 0777, $recursive = false)
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