<?php
namespace Rehike\Model\Results;
use Rehike\i18n\i18n;
use Rehike\Model\Browse\InnertubeBrowseConverter;

use Rehike\ConfigManager\Config;

/**
 * Model for the search results page.
 * 
 * @author Aubrey Pankow <aubyomori@gmail.com>
 * @author The Rehike Maintainers
 */
class ResultsModel
{
    /**
     * Bake a results page model
     *
     * @param object $data           From search response
     * @param object $paginatorInfo  Info for paginated buttons at the bottom
     * @param object $query          The search query that was entered in the searchbox
     * @param array  $dapidata       Data API data for channel video counts
     */
    public static function bake(object $data, object $paginatorInfo, string $query, array $dapidata = []): object
    {
        $i18n = i18n::getNamespace("results");
        $gi18n = i18n::getNamespace("global");

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
        if (isset($contents->contents[$i]->itemSectionRenderer))
        foreach($contents->contents[$i]->itemSectionRenderer->contents as $item2)
        if (isset($item2->promotedSparklesTextSearchRenderer))
        {
            array_splice($contents->contents, $i, 1);
        }

        if (count($dapidata) > 0)
        foreach ($contents->contents as &$data)
        {
            if (isset($data->itemSectionRenderer->contents))
            foreach ($data->itemSectionRenderer->contents as &$cdata)
            {
                if ($cr = @$cdata->channelRenderer)
                {
                    if (isset($dapidata[$cr->channelId]))
                    {
                        /* the monkeys put subscriber count in video count text */
                        $cr->subscriberCountText = $cr->videoCountText ?? null;
                        $videos = (int)$dapidata[$cr->channelId]->videoCount ?? 0;
                        
                        $text = "";

                        if ($videos == 0)
                        {
                            $text = $gi18n->get("videoCountNone");
                        }
                        else if ($videos == 1)
                        {
                            $text = $gi18n->get("videoCountSingular");
                        }
                        else
                        {
                            $text = $gi18n->format("videoCountPlural", $gi18n->formatNumber($videos));
                        }

                        $cr->videoCountText = (object) [
                            "simpleText" => $text
                        ];
                    }
                }
            }
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

    public static function getResultsCount(object $data): int
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