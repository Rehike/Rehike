<?php
namespace Rehike\Model\Rehike\Debugger;

use Rehike\i18n\i18n;

use Rehike\Model\Common\MButton;

/**
 * Implements the history button for the debugger.
 * 
 * This is used by the client to navigate previous pages' states, which are
 * stored in memory on the client-side.
 * 
 * This feature is currently a work in progress.
 * 
 * @author The Rehike Maintainers
 */
class MHistoryButton extends MButton
{
    public $class = ["rebug-history-button"];

    public $attributes = [];

    public function __construct()
    {
        $i18n = i18n::getNamespace("rehike/debugger");

        $this->setText($i18n->get("historyCurrent"));
        
        /*
         * HTML attributes used for client-side string generation.
         */
        $this->attributes = [
            "label-current-page" => $i18n->get("historyCurrent"),
            "label-previous-page" => $i18n->get("historyPrevious"),
            "label-n-pages-ago" => $i18n->get("historyPagesAgo"),
            "label-unavailable" => $i18n->get("historyUnavailable")
        ];

        $this->hasArrow = true;
        $this->disabled = true;
    }
}