<?php
namespace Rehike\i18n\Internal\Router;

use Rehike\i18n\Internal\Lang\SourceInfo;

/**
 * Describes information about a given resource.
 * 
 */
class ResourceInfo
{
    public bool $resourceExists = false;
    public ?IResourceRecord $record = null;
}