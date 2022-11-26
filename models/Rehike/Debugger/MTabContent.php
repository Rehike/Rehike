<?php
namespace Rehike\Model\Rehike\Debugger;

/**
 * Implements the tab content wrapper.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Developers
 */
abstract class MTabContent
{
    use RichContent;

    /**
     * Determines if the tab should be affected by the JS history manager
     * on the client-side.
     * 
     * Generally, this should be true for tabs that update via HTML and false
     * for tabs that update via other methods.
     * 
     * @var bool
     */
    public $enableJsHistory = true;

    /**
     * Create a tab model and automatically wrap it. This should be the general
     * way of creating a tab.
     * 
     * @param string $title    Title of the tab.
     * @param string $id       Unique ID for the tab.
     * @param bool   $selected Whether or not the tab is selected.
     */
    public static function createTab($title, $id, $selected = false)
    {
        $me = new static();

        return new MTab($title, $id, $me, $selected);
    }
}