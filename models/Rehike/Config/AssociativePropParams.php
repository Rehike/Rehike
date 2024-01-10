<?php
namespace Rehike\Model\Rehike\Config;

use Rehike\ConfigManager\Properties\AbstractAssociativeProp;

/**
 * Declares parameters used for baking associative property data models.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */
class AssociativePropParams
{
    public string $path;
    public string $name;
    public AbstractAssociativeProp $source;
}