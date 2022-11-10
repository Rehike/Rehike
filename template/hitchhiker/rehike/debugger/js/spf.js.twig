/**
 * @fileoverview Implements SPF handlers for Rebug.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */

rebug.spf = {};

rebug.spf.events = {};

/**
 * Handle any SPF response.
 */
rebug.spf.events.onSpfDone = function(e)
{
    if (e.detail && e.detail.response && e.detail.response.rebug_data)
    {
        var data = e.detail.response.rebug_data;

        rebug.spf.tryUpdateOpenButton(data.openButton);

        rebug.history.pushAndSwitch(
            data,
            e.detail.url,
            e.detail.response.title
        );
    }
};

/**
 * Attempt to replace the open button if it exists.
 * 
 * @param {string} newHtml 
 */
rebug.spf.tryUpdateOpenButton = function(newHtml)
{
    var el = null;

    if (el = document.getElementById("rebug-open-button-container"))
    {
        rebug.spf.updateOpenButton(newHtml, el);
    }
};

/**
 * Update the open button HTML to reflect changed state.
 * 
 * @param {string} newHtml HTML to replace the button with.
 * @param {Element} openButton Open button container element.
 */
rebug.spf.updateOpenButton = function(newHtml, openButton)
{
    openButton.innerHTML = newHtml;
};