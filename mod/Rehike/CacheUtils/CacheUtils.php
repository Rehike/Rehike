<?php
/**
 * Cache Utilities for Rehike
 * 
 * @author Nightlinbit (Daylin Cooper)
 * @version 1.0
 * @license CC0
 */

namespace Rehike\CacheUtils;

class CacheUtils {
    const CACHE_DIR = 'cache';
    const CACHE_MASTER = '.cache_master.json';
    public static object $master;

    /**
     * Initialise the cache utilities.
     * 
     * @return void
     */
    public static function init(): void {
        self::getMaster();
    }

    public static function save(string $filename, $contents, int $expire = 0) {

    }

    /**
     * Get the master cache information.
     * 
     * @return void
     */
    protected static function getMaster(): void {
        if (file_exists( self::CACHE_DIR . '/' . self::CACHE_MASTER )) {
            self::$master = self::getJson(self::CACHE_DIR . '/' . self::CACHE_MASTER);
        } else {
            self::$master = (object) [];
        }
    }

    /**
     * Return file contents if exists.
     * 
     * @param string $filename  Name of the file to get.
     * @return mixed
     */
    protected static function getFile(string $filename) {
        if (file_exists($filename)) {
            return file_get_contents(self::CACHE_DIR . $filename);
        } else {
            throw new Exception('File ' . $filename . ' does not exist');
        }
    }

    /**
     * Return file contents and parse as JSON if exists.
     * 
     * @param string $filename  Name of the file to get.
     * @return object
     */
    protected static function getJson(string $filename): object {
        return json_decode( self::getFile($filename) );
    }

    /**
     * Save a file to a path.
     * 
     * @param string $filename  Name of the file to be saved
     * @param mixed $contents  Contents of the file.
     * @return void
     */
    protected static function saveFile(string $filename, $contents): void {
        self::forgePath($filename);

        $stream = fopen(self::CACHE_DIR . $filename, 'w');
        fwrite($stream, $contents);
        fclose($stream);
    }

    /**
     * Recursively generate a path so that files can be saved
     * without issue.
     * 
     * @param string $path
     * @return void
     */
    protected static function forgePath(string $path): void {
        // dirname function does the work of removing filename.
        $expectedPath = self::CACHE_DIR . dirname($path);
        if (!is_dir($expectedPath)) mkdir($expectedPath, 0755, true);
    }
}
CacheUtils::init(); // autoinit