<?php
namespace Rehike\ConfigManager\Properties;

/**
 * String property.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */
class StringProp extends AbstractConfigProperty
{
    private string $defaultValue = "";

    public function __construct(string $defaultValue)
    {
        $this->defaultValue = $defaultValue;
    }

    public function getType(): string
    {
        return "string";
    }
}