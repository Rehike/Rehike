/**
 * @fileoverview Provides basic dialog behaviours for Rehike core JS.
 * 
 * @author The Rehike Maintainers
 */

/** @constructor */
rehike.Dialog = function(containerElement, opt_args)
{
    if (opt_args == null)
        opt_args = {};

    this._container = containerElement;
    this._foregroundElement = containerElement.querySelector(".yt-dialog-fg");

    this._openCb = opt_args.openCb || null;
    this._closeCb = opt_args.closeCb || null;
    this._buttonHandlerCb = opt_args.buttonHandlerCb || null;
    this._enableButtonHandling = opt_args.enableButtonHandling || true;
    this._enableKeybinds = opt_args.enableKeybinds || true;
    this._closeOnBackgroundClick = opt_args.closeOnBackgroundClick || true;
    this._currentState = opt_args.initialState || this._currentState;

    this._applyDialogState();
    
    if (opt_args.preventOverflow)
    {
        this._foregroundElement.style.maxWidth = "80%";
    }
};

var temp = Dialog || undefined;
var Dialog = rehike.Dialog.prototype;

/**
 * Set as long as any dialog is visible.
 * 
 * This is used to prevent multiple dialogs from being opened at once due to a
 * spammed user input.
 * 
 * @static
 */
rehike.Dialog.s_isAnyDialogVisible = false;

/**
 * Stores a reference to the dialog container element.
 * 
 * @type {Element}
 */
Dialog._container = null;

/**
 * Stores a reference to the dialog foreground container element.
 * 
 * @type {Element}
 */
Dialog._foregroundElement = null;

/**
 * Determines the current display state of the dialog.
 * 
 * @type {string}
 */
Dialog._currentState = "loading";

/**
 * Callback to be called upon opening the dialog.
 */
Dialog._openCb = null;

/**
 * Callback to be called upon closing the dialog.
 */
Dialog._closeCb = null;

/**
 * Enables or disables button event handling.
 */
Dialog._enableButtonHandling = true;

/**
 * Enables or disables keybind handling.
 */
Dialog._enableKeybinds = true;

/**
 * Callback to be called for custom button event handling.
 */
Dialog._buttonHandlerCb = null;

/**
 * Closes the dialog when the user clicks on the background.
 */
Dialog._closeOnBackgroundClick = true;

/**
 * Handle for the last registered delegated event, used for removing it.
 */
Dialog._lastEventHandle = 0;

/**
 * Keeps track of the bound keybind handler in order to unregister it when the
 * dialog is closed.
 */
Dialog._handleBoundKeybindHandler = null;

Dialog._handleBackgroundClickHandler = null;

/**
 * Opens the dialog.
 */
Dialog.open = function()
{
    if (rehike.Dialog.s_isAnyDialogVisible)
    {
        return;
    }

    rehike.Dialog.s_isAnyDialogVisible = true;
    var container = this._container;

    // Create YT dialog background HTML
    this._createDialogBackground();

    rehike.class.remove(container, "hid");

    if (this._openCb != null)
    {
        this._openCb();
    }

    if (this._enableButtonHandling)
    {
        var self = this;
        this._lastEventHandle = rehike.eventDelegate.add(
            "click",
            "yt-uix-button",
            function () {
                return self._handleButtonEvent.apply(self, arguments)
            }
        );
    }

    if (this._enableKeybinds)
    {
        this._handleBoundKeybindHandler = this._handleKeyPress.bind(this);
        rehike.util.events.add(
            document, "keydown", this._handleBoundKeybindHandler
        );
    }

    if (this._closeOnBackgroundClick)
    {
        var self = this;
        this._handleBackgroundClickHandler = function(event) {
            if (event.target == self._container.querySelector(".yt-dialog-base"))
            {
                self.close();
            }
        };

        rehike.util.events.add(
            this._container.querySelector(".yt-dialog-base"),
            "click",
            this._handleBackgroundClickHandler
        );
    }
};

/**
 * Closes the dialog.
 */
