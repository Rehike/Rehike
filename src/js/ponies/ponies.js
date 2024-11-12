/**
 * @fileoverview Ponies client-side manager.
 * 
 * @author Isabella Lulamoon <kawapure@gmail.com>
 * @author The Rehike Maintainers
 */

/* @include ../shared/delayload_stub.js */(function() {

rehike.ponies = (window.rehike && window.rehike.ponies) || {};

// Module quick identifier.
var module = rehike.ponies;

/**
 * @type {rehike.util.events.EventWrapper[]}
 */
rehike.ponies.registeredEvents_ = rehike.ponies.registeredEvents_ || [];

/**
 * Checks if the script is currently initialized.
 * 
 * @type {boolean}
 */
rehike.ponies.isInitialized_ = rehike.ponies.isInitialized_ || false;

rehike.ponies.CSS_TARGET_ID = "rh-ponies-style";

rehike.ponies.initialize = function()
{
    // Subscribe to SPF navigation so we can know when to change the ponies.
    // We actually use the "spfprocess" event, so these events occur before
    // SPF loads scripts from the next page.
    module.registeredEvents_.push(rehike.util.events.add(
        document,
        "spfprocess",
        module.onSpfNavigation_
    ));
    
    module.isInitialized_ = true;
};

/**
 * Sets the pony CSS rules.
 * 
 * @param {string} rules String containing CSS rules to be set.
 */
rehike.ponies.setPonyRules = function(rules)
{
    var cssTarget = document.getElementById(module.CSS_TARGET_ID);
    
    if (!cssTarget)
    {
        cssTarget = document.createElement("style");
        cssTarget.id = module.CSS_TARGET_ID;
        document.body.appendChild(cssTarget);
    }
    
    cssTarget.textContent = rules;
};

/**
 * Set the color of the logo in the masthead.
 * 
 * @param {boolean} value 
 */
rehike.ponies.setDarkLogo = function(value)
{
    var DARK_LOGO_CLASS = "rh-dark-logo";

    if (value)
    {
        if (!rehike.class.has(document.body, DARK_LOGO_CLASS))
        {
            rehike.class.add(document.body, DARK_LOGO_CLASS);
        }
    }
    else
    {
        while (rehike.class.has(document.body, DARK_LOGO_CLASS))
        {
            rehike.class.remove(document.body, DARK_LOGO_CLASS);
        }
    }
};

rehike.ponies.removePonies_ = function()
{
    var a;

    if (a = document.getElementById(module.CSS_TARGET_ID))
    {
        a.parentNode.removeChild(a);
    }

    rehike.ponies.setDarkLogo(false);
};

/**
 * "spfprocess" event callback.
 * 
 * @param {spf.Event} event 
 */
rehike.ponies.onSpfNavigation_ = function(event)
{
    var url = new URL(event.detail.url);
    
    /*
     * If we're some sort of results page, then we want to remove the current
     * ponies state. One of two cases will then follow:
     * 
     * 1. The user is visiting another pony-applicable results page; local
     *    script will call setPonyRules() on this and bring ponies back
     *    instantly.
     * 2. The user is visiting a non-pony-applicable results page. In this
     *    case, we just want to revert to the default masthead design, so we
     *    are okay with removePonies_() being called.
     */
    if (url.pathname.indexOf("/results") > -1)
    {
        module.removePonies_();
    }
};

/**
 * Shut down the module and remove any references which may prevent us from
 * being garbage collected.
 */
rehike.ponies.shutdown = function()
{
    module.removePonies_();
    
    for (var i = 0, j = module.registeredEvents_.length; i < j; i++)
    {
        module.registeredEvents_[i].remove();
    }
    
    module.isInitialized_ = false;
    
    delete rehike.ponies;
};

// Automatically initialize the script when we're ready.
if (!module.isInitialized_)
    module.initialize();

}); // intentionally not called for the delayload stub