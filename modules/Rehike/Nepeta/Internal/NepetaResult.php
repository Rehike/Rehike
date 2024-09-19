<?php
namespace Rehike\Nepeta\Internal;

use Stringable;

/**
 * A standard result class.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */
class NepetaResult implements Stringable
{
    public const SUCCESS = "success";
    public const FAILED = "failed";

    public string $value;

    public function __construct(string $defaultValue = self::SUCCESS)
    {
        $this->value = $defaultValue;
    }

    public function set(string $newValue): void
    {
        $this->value = $newValue;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}