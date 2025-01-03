/**
 * @fileoverview Shared panel page scripts.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 */

(function(){

/*{{ "*"~"/" }}

 {######################################################
 #                     IMPORTS.                        #
 ######################################################}

//*/

rehike.panel = rehike.panel || {};

/**
 * Handles the click events sent to the "Disable Rehike" button.
 */
rehike.panel.onClickDisableRehikeButton_ = function() {
    var button = document.querySelector(".rehike-config-disable-rehike-button");
    var firstTimeOpened = false;
    var dialog;

    var isEnableButton = 
        button.getAttribute("data-disable-rehike-action") == "enable";

    if (button._rhDialog)
    {
        dialog = button._rhDialog;
    }
    else
    {
        firstTimeOpened = true;
        dialog = button.parentNode.querySelector(".rehike-dialog");
    }

    if (!button)
    {
        return;
    }

    if (isEnableButton || !dialog)
    {
        // If the dialog is unavailable, then just perform the action.
        // Also performed if the button is to enable Rehike.
        rehike.panel.disableRehike_();
        return;
    }

    if (firstTimeOpened)
    {
        button._rhDialog = dialog;
        document.body.appendChild(dialog);
    }

    var dialogController = new rehike.Dialog(dialog, {
        initialState: "content",
        buttonHandlerCb: rehike.panel.disableRehikeDialogProc_,
        preventOverflow: true
    });
    dialogController.open();
};

rehike.panel.disableRehikeDialogProc_ = function(defaultDialogProc, element, event)
{
    if (rehike.class.has(element, "confirm-button"))
    {
        rehike.panel.disableRehike_();
    }

    return defaultDialogProc(element, event);
};

/**
 * Performs the action of disabling Rehike.
 */
rehike.panel.disableRehike_ = function() {
    var button = document.querySelector(".rehike-config-disable-rehike-button");

    var type = button.getAttribute(
        "data-disable-rehike-action"
    ) || "disable";

    var jsonPayload = JSON.stringify({
        "hidden.disableRehike": type == "disable" ? true : false
    });

    var xhr = new XMLHttpRequest();
    xhr.open("POST", "/rehike/update_config");

    if ("enable" == type.toLowerCase())
    {
        xhr.onload = function() {
            location.reload();
        };
    }
    else
    {
        xhr.onload = function() {
            location.href = "/";
        };
    }

    xhr.setRequestHeader('Content-Type', 'application/json');
    xhr.send(jsonPayload);
};

/**
 * Initializes the panel scripts.
 */
rehike.panel.init = function()
{
    rehike.panel.LISTENER$onClickDisableRehikeButton = rehike.eventDelegate.add(
        "click", 
        "rehike-config-disable-rehike-button",
        rehike.panel.onClickDisableRehikeButton_
    );
};

/**
 * Cleans up resources allocated by this script.
 */
rehike.panel.unload = function()
{
    rehike.eventDelegate.remove(
        "click", 
        "rehike-config-disable-rehike-button",
        rehike.panel.LISTENER$onClickDisableRehikeButton
    );
};

rehike.script.registerPageSpecific(
    rehike.panel.init, 
    rehike.panel.unload
);

})();