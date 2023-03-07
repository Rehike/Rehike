<?php
namespace Rehike\Model\Channels;

use Rehike\Model\Channels\Channels4\MChannelAboutMetadata;
use Rehike\Model\Channels\Channels4\BrandedPageV2\MSubnav;
use Rehike\Model\Channels\Channels4\Sidebar\MRelatedChannels;
use Rehike\Model\Browse\InnertubeBrowseConverter;
use Rehike\Model\Channels\Channels4\MSubConfirmationDialog;
use Rehike\Model\Common\MAlert;

class Channels4Model
{
    private static $baseUrl;
    private static $currentTab = null;
    public static $yt;

    private static $videosSort;

    private static $subscriptionCount = "";

    public static $showSort;

    public static function bake(&$yt, $data, $sidebarData = null)
    {
        self::$yt = &$yt;

        self::$videosSort = $yt->videosSort ?? null;
        self::$showSort = $yt->showSort ?? false;

        // Declare the response array.
        $response = [];

        if ($header = @$data->header->c4TabbedHeaderRenderer)
        {
            $response += ["header" => new Channels4\MHeader($header, self::getBaseUrl())];
        }
        elseif ($header = @$data->header->carouselHeaderRenderer)
        {
            $response += ["header" => new Channels4\MCarouselHeader($header, self::getBaseUrl())];
        }

        if (isset($response["header"]) && isset($data->contents->twoColumnBrowseResultsRenderer->tabs))
        {
            // Init appbar
            $yt->appbar->addNav();

            // Also add the owner info we just got to the appbar
            $yt->appbar->nav->addOwner(
                $response["header"]->getTitle(),
                self::getBaseUrl(),
                $response["header"]->thumbnail ?? "",
            );
        }

        $currentTabContents = null;

        if ($alerts = @$data->alerts)
        {
            $response += ["alerts" => []];
            foreach ($alerts as $alert)
            {
                $alert = $alert->alertWithButtonRenderer
                  ?? $alert->alertRenderer
                  ?? null;
                $response["alerts"][] = MAlert::fromData($alert);
            }
        }

        // If we have twoColumnBrowseResultsRenderer with tabs,
        // process them (add navigation and store a reference)
        if ($tabs = @$data->contents->twoColumnBrowseResultsRenderer->tabs)
        {
            if (isset($response["header"]))
            {
                /** @var object */
                $videosTab = null;

                // Splice "live" tab as this should be cascaded into videos.
                for ($i = 0; $i < count($tabs); $i++)
                {
                    if (isset($tabs[$i]->tabRenderer))
                    {
                        // Do NOT call this $tab. It will break the logic for
                        // god only fucking knows why and you'll get some sorta
                        // duplicate tab renderer.
                        $tabR = &$tabs[$i];

                        $tabEndpoint = $tabR->tabRenderer->endpoint->commandMetadata->webCommandMetadata->url ?? null;

                        if (!is_null($tabEndpoint))
                        {
                            if (stripos($tabEndpoint, "/videos"))
                            {
                                $videosTab = &$tabR;
                            }
                            else if (stripos($tabEndpoint, "/streams") || stripos($tabEndpoint, "/shorts"))
                            {
                                $tabR->hidden = true;

                                if (@$tabR->tabRenderer->selected) $videosTab->tabRenderer->selected = true;
                            }
                        }
                    }
                }
                
                $response["header"]->addTabs($tabs, ($yt->partiallySelectTabs ?? false));

                foreach ($tabs as $tab) if (@$tab->tabRenderer)
                {
                    $tabEndpoint = $tab->tabRenderer->endpoint->commandMetadata->webCommandMetadata->url ?? null;

                    if (!is_null($tabEndpoint))
                    {
                        if (!@$tab->hidden && isset($yt->appbar->nav))
                        {
                            $yt->appbar->nav->addItem(
                                $tab->tabRenderer->title,
                                $tabEndpoint,
                                @$tab->tabRenderer->status
                            );
                        }
                    }

                    if (@$tab->tabRenderer->status > 0
                    ||  @$tab->tabRenderer->selected)
                    {
                        $currentTabContents = &$tab->tabRenderer->content;
                    }
                }
                elseif (@$tab->expandableTabRenderer)
                {
                    if (@$tab->expandableTabRenderer->selected) {
                        $currentTabContents = &$tab->expandableTabRenderer->content;
                    }
                }

                if (isset($yt->appbar->nav->items[0]))
                {
                    $yt->appbar->nav->items[0]->title = $response["header"]->getTitle();
                }
            }
        }

        // If we have a header, set the page title from it.
        if (isset($response["header"]))
        {
            $response += [
                "title" => $response["header"]->getTitle()
            ];

            // Also global subscription count for about
            self::$subscriptionCount = $response["header"]->getSubscriptionCount();
        }

        // If we have a sidebar, go through it
        if ($sidebarShelves = @$sidebarData->contents->twoColumnBrowseResultsRenderer->tabs[0]
            ->tabRenderer->content->sectionListRenderer->contents)
        {
            $featuredData = null;

            if ("featured" == self::$currentTab)
            {
                $featuredData = &$data->contents->twoColumnBrowseResultsRenderer->tabs[0]
                    ->tabRenderer->content->sectionListRenderer->contents
                ;
            }

            $sidebarData = self::getSidebarData($sidebarShelves, $featuredData);

            if ($sidebarData)
            {
                self::initSecondaryColumn($response);
                $response["secondaryContent"]->items = $sidebarData;
            }
        }

        if (@$yt->subConfirmation && !is_null($response["header"]->title))
        {
            $response += ["subConfirmationDialog" => new MSubConfirmationDialog($response["header"])];
        }

        $response += ["content" => self::getTabContents($currentTabContents)];

        $response += ["baseUrl" => self::$baseUrl];

        // Send the response array
        return (object)$response;
    }
    
