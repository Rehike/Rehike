<?php
namespace Rehike\Boot;

use Closure;
use InvalidArgumentException;

/**
 * Manages events to run before Rehike shutdown.
 * 
 * @author Isabella Lulamoon <kawapure@gmail.com>
 * @author The Rehike Maintainers
 */
class ShutdownEvents
{
    private static int $anonymousIndexCounter = 0;
    
    private static array $shutdownEvents = [];
    
    /**
     * Observes PHP script shutdown for critical shutdown events.
     * 
     * When the destructor on this object is called, the PHP script is shutting
     * down.
     */
    private static object $shutdownManager;
    
    public static function __initStatic(): void
    {
        // Initialise the shutdown manager:
        self::$shutdownManager = new class(static::class) {
            public function __construct(private string $parent) {}
            
            public function __destruct()
            {
                $criticalEvents = ($this->parent)::_getCriticalEvents();
                
                foreach ($criticalEvents as $event)
                {
                    $event["cb"]();
                }
            }
        };
    }
    
    /**
     * Register a shutdown event.
     * 
     * @return string Token to shutdown the event manager with.
     */
    public static function register(Closure $callback, string $name = "", bool $critical = false): string
    {
        $index = empty($name) ? (string)++self::$anonymousIndexCounter : $name;
        
        self::$shutdownEvents[$index] = [
            "cb" => $callback,
            "critical" => $critical
        ];
        
        return $index;
    }
    
    /**
     * Cancel a registered shutdown event.
     */
    public static function cancel(Closure|string|int $identifier): void
    {
        if (is_string($identifier))
        {
            if (isset(self::$shutdownEvents[$identifier]))
            {
                unset(self::$shutdownEvents[$identifier]);
            }
        }
        else if ($identifier instanceof Closure)
        {
            foreach (self::$shutdownEvents as $index => $event)
            {
                if ($event["cb"] == $identifier)
                {
                    unset(self::$shutdownEvents[$index]);
                }
            }
        }
        else
        {
            throw new InvalidArgumentException(
                "Expected Closure|string for argument \$identifier"
            );
        }
    }
    
    /**
     * Runs all registered events.
     * 
     * This function is meant to be called by Bootloader::shutdown or similar
     * things.
     */
    public static function runAllEvents(): void
    {
        foreach (self::$shutdownEvents as $event)
        {
            $event["cb"]();
        }
    }
    
    /**
     * Get all critical events created during the session.
     * 
     * @internal
     */
    public static function _getCriticalEvents(): array
    {
        return array_filter(self::$shutdownEvents, fn($e) => @$e["critical"] ?? false);
    }
}