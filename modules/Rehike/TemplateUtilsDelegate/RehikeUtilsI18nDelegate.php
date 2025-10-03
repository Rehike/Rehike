<?php
namespace Rehike\TemplateUtilsDelegate;

use Rehike\i18n\i18n;
use Throwable;

use Twig\Markup;

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

    public function formatHtml(string $namespace, string $name, mixed ...$args): Markup
    {
        try
        {
            $effectiveArgs = [];
            $htmlSubstitutionTable = [];

            foreach ($args as $arg)
            {
                $data = random_bytes(16);

                // Set version to 0100 (UUID v4)
                $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
                // Set bits 6-7 to 10 (variant)
                $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
            
                $guid = vsprintf(
                    '%s%s-%s-%s-%s-%s%s%s', 
                    str_split(bin2hex($data), 4)
                );

                if ($arg instanceof SafeHtml)
                {
                    $formattedGuid = "!!HTML--{$guid}!!";

                    $htmlSubstitutionTable[] = [
                        "html" => $arg->html,
                        "guid" => $formattedGuid,
                    ];

                    $effectiveArgs[] = $formattedGuid;
                }
                else
                {
                    $effectiveArgs[] = $arg;
                }
            }

            $result = htmlspecialchars(
                i18n::getFormattedString($namespace, $name, ...$effectiveArgs),
                \ENT_QUOTES | \ENT_SUBSTITUTE,
                "UTF-8",
            );

            if (!empty($htmlSubstitutionTable))
            {
                foreach ($htmlSubstitutionTable as $entry)
                {
                    $result = str_replace(
                        $entry["guid"],
                        $entry["html"],
                        $result
                    );
                }
            }

            return new Markup($result, "UTF-8");
        }
        catch (Throwable $e)
        {
            return new Markup("< unknown string $namespace:$name >", "UTF-8");
        }
    }

    public function dump(string $namespace): object
    {
        try
        {
            return i18n::getAllTemplates($namespace);
        }
        catch (Throwable $e)
        {
            return (object)[];
        }
    }
}