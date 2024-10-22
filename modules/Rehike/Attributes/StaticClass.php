<?php
namespace Rehike\Attributes;

use Attribute;

/**
 * Denotes a class as not supporting object instances.
 */
#[Attribute(Attribute::TARGET_CLASS)]
class StaticClass {}