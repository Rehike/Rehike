<?php
namespace Rehike\Model\Browse;

use Rehike\i18n;
use Rehike\TemplateFunctions as TF;

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

    public static function channelRenderer($data, $context = [])
    {
        $regex = &i18n::getNamespace("main/regex");

        if (@$context["channelRendererNoSubscribeCount"])
            $subscriberCount = "";
        else
            $subscriberCount = preg_replace(
                str_replace("/g", "/", $regex->subscriberCountIsolater),
                "",
                TF::getText($data->subscriberCountText)
            );

        $subscribeButtonBranded = true;

        if (@$context["channelRendererUnbrandedSubscribeButton"]) 
            $subscribeButtonBranded = false;

        $data->subscribeButton = new MSubscriptionActions([
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
}