<?php
namespace Rehike\Model\Rehike\Version;

use Rehike\i18n;

class MNightlyNotice
{
    public $text;
    public $tooltip;

    public function __construct()
    {
        $strings = i18n::getNamespace('rehike/version');

        $this -> text = $strings->nightly;
        $this -> tooltip = $strings->nightlyInfoTooltip;
    }
}