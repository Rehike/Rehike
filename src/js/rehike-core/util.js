/**
 * @fileoverview Provides common utils for Rebug.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */

rehike.util = {};

rehike.util.scrollLock = {};

/**
 * Is scroll lock enabled? 
 * 
 * @private
 * @type {boolean}
 */
rehike.util.scrollLock._enabled = false;

/**
 * The previous scroll height.
 * 
 * @private
 * @type {number}
 */
rehike.util.scrollLock._previousScroll = 0;

/**
 * A list of elements that were changed.
 * 
 * @private
 * @type {Object[]}
 */
rehike.util.scrollLock._elements = [];

/**
 * Info of the body when changed.
 * 
 * @private
 * @type {Object}
 */
rehike.util.scrollLock._bodyInfo = null;

/**
 * Get display information of an element for scroll locking it.
 * 
 * @param {Element} element 
 * @return {Object}
 */
rehike.util.scrollLock.getElementInfo = function(element)
{
    var style = getComputedStyle(element);

    var info = {
        element: element,
        hasPreviousInlineStyle: element.hasAttribute("style"),
        previousInlineStyle: null,
        shouldModify: style.display == "none" ? false : true
    };

    if (info.hasPreviousInlineStyle)
    {
        info.previousInlineStyle = element.getAttribute("style");
    }

    return info;
};

/**
 * Enable scroll lock.
 * 
 * @return {void}
 */
rehike.util.scrollLock.enable = function()
{
    if (rehike.util.scrollLock._enabled) return;

    // Get the original scroll position
    var originalScroll = document.documentElement.scrollTop;
    rehike.util.scrollLock._previousScroll = originalScroll;

    // Iterate all direct children of body and determine
    // if they should be acted upon.
    for (var i = 0, j = document.body.children.length; i < j; i++)
    {
        var el = document.body.children[i];

        var info = rehike.util.scrollLock.getElementInfo(el);

        if (info.shouldModify)
        {
            rehike.util.scrollLock._elements.push(info);

            info.element.setAttribute(
                "style",
                (
                    (info.element.getAttribute("style") || "") +
                    ";position:relative;top:-" + originalScroll + "px"
                )
            );
        }
    }

    // Finally, freeze the body itself in place.
    var bodyInfo = rehike.util.scrollLock.getElementInfo(document.body);
    rehike.util.scrollLock._bodyInfo = bodyInfo;

    // PATCH (izzy): Required to not jump around visually if not already set on <html>
    document.documentElement.style.overflowY = "scroll";
    document.body.style.overflow = "hidden";

    rehike.util.scrollLock._enabled = true;
};

/**
 * Disable scroll lock.
 * 
 * @return {void}
 */
rehike.util.scrollLock.disable = function()
{
    if (!rehike.util.scrollLock._enabled) return;

    // Undo body changes (this must be done first to avoid
    // conflicts with children changes)
    var bodyInfo = rehike.util.scrollLock._bodyInfo;

    document.documentElement.removeAttribute("style");

    if (bodyInfo.hasPreviousInlineStyle)
    {
        document.body.setAttribute("style", bodyInfo.previousInlineStyle);
    }
    else
    {
        document.body.removeAttribute("style");
    }

    // And now iterate children to undo their property changes.
    var children = rehike.util.scrollLock._elements;

    for (var i = 0, j = children.length; i < j; i++)
    {
        var info = children[i];

        if (info.hasPreviousInlineStyle)
        {
            info.element.setAttribute("style", info.previousInlineStyle);
        }
        else
        {
            info.element.removeAttribute("style");
        }
    }

    rehike.util.scrollLock._elements = [];

    document.documentElement.scrollTop = rehike.util.scrollLock._previousScroll;

    rehike.util.scrollLock._enabled = false;
};


rehike.util.events = {};

/**
 * Wraps an event for easy unregistration.
 * 
 * @constructor
 */
rehike.util.events.EventWrapper = function(target, name, cb)
{
    this.target = target;
    this.name = name;
    this.cb = cb;
};

/**
 * Removes this event listener.
 */
rehike.util.events.EventWrapper.prototype.remove = function()
{
    rehike.util.events.remove(this.target, this.name, this.cb);
};

/**
 * Add an event listener.
 * 
 * @param {Element} target 
 * @param {string} name 
 * @param {function(Event)} cb 
 * 
 * @return {rehike.util.events.EventWrapper}
 */
rehike.util.events.add = function(target, name, cb)
{
    if (target.addEventListener)
    {
        target.addEventListener(name, cb);
    }
    else if (target.attachEvent)
    {
        target.attachEvent("on" + name, cb);
    }
    
    return new rehike.util.events.EventWrapper(target, name, cb);
};

/**
 * Remove an event listener.
 * 
 * @param {Element} target 
 * @param {string} name 
 * @param {function(Event)} cb 
 */
rehike.util.events.remove = function(target, name, cb)
{
    if (target.removeEventListener)
    {
        target.removeEventListener(name, cb);
    }
    else if (target.detachEvent)
    {
        target.detachEvent("on" + name, cb);
    }
};

/**
 * Checks if the following element is a child of the parent element, or one of
 * its children.
 * 
 * @param {Element} child
 * @param {Element} parent
 */
rehike.util.isChildOf = function(childEl, parentEl)
{
    var curElement = childEl;

    do
    {
        if (curElement.parentNode && curElement.parentNode == parentEl)
        {
            return true;
        }

        curElement = curElement.parentNode;
    }
    while (curElement != null);

    return false;
};