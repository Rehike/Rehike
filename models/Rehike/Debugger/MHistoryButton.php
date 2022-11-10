<?php
namespace Rehike\Model\Rehike\Debugger;

use Rehike\i18n;

use Rehike\Model\Common\MButton;

class MHistoryButton extends MButton
{
    public $class = ["rebug-history-button"];

    public $attributes = [];

    public function __construct()
    {
        $i18n = i18n::getNamespace("rebug");

        $this->setText($i18n->historyCurrent);
        
        $this->attributes = [
            "label-current-page" => $i18n->historyCurrent,
            "label-previous-page" => $i18n->historyPrevious,
            "label-n-pages-ago" => $i18n->historyPagesAgo,
            "label-unavailable" => $i18n->historyUnavailable
        ];

        $this->hasArrow = true;
        $this->disabled = true;
    }
}