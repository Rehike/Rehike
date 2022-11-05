<?php
namespace Rehike\Model\Browse;

use Rehike\i18n;
use Rehike\TemplateFunctions as TF;
use Rehike\Util\ExtractUtils;
use Rehike\Model\Channels\Channels4\BrandedPageV2\MSubnav;

use Rehike\Model\Common\Subscription\MSubscriptionActions;

class InnertubeBrowseConverter
{
    protected static function generalLockupConverter(&$items, $context)
    {
        foreach ($items as &$item) foreach ($item as $name => &$content)
        {
            switch ($name)
            {
                case "channelRenderer":
                case "gridChannelRenderer":
                    $content = self::channelRenderer($content, $context);
                    break;
            }
        }
    }

    /**
     * Process a grid renderer.
     * 
     * This is, for the most part, supported natively. This
     * method exists in order to streamline replacement of children
     * of grid renderers that may actually need to be modified.
     */
    public static function gridRenderer($data, $context = [])
    {
        self::generalLockupConverter($data->items, $context);

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
        if (isset($data->content->horizontalListRenderer))
        {
            self::generalLockupConverter($data->content->horizontalListRenderer->items, $context);
        }
        else if (isset($data->content->expandedShelfContentsRenderer))
        {
            self::generalLockupConverter($data->content->expandedShelfContentsRenderer->items, $context);
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
        foreach ($data -> contents as &$content) foreach ($content as $name => &$value)
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
        foreach ($data -> contents as &$content) foreach ($content as $name => &$value)
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
        if (@$context["channelRendererNoSubscribeCount"])
            $subscriberCount = "";
        else
            $subscriberCount = ExtractUtils::isolateSubCnt(TF::getText($data->subscriberCountText));

        $subscribeButtonBranded = true;

        if (@$context["channelRendererUnbrandedSubscribeButton"]) 
            $subscribeButtonBranded = false;

        $data->subscriptionActions = new MSubscriptionActions([
            "longText" => $subscriberCount,
            "shortText" => $subscriberCount,
            "branded" => $subscribeButtonBranded
        ]);

        if (@$context["channelRendererNoMeta"])
        {
            unset($data->subscriberCountText);
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
     * @return array
     */
    public static function richItemRenderer($data, $context = [])
    {
        return $data->content;
    }
}