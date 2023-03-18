<?php
namespace YukisCoffee\CoffeeRequest\Util;

use YukisCoffee\CoffeeRequest\Promise;

use Closure;
use YukisCoffee\CoffeeRequest\Exception\UnhandledPromiseException;

/**
 * Tracks pending Promises and displays an error if the Promise is uncaught
 * within the scope.
 * 
 * @static
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 */
final class PromiseResolutionTracker
{
    /**
     * An anonymous object that watches for shutdowns.
     */
    private static object $shutdownTracker;

    private static int $pendingPromiseCount = 0;
    private static array $pendingPromises = [];

    public static function initialize()
    {
        $shutdownFunction = Closure::fromCallable(self::class."::handleShutdown");

        // An anonymous object is used for the destructor hack in order to
        // run code on PHP shutdown.
        self::$shutdownTracker = new class($shutdownFunction) {
            private Closure $shutdownFunction;

            public function __construct(Closure $shutdownFunction)
            {
                $this->shutdownFunction = $shutdownFunction;
            }

            public function __destruct()
            {
                $this->shutdownFunction->__invoke();
            }
        };
    }

    public static function registerPendingPromise(Promise $promise): void
    {
        // Save a reference to the Promise and a copy of its latest trace.
        self::$pendingPromises[] = [$promise, $promise->latestTrace];

        self::$pendingPromiseCount++;
    }

    public static function unregisterPendingPromise(Promise $promise): void
    {
        foreach (self::$pendingPromises as $i => $pending)
        {
            if ($pending[0] == $promise)
            {
                array_splice(self::$pendingPromises, $i, 1);
                self::$pendingPromiseCount--;
            }
        }
    }

    private static function getLatestPromiseList(): array
    {
        return self::$pendingPromises[count(self::$pendingPromises) - 1];
    }

    private static function handleShutdown(): void
    {
        if (self::$pendingPromiseCount > 0)
        {
            // Prevent it from overtaking other errors:
            $lastError = error_get_last();
            if (in_array($lastError["type"], [E_CORE_ERROR, E_USER_ERROR, E_ERROR]))
            {
                return;
            }

            self::throwUnhandledError();
        }
    }

    private static function throwUnhandledError(): void
    {
        $promiseList = self::getLatestPromiseList();
        $promise = $promiseList[0];
        $originTrace = $promiseList[1];

        throw new UnhandledPromiseException($promise, $originTrace);
    }
}

PromiseResolutionTracker::initialize();