<?php
namespace Rehike\i18n\Internal\Router;

use Rehike\i18n\Internal\Lang\SourceInfo;

/**
 * Describes information about a given resource.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 */
class ResourceInfo
{
    public bool $resourceExists = false;
    public ?IResourceRecord $record = null;
}