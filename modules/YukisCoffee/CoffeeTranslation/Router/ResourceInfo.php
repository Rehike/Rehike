<?php
namespace YukisCoffee\CoffeeTranslation\Router;

use YukisCoffee\CoffeeTranslation\Lang\SourceInfo;

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