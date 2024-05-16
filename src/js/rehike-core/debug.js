rehike.debug = {};

/**
 * Throw an error if something goes wrong.
 * 
 * @param {string} namespaceOrMessage 
 * @param {string|null} message 
 */
rehike.debug.error = function(namespaceOrMessage, message)
{
    message = message || null;

    if (namespaceOrMessage && "string" == typeof message)
    {
        var namespace = namespaceOrMessage;

        throw new Error("[rehike:" + namespace + "] " + message);
    }
    else
    {
        throw new Error("[rehike: unknown namespace] " + message);
    }
};