<?php
namespace Rehike\Model\Browse;

use Rehike\i18n\i18n;
use Rehike\Util\ParsingUtils;
use Rehike\Util\ExtractUtils;
use Rehike\Model\Channels\Channels4\BrandedPageV2\MSubnav;
use Rehike\SignInV2\SignIn;

use Rehike\Model\Common\Subscription\MSubscriptionActions;
use Rehike\Model\ViewModelConverter\LockupViewModelConverter;

use Rehike\Model\Common\MCollaborator;

class InnertubeBrowseConverter
{
    public static function generalLockupConverter($items, $context = [])
    {
        foreach ($items as &$item) foreach ($item as $name => &$content)
        {
            switch ($name)
            {
                case "channelRenderer":
                case "gridChannelRenderer":
                    $content = self::channelRenderer($content, $context);
                    break;
                case "videoRenderer":
                case "gridVideoRenderer":
                case "compactVideoRenderer":
                    $content = self::videoRenderer($content, $context);
                    break;
                case "richItemRenderer":
                    $content = self::richItemRenderer($content, $context);
                    break;
                case "reelItemRenderer":
                case "gridReelItemRenderer":
                    $list = $context["listView"] ?? false;
                    $item->{$list ? "videoRenderer" : "gridVideoRenderer"}
                    = self::reelItemRenderer($content, $context);
                    unset($item->reelItemRenderer);
                    break;
                
                // View model renderers:
                case "lockupViewModel":
                    unset($item->{$name});
                    
                    // TODO: Not passing along framework updates is a hack because we can't
                    // access it at this time.
                    $lockupConv = new LockupViewModelConverter($content, (object)[]);
                    if (isset($context["lockupStyle"]))
                        $lockupConv->setStyle($context["lockupStyle"]);
                    $newEntry = $lockupConv->bakeClassicRenderer();
                    
                    foreach ($newEntry as $newName => &$newContext)
                    {
                        $item->{$newName} = $newContext;
                    }
                    
                    break;
            }
        }

        return $items;
    }

    /**
     * Process a grid renderer.
     * 
     * This is, for the most part, supported natively. This
     * method exists in order to streamline replacement of children
     * of grid renderers that may actually need to be modified.title
     */
    public static function gridRenderer($data, $context = [])
    {
        $data->items = self::generalLockupConverter($data->items, $context);

        return $data;
    }

    /**
     * Process a shelf renderer.
     * 
     * This is also, for the most part, supported natively.
     * Ditto above.
     */
    public static function shelfRenderer($data, $context = [])
    {
        foreach ($data->content as $name => &$value)
        {
            switch ($name)
            {
                case "verticalListRenderer":
                case "horizontalListRenderer":
                case "expandedShelfContentsRenderer":
                    $value->items = self::generalLockupConverter($value->items, $context);
                    break;
            }
        }

        return $data;
    }

    /**
     * Process an item section renderer.
     * 
     * Again, mostly natively supported, but we want to
     * easily modify any lockups that need it.
     */
    public static function itemSectionRenderer($data, $context = [])
    {
        foreach ($data->contents as &$content) foreach ($content as $name => &$value)
        {
            switch ($name)
            {
                case "shelfRenderer":
                    $value = self::shelfRenderer($value, $context);
                    break;
                case "channelRenderer":
                case "gridChannelRenderer":
                    $value = self::channelRenderer($value, $context);
                    break;
                case "videoRenderer":
                case "gridVideoRenderer":
                    $value = self::videoRenderer($value, $context);
                    break;
                case "channelFeaturedContentRenderer":
                    $value->items = self::generalLockupConverter($value->items, $context);
                    break;
                    
                // View model renderers:
                case "lockupViewModel":
                    unset($content->{$name});
                    
                    // TODO: Not passing along framework updates is a hack because we can't
                    // access it at this time.
                    $lockupConv = new LockupViewModelConverter($value, (object)[]);
                    $lockupConv->setStyle(LockupViewModelConverter::STYLE_LIST);
                    $newEntry = $lockupConv->bakeClassicRenderer();
                    
                    foreach ($newEntry as $newName => &$newContext)
                    {
                        $content->{$newName} = $newContext;
                    }
                    
                    break;
            }
        }

        return $data;
    }

