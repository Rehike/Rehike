/**
 * Implements the event manager of the Rehike JS core.
 * 
 * @author The Rehike Maintainers
 */

rehike.eventDelegate = {};

/**
 * Stores a map of event handlers.
 * 
 * @type {Object[]}
 * @private
 */
rehike.eventDelegate._handlers = [];

/**
 * Stores the names of all active events.
 * 
 * @type {string[]}
 * @private
 */
rehike.eventDelegate._activeEvents = [];

/**
 * Add a delegated event handler.
 * 
 * @param {string} eventName 
 * @param {string} className 
 * @param {function(Event)} cb 
 * 
 * @return {number}
 */
rehike.eventDelegate.add = function(eventName, className, cb)
{
    if (!rehike.eventDelegate._isActiveEventName(eventName))
    {
        rehike.util.events.add(
            document, 
            eventName, 
            rehike.eventDelegate._getDelegateHandler(eventName)
        );

        rehike.eventDelegate._activeEvents.push(eventName);
    }

    return rehike.eventDelegate._addHandler(eventName, className, cb);
};

rehike.eventDelegate.remove = function(eventName, className, handle)
{
    if (!rehike.eventDelegate._handlers[eventName])
        return;
    if (!rehike.eventDelegate._handlers[eventName][className])
        return;

    rehike.eventDelegate._handlers[eventName][className].splice(handle, 1);
};

/**
 * Determine if an event already has an active handler by
 * its name.
 * 
 * @param {string} name 
 * @return {boolean}
 * 
 * @private
 */
rehike.eventDelegate._isActiveEventName = function(name)
{
    return rehike.eventDelegate._activeEvents.includes(name);
};

/**
 * Generate a new event handler function.
 * 
 * @param {string} eventName 
 * @return {function(Event)}
 * 
 * @private
 */
rehike.eventDelegate._getDelegateHandler = function(eventName)
{
    return function(e) {
        var activeElement = e.target;
        var handlerClassNameList = rehike.eventDelegate._handlers[eventName];
        
        while (null != activeElement)
        {
            if (activeElement.className)
            {
                var classes;
                if (activeElement.classList)
                {
                    classes = activeElement.classList;
                }
                else
                {
                    classes = activeElement.className.split(" ");
                }

                for (var i = 0, j = classes.length; i < j; i++)
                {
                    if (classes[i] in handlerClassNameList)
                    {
                        for (var k = 0, l = handlerClassNameList[classes[i]].length; k < l; k++)
                        {
                            if (typeof handlerClassNameList[classes[i]][k] == "function")
                            {
                                handlerClassNameList[classes[i]][k](activeElement, e);
                            }
                        }
                    }
                    else if ("rehike-no-propagate" == classes[i])
                    {
                        return;
                    }
                }
            }

            activeElement = activeElement.parentElement;
        }
    };
};

/**
 * Add a handler to the internal array.
 * 
 * @param {string} eventName 
 * @param {string} className 
 * @param {function(Event)} cb 
 * 
 * @return {number}
 * 
 * @private
 */
rehike.eventDelegate._addHandler = function(eventName, className, cb)
{
    if (!(eventName in rehike.eventDelegate._handlers))
    {
        rehike.eventDelegate._handlers[eventName] = {};
    }

    if (!(className in rehike.eventDelegate._handlers[eventName]))
    {
        rehike.eventDelegate._handlers[eventName][className] = [];
    }

    rehike.eventDelegate._handlers[eventName][className].push(cb);

    return rehike.eventDelegate._handlers[eventName][className].length - 1;
};