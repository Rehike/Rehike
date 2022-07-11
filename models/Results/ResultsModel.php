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
    public static function bake(&$yt, $data) {
        $response = (object) [];
        $contents = $data -> contents -> twoColumnSearchResultsRenderer -> primaryContents -> sectionListRenderer;
        $filterHeader = $contents -> subMenu -> searchSubMenuRenderer -> groups;
        $resultCount = $data -> estimatedResults;
        $results = $contents -> contents[0] -> itemSectionRenderer -> contents;

        if (!is_null($filterHeader)) $response -> header = new MFiltersHeader($filterHeader, $resultCount);
        $response -> results = $results ?? null;
        $response -> data = $data;

        return $response;
    }
}