    /**
     * Process a section list renderer.
     * 
     * Again, mostly natively supported, but we want to
     * easily modify any lockups that need it.
     */
    public static function sectionListRenderer($data, $context = [])
    {
        foreach ($data->contents as &$content) foreach ($content as $name => &$value)
        {
            switch ($name)
            {
                case "itemSectionRenderer":
                    $value = self::itemSectionRenderer($value, $context);
                    break;
            }
        }

        return $data;
    }

    public static function channelRenderer($data, $context = [])
    {
        $i18n = i18n::getNamespace("browse");

        if (@$context["channelRendererNoSubscribeCount"])
            $subscriberCount = "";
        else if (isset($data->subscriberCountText))
            $subscriberCount = ExtractUtils::isolateSubCnt(ParsingUtils::getText($data->subscriberCountText));

        $subscriberCount = $subscriberCount ?? "";

        $subscribeButtonBranded = true;

        /**
         * You know, I hate this. At first it was fun.
         * I was able to easily make things with a
         * competent API. however, they stopped being
         * fucking competent in 2022. For the handles
         * update they decided it would be a BRIGHT
         * FUCKING IDEA to move the subscription count
         * to the video count text, and put the handle
         * in the subscription count text. How FUCKING
         * HARD IS IT TO ADD ANOTHER FIELD?!?!?!?!?!!?
         * HOW FUCKING HARD?!?!?!!?!?!?!?!?!!?! WHAT
         * THE ACTUAL FUCK?!?!!?!?!? FUCKING DIE IN A
         * DITCH, HOLY FUCKING SHIT.
         *  - love, aubrey <3
         */
        if (substr($subscriberCount, 0, 1) == "@")
        {
            $subscriberCount = ExtractUtils::isolateSubCnt(ParsingUtils::getText($data->videoCountText));
            unset($data->videoCountText);
        }

        if (@$context["channelRendererUnbrandedSubscribeButton"]) 
            $subscribeButtonBranded = false;

        if (@$context["channelRendererChannelBadge"])
        {
            if (!isset($data->badges))
            {
                $data->badges = [];
            }
            
            $data->badges[] = (object) [
                "metadataBadgeRenderer" => (object) [
                    "label" => $i18n->get("channelBadge"),
                    "style" => "BADGE_STYLE_TYPE_SIMPLE"
                ]
            ];
        }

        if (isset($data->subscribeButton->subscribeButtonRenderer))
        {
            $data->subscribeButton = MSubscriptionActions::fromData(
                $data->subscribeButton->subscribeButtonRenderer,
                $subscriberCount,
                $subscribeButtonBranded
            );
        }
        else if (SignIn::isSignedIn())
        {
            $data->subscribeButton = MSubscriptionActions::buildMock(
                $subscriberCount,
                $subscribeButtonBranded
            );
            
        }
        else
        {
            $data->subscribeButton = MSubscriptionActions::signedOutStub(
                $subscriberCount,
                $subscribeButtonBranded
            );
        }

        if (@$context["channelRendererNoMeta"])
        {
            unset($data->subscriberCountText);
        }

        return $data;
    }

