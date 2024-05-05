<?php
namespace Rehike\Model\Results;

use Rehike\Controller\ResultsController;
use Rehike\i18n\i18n;

/**
 * Implements the search paginator model.
 * 
 * @author Daylin Cooper <dcoop2004@gmail.com>
 * @author The Rehike Maintainers
 */
class MPaginator
{
    public const VISIBLE_PAGE_LINKS = 7;

    /**
     * The current page number.
     */
    public int $pageNumber;
    
    /**
     * All of the displayed items in the paginator row.
     * 
     * @var MPaginatorButton[]
     */
    public array $items;
    
    public bool $hasBackButton = false;
    public bool $hasNextButton = false;

    public function __construct($paginatorInfo)
    {
        $this->pageNumber = $paginatorInfo->pageNumber ?? 1;
        $pagesCount = $paginatorInfo->pagesCount ?? 1;

        $this->items = self::getVisiblePages($this->pageNumber, $pagesCount);

        if ($this->pageNumber > $pagesCount)
        {
            $this->hasNextButton = true;
        }

        if ($this->pageNumber > 1)
        {
            $this->hasBackButton = true;
        }
    }

    public static function getVisiblePages(int $pageNumber, int $pagesCount): array
    {
        // The range for pagination is 1 page before the current, and up to 5 after it.
        $lBound = 1;
        $rBound = $pagesCount;

        $displayedPages = [];
        if ($pagesCount > self::VISIBLE_PAGE_LINKS)
        {
            $rangeOffset = 2;
            $range = $pageNumber + self::VISIBLE_PAGE_LINKS;

            while ($range < $lBound)
                $range++;

            while ($range > $rBound)
                $range--;

            while ($pageNumber - $rangeOffset < $lBound)
                $rangeOffset--;

            for ($i = $pageNumber - $rangeOffset; $i < $range - $rangeOffset; $i++)
            {
                $displayedPages[] = (int) $i;
            }
        }
        else
        {
            for ($i = 1; $i < $pagesCount; $i++)
            {
                $displayedPages[] = (int) $i;
            }
        }

        $response = [];
        $strings = i18n::getNamespace("results");

        if ($pageNumber > 1)
        {
            $response[] = new MPaginatorButton(
                text: $strings->get("pagePrev"), 
                selected: false, 
                url: ResultsController::getPageParamUrl(ResultsController::$param, $pageNumber - 1)
            );
        }

        for ($i = 0, $j = count($displayedPages); $i < $j; $i++)
        {
            $text = $displayedPages[$i];
            $selected = $displayedPages[$i] == $pageNumber;
            $url = ResultsController::getPageParamUrl(ResultsController::$param, $displayedPages[$i]);
            
            $response[] = new MPaginatorButton($text, $selected, $url);
        }

        if ($pageNumber < $pagesCount)
        {
            $response[] = new MPaginatorButton(
                text: $strings->get("pageNext"), 
                selected: false, 
                url: ResultsController::getPageParamUrl(ResultsController::$param, $pageNumber + 1)
            );
        }

        return $response;
    }
}