<?php
namespace Rehike\ConfigManager;

use Rehike\ConfigManager\Properties\{
    AbstractConfigProperty,
    AbstractAssociativeProp,
    DependentProp,
    PropGroup
};

use Rehike\Exception\FileSystem\FsWriteFileException;
use Rehike\FileSystem;

use YukisCoffee\PropertyAtPath;
use YukisCoffee\PropertyAtPathException;

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
     * Stores the property schemas for all known properties.
     */
    protected static array $properties = [];

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
        FileSystem::writeFile($file, $cfg, false);
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
        self::$properties = $defs["sources"];
    }

    /**
     * Parses an array of definitions.
     */
    protected static function parseDefs(array $defs): array
    {
        $out = [
            "defs" => [],
            "types" => [],
            "sources" => [],
        ];

        foreach ($defs as $name => $def)
        {
            if (is_array($def))
            {
                $result = self::parseDefs($def);
                $out["defs"][$name] = $result["defs"];
                $out["types"][$name] = $result["types"];
                $out["sources"][$name] = $result["sources"];
            }
            else if ($def instanceof PropGroup)
            {
                $result = self::parseDefs($def->getProperties());
                
                foreach ($result["defs"] as $resultName => $resultDef)
                {
                    $out["defs"][$resultName] = $resultDef;
                    $out["types"][$resultName] = $result["types"][$resultName];
                    $out["sources"][$resultName] = $result["sources"][$resultName];
                }
            }
            else if ($def instanceof DependentProp)
            {
                $prop = $def->getProp();
                $out["defs"][$name] = $prop->getDefaultValue();
                $out["types"][$name] = $prop->getType();
                $out["sources"][$name] = $prop;
                continue;
            }
            else if ($def instanceof AbstractAssociativeProp)
            {
                $out["defs"][$name] = $def->getDefaultValue();
                $out["types"][$name] = $def->getType();
                $out["sources"][$name] = $def;
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
    
    public static function isInitialized(): bool
    {
        return isset(static::$config);
    }

    /**
     * Get the active config.
     * 
     * @return ?object
     */
    public static function getConfig(): ?object
    {
        if (self::isInitialized())
        {
            return is_object(static::$config) ? static::$config : (object) static::$defaultConfig;
        }
        
        return null;
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
            
            try
            {
                $prop = PropertyAtPath::get(self::$properties, $path);
                
                if (is_object($prop) && $prop instanceof AbstractAssociativeProp)
                {
                    $prop->onUpdate();
                }
            }
            catch (\YukisCoffee\PropertyAtPathException $e) {}
        }
        catch (\YukisCoffee\PropertyAtPathException $e)
        {
            return;
        }
    }
    
    /**
     * Removes the value of a configuration property; unsets it.
     */
    public static function removeConfigProp(string $path): void
    {
        $parts = explode(".", $path);
        $target = array_pop($parts);
        $parent = join(".", $parts);
        
        \Rehike\Logging\DebugLogger::print("%s %s", $parent, $target);
        
        try
        {
            $parentObj = PropertyAtPath::get(self::$config, $parent);
            
            unset($parentObj->{$target});
        }
        catch (PropertyAtPathException $e)
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
            try
            {
                static::dumpDefaultConfig();
            }
            catch (FsWriteFileException $e)
            {
                // In this case, we can't write the config (probably because FS
                // permissions are too restrictive), so we'll inform the caller
                // of this problem to display a GUI error message.
                throw new LoadConfigException(
                    LoadConfigException::REASON_COULD_NOT_OPEN_FILE_HANDLE,
                    "Could not open file handle",
                    $e
                );
            }
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
        {
            throw new LoadConfigException(
                LoadConfigException::REASON_PARSE_FAILURE,
                "Failed to parse config file \"{$file}\""
            );
        }

        // Else, set the active config used to this.
        static::$config = $object;

        return static::$config;
    }
}