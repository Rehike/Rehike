<?php
namespace Rehike\TemplateUtilsDelegate;

use Rehike\i18n\i18n;
use Throwable;

/**
 * Internationalization engine delegate for use in Twig land.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */
class RehikeUtilsI18nDelegate
{
    public function get(string $namespace, string $name): string
    {
        try
        {
            return i18n::getRawString($namespace, $name);
        }
        catch (Throwable $e)
        {
            return "< unknown string $namespace:$name >";
        }
    }

    public function format(string $namespace, string $name, mixed ...$args): string
    {
        try
        {
            return i18n::getFormattedString($namespace, $name, ...$args);
        }
        catch (Throwable $e)
        {
            return "< unknown string $namespace:$name >";
        }
    }
}