<?php
namespace Rehike\Model\Guide;

use Rehike\i18n\i18n;
use Rehike\Signin\API as Signin;
use Rehike\ConfigManager\Config;
use Rehike\Model\Common\MButton;
use Rehike\Model\Traits\NavigationEndpoint;

/**
 * A god class for converting InnerTube guide responses to
 * the Rehike format.
 * 
 * It's messy because I was lazy and sick while writing this,
 * sorry. <--- hi taniko from the past i love you and it really wasn't bad
 * code at all :3 it's very readable and well documented
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */
class Converter
{
    /**
     * The user's current UCID.
     */
    private static string $ucid = "";

    /**
     * Since the entire purpose of this class is to convert from
     * data, this is the insertion point function.
     * 
     * It takes in a raw (but JSON decoded) YTI guide response and
     * processes it into a Rehike format (which is mostly just reorganised
     * to change the layout a little bit).
     * 
     * @param object $data of the raw InnerTube response
     * @return object[] array of the modified items.
     */
    public static function fromData($data)
    {
        // Log the sign in state from the Signin service
        // so that I can use it later.
        $signedIn = Signin::isSignedIn();

        // Guide update (October 2023) makes obtaining the UCID a little bit
        // more difficult.
        $canonicalLibrary = self::getInnertubeLibrarySection($data);
        if (null != $canonicalLibrary)
        {
            self::initUcid($canonicalLibrary);
        }

        $response = [];

        // Push the main section to the response
        $response[] = self::getMainSection($data, $signedIn);

        // If signed in,
        //    push the library and subscriptions sections,
        // else
        //    push only the best of YouTube section
        if ($signedIn)
        {
            $response[] = self::getLibrarySection($data);
            $response[] = self::getSubscriptionSection($data);
        }
        else
        {
            $response[] = self::getBestOfYouTubeSection();
        }
        
        // Push the guide management items (the "end section")
        $response[] = self::getEndSection($signedIn);

        // Add sign in promo if not logged in
        if (!$signedIn)
        {
            foreach ($data->items as $item)
            foreach ($item as $key => $content)
            if ("guideSigninPromoRenderer" == $key)
            {
                $response[] = $item;
            }
        }

        return $response;
    }

    /**
     * This function is responsible for getting the "main section" of the
     * guide.
     * 
     * This section should contain the following items:
     * 
     *    - Home
     *    - My channel [if logged in]
     *    - Trending
     *    - Subscriptions [if logged in]
     *    - History [only if logged out]
     * 
     * @param object $data
     * @param bool $signedIn
     * @return object {guideSectionRenderer}
     */
    public static function getMainSection($data, $signedIn = false)
    {
        $response = [];

        // Some strings, like "My channel" and "Trending" aren't
        // reported at all by InnerTube, so I have to use a custom
        // language file to store these strings in.
        $strings = i18n::getNamespace("guide");

        // Get signin info if possible (needed for the UCID)
        $signinInfo = null;

        // Casted to object in order to access in string interpolation
        if ($signedIn)
            $signinInfo = (object)Signin::getInfo();

        $mainSection = $data->items[0]->guideSectionRenderer->items;

        $homeItem = self::getItemByIcon($mainSection, "WHAT_TO_WATCH");
        $subscriptionsItem = self::getItemByIcon($mainSection, "SUBSCRIPTIONS");
        
        // Correct icon type for subscriptions icon
        if (null != $subscriptionsItem)
        {
            $subscriptionsItem->guideEntryRenderer->icon->iconType = "MY_SUBSCRIPTIONS";
        }
        
        //
        // Push the main section items to the response array
        //

        // Home item
        $response[] = $homeItem;

        // My channel item (if signed in)
        if ($signedIn && !empty(self::getUcid()))
        {
            $response[] = self::bakeGuideItem(
                "/channel/" . self::getUcid(),
                $strings->get("myChannel"),
                "SYSTEM::MY_CHANNEL"
            );
        }

        // Trending item
        $response[] = self::bakeGuideItem(
            "/feed/trending", $strings->get("trending"), "SYSTEM::TRENDING"
        );

        // Subscriptions item (if signed in)
        if ($signedIn)
        {
            $response[] = $subscriptionsItem;
        }

        // History item (only if signed out)
        if (!$signedIn)
        {
            $secondarySection = $data->items[1]->guideSectionRenderer->items;
            $historyItem = self::getItemByIcon($secondarySection, "WATCH_HISTORY");
            
            // Correct icon type
            $historyItem->guideEntryRenderer->icon->iconType = "HISTORY";

            $response[] = $historyItem;
        }

        // Response with a synthesised guideSectionRenderer wrapper.
        return (object)[
            "guideSectionRenderer" => (object)[
                "items" => $response
            ]
        ];
    }

