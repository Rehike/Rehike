<?php
namespace Rehike\Model\Results;

use Rehike\Controller\ResultsController;
use Rehike\i18n;
use Rehike\Model\Common\MButton;

/**
 * Implements the search paginator model.
 * 
 * @author Daylin Cooper <dcoop2004@gmail.com>
 * @author The Rehike Maintainers
 */
class MPaginator {
    const VISIBLE_PAGE_LINKS = 7;

    public $pageNumber;
    public $items;
    public $hasBackButton = false;
    public $hasNextButton = false;

    public function __construct($paginatorInfo) {
        $this->pageNumber = $paginatorInfo->pageNumber ?? 1;
        $pagesCount = $paginatorInfo->pagesCount ?? 1;

        $this->items = self::getVisiblePages($this->pageNumber, $pagesCount);

        if ($this->pageNumber > $pagesCount) {
            $this->hasNextButton = true;
        }

        if ($this->pageNumber > 1) {
            $this->hasBackButton = true;
        }
    }

    public static function getVisiblePages($pageNumber, $pagesCount) {
        $lBound = 1;
        $rBound = $pagesCount;

        $displayedPages = [];
        if ($pagesCount > self::VISIBLE_PAGE_LINKS) {
            $rangeOffset = 2;
            $range = $pageNumber + self::VISIBLE_PAGE_LINKS;

            while ($range < $lBound) $range++;
            while ($range > $rBound) $range--;
            while ($pageNumber - $rangeOffset < $lBound) $rangeOffset--;

            for ($i = $pageNumber - $rangeOffset; $i < $range - $rangeOffset; $i++) {
                $displayedPages[] = (int) $i;
            }
        } else {
            for ($i = 1; $i < $pagesCount; $i++) {
                $displayedPages[] = (int) $i;
            }
        }

        $response = [];
        $strings = i18n::getNamespace("results");

        if ($pageNumber > 1) {
            $response[] = new MPaginatorButton($strings->pagePrev, false, ResultsController::getPageParamUrl(ResultsController::$param, $pageNumber - 1));
        }

        for ($i = 0, $j = count($displayedPages); $i < $j; $i++) {
            $text = $displayedPages[$i];
            $selected = $displayedPages[$i] == $pageNumber;
            $url = ResultsController::getPageParamUrl(ResultsController::$param, $displayedPages[$i]);
            
            $response[] = new MPaginatorButton($text, $selected, $url);
        }

        if ($pageNumber < $pagesCount) {
            $response[] = new MPaginatorButton($strings->pageNext, false, ResultsController::getPageParamUrl(ResultsController::$param, $pageNumber + 1));
        }

        return $response;
    }
}

class MPaginatorButton extends MButton {
    public function __construct($text, $selected, $url) {
        $this->setText($text);
        
        if ($selected) {
            $this->customAttributes["disabled"] = "True";
            $this->attributes["redirect-url"] = $url;
        } else {
            $this->navigationEndpoint = (object) [
                "commandMetadata" => (object) [
                    "webCommandMetadata" => (object) [
                        "url" => $url
                    ]
                ]
            ];
        }
    }
}