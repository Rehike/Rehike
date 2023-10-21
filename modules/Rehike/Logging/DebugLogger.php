<?php
namespace Rehike\Logging;

/**
 * A proper debug log system that can be logged to globally.
 * 
 * Honestly, I can't believe that it took us nearly two year to get around to
 * this, but it's finally here.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */
class DebugLogger
{
    /**
     * Stores all log messages sent during the server runtime.
     * 
     * @var string[]
     */
    private static array $logs = [];

    /**
     * Send a debug message (with printf formatting).
     */
    public static function print(string $template, mixed ...$args): void
    {
        self::$logs[] = sprintf($template, ...$args);
    }

    /**
     * Gets all logs from the session.
     * 
     * @return string[]
     */
    public static function getLogs(): array
    {
        // We want them to go in chronological order, not the internal stack
        // order.
        return array_reverse(self::$logs);
    }
}