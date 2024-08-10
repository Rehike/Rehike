var rehike = rehike || {};
window.rehike = rehike;

(function(){

// @include class.js
// @include debug.js
// @include dialog.js
// @include event_delegate.js
// @include pubsub.js
// @include script.js
// @include util.js

/**
 * Implements simple OOP inheritance.
 * 
 * @export
 * 
 * @param {Object} childClass 
 * @param {Object} parentClass 
 * 
 * @return {void}
 */
rehike.inherits = function(childClass, parentClass)
{
    // Create a new scope copy, such that changes to the
    // child do not affect the parent.
    /** @constructor */
    var parentCopy = function() {};
    parentCopy.prototype = parentClass.prototype;

    childClass.prototype = new parentCopy();
    childClass.prototype.constructor = childClass;

    /**
     * Call a method on the parent class.
     * 
     * Additional arguments passed to this method will be
     * interpreted as arguments to pass to the method to call.
     * 
     * @param {Object} self 
     * @param {string} method 
     */
    childClass.super = function(self, method) {
        var args = [];

        for (var i = 2; i < arguments.length; i++)
        {
            args[i - 2] = arguments[i];
        }

        return parentClass.prototype[method].apply(self, args);
    };
};

rehike.rehikeCoreInit_ = function()
{
    // Load delayloaded modules:
    var dlmPath = "_rehikeCoreDelayLoadModules";
    if (dlmPath in window && Array.isArray(window[dlmPath]))
    {
        for (var i = 0, j = window[dlmPath].length; i < j; i++)
        {
            var func = window[dlmPath][i];
            
            if (typeof func == "function")
            {
                func();
            }
        }
        
        delete window[dlmPath];
    }
};

rehike.rehikeCoreInit_();

})();