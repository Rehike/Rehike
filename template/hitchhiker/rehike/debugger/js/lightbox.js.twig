/**
 * @fileoverview Provides basic lightbox behaviors for Rebug.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */

rebug.lightbox = {};

rebug.lightbox.RebugLightbox = function(containerElement) {
    rehike.Dialog.call(this, containerElement);
};
rehike.inherits(rebug.lightbox.RebugLightbox, rehike.Dialog);

/**
 * Opens the Rebug lightbox.
 */
rebug.lightbox.RebugLightbox.prototype.open = function()
{
    rebug.lightbox.RebugLightbox.super(this, "open");

    // Unhide the lightbox
    var container = this._container;
    rehike.class.add(container, "open");
};

/**
 * Closes the Rebug lightbox.
 */
rebug.lightbox.RebugLightbox.prototype.close = function()
{
    rebug.lightbox.RebugLightbox.super(this, "close");

    // Hide the lightbox
    var container = this._container;
    rehike.class.remove(container, "open");
};

/**
 * Stores a reference to the lightbox API.
 * 
 * @type {rehike.lightbox.RebugLightbox}
 */
rebug.lightbox._instance = null;

/**
 * Opens the Rebug lightbox.
 */
rebug.lightbox.open = function()
{
    rebug.lightbox._instance.open();

    // Alert any potential listeners to the new state.
    rehike.pubsub.publish("rebug-lightbox-opened");
};

/**
 * Closes the Rebug lightbox.
 */
rebug.lightbox.close = function()
{
    rebug.lightbox._instance.close();

    // Alert any potential listeners to the new state.
    rehike.pubsub.publish("rebug-lightbox-closed");
};

/**
 * Runs upon Rebug initialization.
 * 
 * @private
 */
rebug.lightbox._init = function()
{
    rebug.lightbox._instance = new rebug.lightbox.RebugLightbox(
        document.getElementById("rebug-lightbox-container") || null
    );
};

rehike.pubsub.subscribe("rebug-init-finish", rebug.lightbox._init);