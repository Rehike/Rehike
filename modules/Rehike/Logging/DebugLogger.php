<?php
namespace Rehike\Logging;

/**
 * A proper debug log system that can be logged to globally.
 * 
 * Honestly, I can't believe that it took us nearly two year to get around to
 * this, but it's finally here.
 * 
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
        $message = sprintf($template, ...$args);
        
        $latestMessage = @self::$logs[count(self::$logs) - 1];
        if (is_string($latestMessage) && strstr($latestMessage, $message) !== false)
        {
            if (substr($latestMessage, 0, 2) == "[x")
            {
                $currentCount = explode("[x", $latestMessage)[1];
                $currentCount = (int)(explode("]", $currentCount)[0]);
                
                $newMessage = "[x" . ++$currentCount . "] " . $message;
                //self::$logs[] = $currentCount;
                self::$logs[count(self::$logs) - 1] = $newMessage;
            }
            else
            {
                self::$logs[count(self::$logs) - 1] = "[x2] $message";
            }
        }
        else
        {
            self::$logs[] = $message;
        }
    }

    /**
     * Gets all logs from the session.
     * 
     * @return string[]
     */
    public static function getLogs(): array
    {
        return self::$logs;
    }
}