Dialog.close = function()
{
    rehike.Dialog.s_isAnyDialogVisible = false;
    var container = this._container;

    // Remove the dialog background
    this._removeDialogBackground();

    rehike.class.add(container, "hid");

    if (this._closeCb != null)
    {
        this._closeCb();
    }

    if (this._enableButtonHandling)
    {
        rehike.eventDelegate.remove(
            "click",
            "yt-uix-button",
            this._lastEventHandle
        );
    }

    if (this._enableKeybinds)
    {
        rehike.util.events.remove(
            document, "keydown", this._handleBoundKeybindHandler
        );
    }

    if (this._closeOnBackgroundClick)
    {
        rehike.util.events.remove(
            this._container.querySelector(".yt-dialog-base"),
            "click",
            this._handleBackgroundClickHandler
        );
    }
};

/**
 * Sets the display state of the dialog.
 * 
 * @param {string} newState
 */
Dialog.setDialogState = function(newState)
{
    var verify = newState.toLowerCase();
    if (
        "loading" != verify &&
        "content" != verify &&
        "working" != verify
    )
    {
        rehike.debug.error("core.Dialog", "Invalid dialog state: " + newState);
    }

    this._currentState = newState;
    this._applyDialogState();
};

/**
 * Creates the dialog background.
 * 
 * @private
 */
Dialog._createDialogBackground = function()
{
    rehike.util.scrollLock.enable();
    
    rehike.class.add(document.body, "hide-players");
    rehike.class.add(document.body, "yt-dialog-active");

    // Create the dialog
    var bgdiv = document.createElement("DIV");
    bgdiv.setAttribute("id", "yt-dialog-bg");
    bgdiv.setAttribute("class", "yt-dialog-bg");
    bgdiv.setAttribute("style", "height: 100%; width: 100%; position: fixed;");
    document.body.insertBefore(bgdiv, 
        (
            document.getElementById("footer-container") ||
            document.body.children[document.body.children.length - 1]
        ).nextSibling
    );
};

/**
 * Removes the dialog background.
 * 
 * @private
 */
Dialog._removeDialogBackground = function()
{
    // Remove the dialog and body classes
    rehike.class.remove(document.body, "hide-players");
    rehike.class.remove(document.body, "yt-dialog-active");

    document.getElementById("yt-dialog-bg").remove();

    rehike.util.scrollLock.disable();
};

/**
 * Applies the currently set dialog state.
 * 
 * @private
 */
Dialog._applyDialogState = function()
{
    var parentContainer = this._container;
    var dialogBase = parentContainer.querySelector(".yt-dialog-base");

    if (!dialogBase) return;

    rehike.class.remove(dialogBase, "yt-dialog-show-loading");
    rehike.class.remove(dialogBase, "yt-dialog-show-content");
    rehike.class.remove(dialogBase, "yt-dialog-show-working");

    if ("content" == this._currentState.toLowerCase())
    {
        rehike.class.add(dialogBase, "yt-dialog-show-content");
    }
    else if ("working" == this._currentState.toLowerCase())
    {
        rehike.class.add(dialogBase, "yt-dialog-show-working");
    }
    else // loading
    {
        rehike.class.add(dialogBase, "yt-dialog-show-loading");
    }
};

/**
 * The parent function which dispatches the button event handler.
 * 
 * @param {Element} element 
 * @param {Event} event 
 */
Dialog._handleButtonEvent = function(element, event)
{
    if (this._buttonHandlerCb)
    {
        this._buttonHandlerCb(
            this._defaultHandleButtonEvent.bind(this), 
            element, 
            event
        );
    }
    else
    {
        this._defaultHandleButtonEvent(element, event);
    }
};

/**
 * Implements the default button event handler for the dialog.
 * 
 * @param {Element} element 
 * @param {Event} event 
 */
Dialog._defaultHandleButtonEvent = function(element, event)
{
    if (
        rehike.class.has(element, "cancel-button") ||
        rehike.class.has(element, "yt-dialog-close")
    )
    {
        this.close();
    }
};

Dialog._handleKeyPress = function(event)
{
    if (event.keyCode == 27) // 27 == escape key code
    {
        this.close();
    }
};

Dialog = temp;