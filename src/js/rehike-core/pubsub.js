/**
 * @fileoverview Simple publish-subscription messaging.
 * 
 * @author The Rehike Maintainers
 */

rehike.pubsub = {};

rehike.pubsub._subscriptions = {};

/**
 * Subscribe to a topic.
 * 
 * @export
 * 
 * @param {string} topic 
 * @param {Function} cb 
 * @return {void}
 */
rehike.pubsub.subscribe = function(topic, cb)
{
    if (topic && cb)
    {
        rehike.pubsub._ensureOpenScope(topic);

        rehike.pubsub._subscriptions[topic].push(cb);
    }
    else
    {
        rehike.debug.error("rehike.pubsub.subscribe", "Invalid call to rehike.pubsub.subscribe().");
    }
};

/**
 * Unsubscribe from a topic.
 * 
 * @export
 * 
 * @param {string} topic 
 * @param {Function} cb 
 * @return {void}
 */
rehike.pubsub.unsubscribe = function(topic, cb)
{
    if (topic && cb)
    {
        rehike.pubsub._ensureOpenScope(topic);

        var subscriptions = rehike.pubsub._subscriptions[topic];

        for (var i = 0, j = subscriptions.length; i < j; i++)
        {
            if (cb == subscriptions[i])
            {
                subscriptions[i] = null;
            }
        }
    }
    else
    {
        rehike.debug.error("rehike.pubsub.unsubscribe", "Invalid call to rehike.pubsub.unsubscribe().");
    }
};

/**
 * Publish a topic.
 * 
 * @export
 * 
 * @param {string} topic 
 * @param {*} extraData 
 * @return {void}
 */
rehike.pubsub.publish = function(topic, extraData)
{
    extraData = extraData || null;

    var subscriptions;

    if (topic && rehike.pubsub._subscriptions[topic])
    {
        subscriptions = rehike.pubsub._subscriptions[topic];

        for (var i = 0, j = subscriptions.length; i < j; i++)
        if (null != subscriptions[i])
        {
            if (null != extraData)
            {
                subscriptions[i](extraData);
            }
            else
            {
                subscriptions[i]();
            }
        }
    }
    else if (topic) {} // Shouldn't error if there are no subscribers
    else
    {
        rehike.debug.error("rehike.pubsub.publish", "Missing topic in call or registration.");
    }
};

/**
 * Remove a topic.
 * 
 * @export
 * 
 * @param {string} topic 
 * @return {void}
 */
rehike.pubsub.clear = function(topic)
{
    var subscriptions = rehike.pubsub._subscriptions[topic];

    // Unsubscribe all listeners as to not cause a rift in spacetime.
    for (var i = 0, j = subscriptions.length; i < j; i++)
    {
        rehike.pubsub.unsubscribe(topic, subscriptions[i]);
    }

    delete rehike.pubsub._subscriptions[topic];
};

//
// Declare private API
//

/**
 * Open a new topic scope in the pubsub subscriptions array.
 * 
 * @private
 * 
 * @param {string} topic
 * @return {void}
 */
rehike.pubsub._ensureOpenScope = function(topic)
{
    rehike.pubsub._subscriptions[topic] = rehike.pubsub._subscriptions[topic] || [];
};