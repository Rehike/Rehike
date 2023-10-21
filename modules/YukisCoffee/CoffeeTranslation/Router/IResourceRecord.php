<?php
namespace YukisCoffee\CoffeeTranslation\Router;

/**
 * Represents a resource record.
 * 
 * A resource record must be able to be converted into an object, and otherwise
 * does not represent much value.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 */
interface IResourceRecord
{
    public function toObject(): object;
}