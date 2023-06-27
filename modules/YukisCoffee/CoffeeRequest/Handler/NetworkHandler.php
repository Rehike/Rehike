<?php
namespace YukisCoffee\CoffeeRequest\Handler;

use YukisCoffee\CoffeeRequest\Event;
use YukisCoffee\CoffeeRequest\Attributes\Override;

use YukisCoffee\CoffeeRequest\Network\Request;
use YukisCoffee\CoffeeRequest\Network\Response;

/**
 * A template for all network handlers.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 */
abstract class NetworkHandler extends Event
{
    /**
     * Used internally to prevent the event loop from nullifying this
     * event when it's reported as fulfilled.
     */
    public bool $preventNullification = true;

    /**
     * Add a request to the request manager.
     */
    abstract public function addRequest(Request $request): void;

    /**
     * Clear all requests from the request manager.
     */
    abstract public function clearRequests(): void;

    /**
     * Called in order to restart the manager when needed by the request
     * manager.
     * 
     * This is required in order for the event to run more than once, as
     * otherwise it is never gotten to again once it finishes its job.
     */
    public function restartManager(): void
    {
        unset($this->generator);
        $this->fulfilled = false;
    }
}