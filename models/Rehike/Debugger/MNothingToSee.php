<?php
namespace Rehike\Model\Rehike\Debugger;

use Rehike\i18n;

/**
 * Implements the Rehike Debugger "Nothing to See" renderer.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Developers
 */
class MNothingToSee
{
    public $text;

    public function __construct()
    {
        $i18n = &i18n::getNamespace("rebug");

        $this->text = $i18n->nothingToSee;
    }
}