<?php
use \Com\Youtube\Innertube\Request\BrowseRequestParams;

use \Rehike\Controller\core\NirvanaController;
use \Rehike\Model\Playlist\PlaylistModel;
use \Rehike\Model\Channels\Channels4Model;
use \Rehike\Util\Base64Url;
use \Rehike\Network;
use \Rehike\i18n;

return new class extends NirvanaController {
    public $template = "playlist";

    public function onGet(&$yt, $request) {
        if (!isset($request -> params -> list)) {
            header("Location: /oops");
        }

        // The playlist ID is stored in the URL parameter ?list=...
        $yt -> playlistId = $request -> params -> list;

        // Internally, all playlist IDs are prefixed with VL, followed by
        // their canonical prefix (PL, RD, LL, UU, etc.).
        $this -> setEndpoint("browse", "VL" . $yt -> playlistId);

        Network::innertubeRequest(
            action: "browse",
            body: [
                "browseId" => "VL" . $yt -> playlistId
            ]
        )->then(function ($response) use ($yt) {
            $ytdata = $response->getJson();

            $yt -> page = PlaylistModel::bake($ytdata);

            // Hitchhiker also showed the channel's header, so this also
            // requests the channel page in order to get its owner's header.
            $yt -> ucid = $ytdata -> header -> playlistHeaderRenderer 
                -> ownerEndpoint -> browseEndpoint -> browseId ?? null;

            if (isset($yt -> ucid)) {
                // Init i18n for channel model
                $i18n = &i18n::newNamespace("channels");
                $i18n->registerFromFolder("i18n/channels");

                $params = new BrowseRequestParams();
                $params -> setTab("playlists");
                $yt -> partiallySelectTabs = true;

                return Network::innertubeRequest(
                    action: "browse", 
                    body: [
                        "browseId" => $yt -> ucid,
                        "params" => Base64Url::encode($params 
                            -> serializeToString()
                        )
                    ]
                );
            }
        })->then(function ($channelResponse) use ($yt) {
            // If there's a channel response, then use it.
            // Otherwise this then is never executed.
            $channelData = $channelResponse->getJson();

            // TODO: Inefficient procedure, should render header directly.
            $channelModel = Channels4Model::bake($yt, $channelData);
            $yt -> page -> channelHeader = $channelModel -> header ?? null;
        });
    }
};
