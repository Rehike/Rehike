<?php
namespace Rehike;

use function class_alias;

/**
 * Implements a safe version of class_alias that retains casing.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */
function safeClassAlias(string $class, string $alias, bool $autoload = true): void
{
    \RehikeBase\Autoloader::registerClassAlias($alias);
    class_alias($class, $alias, $autoload);
}