    public static function videoRenderer($data, $context = [])
    {
        $regex = i18n::getNamespace("regex");
        $i18n = i18n::getNamespace("browse");

        if (isset($data->badges))
        foreach ($data->badges as $badge) foreach ($badge as &$content)
        {
            if ($content->style == "BADGE_STYLE_TYPE_LIVE_NOW"
            &&  $content->label == $i18n->get("liveBadgeOriginal"))
            {
                $content->label = $i18n->get("liveBadge");
            }
        }

        if (isset($data->thumbnailOverlays))
        foreach ($data->thumbnailOverlays as $index => &$overlay) foreach ($overlay as $name => &$content)
        {
            switch ($name)
            {
                case "thumbnailOverlayTimeStatusRenderer":
                    switch ($content->style)
                    {
                        case "LIVE":
                            if (!isset($data->badges))
                            $data->badges = [];

                            $data->badges[] = (object) [
                                "metadataBadgeRenderer" => (object) [
                                    "label" => $i18n->get("liveBadge"),
                                    "style" => "BADGE_STYLE_TYPE_LIVE_NOW"
                                ]
                            ];

                            array_splice($data->thumbnailOverlays, $index);
                            break;
                        case "SHORTS":
                            $content->style = "DEFAULT";
                            $atitle = $data->title->accessibility->accessibilityData->label;

                            preg_match($regex->get("videoTimeIsolator"), $atitle, $matches);

                            $text = null;
                            if (!isset($matches[0]))
                            {
                                $text = "1:00";
                            }
                            else
                            {
                                $time = (int) preg_replace($regex->get("secondsIsolator"), "", $matches[0]);

                                if ($time < 10)
                                {
                                    $text = "0:0$time";
                                }
                                else
                                {
                                    $text = "0:$time";
                                }
                            }

                            $content->text = (object) [
                                "simpleText" => $text
                            ];

                            break;
                    }
                    break;
            }
        }
        
        // Handle the multiple authors bullshit by just grabbing the first one.
        if (isset($data->longBylineText->runs[0]))
        {
            $run = &$data->longBylineText->runs[0];
            if (isset($run->navigationEndpoint->showDialogCommand->panelLoadingStrategy))
            {
                try
                {
                    $channel = new MCollaborator($run->navigationEndpoint->showDialogCommand->panelLoadingStrategy->inlineContent->dialogViewModel->customContent
                        ->listViewModel->listItems[0]->listItemViewModel);

                    $run->text = $channel->name;
                    $run->navigationEndpoint = $channel->navigationEndpoint;
                    $badgeIcon = @$channel->rawData->title->attachmentRuns[0]->element->type->imageType->image->sources[0]->clientResource->imageName ?? null;
                    
                    if ($badgeIcon)
                    {
                        $data->ownerBadges = [
                            (object)[
                                "metadataBadgeRenderer" => (object)[
                                    "icon" => (object)[
                                        "iconType" => "CHECK_CIRCLE_THICK",
                                    ],
                                    "style" => "BADGE_STYLE_TYPE_VERIFIED",
                                    "tooltip" => i18n::getNamespace("global", "verified"),
                                    "accessibilityData" => (object)[
                                        "label" => i18n::getNamespace("global", "verified"),
                                    ],
                                ]
                            ]
                        ];
                    }
                }
                catch (\Exception $e)
                {
                    \Rehike\Logging\DebugLogger::print("Failed to get byline text for %s: %s", $data->videoId, $e);
                }
            }
        }

        // Swapped date/view count:
        if (@$context["searchMetadataOrder"])
        {
            $data->dateBeforeViews = true;
        }

        return $data;
    }

    /**
     * Convert a rich grid renderer to regular grid renderer
     */
    public static function richGridRenderer($data, $context = [])
    {
        $items = [];

        foreach ($data->contents as $item)
        {
            if (@$item->richItemRenderer)
            {
                $items[] = self::richItemRenderer($item->richItemRenderer);
            }
            else if (@$item->continuationItemRenderer)
            {
                $items[] = $item;
            }
        }

        return (object)[
            "items" => $items
        ];
    }

    /**
     * Convert a rich item renderer to its canonical type.
     * 
     * @return object
     */
    public static function richItemRenderer($data, $context = [])
    {
        if (!@$context["listView"])
        {
            foreach ($data->content as $name => $value)
            {
                if ($name == "lockupViewModel")
                {
                    // Bandaid to fix homepage:
                    return self::generalLockupConverter([$data->content])[0];
                }
                
                return (object) [
                    "grid" . ucfirst($name) => $value
                ];
            }
        }
        
        return $data->content;
    }

    public static function reelItemRenderer(object $data, array $context = []): object
    {
        $data->title = $data->headline;
        unset($data->headline);

        return $data;
    }
}