<?php
namespace Rehike\Async\Concurrency;

use Rehike\Async\Promise;

/**
 * A nice debugging tool I made because I was having an issue with an error
 * being swallowed.
 * 
 * @static
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */
final class AntiSwallowedErrorHelper
{
    private static int $pendingResolutionCount = 0;
    private static array $pendingResolutions = [];
    private static array $sources = [];
    private static object $shutdownHelper;

    public static function __initStatic()
    {
        self::$shutdownHelper = new class() {
            public function __destruct()
            {
                AntiSwallowedErrorHelper::_handleShutdown();
            }
        };
    }

    /**
     * Report a pending resolution.
     */
    public static function reportPendingResolution(Promise $function, array $source): void
    {
        self::$pendingResolutionCount++;
        self::$pendingResolutions[] = $function;
        self::$sources[] = $source;
    }

    public static function resolvePendingResolution(Promise $function): void
    {
        self::$pendingResolutionCount--;

        if ($pos = array_search($function, self::$pendingResolutions))
        {
            array_splice(self::$pendingResolutions, $pos, 1);
            array_splice(self::$sources, $pos, 1);
        }
    }

    public static function _handleShutdown()
    {
        if (self::$pendingResolutionCount > 0)
        {
            // Throw out the current output buffer because it isn't wanted.
            do
            {
                ob_end_clean();
            }
            while (ob_get_level() > 0);

            $index = count(self::$pendingResolutions) - 1;

            $promise = self::$pendingResolutions[$index];
            $source = self::$sources[$index];

            $reflection = new \ReflectionClass($promise);
            $name = $reflection->getName();

            $file = $source["file"];
            $line = $source["line"];

            throw new \Exception(
                "Unresolved promise ($name) in async function ending at $file:$line."
            );
        }
    }
}