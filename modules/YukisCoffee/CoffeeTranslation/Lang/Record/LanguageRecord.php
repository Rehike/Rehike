<?php
namespace YukisCoffee\CoffeeTranslation\Lang\Record;

use YukisCoffee\CoffeeTranslation\Router\IResourceRecord;

/**
 * A data representation of an i18n record.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 */
class LanguageRecord implements IResourceRecord
{
    private RecordEntries $entries;

    public function __construct(RecordEntries $entries)
    {
        $this->entries = $entries;
    }

    public function toObject(): object
    {
        return $this->entries;
    }

    /**
     * Try to get a property in the language record.
     */
    public function tryGetProperty(string $path, mixed &$out): bool
    {
        $root = $this->toObject();
        $tokens = ".[]";

        $tok = strtok($path, $tokens);

        $cur = $root;
        $type = "other"; // real type name doesn't matter here

        if (isset($cur->{$tok}))
        {
            $cur = $cur->{$tok};
        }

        do
        {
            if ("object" == $type && isset($cur->{$tok}))
            {
                $cur = $cur->{$tok};
                $type = gettype($cur);
                $tok = strtok($tokens);
                continue;
            }
            else if ("array" == $type && isset($cur[$tok]))
            {
                $cur = $cur[$tok];
                $type = gettype($cur);
                $tok = strtok($tokens);
                continue;
            }
            else if ("object" == $type || "array" == $type)
            {
                // If we get here, then the requested property is
                // unavailable.
                $out = null;
                return false;
            }
            else
            {
                // We hit another type (string, int, whatever).
                // Return that.
                $out = $cur;
                return true;
            }
        }
        while ($tok !== false);

        $out = null;
        return false;
    }

    /**
     * Try to get a string property in the language record.
     */
    public function tryGetStringProperty(string $path, ?string &$out): bool
    {
        if ($this->tryGetProperty($path, $result))
        {
            if (is_string($result))
            {
                $out = $result;
                return true;
            }
        }

        $out = null;
        return false;
    }
}