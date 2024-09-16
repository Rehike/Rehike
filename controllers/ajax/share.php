<?php
namespace Rehike\Controller\ajax;

use Rehike\YtApp;
use Rehike\ControllerV2\RequestMetadata;

use Rehike\Async\Promise;
use \Rehike\Controller\core\AjaxController;
use \Rehike\Network;
use \Rehike\Util\ParsingUtils;
use Rehike\SignInV2\SignIn;
use \Rehike\Model\Share\ShareBoxModel;
use \Rehike\Model\Share\ShareEmbedModel;
use \Rehike\Model\Share\ShareEmailModel;

use \Rehike\Model\Playlist\PlaylistModel;

use function Rehike\Async\async;

return new class extends AjaxController {
    private ?string $videoId;
    private ?string $listId;

    public function onGet(YtApp $yt, RequestMetadata $request): void
    {
        $action = self::findAction();

        if (
            is_null($action) ||
            (!isset($request->params->video_id) && !isset($request->params->list))
        )
            self::error();

        $this->videoId = $request->params->video_id ?? null;
        $this->listId = $request->params->list ?? null;

        switch ($action)
        {
            case "get_share_box":
                self::getShareBox($yt, $request);
                break;
            case "get_embed":
                self::getEmbed($yt, $request);
                break;
            case "get_email":
                self::getEmail($yt, $request);
                break;
        }
    }

    /**
     * Get the share box.
     */
    private function getShareBox(YtApp $yt, RequestMetadata $request): Promise /*<void>*/
    {
        return async(function () use (&$yt) {
            $this->template = "ajax/share/get_share_box";

            $priInfoReq = $this->videoId ? self::videoInfo($this->videoId) : new Promise(fn($r) => $r());
            $listInfoReq = $this->listId ? self::playlistInfo($this->listId) : new Promise(fn($r) => $r());


            Promise::all([
                "priInfo" => $priInfoReq,
                "listInfo" => $listInfoReq
            ])->then(function ($responses) use (&$yt) {

                $priInfo = $responses["priInfo"];
                $listInfo = $responses["listInfo"];
                if (is_null($priInfo))
                {
                    $yt->page = ShareBoxModel::playlistBake(
                        listModel: PlaylistModel::bake($listInfo),
                        listId: $this->listId
                    );
                }
                else
                {
                    $yt->page = ShareBoxModel::bake(
                        videoId: $this->videoId,
                        title: ParsingUtils::getText($priInfo->title),
                        listId: $this->listId
                    );
                }
            });

        });
    }



    private function getEmbed(YtApp $yt, RequestMetadata $request): Promise /*<void>*/
    {
        return async(function () use (&$yt) {
            $this->template = "ajax/share/get_embed";

            $priInfoReq = $this->videoId ? self::videoInfo($this->videoId) : new Promise(fn($r) => $r());
            $listInfoReq = $this->listId ? self::playlistInfo($this->listId) : new Promise(fn($r) => $r());

            Promise::all([
                "priInfo" => $priInfoReq,
                "listInfo" => $listInfoReq
            ])->then(function ($responses) use (&$yt) {

                $priInfo = $responses["priInfo"];
                $listInfo = $responses["listInfo"];

                $yt->page = ShareEmbedModel::bake(
                    videoId: $this->videoId,
                    title: ParsingUtils::getText($priInfo->title),
                    listData: $listInfo
                );

            });

        });
    }


    // TODO: Playlist share email tab, I have no idea what it looked like (only the main share tab was archived/ss'ed)
    private function getEmail(YtApp $yt, RequestMetadata $request): Promise
    {
        return async(function () use (&$yt) {
            $this->template = "ajax/share/get_email";

            $vidInfo = yield self::videoInfo($this->videoId);
            $priInfo = $vidInfo->videoPrimaryInfoRenderer;
            $secInfo = $vidInfo->videoSecondaryInfoRenderer;

            $userData = SignIn::getSessionInfo()->getCurrentChannel();

            $yt->page = ShareEmailModel::bake(
                videoId: $this->videoId,
                title: ParsingUtils::getText($priInfo->title),
                userId: $userData->getUcid(),
                userName: $userData->getDisplayName(),
                desc: $secInfo->attributedDescription->content
            );
        });
    }





    protected function videoInfo(string $videoId): Promise /*<object>*/
    {
        return async(function () use ($videoId) {

            $response = yield Network::innertubeRequest(
                action: "next",
                body: [
                    "videoId" => $videoId
                ]
            );
            $ytdata = $response->getJson();
            $results = $ytdata->contents->twoColumnWatchNextResults->results->results->contents ?? [];

            $priInfo = null;
            $secInfo = null;
            foreach ($results as $infoItem)
            {
                $priInfo = $infoItem->videoPrimaryInfoRenderer ?? $priInfo;
                $secInfo = $infoItem->videoSecondaryInfoRenderer ?? $secInfo;
            }


            if (!isset($priInfo) || !isset($secInfo))
            {
                echo $response;
                self::error();
            }

            // return $priInfo;
            return (object) [
                "videoPrimaryInfoRenderer" => $priInfo,
                "videoSecondaryInfoRenderer" => $secInfo
            ];

        });
    }



    protected function playlistInfo(string $playlistId): Promise
    {
        return async(function () use ($playlistId) {
            $response = yield Network::innertubeRequest(
                action: "browse",
                body: [
                    "browseId" => "VL" . $playlistId
                ]
            );
            return $response->getJson();
        });
    }
};