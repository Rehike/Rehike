/**
 * @fileoverview Provides tab behaviours for Rebug.
 * 
 * @author The Rehike Maintainers
 */

rebug.tabs = {};

/**
 * Get the currently active tab.
 * 
 * @return {Element}
 */
rebug.tabs.getCurrent = function()
{
    return document.querySelector("#rebug-tabs-switcher .rebug-tab-selected");
};

/**
 * Get the currently active tab's content.
 * 
 * @return {Element}
 */
rebug.tabs.getCurrentContent = function()
{
    return document.querySelector("#rebug-tabbed-content-wrapper .rebug-tab-selected") || null;
};

/**
 * Get the currently active tab's ID.
 * 
 * @return {string}
 */
rebug.tabs.getCurrentId = function()
{
    var tab = rebug.tabs.getCurrent();

    if (!tab)
        return "";

    return tab.getAttribute("data-tab-target") || "";
};

/**
 * Get a tab's element by its ID.
 * 
 * @param {string} id
 * @return {Element}
 */
rebug.tabs.getTabById = function(id)
{
    return document.querySelector("#rebug-tabs-switcher [data-tab-target='" + id + "']") || null;
};

/**
 * Get a tab's title by its ID.
 * 
 * @param {string} id
 * @return {string}
 */
rebug.tabs.getTitleById = function(id)
{
    return rebug.tabs.getTabById(id).innerHTML;
};

/**
 * Get a tab's content by its ID.
 * 
 * @param {string} id
 * @return {Element}
 */
rebug.tabs.getContentById = function(id)
{
    return document.querySelector("#rebug-tabbed-content-wrapper [data-tab-id='" + id + "']");
};

/**
 * Switch to a tab.
 * 
 * @param {string} tabId 
 */
rebug.tabs.switchTab = function(tabId)
{
    rebug.tabs._clearSelectedTab();

    var tab = rebug.tabs.getTabById(tabId);
    var content = rebug.tabs.getContentById(tabId);

    if (tab)
    {
        rehike.class.add(tab, "rebug-tab-selected");
    }
    
    if (content)
    {
        rehike.class.add(content, "rebug-tab-selected");
    }

    rehike.pubsub.publish("rebug-tab-switch");
};

/**
 * Clear the currently selected tab.
 * 
 * @private
 */
rebug.tabs._clearSelectedTab = function()
{
    var tab = rebug.tabs.getCurrent();
    var content = rebug.tabs.getCurrentContent();

    if (tab)
    {
        rehike.class.remove(tab, "rebug-tab-selected");
    }
    
    if (content)
    {
        rehike.class.remove(content, "rebug-tab-selected");
    }
};