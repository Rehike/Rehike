<?php
namespace Rehike\Model\Channels\Channels4\Sidebar;

use Rehike\TemplateFunctions as TF;
use Rehike\Model\Browse\InnertubeBrowseConverter as Converter;

class MRelatedChannels
{
    public $title = "";
    public $items = [];
    public $seeMoreButton;

    public static function fromShelf($shelf)
    {
        $me = new self();

        // Convert the shelf first
        $shelf = Converter::shelfRenderer($shelf, [
            "channelRendererNoMeta" => true,
            "channelRendererUnbrandedSubscribeButton" => true,
            "channelRendererNoSubscribeCount" => true
        ]);

        if (isset($shelf->title))
        {
            $me->title = $shelf->title;
        }

        $items = $shelf->content->horizontalListRenderer->items
        ??       $shelf->content->expandedShelfContentsRenderer->items
        ??       null;

        if (!is_null($items))
        foreach ($items as $i => $item)
        {
            $me->items[] = $item->gridChannelRenderer
            ??             $item->channelRenderer
            ??             null;

            // Break at the 10th item
            if ($i >= 9)
            {
                $me->seeMoreButton = new MRelatedChannelsSeeMoreButton(
                    @$shelf->endpoint->commandMetadata->webCommandMetadata->url
                );
                break;
            }
        }

        return $me;
    }
}