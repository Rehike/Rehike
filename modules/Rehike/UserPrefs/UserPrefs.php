<?php
namespace Rehike\UserPrefs;

/**
 * Provides utilities for managing YouTube user preferences in Rehike.
 * 
 * This processes the value of the PREF cookie and allows easy parsing of
 * its value.
 * 
 * Most YouTube preferences set through this cookie are flags, which are
 * implemented efficiently through a bitmask. Other implementations are
 * permitted by YouTube's code, but they seem to be unused.
 * 
 * @author Isabella Lulamoon <kawapure@gmail.com>
 * @author The Rehike Maintainers
 */
class UserPrefs
{
    private static UserPrefs $instance;

    /**
     * The name of the cookie YouTube stores user information in.
     */
    protected const COOKIE_NAME = "PREF";

    /**
     * Stores parsed user preferences.
     */
    protected array $prefs = [];

    public function __construct()
    {
        $cookie = self::getPrefCookie();
        $this->parse($cookie);
    }

    public static function __initStatic(): void
    {
        static::$instance = new static();
    }

    public static function getInstance(): UserPrefs
    {
        return static::$instance;
    }

    /**
     * Get the value of a flag.
     */
    public function getFlag(int $flag): bool
    {
        [$index, $bitmask] = self::calcFlagVars($flag);

        return $this->getFlagValue("f$index", $bitmask);
    }

    /**
     * Set the value of a flag.
     */
    public function setFlag(int $flag, bool $value): void
    {
        [$index, $bitmask] = self::calcFlagVars($flag);

        $flagBits = $this->getNumber($flag) ?? 0;
        $flagBits = $value
            ? $flagBits | $bitmask
            : $flagBits & ~$bitmask;
            
        if (0 == $flagBits)
        {
            $this->deleteValue("f" . $index);
        }
        else
        {
            $this->setValue("f" . $index, dechex($flagBits));
        }
    }

    /**
     * Dump the changes made on the instance.
     */
    public function dump(): string
    {
        $out = [];

        foreach ($this->prefs as $name => $value)
        {
            // rawurlencode to emulate JS escape
            $out[] = "$name=" . rawurlencode($value);
        }

        return implode("&", $out);
    }

    /**
     * Parse a serialised preferences string.
     */
    protected function parse(string $cookie): void
    {
        $fields = explode("&", $cookie);

        foreach ($fields as $field)
        {
            $pair = explode("=", $field);

            // Ignore malformed input.
            if (isset($pair[1]))
            {
                $key = $pair[0];
                $value = $pair[1];

                // rawurldecode to emulate JS unescape
                $this->prefs[$key] = rawurldecode($value);
            }
        }
    }

    /**
     * Get the number of a numeric field, i.e. flags.
     */
    protected function getNumber(string $index): ?int
    {
        if (isset($this->prefs[$index]))
        {
            return hexdec( (string)$this->prefs[$index] );
        }
        else
        {
            return null;
        }
    }

    /**
     * Get the boolean value of a flag.
     * 
     * Internally, the flag index is handled as hexadecimal and AND'd with
     * the bitmask.
     * 
     * If the result of the AND is 0, this will return false. Otherwise
     * it always returns true.
     */
    protected function getFlagValue(string $index, int $bitmask): bool
    {
        $result = ($this->getNumber($index) ?? 0) & $bitmask;

        return (0 != $result) ? true : false;
    }
    
    protected function deleteValue(int $key): void
    {
        unset($this->prefs[$key]);
    }
    
    protected function setValue(int $key, $value): void
    {
        $this->prefs[$key] = (string)$value;
    }

    /**
     * Calculate the variables required to read or write a flag.
     */
    protected static function calcFlagVars(int $flag): array
    {
        return [
            floor($flag / 31) + 1,
            1 << $flag % 31
        ];
    }

    /**
     * Get the PREF cookie value.
     */
    protected static function getPrefCookie(): string
    {
        return $_COOKIE[self::COOKIE_NAME] ?? "";
    }
}