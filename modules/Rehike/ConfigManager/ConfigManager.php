<?php
namespace Rehike\ConfigManager;

use YukisCoffee\PropertyAtPath;

/**
 * An abstract ConfigManager
 */
class ConfigManager
{
    /** @var array (because PHP limitations) */
    public static $defaultConfig = [];

    /** @var array (because PHP limitation) */
    public static $types = [];

    /** @var string */
    protected static $file = 'config.json';

    /** @var object|null */
    protected static $config;

    /**
     * Dump a config file.
     * 
     * @abstract
     * @return void
     */
    protected static function dump($file, $cfg)
    {
        try
        {
            $stream = fopen($file, "w");
            fwrite($stream, $cfg);
            fclose($stream);
        }
        catch (\Throwable $e)
        {
            throw DumpFileException::from($e);
        }
    }

    /**
     * Dump the active config and override
     * the active file.
     * 
     * @return void
     */
    public static function dumpConfig()
    {
        return static::dump(
            static::$file, 
            json_encode(static::$config, JSON_PRETTY_PRINT)
        );
    }

    /**
     * Dump the default config and override or create
     * the active file.
     * 
     * @return void
     */
    public static function dumpDefaultConfig()
    {
        return static::dump(
            static::$file, 
            json_encode((object) static::$defaultConfig, JSON_PRETTY_PRINT)
        );
    }

    /**
     * Set the config location
     * 
     * @param string $filePath
     * @return void
     */
    public static function setConfigFile($filePath)
    {
        if (!is_string($filePath)) throw new FilePathException("Type of file path must be string.");

        self::$file = $filePath;
    }

    /**
     * Get the active config
     * 
     * @return object
     */
    public static function getConfig()
    {
        return is_object(static::$config) ? static::$config : (object) static::$defaultConfig;
    }

    /**
     * Get the types of the configs
     * 
     * @return object
     */
    public static function getTypes() {
        return json_decode(json_encode(static::$types));
    }

    /**
     * Get a configuration option
     * 
     * This handles checking if an option is set in the
     * config. If it isn't, this returns null.
     * 
     * @param string $path  Period-delimited path of the config
     * @return mixed
     */
    public static function getConfigProp($path)
    {
        $cfg = static::getConfig();

        try {
            $value = PropertyAtPath::get($cfg, $path);
        } catch (\YukisCoffee\PropertyAtPathException $e) {
            return null;
        }

        return $value;
    }

    /**
     * Set a configuration option
     * 
     * This handles checking if an option is set in the
     * config. If it isn't, this returns null.
     * 
     * @param string $path   Period-delimited path of the config
     * @param string $value  New value
     * @return void
     */
    public static function setConfigProp($path, $value) {
        try {
            PropertyAtPath::set(static::$config, $path, $value);
        } catch (\YukisCoffee\PropertyAtPathException $e) {
            return;
        }
    }

    /**
     * Get a configuration option's type
     * 
     * This handles checking if an option is set in the
     * config. If it isn't, this returns null.
     * 
     * @param string $path  Period-delimited path of the config
     * @return string
     */
    public static function getConfigType($path) {
        $types = static::getTypes();

        try {
            $value = PropertyAtPath::get($types, $path);
        } catch(\YukisCoffee\PropertyAtPathException $e) {
            return null;
        }

        return $value;
    }

    /**
     * Set the config to be an object parsed
     * from the provided file name.
     * 
     * @return object
     */
    public static function loadConfig()
    {
        $file = self::$file;
        $object = \json_decode(file_get_contents($file));

        // Throw an exception if response type is not object
        // This is because PHP does not throw an exception if
        // json_decode fails.
        if (!is_object($object)) throw new LoadConfigException("Failed to parse config file \"{$file}\"");

        // Else, set the active config used to this.
        static::$config = $object;

        return static::$config;
    }
}