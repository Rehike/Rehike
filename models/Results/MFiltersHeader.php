<?php
namespace Rehike\Model\Results;

class MFiltersHeader {

    /** @var object[] */
    public $filters;

    /**
     *  List of selected filters to appear
     *  in the filter header.
     * 
     *  @var array[]
     */
    public $headerFilters;

    /** @var boolean[] */
    public $hasSelectedFilter;

    /** @var string[] */
    public $resultCount;

    public function __construct($header, $count) {
        $resultCount = @$count ?? null;
        if ((int) $resultCount > 1) $this -> resultCount = "About " . number_format((int) $resultCount) . " results";

        $tempFilters = [];

        for ($i = 0; $i < count($header); $i++) {
            // sort behaves differently
            // this is not denoted in innertube
            // probably handled by polymer js
            if ($i == count($header) - 1) $header[$i] -> searchFilterGroupRenderer -> isSort = true;

            for ($k = 0; $k < count($header[$i] -> searchFilterGroupRenderer -> filters); $k++) {
                $status = $header[$i] -> searchFilterGroupRenderer -> filters[$k] -> searchFilterRenderer -> status ?? null;
                if ($status == "STATUS_FILTER_SELECTED") {
                    $this -> headerFilters[] = $header -> searchFilterGroupRenderer -> filters[$k] -> searchFilterRenderer;
                    $this -> hasSelectedFilter = true;
                }
            }

            $tempFilters[] = $header[$i];
        }

        $this -> filters = $tempFilters;
    }
}