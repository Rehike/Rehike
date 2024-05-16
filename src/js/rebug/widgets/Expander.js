rebug.widgets.Expander = {};

rebug.widgets.Expander.onClick = function(elm, targetElement, noTargetCheck)
{
    noTargetCheck = noTargetCheck || false;

    if (rehike.class.has(elm, "rebug-expander-has-target") && !noTargetCheck)
    {
        return;
    }

    if (rehike.class.has(elm, "rebug-expander-collapsed"))
    {
        rehike.class.remove(elm, "rebug-expander-collapsed");
        rehike.class.add(elm, "rebug-expander-expanded");

        rehike.pubsub.publish("rebug-expander-toggled", {
            state: "opened",
            target: elm
        });
    }
    else
    {
        rehike.class.remove(elm, "rebug-expander-expanded");
        rehike.class.add(elm, "rebug-expander-collapsed");

        rehike.pubsub.publish("rebug-expander-toggled", {
            state: "closed",
            target: elm
        });
    }
};