    /**
     * This function is responsible for getting the Best of YouTube
     * section.
     * 
     * Best of YouTube used to be directly obtained from InnerTube,
     * but as of late 2022, they've changed it in a way that makes
     * it incompatible with Hitchhiker. Now, we build it entirely
     * locally, including the images (which are in 
     * /rehike/static/best_of_youtube)
     */
    public static function getBestOfYouTubeSection() {
        $strings = i18n::getNamespace("guide");

        // Thumbnail prefix and suffix
        $format = Config::getConfigProp("appearance.oldBestOfYouTubeIcons")
        ? "/rehike/static/best_of_youtube/%s_old.jpg"
        : "/rehike/static/best_of_youtube/%s.jpg";

        $response = (object) [];
        $response->formattedTitle = (object) [
            "simpleText" => $strings->get("bestOfYouTubeTitle")
        ];

        $items = [];

        $items[] = self::bakeGuideItem(
            "/channel/UC-9-kyTW8ZkZNDHQJ6FgpwQ",
            $strings->get("bestOfYouTubeMusic"),
            sprintf($format, "music")
        );

        $items[] = self::bakeGuideItem(
            "/channel/UCEgdi0XIXXZ-qJOFPf4JSKw",
            $strings->get("bestOfYouTubeSports"),
            sprintf($format, "sports")
        );

        $items[] = self::bakeGuideItem(
            "/gaming",
            $strings->get("bestOfYouTubeGaming"),
            sprintf($format, "gaming")
        );

        $items[] = self::bakeGuideItem(
            "/channel/UClgRkhTL3_hImCAmdLfDE4g",
            $strings->get("bestOfYouTubeMoviesTv"),
            sprintf($format, "movies_tv")
        );
        
        $items[] = self::bakeGuideItem(
            "/channel/UCYfdidRxbB8Qhf0Nx7ioOYw",
            $strings->get("bestOfYouTubeNews"),
            sprintf($format, "news")
        );

        $items[] = self::bakeGuideItem(
            "/channel/UC4R8DWoMoI7CAwX8_LjQHig",
            $strings->get("bestOfYouTubeLive"),
            sprintf($format, "live")
        );

        $items[] = self::bakeGuideItem(
            "/channel/UCBR8-60-B28hp2BmDPdntcQ",
            $strings->get("bestOfYouTubeSpotlight"),
            sprintf($format, "spotlight")
        );

        $items[] = self::bakeGuideItem(
            "/channel/UCzuqhhs6NWbgTzMuM09WKDQ",
            $strings->get("bestOfYouTube360"),
            sprintf($format, "360")
        );

        $response->items = $items;

        return (object) [
            "guideSectionRenderer" => $response
        ];
    }

    /**
     * Get the InnerTube library section structure.
     * 
     * Since this is used in multiple areas, there is one common helper function
     * for this behavior.
     */
    private static function getInnertubeLibrarySection(object $data): ?object
    {
        $mainSection = $data->items[0]->guideSectionRenderer->items;

        // This atrocious pattern appears a lot here.
        // It's just the easiest way to get the last item of the array.
        if (isset($mainSection[count($mainSection) - 1]->guideCollapsibleSectionEntryRenderer))
        {
            return $mainSection[count($mainSection) - 1]->guideCollapsibleSectionEntryRenderer;
        }
        
        return null;
    }

