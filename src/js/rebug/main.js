/**
 * Implements the base JS for the Rehike debugger.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */

var _rebug = _rebug || {};

(function(rebug) {

// @include polyfill/Array.includes.js
// @include polyfill/Element.remove.js

// @include lightbox.js
// @include tabs.js
// @include menu.js
// @include history.js
// @include network.js
// @include spf.js
// @include global_walker.js
// @include widgets.js

function init()
{
    // Register events for clicking the open button.
    rehike.eventDelegate.add(
        "click", 
        "rebug-open-button", 
        rebug.widgets.OpenButton.onClick
    );
    rehike.eventDelegate.add(
        "click", 
        "rebug-close-button", 
        rebug.widgets.CloseButton.onClick
    );

    rehike.eventDelegate.add(
        "click", 
        "rebug-tab", 
        rebug.widgets.Tab.onClick
    );
    rehike.eventDelegate.add(
        "click", 
        "rebug-expander",
        rebug.widgets.Expander.onClick
    );
    rehike.eventDelegate.add(
        "click", 
        "rebug-expander-target", 
        rebug.widgets.ExpanderTarget.onClick
    );

    if (window.ytspf && window.ytspf.enabled)
    {
        // Register SPF event handlers
        rehike.util.events.add(
            document, 
            "spfdone", 
            rebug.spf.events.onSpfDone
        );
    }

    rehike.pubsub.publish("rebug-init-finish");
}

init();

})(_rebug)