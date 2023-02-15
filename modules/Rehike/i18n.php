<?php
namespace Rehike;

use \Rehike\Exception\i18n\I18nUnsupportedFileException;

/**
 * Implements the base Rehike internationalisation system.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */
class i18n
{
    /**
     * Declare the default language to be loaded in the case there is no
     * fallback.
     *  
     * @var string 
     */
    protected static $defaultLanguage = "en";

    /**
     * Declare the global language to be used as a default for all instances.
     * 
     * @var string
     */
    protected static $globalLanguage = "en";

    /**
     * Store all namespaces (instances) so that they may be accessed globally.
     * 
     * @var i18n[]
     */
    protected static $namespaces = [];

    /** 
     * Declare an array that will contain all language strings.
     * 
     * @var string[] 
     */
    protected $strings = [];

    /** 
     * The current language of the instance.
     * 
     * @var string 
     */
    protected $language = "";

    public function __construct()
    {
        $this->setLanguage(self::$globalLanguage);
    }

    /**
     * Create an instance and store it so that it may be accessed
     * globally. This is convenient for most of Rehike's structure.
     * 
     * As this returns a reference, you must use:
     * 
     *     $variable = &newNamespace($name)
     * 
     * or you will encounter much confusion.
     * 
     * @see __construct
     */
    public static function &newNamespace($name)
    {
        $i = new static();

        self::$namespaces[$name] = &$i;

        return $i;
    }

    /**
     * Return a reference to the requested namespace.
     * 
     * As this returns a reference, you must use:
     * 
     *     $variable = &getNamespace($name)
     * 
     * or you will encounter much confusion.
     * 
     * @param string $name
     * @return i18n
     */
    public static function &getNamespace($name)
    {
        if (!isset(self::$namespaces[$name]))
            trigger_error("Namespace $name does not exist", E_USER_WARNING);

        return self::$namespaces[$name];
    }

    /**
     * Get the internal strings stored.
     * 
     * @return string[]
     */
    public function getStrings() {
        return $this -> strings;
    }

    /**
     * Determine if a namespace exists.
     * 
     * @return bool
     */
    public static function namespaceExists($name)
    {
        return isset(self::$namespaces[$name]);
    }

    /**
     * Register language definitions from a file.
     * 
     * This supports loading from a JSON list or PHP list.
     * 
     * The PHP type should be used sparingly. It allows for advanced
     * use, such as registering functions, but it can cause more 
     * problems than it solves. It should always return an associative
     * array.
     * 
     * @param string $languageName
     * @param string $filename
     * @return i18n
     */
    public function registerFromFile($languageName, $filename)
    {
        $supportedFileTypes = [
            "json",
            "php"
        ];

        $fileType = FileSystem::getExtension($filename);

        if (in_array($fileType, $supportedFileTypes))
        {
            if ("json" == $fileType)
            {
                $rawFile = FileSystem::getFileContents($filename);

                $json = json_decode($rawFile, true);

                self::registerFromArray($languageName, $json);
            }
            else if ("php" == $fileType)
            {
                // PHP format should be well formed and return an array.
                // This is presumed. Don't fuck up.
                $result = include $filename;

                if (is_array($result))
                {
                    self::registerFromArray($languageName, $result);
                }
                else
                {
                    throw new I18nUnsupportedFileException(
                        "Unsupported language file $filename. PHP type files must return an associative array."
                    );
                }
            }
        }
        else
        {
            throw new I18nUnsupportedFileException(
                "Unsupported file type $fileType in file $filename."
            );
        }

        return $this;
    }

    /**
     * Register language definitions, per language, from a folder.
     * 
     * As this relies on the above function, it inherits the same
     * general functionality from it.
     * 
     * @param string $folderName
     * @return i18n
     */
    public function registerFromFolder($folderName)
    {
        foreach (glob("$folderName/*") as $file)
        {
            // Isolate the file name itself.
            $languageName = explode("/", $file);
            $languageName = $languageName[count($languageName) - 1];
            $languageName = explode(".", $languageName)[0];

            // Register from a file this path.
            self::registerFromFile($languageName, $file);
        }
        
        return $this;
    }

    /**
     * Register a language array
     * 
     * @param string $name
     * @param string[] $array of language strings
     * @return i18n
     */
    public function registerFromArray($name, $array)
    {
        $this->strings += [$name => &$array];

        return $this;
    }

    /**
     * Legacy alias for registerFromArray.
     * 
     * @deprecated
     * @see registerFromArray
     */
    public function register($name, $array)
    {
        trigger_error("i18n::register is deprecated. Use i18n::registerFromArray instead.", E_USER_DEPRECATED);
        return self::registerFromArray($name, $array);
    }

    /**
     * Get a string definition by its ID.
     * 
     * @param string $id of the string
     * @return string
     */
    protected function getStringId($id)
    {
        // Try the instance's registered definitions, then fallback to the
        // global default, and finally return null if nothing worked out.
        return @$this->strings[$this->language][$id]
            ?? @$this->strings[self::$defaultLanguage][$id]
            ?? null
        ;
    }

    /**
     * Get a string's contents.
     * 
     * @param string $id of the string
     * @param mixed[] $params
     */
    public function get($id, ...$params)
    {
        $string = self::getStringId($id);

        if (is_string($string))
        {
            return sprintf($string, ...$params);
        }
        else
        {
            return $string;
        }
    }

    public function __get($id)
    {
        // Convert a magic instance getter to an ID (without further arguments)
        return $this->get($id);
    }

    public function __call($id, $args)
    {
        // Convert a magic instance call to an ID (with arguments!)
        return $this->get($id, ...$args);
    }

    /**
     * Set the active language of an instance.
     * 
     * @param string $value
     * @return void
     */
    public function setLanguage($value)
    {
        $this->language = $value;
    }

    /**
     * Get the active language of an instance.
     * 
     * @return string
     */
    public function getLanguage() {
        return $this->language;
    }

    /**
     * Set the global language used by the system.
     * 
     * @param string $value
     * @return void
     */
    public static function setGlobalLanguage($value)
    {
        self::$globalLanguage = $value;
    }

    /**
     * Set the default (fallback) language of the system.
     * 
     * @param string $value
     * @return void
     */
    public static function setDefaultLanguage($value)
    {
        self::$defaultLanguage = $value;
    }
}