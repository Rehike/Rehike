<?php
namespace Rehike\ConfigManager\Properties;

/**
 * Defines a base for a configuration property.
 * 
 * @author The Rehike Maintainers
 */
abstract class AbstractConfigProperty
{
    abstract public function getType(): string;
}