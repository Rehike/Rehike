<?php
namespace Rehike\Async;

use Rehike\Async\Pubsub\Topic;

/**
 * A simple publish/subscribe messaging system.
 * 
 * PHP generally isn't asynchronous, so something this typically isn't 
 * necessary, however Rehike does use an asynchronous design and this is
 * helpful in wrapping that. JS programmers may find this API familiar,
 * however.
 * 
 * This is a very simple pattern. Subscribing to a topic with a callback adds
 * that callback to a queue internally, which is then called when the topic
 * gets published.
 * 
 * A topic is a simple string that acts as a unique ID for that particular
 * queue action.
 * 
 * Using this, code can be queued for later, which preserves the flow and
 * structure of source code while making it wait for another task to continue
 * elsewhere.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */
class Pubsub
{
    /**
     * An array of topics.
     * 
     * @var Topic[]
     */
    private static array $topics = [];

    /**
     * Subscribe to a topic.
     */
    public static function subscribe(string $id, callable $cb): void
    {
        self::getTopicById($id)->subscribe($cb);
    }

    /**
     * Unsubscribe from a topic.
     */
    public static function unsubscribe(string $id, callable $cb): void
    {
        self::getTopicById($id)->unsubscribe($cb);
    }

    /**
     * Publish a topic.
     */
    public static function publish(string $id, mixed $extraData = null): void
    {
        self::getTopicById($id)->publish($extraData);
    }

    /**
     * Remove a topic.
     */
    public static function clear(string $id): void
    {
        self::getTopicById($id)->clear();
    }

    /**
     * Get a topic by its ID.
     */
    private static function getTopicById(string $id): Topic
    {
        foreach (self::$topics as $topic)
        {
            if ($id == $topic->id)
            {
                return $topic;
            }
        }

        // Otherwise it doesn't exist, so add it.
        $topic = new Topic($id);
        self::$topics[] = $topic;

        return $topic;
    }
}