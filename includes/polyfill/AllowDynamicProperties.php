<?php
// Just a fix for IDEs. PHP itself doesn't care about illegal class
// names used as attributes and will continue executing anyways.
// Still, this is safer.

if (!class_exists("AllowDynamicProperties")) // PHP 8.1 and prior
{
    #[Attribute()]
    final class AllowDynamicProperties {}
}