<?php
namespace Rehike\Model\Rehike\Panel;

/**
 * Data model for the Rehike panel pages.
 * 
 * This is a shared base for GUI pages accessible through /rehike/
 * URLs, which includes Config and Version.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */
class RehikePanelPage
{
    /**
     * The root of the sidebar renderer.
     */
    public Sidebar $sidebar;

    public function __construct(string $selectedTabId = "")
    {
        $this->sidebar = new Sidebar($selectedTabId);
    }
}