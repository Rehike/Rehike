<?php
namespace Rehike\Model\Channels;

use Rehike\Model\Channels\Channels4\MChannelAboutMetadata;
use Rehike\Model\Channels\Channels4\BrandedPageV2\MSubnav;
use Rehike\Model\Channels\Channels4\Sidebar\MRelatedChannels;
use Rehike\Model\Browse\InnertubeBrowseConverter;
use Rehike\Model\Channels\Channels4\MSubConfirmationDialog;
use Rehike\Model\Common\MAlert;
use Rehike\Util\Base64Url;
use Rehike\Util\ParsingUtils;
use Rehike\i18n\i18n;
use Rehike\YtApp;
use Com\Youtube\Innertube\Helpers\VideosContinuationWrapper;
use Rehike\Model\Channels\Channels4\MHeader;

/**
 * Model bakery for the channels page.
 * 
 * @author Aubrey Pankow <aubyomori@gmail.com>
 * @author Isabella Lulamoon <kawapure@gmail.com>
 * @author The Rehike Maintainers
 */
class Channels4Model
{
    /**
     * The canonical base URL of the channel.
     * 
     * This is equivalent to the URL path without the tab name or any additional
     * arguments. For example:
     * 
     * /user/PewDiePie
     * /channel/UC-lHJZR3Gqxm24_Vd_AJ5Yw
     * /c/PewDiePie
     * /@PewDiePie
     * 
     * This may be late-binding. If the base URL is not provided to the model
     * bakery by an outside actor, then we will attempt to deduce it from the
     * URLs of the tabs on the channel.
     */
    private ?string $baseUrl = null;
    
    /**
     * The current tab of the channel.
     * 
     * This defaults to the featured tab, which is the default tab navigated to
     * for any unknown tab offset.
     */
    private string $currentTab = "featured";

    /**
     * The sorting mode to use on the videos page.
     */
    private int $videosSort;

    /**
     * The subscription count of the channel.
     * 
     * This is referenced by the about page bakery, but we store it here for
     * temporary reasons.
     */
    private string $subscriptionCount = "";

    /**
     * Special unique tabs to be cascaded into the videos tab.
     */
    public array $extraVideoTabs = [];

    /**
     * The contents of the current tab.
     */
    private ?object $currentTabContents = null;

    /**
     * InnerTube response data.
     */
    private object $responseData;
    
    /**
     * Reference to the global state object.
     */
    public YtApp $yt;
    
    public MHeader $header;
    
    /**
     * Fixed order of tabs.
     * 
     * Tabs will be sorted to always appear in this order, where RH_SPECIAL_EXTRA
     * represents the position for any unknown tabs.
     */
    public const TAB_ORDER = [
        "featured", // Home
        "videos",
        "podcasts",
        "releases",
        "RH_SPECIAL_EXTRA",
        "playlists",
        "channels", // Not officially supported anymore
        "community",
        "discussion", // Not supported anymore
        "store",
        "about",
        "search",
    ];
    
    /**
     * Tabs where the "featured channels" (secondary results) sidebar should
     * be displayed on.
     */
    public const SECONDARY_RESULTS_ENABLED_TAB_IDS = [
        "featured",
        "discussion",
        "community",
        "about",
    ];

    /**
     * Maps cloud chip sorting indices to their classical names.
     */
    public const VIDEO_TAB_SORT_INDEX_MAP = [
        0 => "dd",
        1 => "p",
        2 => "da",
    ];

    /**
     * Sort map for regular tabs that still use the old sorting backend.
     */
    public const SORT_MAP = [
        null,
        "p",
        "da",
        "dd",
        "lad",
    ];

    /**
     * Tabs which store types of video content, and thus should be cascaded into the videos tab.
     */
    public const VIDEO_TABS = [
        "videos",
        "streams",
        "shorts",
    ];

