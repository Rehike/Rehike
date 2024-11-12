<?php
namespace Rehike\Model\Rehike\Extensions;

use Rehike\Model\Rehike\Panel\RehikePanelPage;

/**
 * Page model for the extensions page.
 * 
 * @author Isabella Lulamoon <kawapure@gmail.com>
 * @author The Rehike Maintainers
 */
class ExtensionsPageModel extends RehikePanelPage
{
    public function __construct()
    {
        parent::__construct(selectedTabId: "extensions");
    }
}