    /**
     * This function is responsible for getting the library section.
     * 
     * It's a huge mess.
     * 
     * This section is mostly taken raw from InnerTube, with some filters
     * to skip past undesirable items. It should contain (in no specific order):
     * 
     *    - History
     *    - Watch later
     *    - Liked videos
     *    - <All user playlists>
     * 
     * It only appears if logged in.
     * 
     * @param object $data
     * @return object {guideSectionRenderer}
     */
    public static function getLibrarySection($data)
    {
        // Custom strings are only used this time for the expander
        // text.
        $strings = i18n::getNamespace("guide");

        $ucid = self::getUcid();

        $librarySection = self::getInnertubeLibrarySection($data);

        if ($librarySection == null)
            return null;

        // Store the title for later use (it needs to have a navigation endpoint later on)
        $title = $strings->get("libraryTitle");

        $response = [];

        /**
         * A support types map.
         * 
         * The left side is checked against, and then replaced with the
         * content of the right side.
         * 
         * This is only currently needed for the history icon type, but in case
         * InnerTube changes the icon types later I just layed them all out
         * here.
         * 
         * @var string[]
         */
        $supportedTypes = [
            "WATCH_HISTORY"  => "HISTORY",
            "WATCH_LATER"    => "WATCH_LATER",
            "LIKES_PLAYLIST" => "LIKES_PLAYLIST",
            "PLAYLISTS"      => "PLAYLISTS"
        ];

        // Count all items and add them if they're a supported type
        // (also correct the icon types)
        // Basically just make use of everything I did above!
        foreach ($librarySection->sectionItems as $item)
        {
            // Skip non guide entry renderers
            if (!isset($item->guideEntryRenderer)) continue;

            $iconType = @$item->guideEntryRenderer->icon->iconType;

            if (isset($supportedTypes[$iconType]))
            {
                $item->guideEntryRenderer->icon->iconType = $supportedTypes[$iconType];
                $response[] = $item;
            }
        }
        
        // Hack: move the official collapsible (too small for my taste)
        // into the response body (very large) so that we can shrink it
        // down to 4 (perfect)
        if (isset($librarySection->sectionItems[count($librarySection->sectionItems) - 1]->guideCollapsibleEntryRenderer))
        {
            $c = $librarySection->sectionItems[count($librarySection->sectionItems) - 1]->guideCollapsibleEntryRenderer;

            $response = array_merge($response, $c->expandableItems);
        }

        // Move overflow items to the show more container
        // if needed
        if (count($response) > 4)
        {
            $collapsible = [];

            for ($i = 4; $i < count($response); $i++)
            {
                $collapsible[] = $response[$i];
            }

            // Remove the original items
            array_splice($response, 4);

            // Synthesise a little guide collapsible entry renderer
            // for containing the excess items. This differs a little
            // bit from the InnerTube specification.
            $response[] = (object)[
                "guideCollapsibleEntryRenderer" => (object)[
                    "expandableItems" => $collapsible,
                    "expanderItem" => (object)[
                        "text" => $strings->get("showMore")
                    ],
                    "collapserItem" => (object)[
                        "text" => $strings->get("showFewer")
                    ]
                ]
            ];
        }

        // Finally, synthesise the section renderer.
        // The formatted title is an ugly bit of code to make the title
        // also an anchor. This actually is according to the InnerTube spec.
        return (object)[
            "guideSectionRenderer" => (object)[
                "formattedTitle" => (object)[
                    "runs" => [
                        (object)[
                            "text" => $title,
                            "navigationEndpoint" => (object)[
                                "commandMetadata" => (object)[
                                    "webCommandMetadata" => (object)[
                                        "url" => "/channel/$ucid/playlists"
                                    ]
                                ]
                            ]
                        ]
                    ]
                ],
                "items" => $response
            ]
        ];
    }

    /**
     * This function is responsible for getting the subscriptions section
     * of the guide data.
     * 
     * This is one of the most complicated parts of the guide, since it
     * can refresh dynamically with user interactions across the site
     * (i.e. subscribing to a channel).
     * 
     * This is also taken directly from InnerTube, so watch out for any
     * possible changes that may break it.
     * 
     * It should contain all user subscriptions.
     * 
     * @param object $data
     * @return object {guideSubscriptionsSectionRenderer} (from InnerTube)
     */
    public static function getSubscriptionSection($data)
    {
        // Find the subscription section index
        foreach ($data->items as &$item)
        foreach ($item as $key => &$contents)
        {
            if ("guideSubscriptionsSectionRenderer" == $key)
            {
                $section = &$contents;
                $responseItem = &$item;
            }
        }

        if (!isset($section)) return self::buildSubscriptionsPromoSection();

        // Ugly last item hack to get rid of the "show more" button WEB v2
        // reports
        if (isset($section->items[count($section->items ?? []) - 1]->guideCollapsibleEntryRenderer))
        {
            $contents = $section->items[count($section->items) - 1]->guideCollapsibleEntryRenderer->expandableItems;

            // Remove the item from the array so it doesn't cause any issues.
            array_splice($section->items, count($section->items) - 1, 1);

            // Then add all of its children back into the parent array (to flatten it)
            $section->items = array_merge($section->items, $contents);
        }

        // Ditto: remove guide builder hack
        if (isset($section->items[count($section->items) - 1]->guideEntryRenderer->icon))
        {
            array_splice($section->items, count($section->items) - 1, 1);
        }

        // Iterate all remaining and add counts if needed
        foreach ($section->items as &$item)
        {
            $content = &$item->guideEntryRenderer;

            // Live badge trumps count badge, but this check is done on the
            // templater end. Both are reported otherwise.
            // In addition, this may be reformed in the future to
            // actually count the number of unwatched videos from a user's subscribed
            // channels :O (when? how?)
            if ("GUIDE_ENTRY_PRESENTATION_STYLE_NEW_CONTENT" == $content->presentationStyle)
            {
                if (!isset($content->badges)) $content->badges = (object)[];

                // Hardcoded feed count since feeds no longer exist
                // Hitchhiker also used the hardcoded value of 1
                $content->badges->count = 1;
            }
        }

        return $responseItem;
    }

