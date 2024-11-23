<?php
namespace Rehike\ConfigManager\Properties;

/**
 * Boolean property.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */
class BoolProp extends AbstractAssociativeProp
{
    protected bool $defaultValue = false;

    public function __construct(bool $defaultValue)
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
        return "bool";
    }
}