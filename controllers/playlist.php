<?php
use \Com\Youtube\Innertube\Request\BrowseRequestParams;

use Rehike\YtApp;
use Rehike\ControllerV2\RequestMetadata;

use \Rehike\Controller\core\NirvanaController;
use \Rehike\Model\Playlist\PlaylistModel;
use \Rehike\Model\Channels\Channels4Model;
use \Rehike\Model\Channels\Channels4\MHeader;
use \Rehike\Model\Channels\Channels4\MCarouselHeader;
use \Rehike\Model\Channels\Channels4\MSecondaryHeader;
use \Rehike\Util\Base64Url;
use \Rehike\Network;
use Rehike\Util\ChannelUtils;
use Rehike\Signin\API as SignIn;

use function Rehike\Async\async;

return new class extends NirvanaController
{
    public string $template = "playlist";

    /**
     * Configures view properties for the page.
     */
    public function setupViewProps(\Rehike\ViewProperties $vp): void
    {
        $vp->pageClassName = "";
        $vp->jsPageName = "playlist";
        $vp->guideDefaultVisibility = true;
        $vp->appbarDefaultVisibility = isset($this->yt->page->channelHeader);
        $vp->enableSnapScaling = true;
    }

    public function onGet(YtApp $yt, RequestMetadata $request): void
    {
        async(function() use (&$yt, $request) {
            if (!isset($request->params->list))
            {
                header("Location: /oops");
            }

            // The playlist ID is stored in the URL parameter ?list=...
            $yt->playlistId = $request->params->list;

            // Internally, all playlist IDs are prefixed with VL, followed by
            // their canonical prefix (PL, RD, LL, UU, etc.).
            $this->setEndpoint("browse", "VL" . $yt->playlistId);

            $response = yield Network::innertubeRequest(
                action: "browse",
                body: [
                    "browseId" => "VL" . $yt->playlistId
                ]
            );

            $ytdata = $response->getJson();

            $yt->page = PlaylistModel::bake($ytdata);

            // Hitchhiker also showed the channel's header, so this also
            // requests the channel page in order to get its owner's header.
            $yt->ucid = $ytdata->header->playlistHeaderRenderer 
                ->ownerEndpoint->browseEndpoint->browseId ?? null;

            if (isset($yt->ucid))
            {
                $params = new BrowseRequestParams();
                $params->setTab("playlists");
                $yt->partiallySelectTabs = true;

                $channelResponse = yield Network::innertubeRequest(
                    action: "browse", 
                    body: [
                        "browseId" => $yt->ucid,
                        "params" => Base64Url::encode($params 
                            ->serializeToString()
                        )
                    ]
                );

                // If there's a channel response, then use it.
                // Otherwise this then is never executed.
                $channelData = $channelResponse->getJson();

                if ($header = @$channelData->header->c4TabbedHeaderRenderer)
                {
                    $yt->page->channelHeader = new MHeader($header, "/channel/$yt->ucid");
                }
                elseif ($header = @$channelData->header->carouselHeaderRenderer)
                {
                    $yt->page->channelHeader = new MCarouselHeader($header, "/channel/$yt->ucid");
                }

                // If user is signed in and channel owner, get data for the
                // secondary channel header.
                $ownerData = null;
                if ($yt->ucid == @SignIn::getInfo()["ucid"])
                {
                    $ownerData = yield ChannelUtils::getOwnerData($yt->ucid);
                }


                if (!is_null($ownerData))
                {
                    $yt->page->secondaryHeader = new MSecondaryHeader($ownerData);
                }

                if (isset($yt->page->channelHeader))
                {
                    $header = &$yt->page->channelHeader;
                    $yt->appbar->addNav();

                    $yt->appbar->nav->addOwner(
                        $header->getTitle(),
                        "/channel/$yt->ucid",
                        $header->thumbnail ?? "",
                    );
                }

                if ($tabs = @$channelData->contents->twoColumnBrowseResultsRenderer->tabs)
                {
                    Channels4Model::processAndAddTabs(
                        $yt,
                        $tabs,
                        $yt->page->channelHeader
                    );
                    
                    if (isset($yt->page->header->title))
                    {
                        $this->setTitle($yt->page->header->title);
                    }
                }
            }
        });
    }
};
