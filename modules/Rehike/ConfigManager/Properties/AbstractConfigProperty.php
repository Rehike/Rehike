<?php
namespace Rehike\ConfigManager\Properties;

/**
 * Defines a base for a configuration property.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */
abstract class AbstractConfigProperty
{
    private mixed $defaultValue = null;

    public function __construct(mixed $defaultValue)
    {
        $this->defaultValue = $defaultValue;
    }

    /**
     * Get the default value of the property.
     */
    public function getDefaultValue(): mixed
    {
        return $this->defaultValue;
    }

    abstract public function getType(): string;
}