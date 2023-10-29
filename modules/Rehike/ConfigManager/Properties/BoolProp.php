<?php
namespace Rehike\ConfigManager\Properties;

/**
 * Boolean property.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */
class BoolProp extends AbstractConfigProperty
{
    private bool $defaultValue = false;

    public function __construct(bool $defaultValue)
    {
        $this->defaultValue = $defaultValue;
    }

    public function getType(): string
    {
        return "bool";
    }
}