<?php
namespace Rehike;

/**
 * A simple profiler utility.
 * 
 * @author The Rehike Maintainers
 */
class Profiler
{
    /**
     * A map of key names to values.
     * 
     * The values are two-member arrays which feature a start and end timestamp.
     * If they are incomplete, then they may be one-member instead.
     */
    private static array $timings = [];

    /**
     * Start a named profiler timing.
     */
    public static function start(string $name): void
    {
        self::$timings[$name] = [hrtime(as_number: true)];
    }

    /**
     * End a named profiler timing.
     */
    public static function end(string $name): void
    {
        self::$timings[$name][1] = hrtime(as_number: true);
    }

    /**
     * Get a set of all timings made during the session.
     */
    public static function getTimings(): array
    {
        $result = [];

        foreach (self::$timings as $name => $timing)
        {
            if (count($timing) < 2)
            {
                $timing[1] = hrtime(as_number: true);
            }

            $result[$name] = $timing[1] - $timing[0];
        }

        return $result;
    }

    /**
     * Get a particular named profiler timing.
     */
    public static function get(string $name): int|float
    {
        return self::$timings[$name][1] - self::$timings[$name][0];
    }
}