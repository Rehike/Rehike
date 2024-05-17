rebug.widgets.Tab = {};

rebug.widgets.Tab.onClick = function(elm)
{
    var a;
    if (a = elm.getAttribute("data-tab-target"))
    {
        rebug.tabs.switchTab(a);
    }
};