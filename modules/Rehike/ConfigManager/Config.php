<?php
namespace Rehike\ConfigManager;

use Rehike\ConfigManager\Properties\AbstractConfigProperty;
use Rehike\FileSystem;
use YukisCoffee\PropertyAtPath;

/**
 * Manages Rehike user configuration.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */
class Config
{
    /**
     * Maps configuration key names to their default values.
     */
    protected static array $defaultConfig = [];

    /**
     * Maps configuration key names to their types.
     */
    protected static array $types = [];

    /**
     * The name of the configuration file.
     */
    protected static string $file = "config.json";
    
    /**
     * The currently used configuration.
     */
    protected static object $config;

    /**
     * Dump a config file.
     */
    protected static function dump(string $file, string $cfg): void
    {
        try
        {
            FileSystem::writeFile($file, $cfg, false);
        }
        catch (\Throwable $e)
        {
            throw DumpFileException::from($e);
        }
    }

    /**
     * Registers configuration definitions from a list of definitions.
     * 
     * @see Rehike\ConfigDefinitions
     */
    public static function registerConfigDefinitions(array $defs): void
    {
        $defs = self::parseDefs($defs);
        self::$defaultConfig = $defs["defs"];
        self::$types = $defs["types"];
    }

    /**
     * Parses an array of definitions.
     */
    protected static function parseDefs(array $defs): array
    {
        $out = [
            "defs" => [],
            "types" => []
        ];

        foreach ($defs as $name => $def)
        {
            if (is_array($def))
            {
                $result = self::parseDefs($def);
                $out["defs"][$name] = $result["defs"];
                $out["types"][$name] = $result["types"];
            }
            else if ($def instanceof AbstractConfigProperty)
            {
                $out["defs"][$name] = $def->getDefaultValue();
                $out["types"][$name] = $def->getType();
            }
            else
            {
                throw new \Exception("Invalid property passed to parseDefs.");
            }
        }

        return $out;
    }

    /**
     * Dump the active config and override the active file.
     */
    public static function dumpConfig(): void
    {
        self::dump(
            self::$file, 
            json_encode(static::$config, JSON_PRETTY_PRINT)
        );
    }

    /**
     * Dump the default config and override or create the active file.
     */
    protected static function dumpDefaultConfig(): void
    {
        self::dump(
            self::$file, 
            json_encode((object) static::$defaultConfig, JSON_PRETTY_PRINT)
        );
    }

    /**
     * Set the config location.
     */
    public static function setConfigFile(string $filePath): void
    {
        if (!is_string($filePath))
            throw new FilePathException("Type of file path must be string.");

        self::$file = $filePath;
    }

    /**
     * Get the active config.
     * 
     * @return object
     */
    public static function getConfig(): object
    {
        return is_object(static::$config) ? static::$config : (object) static::$defaultConfig;
    }

    /**
     * Get the types of the configs.
     * 
     * @return object
     */
    public static function getTypes(): object
    {
        return json_decode(json_encode(static::$types));
    }

    /**
     * Get a configuration option.
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
     * Set a configuration option.
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
     * Get a configuration option's type.
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
     * If configuration doesn't exist upon attempt to load it, save it.
     */
    public static function loadConfig(): object
    {
        if (!FileSystem::fileExists(self::$file))
        {
            static::dumpDefaultConfig();
        }

        self::_loadConfig();

        $redump = false;
        
        // Make sure new defaults get added to the config file.
        // json_encode wrapped in json_decode as an quick 'n easy
        // way to cast all associative arrays to objects
        foreach (json_decode(json_encode(self::$defaultConfig)) as $key => $value)
        {
            if (!isset(self::$config->{$key}))
            {
                self::$config->{$key} = $value;
                
                $redump = true;
            }
            else
            foreach (self::$defaultConfig[$key] as $option => $val)
            {
                if (!isset(self::$config->{$key}->{$option}))
                {
                    self::$config->{$key}->{$option} = $val;

                    $redump = true;
                }
            }
        }

        if ($redump) self::dumpConfig();

        return self::$config;
    }

    /**
     * Set the config to be an object parsed from the provided file name.
     * 
     * @return object
     */
    protected static function _loadConfig(): object
    {
        $file = self::$file;
        $object = \json_decode(FileSystem::getFileContents($file));

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