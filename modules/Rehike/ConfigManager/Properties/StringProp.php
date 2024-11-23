<?php
namespace Rehike\ConfigManager\Properties;

/**
 * String property.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */
class StringProp extends AbstractAssociativeProp
{
    protected string $defaultValue = "";

    public function __construct(string $defaultValue)
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

    public function getType(): string
    {
        return "string";
    }
}