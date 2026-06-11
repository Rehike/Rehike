<?php
declare(strict_types=1);
namespace Rehike\Debugger;

use Rehike\Network\Internal\Request;

/**
 * Manages logging of network requests.
 * 
 * @author Isabella Lulamoon <kawapure@gmail.com>
 * @author The Rehike Maintainers
 */
class NetworkLogging
{
    private static array $requests = [];
    
    /**
     * Logs a network request.
     * 
     * If the debugger is fully enabled (not condensed), then the entire contents
     * of the network response will be preserved. Otherwise, only basic statistical
     * data will be preserved.
     */
    public static function logRequest(Request $request): LoggedRequestContext
    {
        $context = new LoggedRequestContext($request);
        self::$requests[] = $context;
        return $context;
    }
    
    /**
     * Gets all logged requests during the application session.
     */
    public static function getLoggedRequests(): array
    {
        return self::$requests;
    }
}