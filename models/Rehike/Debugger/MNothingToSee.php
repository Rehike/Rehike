<?php
namespace Rehike\Model\Rehike\Debugger;

use Rehike\i18n\i18n;

/**
 * Implements the Rehike Debugger "Nothing to See" renderer.
 * 
 * This is a general placeholder that can be used when there's no content
 * to display.
 * 
 * @author The Rehike Developers
 */
class MNothingToSee
{
    public $text;

    public function __construct()
    {
        $i18n = i18n::getNamespace("rehike/debugger");

        $this->text = $i18n->get("nothingToSee");
    }
}