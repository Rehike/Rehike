<?php
namespace Rehike\Model\Rehike\Debugger;

/**
 * Implements the tab wrapper. General use should use the
 * createTab method of a MTabContent child.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Developers
 */
class MTab
{
    /**
     * The title of a tab.
     * 
     * @var string
     */
    public $title;

    /**
     * A unique identifier for this tab.
     * 
     * @var string
     */
    public $id = "";

    /**
     * Determines if the tab should be selected by default.
     * 
     * @var bool
     */
    public $selected = false;

    /**
     * Stores the content of the tab.
     * 
     * @var MTabContent
     */
    public $content;

    /**
     * Construct a new tab wrapper.
     * 
     * @param string      $title    Title of the tab
     * @param string      $id       Unique ID for the tab
     * @param MTabContent $content  Content of the tab
     * @param bool        $selected Whether or not to select the tab.
     */
    public function __construct($title, $id, $content, $selected)
    {
        $this->title = $title;
        $this->id = $id;
        $this->content = $content;
        $this->selected = $selected;
    }
}