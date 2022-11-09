<?php
use \Com\YouTube\Innertube\Request\BrowseRequestParams;

use \Rehike\Controller\core\NirvanaController;
use \Rehike\Model\Playlist\PlaylistModel;
use \Rehike\Model\Channels\Channels4Model;
use \Rehike\Util\Base64Url;
use \Rehike\Request;
use \Rehike\i18n;

return new class extends NirvanaController {
    public $template = "playlist";

    public function onGet(&$yt, $request) {
        if (!isset($request -> params -> list)) {
            header("Location: /oops");
        }

        $yt -> playlistId = $request -> params -> list;

        $this -> setEndpoint("browse", "VL" . $yt -> playlistId);

        Request::queueInnertubeRequest("main", "browse", (object) [
            "browseId" => "VL" . $yt -> playlistId
        ]);
        $ytdata = json_decode(Request::getResponses()["main"]);

        $yt -> ucid = $ytdata -> header -> playlistHeaderRenderer -> ownerEndpoint -> browseEndpoint -> browseId ?? null;

        $yt -> page = PlaylistModel::bake($ytdata);

        if (isset($yt -> ucid)) {
            // Init i18n for channel model
            $i18n = &i18n::newNamespace("channels");
            $i18n->registerFromFolder("i18n/channels");

            $params = new BrowseRequestParams();
            $params -> setTab("playlists");
            $yt -> partiallySelectTabs = true;

            Request::queueInnertubeRequest("channel", "browse", (object) [
                "browseId" => $yt -> ucid,
                "params" => Base64Url::encode($params -> serializeToString())
            ]);
            $channeldata = json_decode(Request::getResponses()["channel"]);
            $channelmodel = Channels4Model::bake($yt, $channeldata);
            $yt -> page -> channelHeader = $channelmodel -> header ?? null;
        }
    }
};