var rehike = rehike || {};

(function(){

/*{{ "*"~"/" }}

 {######################################################
 #                     IMPORTS.                        #
 #                                                     #
 #  The order in which these are added doesn't matter  #
 #  too much, since the JS interpreter will resolve    #
 #  function declarations regardless of their order.   #
 #                                                     #
 #  By the way, that little mess above this comment's  #
 #  a hack to get the IDE to display right with these  #
 #  Twig imports.                                      #
 #                                                     #
 ######################################################}

{% include "rehike/core/debug.js.twig" %}
{% include "rehike/core/script.js.twig" %}
{% include "rehike/core/event_delegate.js.twig" %}
{% include "rehike/core/class.js.twig" %}
{% include "rehike/core/pubsub.js.twig" %}
{% include "rehike/core/util.js.twig" %}
{% include "rehike/core/dialog.js.twig" %}

//*/

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

})();