    public function bake(YtApp $yt, object $data, ?object $sidebarData = null, ?object $ownerData = null)
    {
        $i18n = i18n::getNamespace("channels");

        $this->yt = &$yt;
        $this->responseData = $data;

        $this->videosSort = $yt->videosSort ?? 0;

        // Declare the response array.
        $response = [];

        if ($header = @$data->header->c4TabbedHeaderRenderer)
        {
            $this->header = $header = new Channels4\MHeader(
                $header, 
                $this->getBaseUrl(),
                isOld: true
            );
            
            $response += ["header" => $header];
        }
        else if ($header = @$data->header->pageHeaderRenderer)
        {
            $this->header = $header = new Channels4\MHeader(
                $header, 
                $this->getBaseUrl(),
                isOld: false,
                frameworkUpdates: $this->responseData->frameworkUpdates ?? null
            );
            
            $response += ["header" => $header];
        }
        else if ($header = @$data->header->carouselHeaderRenderer)
        {
            $this->header = new Channels4\MCarouselHeader($header, $this->getBaseUrl());
            
            $response += ["header" => $this->header];
        }

        if (isset($response["header"]) && isset($data->contents->twoColumnBrowseResultsRenderer->tabs))
        {
            // Init appbar
            $yt->appbar->addNav();

            // Also add the owner info we just got to the appbar
            $yt->appbar->nav->addOwner(
                $response["header"]->getTitle(),
                $this->getBaseUrl(),
                $response["header"]->thumbnail ?? "",
            );
        }

        // Add alerts if necessary:
        if ($alerts = @$data->alerts)
        {
            $response += ["alerts" => []];
            
            foreach ($alerts as $alert)
            {
                $alert = $alert->alertWithButtonRenderer
                  ?? $alert->alertRenderer
                  ?? null;
                if (
                    ParsingUtils::getText($alert->text) == $i18n->get("nonexistent")
                &&  isset($response["header"])
                )
                {
                    $response["header"]->nonexistentMessage = ParsingUtils::getText($alert->text);
                }
                else
                {
                    $response["alerts"][] = MAlert::fromData($alert, [
                        "forceCloseButton" => true
                    ]);
                }
            }
        }

        // If we have a header, do some header specific stuff.
        if (isset($response["header"]))
        {
            // If we have twoColumnBrowseResultsRenderer with tabs,
            // process them (add navigation and store a reference)
            if ($tabsR = @$data->contents->twoColumnBrowseResultsRenderer->tabs)
            {
                $this->processAndAddTabs($yt, $tabsR, $response["header"]);
            }

            $response += [
                "title" => $response["header"]->getTitle()
            ];
        }

        if (!is_null($ownerData))
        {
            $response["secondaryHeader"] = new Channels4\MSecondaryHeader($ownerData);
        }
        
        // If we have a sidebar, go through it
        if (
            $sidebarShelves = @$sidebarData->contents->twoColumnBrowseResultsRenderer->tabs[0]
                ->tabRenderer->content->sectionListRenderer->contents
        )
        {
            $featuredData = null;

            if ("featured" == $this->currentTab)
            {
                $featuredData = &$data->contents->twoColumnBrowseResultsRenderer->tabs[0]
                    ->tabRenderer->content->sectionListRenderer->contents
                ;
            }

            $sidebarData = $this->getSidebarData($sidebarShelves, $featuredData);

            if ($sidebarData)
            {
                $this->initSecondaryColumn($response);
                $response["secondaryContent"]->items = $sidebarData;
            }
        }

        if (@$yt->subConfirmation && !is_null($response["header"]->title))
        {
            $response += ["subConfirmationDialog" => new MSubConfirmationDialog($response["header"])];
        }

        if (!is_null($this->currentTabContents))
        {
            $response += ["content" => $this->getTabContents($this->currentTabContents)];
        }

        $response += ["baseUrl" => $this->baseUrl];

        // Send the response array
        return (object)$response;
    }

