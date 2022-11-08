<?php
namespace Rehike\Model\Results;
use Rehike\i18n;
use Rehike\Model\Common\Subscription\MSubscriptionActions;
use Rehike\Util\ExtractUtils;
use Rehike\TemplateFunctions;

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
     * @param object $data           From search response
     * @param object $paginatorInfo  Info for paginated buttons at the bottom
     */
    public static function bake($data, $paginatorInfo, $query) {
        $i18n = i18n::newNamespace("results");
        $i18n -> registerFromFolder("i18n/results");

        $response = (object) [];
        $contents = $data -> contents -> twoColumnSearchResultsRenderer -> primaryContents -> sectionListRenderer;
        $submenu = &$contents -> subMenu -> searchSubMenuRenderer;
        $submenu -> resultCountText = $i18n -> resultCount(number_format(self::getResultsCount($data)));

        $filterCrumbs = [];
        if (isset($submenu -> groups))
        foreach ($submenu -> groups as $group)
        if (isset($group -> searchFilterGroupRenderer))
        foreach($group -> searchFilterGroupRenderer -> filters as $filter)
        if (@$filter -> searchFilterRenderer -> status == "FILTER_STATUS_SELECTED" 
        && isset($filter -> searchFilterRenderer -> navigationEndpoint)) {
            $filterCrumbs[] = $filter -> searchFilterRenderer;
        }
        $submenu -> filterCrumbs = $filterCrumbs;

        if (count($filterCrumbs) > 0) {
            $submenu -> clearAll = (object) [
                "simpleText" => $i18n -> filtersClear,
                "navigationEndpoint" => (object) [
                    "commandMetadata" => (object) [
                        "webCommandMetadata" => (object) [
                            "url" => "/results?search_query=$query" 
                        ]
                    ]
                ]
            ];
        }

        for ($i = 0; $i < count($contents -> contents); $i++)
        if (isset($contents -> contents[$i] -> itemSectionRenderer))
        foreach($contents -> contents[$i] -> itemSectionRenderer -> contents as $item2)
        if (isset($item2 -> promotedSparklesTextSearchRenderer)) {
            array_splice($contents -> contents, $i, 1);
        }

        foreach ($contents -> contents as &$content)
        if (isset($content -> itemSectionRenderer))
        foreach($content -> itemSectionRenderer -> contents as &$item)
        if (isset($item -> channelRenderer)) {
            $channel = &$item -> channelRenderer;

            if (!isset($channel -> badges)) {
                $channel -> badges = [];
            }

            array_unshift($channel -> badges, (object) [
                "metadataBadgeRenderer" => (object) [
                    "label" => $i18n -> channelBadge,
                    "style" => "BADGE_STYLE_TYPE_SIMPLE"
                ]
            ]);

            if (isset($channel -> subscribeButton -> subscribeButtonRenderer)
            ||  isset($channel -> subscribeButton -> buttonRenderer)) {
                $channel -> subscriptionActions = MSubscriptionActions
                ::fromData(
                        $channel -> subscribeButton -> subscribeButtonRenderer,
                        ExtractUtils::isolateSubCnt(
                            TemplateFunctions::getText($channel -> subscriberCountText
                        )),
                        false
                );
            } else {
                $channel -> subscriptionActions = MSubscriptionActions
                ::buildMock(
                        ExtractUtils::isolateSubCnt(
                            TemplateFunctions::getText($channel -> subscriberCountText
                        )),
                        false
                );
            }
        }

        $response -> content = $contents;

        // Paginator
        if (isset($paginatorInfo -> pagesCount) && $paginatorInfo -> pagesCount > 1) {
            $response -> paginator = new MPaginator($paginatorInfo);
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