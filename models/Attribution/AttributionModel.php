<?php
namespace Rehike\Model\Attribution;

use Rehike\i18n\i18n;
use Rehike\Network;
use Rehike\Util\ParsingUtils;

class AttributionModel
{
    public $header;
    public $contents;

    public static function bake($dataHost, $videoId)
    {
        $i18n = i18n::getNamespace("attribution");
        $response = (object) [];
        $response->videoId = $videoId;

        $items = $dataHost->contents->twoColumnBrowseResultsRenderer->tabs[0]->tabRenderer->content->richGridRenderer->contents ?? null;

        if (isset($items[0]->richSectionRenderer))
        {
            $head = $items[0]->richSectionRenderer->content->sourcePivotHeaderRenderer->headerInformation->profilePageHeaderInformationRenderer ?? null;
            $header = (object) [];
            $header->title = $i18n->get('title');
            $header->video = (object) [];
            $header->video->title = $head->title->profilePageHeaderTitleRenderer->title ?? null;
            $thumb = $head->thumbnail->profilePageHeaderThumbnailRenderer->thumbnail->thumbnails[0]->url ?? null;
            // Remove shorts crop
            $header->video->thumbnail = preg_replace("/\?sqp=.*/", "" , $thumb);

            $header->video->watchLater = (object) [
                "isToggled" => false,
                "untoggledTooltip" => $i18n->get("watchLater")
            ];

            $response->header = $header;
        }

        if(isset($items[1]->richItemRenderer))
        {
            $contents = (object) [];
            $contents->title = $i18n->get("sectionTitle");
            $contents->subtitle = $i18n->format("sectionSubtitle", ParsingUtils::getText($header->video->title));
            $contents->items = [];
            for ($i = 1; $i < count($items); $i++)
            {
                if (isset($items[$i]->richItemRenderer))
                {
                    $videoId = $items[$i]->richItemRenderer->content->reelItemRenderer->videoId ?? null;

                    // TODO (kirasicecreamm): I DON'T WANT TO FIX THIS WTF THIS
                    // WILL NEVER WORK OUT 😭😭

                    // // sfv_audio_pivot doesn't give us adequate data
                    // $reelItem = Request::innertubeRequest("reel/reel_item_watch", (object) [
                    //     "disablePlayerResponse" => true,
                    //     "playerRequest" => (object) [
                    //         "videoId" => $videoId
                    //     ]
                    // ]);
                    // $reelData = json_decode($reelItem);

                    // $reelHeader = $reelData->overlay->reelPlayerOverlayRenderer->reelPlayerHeaderSupportedRenderers->reelPlayerHeaderRenderer ?? null;
                    // $contents->items[] = (object) [];
                    // $current = $contents->items[array_key_last($contents->items)];
                    // $thumb = $items[$i]->richItemRenderer->content->reelItemRenderer->thumbnail->thumbnails[0]->url ?? null;
                    // $current->thumbnail = preg_replace("/\?sqp=.*/", "", $thumb);
                    // $current->title = $reelHeader->reelTitleText;
                    // $current->author = $reelHeader->channelTitleText;
                    // $current->authorA11yLabel = $i18n->get("goToUser", ParsingUtils::getText($reelHeader->channelTitleText));
                    // $current->author->navigationEndpoint = $reelHeader->channelNavigationEndpoint;
                    // $current->attrLink = (object) [
                    //     "simpleText" => $i18n->get("viewAttrs"),
                    //     "navigationEndpoint" => (object) [
                    //         "commandMetadata" => (object) [
                    //             "webCommandMetadata" => (object) [
                    //                 "url" => ""
                    //             ]
                    //         ]
                    //     ]
                    // ];
                    // $current->videoId = $videoId;
                    // $current->watchLater = (object) [
                    //     "isToggled" => false,
                    //     "untoggledTooltip" => $i18n->get("watchLater")
                    // ];
                }
            }

            $response->contents = $contents;
        }

        return $response;
    }
}