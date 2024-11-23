/**
 * @fileoverview Implements network handlers for rebug.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */

rebug.network = {};

/**
 * Stores previous network captures.
 * 
 * @type {rebug.history.DebuggerSnapshot[]}
 */
rebug.network._captures = [];

/**
 * Add a response to the capture store.
 * 
 * @param {Object} capture 
 */
rebug.network.addCapture = function(capture)
{
    rebug.network._captures.push(capture);
};

/**
 * Modifies a request to make it request Rebug info (under certain
 * circumstances).
 * 
 * @param {Object} request 
 * @private
 */
rebug.network._modifyRequest = function(request)
{
    var spfPos = -1;

    // Don't want to modify any foreign requests obviously!
    if (request.url.indexOf(window.location.origin) != 0)
    {
        return;
    }

    // Modifying SPF requests is redundant in this case.
    if ((spfPos = request.url.indexOf("spf")) > -1)
    {
        var prevChar = request.url.charAt(spfPos - 1);

        if ("?" == prevChar || "&" == prevChar)
        {
            return;
        }
    }

    // Lazy
    if (request.url.indexOf("?") > -1)
    {
        request.url += "&";
    }
    else
    {
        request.url += "?";
    }

    request.url += "rebug_get_info=1";
};

/**
 * Modifies the contents of a response if they have Rebug info.
 * 
 * @param {Object} request 
 * @param {Object} response 
 * 
 * @private
 */
rebug.network._modifyResponse = function(request, response)
{
    if (response.headers["x-rebug-get-info"])
    {
        var data = JSON.parse(response.data);

        rebug.network.addCapture(data);

        response.headers["content-type"] = data.content_type;

        response.data = data.response;
        response.text = data.response;
    }
};

/**
 * Sets up xhook for intercepting AJAX.
 * 
 * @private
 */
rebug.network._setupXhook = function()
{
    window.xhook.enable();

    window.xhook.before(rebug.network._modifyRequest);
    window.xhook.after(rebug.network._modifyResponse);
};

/**
 * Runs on Rebug initialisation.
 * 
 * @private
 */
rebug.network._onRebugInit = function()
{
    if (window._rebugcfg && window._rebugcfg.CONDENSED === false)
    {
        rebug.network._setupXhook();
    }
};

rehike.pubsub.subscribe("rebug-init-finish", rebug.network._onRebugInit);