    /**
     * Process channel tabs and add them to the header.
     * 
     * @param object $yt                 Global state variable.
     * @param object[] $tabs             Array of tabs to process and add.
     * @param MHeader $header            Header to add the tabs to.
     */
    public function processAndAddTabs(object &$yt, array $tabs, Channels4\MHeader &$header): void
    {
        if (!isset($this->baseUrl) || empty($this->baseUrl))
        {
            $this->guessBaseUrlFromTabs($tabs);
        }
        
        /** @var object */
        $videosTab = null;
        
        /** @var object */
        $aboutTab = null;

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
                    else if (!empty(array_filter( self::VIDEO_TABS, fn($item) => stripos( $tabEndpoint, "/$item" ) )))
                    {
                        // If the tab is any of the extra video tabs, then we want to cascade it into the videos tab.
                        // The above array technically contains "videos" too, but we test for that already above.
                        $this->extraVideoTabs[] = substr($tabEndpoint, strrpos($tabEndpoint, "/") + 1);
                        $tabR->hidden = true;

                        if (@$tabR->tabRenderer->selected) $videosTab->tabRenderer->selected = true;
                    }
                    else if (stripos($tabEndpoint, "/about"))
                    {
                        $aboutTab = &$tabR;
                    }
                }
            }
        }

        // 2023/11/03: YouTube are experimenting with moving the about tab into
        // a popup menu. Thus, we must manually restore the tab:
        if (is_null($aboutTab))
        {
            if (@$yt->tab == "about")
            {
                foreach ($tabs as $t)
                {
                    if (isset($t->tabRenderer))
                    {
                        $t->tabRenderer->selected = false;
                    }
                }

                $aboutTabSelected = true;
            }
            else
            {
                $aboutTabSelected = false;
            }

            $aboutContent = (object)[
                "tabRenderer" => (object)[
                    "endpoint" => (object)[
                        "commandMetadata" => (object)[
                            "webCommandMetadata" => (object)[
                                "url" => $this->baseUrl . "/about"
                            ]
                        ],
                        "browseEndpoint" => (object)[
                            "browseId" => $yt->ucid
                        ]
                    ],
                    "selected" => $aboutTabSelected,
                    "content" => (object)["rehikeStateParams" => "Special::PopupAboutTab"],
                    "title" => i18n::getRawString("channels", "tabAbout")
                ]
            ];
            
            $tabs[] = $aboutContent;
        }
        
        // We want to keep the same order of tabs by channel, and a reliable way to do that is to
        // simply make a fixed order map and then sort the tabs by that order. We do this as such:
        $tabIdMap = [];
        $sortedTabs = [];
        
        foreach ($tabs as $tab)
        {
            $tabIdMap[$this->getTabId($tab)] = $tab;
        }
        
        foreach (self::TAB_ORDER as $tabId)
        {
            if ($tabId == "RH_SPECIAL_EXTRA")
            {
                foreach ($tabIdMap as $uniqueTabId => $uniqueTab)
                {
                    if (!in_array($uniqueTabId, self::TAB_ORDER))
                    {
                        $sortedTabs[] = $uniqueTab;
                    }
                }
            }
            
            if (isset($tabIdMap[$tabId]))
            {
                $sortedTabs[] = $tabIdMap[$tabId];
            }
        }
        
        $header->addTabs($sortedTabs, ($yt->partiallySelectTabs ?? false));

        foreach ($sortedTabs as $tab) if (@$tab->tabRenderer)
        {
            $tabEndpoint = ParsingUtils::getUrl($tab->tabRenderer->endpoint);

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
                $this->currentTabContents = &$tab->tabRenderer->content;
            }
        }
        else if (@$tab->expandableTabRenderer)
        {
            if (@$tab->expandableTabRenderer->selected)
            {
                $this->currentTabContents = &$tab->expandableTabRenderer->content;
            }
        }

        if (isset($yt->appbar->nav->items[0]))
        {
            $yt->appbar->nav->items[0]->title = $header->getTitle();
        }
    }
    
    public function initSecondaryColumn(&$response)
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

    public function getTabContents($content)
    {
        if (isset($content->sectionListRenderer->contents[0]->itemSectionRenderer->contents[0]->channelAboutFullMetadataRenderer))
        {
            // old about format
            return (object)[
                "channelAboutMetadataRenderer" => 
                    new MChannelAboutMetadata(
                        $this,
                        $content->sectionListRenderer->contents[0]->itemSectionRenderer->contents[0]->channelAboutFullMetadataRenderer
                    )
            ];
        }
        else if (@$content->rehikeStateParams == "Special::PopupAboutTab")
        {
            // new about format hack
            return (object)[
                "channelAboutMetadataRenderer" =>
                    new MChannelAboutMetadata(
                        $this,
                        $this->responseData?->rehikeAboutTab
                    )
            ];
        }
        else if ($a = @$content->sectionListRenderer->contents[0]->itemSectionRenderer->contents[0]->gridRenderer)
        {
            return $this->handleGridTab($a, $content);
        }
        else if ($a = @$content->richGridRenderer)
        {
            return $this->handleGridTab(InnertubeBrowseConverter::richGridRenderer($a), $content, true);
        }
        else if (($a = @$content->sectionListRenderer->contents[0]->itemSectionRenderer) && (isset($a->contents[0]->backstagePostThreadRenderer)))
        {
            return $this->handleBackstage($a);
        }
        else if ($a = @$content->sectionListRenderer)
        {
            if ($submenu = @$a->subMenu->channelSubMenuRenderer)
            {
                $brandedPageV2SubnavRenderer = MSubnav::fromData($this, $submenu);
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

    public function handleGridTab($data, $parentTab, $rich = false)
    {
        $response = [];

        switch ($this->currentTab)
        {
            case "videos":
            case "streams":
            case "shorts":
                if ($rich)
                {
                    $response += [
                        "brandedPageV2SubnavRenderer" => MSubnav::bakeVideos($this)
                    ];
                }
                break;
            default:
                if ($subnav = @$parentTab->sectionListRenderer->subMenu->channelSubMenuRenderer)
                {
                    $subnav = $subnav ?? null;

                    $response += [
                        "brandedPageV2SubnavRenderer" => MSubnav::fromData($this, $subnav)
                    ];
                }
        }

        if ($rich && @$_GET["flow"] == "list")
        {
            if (isset($data->items[count($data->items) - 1]->continuationItemRenderer))
            {
                $token = &$data->items[count($data->items) - 1]->continuationItemRenderer->continuationEndpoint->continuationCommand->token;
                $contWrapper = new VideosContinuationWrapper();
                $contWrapper->setContinuation(
                    $token
                );
                $contWrapper->setList(true);

                $token = Base64Url::encode($contWrapper->serializeToString());
            }

            $response += [
                "items" => InnertubeBrowseConverter::generalLockupConverter($data->items, [
                    "listView" => true
                ])
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

    public function handleBackstage($data)
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

    public function getSidebarData($shelves, &$featuredData)
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
                    unset($featuredData[$i]);
                }
            }
        }

        if (0 < count($channelsShelves))
        {
            $shelves = [];
            foreach ($channelsShelves as $shelf)
            {
                $shelves[] = (object) [
                    "relatedChannelsRenderer" =>
                    MRelatedChannels::fromShelf($shelf)
                ];
            }

            return $shelves;
        }

        return null;
    }

    public function registerCurrentTab(string $currentTab): void
    {
        $this->currentTab = $currentTab;
    }

    public function getCurrentTab()
    {
        return $this->currentTab;
    }

    public function registerBaseUrl($baseUrl)
    {
        $this->baseUrl = $baseUrl;
    }

    public function getBaseUrl()
    {
        return $this->baseUrl;
    }

    public function getVideosSort()
    {
        return $this->videosSort;
    }
    
    /**
     * Guess the base URL based on the tabs.
     * 
     * We need this in case of playlist views, which don't have the information accessible
     * from their URL directly.
     */
    public function guessBaseUrlFromTabs(array $tabs): void
    {
        $renderer = null;
        
        foreach ($tabs as $tab)
        {
            if (isset($tab->tabRenderer))
            {
                $renderer = $tab->tabRenderer;
            }
            else if (isset($tab->expandableTabRenderer))
            {
                $renderer = $tab->expandableTabRenderer;
            }
            
            if ($renderer)
            {
                $url = $renderer->endpoint->commandMetadata->webCommandMetadata->url;
                $parts = explode("/", trim($url, "/"));
                
                if (empty($parts[0]))
                {
                    $parts = array_slice($parts, 1);
                }
                
                if (in_array($parts[0], ["channel", "user", "c"]))
                {
                    // [channel|user|c, CHANNEL_NAME, *TAB_ID*]
                    if (!isset($this->baseUrl))
                    {
                        $this->baseUrl = "/" . $parts[0] . "/" . $parts[1];
                        return;
                    }
                }
                else if (count($parts) == 2)
                {
                    // [CHANNEL_NAME, *TAB_ID*]
                    if (!isset($this->baseUrl))
                    {
                        $this->baseUrl = "/" . $parts[0];
                        return;
                    }
                }
            }
        }
    }
    
    /**
     * Get the ID of a particular tab.
     */
    private function getTabId(object $tab): string
    {
        $renderer = null;
        
        if (isset($tab->tabRenderer))
        {
            $renderer = $tab->tabRenderer;
        }
        else if (isset($tab->expandableTabRenderer))
        {
            $renderer = $tab->expandableTabRenderer;
        }
        
        if ($renderer)
        {
            $url = $renderer->endpoint->commandMetadata->webCommandMetadata->url;
            $parts = explode("/", $url);
            
            if (empty($parts[0]))
            {
                $parts = array_slice($parts, 1);
            }
            
            if (in_array($parts[0], ["channel", "user", "c"]))
            {
                // [channel|user|c, CHANNEL_NAME, *TAB_ID*]
                return $parts[2];
            }
            else if (count($parts) == 2)
            {
                // [CHANNEL_NAME, *TAB_ID*]    
                return $parts[1];
            }
            else
            {
                return $parts[0] ?? "";
            }
        }
        
        return "";
    }
}
