<?php
namespace Rehike\ConfigManager\Properties;

/**
 * Defines a base for a configuration property.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */
abstract class AbstractAssociativeProp extends AbstractConfigProperty
{
    abstract public function getDefaultValue(): mixed;
    abstract public function getType(): string;
}