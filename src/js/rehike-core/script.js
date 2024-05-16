/**
 * @fileoverview Script lifetime management utilities.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */

rehike.script = {};

/**
 * Determines if page-specific callbacks have already been installed.
 * 
 * @var {boolean}
 */
rehike.script.pageSpecificCallbackInstalled_ = false;

/**
 * Stores an array of callback functions for script shutdown.
 * 
 * @var {function()[]}
 */
rehike.script.pageSpecificCallbackRegistry_ = [];

/**
 * Registers a page-specific module.
 * 
 * A page-specific module implements 
 * 
 * @param {function()} installCb Installation callback, called instantly.
 * @param {function()} uninstallCb Uninstall callback, called when the user
 *                                 changes the page.
 */
rehike.script.registerPageSpecific = function(installCb, uninstallCb)
{
    // Register an event to monitor user navigations if that hasn't been done
    // already.
    if (!rehike.script.pageSpecificCallbackInstalled_)
    {
        rehike.util.events.add(
            document, 
            "spfprocess",
            rehike.script.handleGlobalPageSpecificShutdown_
        );
        rehike.script.pageSpecificCallbackInstalled_ = true;
    }

    // Store the pointer to the uninstall callback for later.
    rehike.script.pageSpecificCallbackRegistry_.push(uninstallCb);

    // Call the install callback right now, since this function will only ever
    // be called during script initialization.
    installCb();
};

rehike.script.handleGlobalPageSpecificShutdown_ = function()
{
    // Free the event listener so it doesn't overstay its welcome and leak.
    // This is actually required in order to prevent a memory leak if the user
    // visits multiple pages with Rehike scripts.
    rehike.util.events.remove(
        document,
        "spfprocess",
        rehike.script.handleGlobalPageSpecificShutdown_
    );

    // Dispatch to each loaded module's shutdown procedures.
    var callbacks = rehike.script.pageSpecificCallbackRegistry_;
    for (var i = 0, j = callbacks.length; i < j; i++)
    {
        callbacks[i]();
    }
};