    /**
     * Build the subscription promo section.
     * This shows when you have no subscriptions.
     */
    public static function buildSubscriptionsPromoSection() {
        $i18n = i18n::getNamespace("guide");
        $response = (object) [];
        $format = Config::getConfigProp("appearance.oldBestOfYouTubeIcons")
        ? "/rehike/static/best_of_youtube/%s_old.jpg"
        : "/rehike/static/best_of_youtube/%s.jpg";

        $response->formattedTitle = (object) [
            "simpleText" => $i18n->get("subscriptions"),
            "navigationEndpoint" => NavigationEndpoint::createEndpoint("/feed/channels")
        ];

        $response->button = new MButton([
            "style" => "STYLE_PRIMARY",
            "text" => (object) [
                "simpleText" => $i18n->get("subscriptionsPromoButton")
            ],
            "icon" => (object) [
                "iconType" => "PLUS"
            ],
            "navigationEndpoint" => NavigationEndpoint::createEndpoint("/feed/guide_builder")
        ]);

        $response->tooltip = (object) [
            "text" => (object) [
                "simpleText" => $i18n->get("subscriptionsPromoTooltip")
            ],
            "navigationEndpoint" => NavigationEndpoint::createEndpoint("/feed/guide_builder")
        ];

        $response->items =  [];

        $response->items[] = self::bakeGuideItem(
            "/channel/UCF0pVplsI8R5kcAqgtoRqoA",
            $i18n->get("bestOfYouTubePopularOnYouTube"),
            sprintf($format, "popular_on_youtube")
        );

        $response->items[] = self::bakeGuideItem(
            "/channel/UC-9-kyTW8ZkZNDHQJ6FgpwQ",
            $i18n->get("bestOfYouTubeMusic"),
            sprintf($format, "music")
        );

        $response->items[] = self::bakeGuideItem(
            "/channel/UCEgdi0XIXXZ-qJOFPf4JSKw",
            $i18n->get("bestOfYouTubeSports"),
            sprintf($format, "sports")
        );

        $response->items[] = self::bakeGuideItem(
            "/gaming",
            $i18n->get("bestOfYouTubeGaming"),
            sprintf($format, "gaming")
        );

        return (object) [
            "guideSubscriptionsPromoSectionRenderer" => $response
        ];
    }

    /**
     * This function is responsible for getting the guide
     * "end section".
     * 
     * This section contains a few extra items related to subscription
     * management.
     * 
     * This section doesn't use any data from InnerTube at all, so it should
     * be the sturdiest section in terms of long-term stability.
     * 
     * This section should contain:
     * 
     *    - Browse channels
     *    - Manage subscriptions [if logged in]
     * 
     * @param bool $signedIn
     * @return object {guideSectionRenderer}
     */
    public static function getEndSection($signedIn = false)
    {
        $response = [];

        $strings = i18n::getNamespace("guide");

        // Bake "browse channels" (guide builder) item
        $response[] = self::bakeGuideItem(
            "/feed/guide_builder",
            $strings->get("guideBuilderLabel"),
            "SYSTEM::BUILDER"
        );

        // Also, if signed in, add the "manage subscriptions" item
        if ($signedIn)
        {
            $response[] = self::bakeGuideItem(
                "/feed/channels",
                $strings->get("manageSubscriptionsLabel"),
                "SYSTEM::SUBSCRIPTION_MANAGER"
            );
        }

        // And return
        return (object)[
            "guideSectionRenderer" => (object)[
                "items" => $response
            ]
        ];
    }

