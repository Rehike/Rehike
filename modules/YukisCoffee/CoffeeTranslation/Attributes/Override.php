<?php
namespace YukisCoffee\CoffeeTranslation\Attributes;

use Attribute;

/**
 * Denotes a property or method as an override from a parent class.
 */
#[Attribute(Attribute::TARGET_METHOD|Attribute::TARGET_PROPERTY)]
class Override {}