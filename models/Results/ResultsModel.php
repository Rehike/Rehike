<?php
namespace Rehike\Model\Results;
use Rehike\i18n\i18n;
use Rehike\Model\Common\Subscription\MSubscriptionActions;
use Rehike\Util\ExtractUtils;
use Rehike\Model\Browse\InnertubeBrowseConverter;

use Rehike\ConfigManager\Config;

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
    public static function bake($data, $paginatorInfo, $query)
    {
        $i18n = i18n::getNamespace("results");

        $response = (object) [];
        $contents = $data->contents->twoColumnSearchResultsRenderer->primaryContents->sectionListRenderer;
        $submenu = &$contents->subMenu->searchSubMenuRenderer;

        if ($filters = @$data->header->searchHeaderRenderer->searchFilterButton->buttonRenderer)
        {
            $submenu->button = (object) [
                "toggleButtonRenderer" => $filters
            ];
    
            $submenu->groups = $filters->command->openPopupAction->popup->searchFilterOptionsDialogRenderer->groups;
    
            $filters = &$submenu->button->toggleButtonRenderer;
    
            unset($filters->command);
            $filters->defaultText = $filters->text;
            unset($filters->text);
        }
        
        $submenu->resultCountText = self::getResultsCount($data) > 1
            ? $i18n->format("resultCountPlural", $i18n->formatNumber(self::getResultsCount($data)))
            : $i18n->format("resultCountSingular", $i18n->formatNumber(self::getResultsCount($data)));

        $filterCrumbs = [];
        if (isset($submenu->groups))
        foreach ($submenu->groups as $group)
        if (isset($group->searchFilterGroupRenderer))
        foreach($group->searchFilterGroupRenderer->filters as $filter)
        if (@$filter->searchFilterRenderer->status == "FILTER_STATUS_SELECTED" 
        && isset($filter->searchFilterRenderer->navigationEndpoint))
        {
            $filterCrumbs[] = $filter->searchFilterRenderer;
        }
        $submenu->filterCrumbs = $filterCrumbs;

        if (count($filterCrumbs) > 0)
        {
            $submenu->clearAll = (object) [
                "simpleText" => $i18n->get("filtersClear"),
                "navigationEndpoint" => (object) [
                    "commandMetadata" => (object) [
                        "webCommandMetadata" => (object) [
                            "url" => "/results?search_query=$query" 
                        ]
                    ]
                ]
            ];
        }

        for ($i = 0; $i < count($contents->contents); $i++)
        if (isset($contents->contents[$i] ->itemSectionRenderer))
        foreach($contents->contents[$i] ->itemSectionRenderer->contents as $item2)
        if (isset($item2->promotedSparklesTextSearchRenderer))
        {
            array_splice($contents->contents, $i, 1);
        }

        $response->content = InnerTubeBrowseConverter::sectionListRenderer($contents, [
            "channelRendererUnbrandedSubscribeButton" => true,
            "channelRendererChannelBadge" => true,
            "searchMetadataOrder" => false == Config::getConfigProp("appearance.swapSearchViewsAndDate")
        ]);

        // Paginator
        if (isset($paginatorInfo->pagesCount) && $paginatorInfo->pagesCount > 1)
        {
            $response->paginator = new MPaginator($paginatorInfo);
        }

        return $response;
    }

    public static function getResultsCount($data)
    {
        if (isset($data->estimatedResults))
        {
            return (int) $data->estimatedResults;
        }
        else
        {
            return 0;
        }
    }
}