    /**
     * This function is used to conveniently synthesise my own guide 
     * items.
     * 
     * @param string $endpoint
     * @param string $name
     * @param string $icon (will be a thumbnail unless prefixed with SYSTEM::)
     * @return object {guideEntryRenderer}
     */
    public static function bakeGuideItem($endpoint, $name, $icon)
    {
        //
        // Step 1: Determine the endpoint of the data and create a new
        //         item template containing the provided information.
        //         This entire section is object oriented programming at
        //         its finest!
        //

        /**
         * Contains extra information about the endpoint that isn't just
         * its command metadata.
         * 
         * As you can guess, this was added particularly last minute, and
         * particularly when I was just drifting off to sleep.
         * 
         * As such, this implement is absolutely garbage. It's mostly used here
         * to synthesise a browseEndpoint or urlEndpoint for encoding
         * the guide's endpoint later on.
         * 
         * @var string[] (but only by technicality, it will be casted to an object later)
         */
        $extraEndpointInfo = [];

        if (
            "/feed"     == substr($endpoint, 0, 5) ||
            "/channel"  == substr($endpoint, 0, 8) ||
            "/c"        == substr($endpoint, 0, 2) ||
            "/user"     == substr($endpoint, 0, 5) ||
            "/playlist" == substr($endpoint, 0, 9)
        )
        {
            // NO ASSOCIATIVE ARRAY?
            $before = [
                "/feed/",
                "/channel/",
                "/c/",
                "/user/",
                "/playlist?list="
            ];

            // NO ASSOCIATIVE ARRAY?
            $after = [
                "FE",
                "",
                "",
                "",
                "PL"
            ];

            // This needs to be a variable (not a literal) because it is
            // a forced reference. Wtf php?
            $wtfphp = 1;
            $browseId = str_replace($before, $after, $endpoint, $wtfphp);

            $extraEndpointInfo = [
                "browseEndpoint" => (object)[
                    "browseId" => $browseId
                ]
            ];
        }
        else // The easier case
        {
            $extraEndpointInfo = [
                "urlEndpoint" => (object)[
                    "url" => $endpoint
                ]
            ];
        }

        $item = (object)[
            "navigationEndpoint" => (object)([
                "commandMetadata" => (object)[
                    "webCommandMetadata" => (object)[
                        "url" => $endpoint
                    ]
                ]
            ] + $extraEndpointInfo), // Merge this data with the extra info
            "formattedTitle" => (object)[
                "simpleText" => $name
            ]
        ];

        //
        // Step 2: Determine the icon type and add it to the item template.
        //
        if ("SYSTEM::" == substr($icon, 0, 8))
        {
            $icon = substr($icon, 8);

            $item->icon = (object)[
                "iconType" => $icon
            ];
        }
        else
        {
            $item->thumbnail = (object)[
                "thumbnails" => [
                    (object)[
                        "url" => $icon
                    ]
                ]
            ];
        }

        // Then just a standard return.
        return (object)[
            "guideEntryRenderer" => $item
        ];
    }

    /**
     * This is a convenient helper function for crawling the InnerTube response.
     * 
     * This allows me to search for a specific item by using its icon type, which
     * is the most consistent variable for finding them.
     * 
     * @param object[] $items array
     * @param string $icon to search for
     * @return object|null
     */
    public static function getItemByIcon($items, $icon)
    {
        // Skip all non-guide-entry-renderers because I don't want them
        // anyways
        foreach ($items as $item) if ($content = @$item->guideEntryRenderer)
        {
            // Also skip items that don't have an icon type
            if (!is_string(@$content->icon->iconType)) continue;

            // strtolower for case insensitivity
            if (strtolower($icon) == strtolower($content->icon->iconType))
                return $item;
        }

        // Otherwise it doesn't exist
        return null;
    }

    /**
     * Get the user's current UCID.
     */
    public static function getUcid(): string
    {
        return self::$ucid;
    }

    private static function initUcid(object $librarySection): void
    {
        $signinInfo = Signin::getInfo();

        if (
            isset($signinInfo["ucid"]) && 
            is_string($signinInfo["ucid"]) && 
            !empty($signinInfo["ucid"])
        )
        {
            self::$ucid = $signinInfo["ucid"];
        }
        else if (isset($librarySection->sectionItems[0]->guideEntryRenderer->icon->iconType))
        {
            $a = $librarySection->sectionItems[0]->guideEntryRenderer;

            if ("ACCOUNT_BOX" == $a->icon->iconType)
            {
                self::$ucid = $a->navigationEndpoint->browseEndpoint->browseId;
            }
        }
    }
}