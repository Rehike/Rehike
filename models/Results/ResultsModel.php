<?php
namespace Rehike\Model\Results;

class ResultsModel {
    static $yt;
    static $response;

    // Shorthand data references
    static $filterHeader = null;
    static $results = null;
    static $pageFooter = null;

    /**
     * Bake a results page model
     *
     * @param object $yt (global state)
     * @param object $data from search response
     */
    public static function bake(&$yt, $data, $paginatorInfo) {
        $response = (object) [];
        $contents = $data -> contents -> twoColumnSearchResultsRenderer -> primaryContents -> sectionListRenderer;
        $filterHeader = $contents -> subMenu -> searchSubMenuRenderer -> groups;
        $resultCount = $data -> estimatedResults;
        // remove ad because it fucks up everything
        if (isset($contents -> contents[0] -> itemSectionRenderer -> contents[0] -> promotedSparklesWebRenderer)) {
            $contents -> contents = array_splice($contents -> contents, 1);
        }
        $results = $contents -> contents[0] -> itemSectionRenderer -> contents;

        if (!is_null($filterHeader)) $response -> header = new MFiltersHeader($filterHeader, $resultCount);
        $response -> results = $results ?? null;
        $response -> data = $data;

        // paginator
        if (isset($paginatorInfo->pagesCount) && $paginatorInfo->pagesCount > 1) {
            $response->paginator = new MPaginator($paginatorInfo);
        }

        return $response;
    }

    public static function getResultsCount($data) {
        if (isset($data->estimatedResults)) {
            return (int) $data->estimatedResults;
        } else {
            return 0;
        }
    }
}