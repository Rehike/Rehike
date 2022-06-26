<?php
namespace Rehike\Model\Rehike\Debugger;

/**
 * Implements the tab content wrapper.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Developers
 */
class MTabContent
{
    use RichContent;

    public static function createTab($title, $id, $selected = false)
    {
        $me = new static();

        return new MTab($title, $id, $me, $selected);
    }
}