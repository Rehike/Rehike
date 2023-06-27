<?php
namespace Rehike\Async\Pubsub;

use Closure;

/**
 * Implements a pubsub topic handler.
 * 
 * @internal
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */
class Topic
{
    public string $id;

    /**
     * @var callable[]
     */
    private array $listeners = [];

    public function __construct(string $id)
    {
        $this->id = $id;
    }

    /**
     * Subscribe to this topic.
     */
    public function subscribe(callable $cb): void
    {
        $this->listeners[] = $cb;
    }

    /**
     * Unsubscribe from this topic.
     */
    public function unsubscribe(callable $cb): void
    {
        if ($index = array_search($cb, $this->listeners))
        {
            array_splice($this->listeners, $index, 1);
        }
    }

    /**
     * Publish this topic.
     * 
     * This will notify all subscriptions.
     * 
     * @param mixed $extraData
     */
    public function publish(mixed $extraData = null): void
    {
        foreach ($this->listeners as $cb)
        {
            $cb($extraData);
        }
    }

    /**
     * Clear all subscriptions from this topic.
     */
    public function clear(): void
    {
        $this->listeners = [];
    }
}