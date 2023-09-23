<?php
namespace Rehike\ConfigManager;

use YukisCoffee\PropertyAtPath;

/**
 * An abstract ConfigManager
 */
class ConfigManager
{
    /** @var array (because PHP limitations) */
    public static array $defaultConfig = [];

    /** @var array (because PHP limitation) */
    public static array $types = [];

    protected static string $file = 'config.json';

    protected static object $config;

    /**
     * Dump a config file.
     */
    protected static function dump(string $file, string $cfg): void
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
     */
    public static function dumpConfig(): void
    {
        static::dump(
            static::$file, 
            json_encode(static::$config, JSON_PRETTY_PRINT)
        );
    }

    /**
     * Dump the default config and override or create
     * the active file.
     */
    public static function dumpDefaultConfig(): void
    {
        static::dump(
            static::$file, 
            json_encode((object) static::$defaultConfig, JSON_PRETTY_PRINT)
        );
    }

    /**
     * Set the config location
     */
    public static function setConfigFile(string $filePath): void
    {
        if (!is_string($filePath))
            throw new FilePathException("Type of file path must be string.");

        self::$file = $filePath;
    }

    /**
     * Get the active config
     * 
     * @return object
     */
    public static function getConfig(): object
    {
        return is_object(static::$config) ? static::$config : (object) static::$defaultConfig;
    }

    /**
     * Get the types of the configs
     * 
     * @return object
     */
    public static function getTypes(): object
    {
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
    public static function getConfigProp(string $path): mixed
    {
        $cfg = static::getConfig();

        try
        {
            $value = PropertyAtPath::get($cfg, $path);
        }
        catch (\YukisCoffee\PropertyAtPathException $e)
        {
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
     */
    public static function setConfigProp(string $path, mixed $value): void
    {
        try
        {
            PropertyAtPath::set(static::$config, $path, $value);
        }
        catch (\YukisCoffee\PropertyAtPathException $e)
        {
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
     */
    public static function getConfigType(string $path): ?string
    {
        $types = static::getTypes();

        try
        {
            $value = PropertyAtPath::get($types, $path);
        }
        catch(\YukisCoffee\PropertyAtPathException $e)
        {
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
    public static function loadConfig(): object
    {
        $file = self::$file;
        $object = \json_decode(file_get_contents($file));

        // Throw an exception if response type is not object
        // This is because PHP does not throw an exception if
        // json_decode fails.
        if (!is_object($object))
            throw new LoadConfigException("Failed to parse config file \"{$file}\"");

        // Else, set the active config used to this.
        static::$config = $object;

        return static::$config;
    }
}