<?php
namespace Rehike\Async\Debugging;

use Rehike\Async\Utils;
use Rehike\Logging\DebugLogger;

/**
 * Basic event tracing engine for the async framework.
 * 
 * @author Isabella Lulamoon <kawapure@gmail.com>
 * @author The Rehike Maintainers
 */
final class Tracing
{
    private static array $s_log = [];
    
    private const LOG_TIME = 0;
    private const LOG_ID   = 1;
    private const LOG_DATA = 2;
    
    // Filters:
    private static bool $s_logSuccessful = false;
    
    /**
     * Logs an event.
     * 
     * @param int $traceEventId  One of the {@see TraceEventId} values.
     * @param mixed $data  Any unique data for this log. Be wary of exhausing memory
     *                     with common logs.
     */
    public static function logEvent(int $traceEventId, mixed $data = null): void
    {
        // The time is retrieved as a float because it weighs less in that format
        // than as a string. A float will just be a processor word, such as 4 or
        // 8 bytes.
        $time = microtime(as_float: true);
        self::$s_log[] = [$time, $traceEventId, $data];
    }
    
    /**
     * Logs a failure of a regular event.
     * 
     * @param int $traceEventId  One of the {@see TraceEventId} values.
     * @param mixed $data  Any unique data for this log. Be wary of exhausing memory
     *                     with common logs.
     */
    public static function logFailure(int $traceEventId, mixed $data = null): void
    {
        self::logEvent(TraceEventId::makeFailed($traceEventId), $data);
    }
    
    /**
     * Sets whether or not successful logs will be kept.
     */
    public static function enableSuccessfulLogs(bool $value): void
    {
        self::$s_logSuccessful = $value;
    }
    
    /**
     * Forwards the logs collected here to the standard debug logger.
     */
    public static function logToDebugLogger(): void
    {
        $logs = self::getFilteredLog();
        
        if (empty($logs))
        {
            return;
        }
        
        DebugLogger::print("BEGIN ASYNC FRAMEWORK LOG===========================================");
        
        $visitedCookies = [];
        
        foreach ($logs as $log)
        {
            $name = Utils::getEnumValueName(TraceEventId::class, $log[self::LOG_ID]);
            $formattedData = "";
            
            if (
                is_object($log[self::LOG_DATA]) && 
                $log[self::LOG_DATA] instanceof TrackingCookie
            )
            {
                /** @var TrackingCookie */
                $cookie = $log[self::LOG_DATA];
                $formattedData = $cookie->__toString();
                
                if (!in_array($cookie, $visitedCookies))
                {
                    $formattedData .= ", definition: ";
                    $formattedData .= $cookie->getCreationTrace();
                    $visitedCookies[] = $cookie;
                }
            }
            
            DebugLogger::print(" - [%s] %s %s",
                $log[self::LOG_TIME],
                $name,
                $formattedData,
            );
        }
        
        DebugLogger::print("END ASYNC FRAMEWORK LOG=============================================");
    }
    
    private static function getFilteredLog(): array
    {
        return self::$s_logSuccessful
            ? self::$s_log
            : array_filter(self::$s_log, fn($i) => TraceEventId::failed($i[self::LOG_ID]));
    }
}