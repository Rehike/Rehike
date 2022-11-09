<?php
namespace Rehike\Model\Channels;

use Rehike\Model\Channels\Channels4\MChannelAboutMetadata;
use Rehike\Model\Channels\Channels4\BrandedPageV2\MSubnav;
use Rehike\Model\Channels\Channels4\Sidebar\MRelatedChannels;
use Rehike\Model\Appbar\MAppbarNavItem;
use Rehike\Model\Browse\InnertubeBrowseConverter;
use Rehike\Request;

class Channels4Model
{
    private static $baseUrl;
    public static $currentTab = null;
    public static $yt;

    private static $subscriptionCount = "";

    public static function bake(&$yt, $data, $sidebarData = null)
    {
        self::$yt = &$yt;

        // Declare the response array.
        $response = [];

        // Init appbar
        $yt->appbar->addNav();

        if ($header = @$data->header->c4TabbedHeaderRenderer)
        {
            $response += ["header" => new Channels4\MHeader($header, self::getBaseUrl())];

            // Also add the owner info we just got to the appbar
            $yt->appbar->nav->addOwner(
                $response["header"]->getTitle(),
                self::getBaseUrl(),
                $response["header"]->thumbnail ?? "",
            );
        }

        $currentTabContents = null;

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

                        $tabEndpoint = $tabR->tabRenderer->endpoint->commandMetadata->webCommandMetadata->url;

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
                
                $response["header"]->addTabs($tabs, ($yt -> partiallySelectTabs ?? false));

                foreach ($tabs as $tab) if (@$tab -> tabRenderer)
                {
                    $tabEndpoint = $tab->tabRenderer->endpoint->commandMetadata->webCommandMetadata->url;

                    if (!@$tab->hidden)
                    {
                        $yt->appbar->nav->addItem(
                            $tab->tabRenderer->title,
                            $tabEndpoint,
                            @$tab->tabRenderer->status
                        );
                    }

                    if (@$tab->tabRenderer->status > 0)
                    {
                        $baseUrl = self::$baseUrl;
                        self::$currentTab = str_replace("$baseUrl/", "", $tabEndpoint);
                        $currentTabContents = &$tab->tabRenderer->content;
                    }
                }
                elseif (@$tab -> expandableTabRenderer)
                {
                    if (@$tab->expandableTabRenderer->selected) {
                        $baseUrl = self::$baseUrl;
                        self::$currentTab = str_replace("$baseUrl/", "", $tabEndpoint);
                        $currentTabContents = &$tab->expandableTabRenderer->content;
                    }
                }

                $yt->appbar->nav->items[0]->title = $response["header"]->getTitle();
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
                $response["secondaryContent"]->items[] = $sidebarData;
            }
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
            return self::handleGridTab(InnertubeBrowseConverter::richGridRenderer($a), $content, true);
        }
        else if (($b = @$content->sectionListRenderer->contents[0]->itemSectionRenderer) && ($c = @$b->contents[0]->backstagePostThreadRenderer))
        {
            return self::handleBackstage($b);
        }
        else if ($b = @$content->sectionListRenderer)
        {
            if ($submenu = @$b -> subMenu -> channelSubMenuRenderer)
            {
                $brandedPageV2SubnavRenderer = MSubnav::fromData($submenu);
                unset($b -> subMenu);
            }

            return (object) [
                "sectionListRenderer" => InnertubeBrowseConverter::sectionListRenderer($b, [
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

    public static function handleGridTab($data, $parentTab, $rich = false)
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
                        "brandedPageV2SubnavRenderer" => MSubnav::bakeVideos($subnav)
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
        $channelsShelf = null;

        // Find the first channel shelf
        foreach ($shelves as $i => $shelf)
        {
            $shelf = @$shelf->itemSectionRenderer->contents[0]
                ->shelfRenderer;

            if ($channelItem = @$shelf->content->horizontalListRenderer
                ->items[0]->gridChannelRenderer
            )
            {
                $channelsShelf = $shelf;

                if (null != $featuredData)
                {
                    array_splice($featuredData, $i, 1);
                }

                break;
            }
        }

        if (null != $channelsShelf)
        {
            $model = MRelatedChannels::fromShelf($channelsShelf);

            return (object)["relatedChannelsRenderer" => $model];
        }

        return null;
    }

    public static function registerBaseUrl($baseUrl)
    {
        self::$baseUrl = $baseUrl;
    }

    public static function getBaseUrl()
    {
        return self::$baseUrl;
    }
}