    public static function initSecondaryColumn(&$response)
    {
        if (!isset($response["secondaryContent"]))
        {
            $response += [
                "secondaryContent" => (object)[
                    "items" => []
                ]
            ];
        }
    }

    public static function getTabContents($content)
    {
        if (isset($content->sectionListRenderer->contents[0]->itemSectionRenderer->contents[0]->channelAboutFullMetadataRenderer))
        {
            return (object)[
                "channelAboutMetadataRenderer" => 
                    new MChannelAboutMetadata(
                        self::$subscriptionCount,
                        $content->sectionListRenderer->contents[0]->itemSectionRenderer->contents[0]->channelAboutFullMetadataRenderer
                    )
            ];
        }
        else if ($a = @$content->sectionListRenderer->contents[0]->itemSectionRenderer->contents[0]->gridRenderer)
        {
        return self::handleGridTab($a, $content);
        }
        else if ($a = @$content->richGridRenderer)
        {
            return self::handleGridTab(InnertubeBrowseConverter::richGridRenderer($a), $content, self::$videosSort, true);
        }
        else if (($a = @$content->sectionListRenderer->contents[0]->itemSectionRenderer) && (isset($a->contents[0]->backstagePostThreadRenderer)))
        {
            return self::handleBackstage($a);
        }
        else if ($a = @$content->sectionListRenderer)
        {
            if ($submenu = @$a->subMenu->channelSubMenuRenderer)
            {
                $brandedPageV2SubnavRenderer = MSubnav::fromData($submenu);
                unset($a->subMenu);
            }

            return (object) [
                "sectionListRenderer" => InnertubeBrowseConverter::sectionListRenderer($a, [
                    "channelRendererUnbrandedSubscribeButton" => true
                ]),
                "brandedPageV2SubnavRenderer" => $brandedPageV2SubnavRenderer ?? null
            ];
        }
        else
        {
            return $content;
        }
    }

    public static function handleGridTab($data, $parentTab, $sort = null, $rich = false)
    {
        $currentTab = self::$currentTab;

        $response = [];

        switch ($currentTab)
        {
            case "videos":
            case "streams":
                if ($subnav = @$parentTab->sectionListRenderer->subMenu->channelSubMenuRenderer || $rich)
                {
                    $subnav = $subnav ?? null;

                    $response += [
                        "brandedPageV2SubnavRenderer" => MSubnav::bakeVideos()
                    ];
                }
                break;
            default:
                if ($subnav = @$parentTab->sectionListRenderer->subMenu->channelSubMenuRenderer)
                {
                    $subnav = $subnav ?? null;

                    $response += [
                        "brandedPageV2SubnavRenderer" => MSubnav::fromData($subnav)
                    ];
                }
        }

        if ($rich && isset($_GET["flow"]) && "list" == $_GET["flow"])
        {
            $response += [
                "items" => $data->items
            ];
        }
        else
        {
            $response += [
                "browseContentGridRenderer" => InnertubeBrowseConverter::gridRenderer($data, [
                    "channelRendererUnbrandedSubscribeButton" => true
                ])
            ];
        }

        return (object)$response;
    }

    public static function handleBackstage($data)
    {
        $response = [
            "backstageRenderer" => [
                "comments" => (object)[
                    "commentThreads" => []
                ]
            ]
        ];

        foreach ($data->contents as $item)
        {
            $content = $item->backstagePostThreadRenderer->post->backstagePostRenderer;

            $response["backstageRenderer"]["comments"]->commentThreads[] = (object)[
                "commentThreadRenderer" => (object)["commentRenderer" => $content]
            ];
        }

        return (object)$response;
    }

    public static function getSidebarData($shelves, &$featuredData)
    {
        $channelsShelves = [];

        // Find the first channel shelf
        foreach ($shelves as $i => $shelf)
        {
            $shelf = @$shelf->itemSectionRenderer->contents[0]
                ->shelfRenderer;

            if ($channelItem = @$shelf->content->horizontalListRenderer
                ->items[0]->gridChannelRenderer
            ||
                $channelItem = @$shelf->content->expandedShelfContentsRenderer
                ->items[0]->channelRenderer
            )
            {
                $channelsShelves[] = $shelf;

                if (null != $featuredData)
                {
                    //array_splice($featuredData, $i, 1);
                    unset($featuredData[$i]);
                }
            }
        }

        if (0 < count($channelsShelves))
        {
            $shelves = [];
            foreach ($channelsShelves as $shelf) {
                $shelves[] = (object) [
                    "relatedChannelsRenderer" =>
                    MRelatedChannels::fromShelf($shelf)
                ];
            }

            return $shelves;
        }

        return null;
    }

    public static function registerCurrentTab($currentTab)
    {
        self::$currentTab = $currentTab;
    }

    public static function getCurrentTab()
    {
        return self::$currentTab;
    }

    public static function registerBaseUrl($baseUrl)
    {
        self::$baseUrl = $baseUrl;
    }

    public static function getBaseUrl()
    {
        return self::$baseUrl;
    }

    public static function getVideosSort() {
        return self::$videosSort;
    }
}
