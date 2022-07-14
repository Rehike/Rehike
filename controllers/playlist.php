<?php
use \Rehike\Controller\core\NirvanaController;
use \Rehike\Model\Playlist\PlaylistModel;
use \Rehike\Model\Common\Alert\MAlert;
use \Rehike\Model\Common\Alert\MAlertType;

use \Rehike\Request;
use \Rehike\i18n;

return new class extends NirvanaController {
    public $template = "playlist";

    public function onGet(&$yt, $request) {
        if (!isset($request -> params -> list)) {
            header("Location: /oops");
        }

        $yt -> playlistId = $request -> params -> list;

        $response = Request::innertubeRequest("browse", (object) [
            "browseId" => "VL" . $yt -> playlistId,
            "params" => "wgYCCAA%3D"
        ]);
        $ytdata = json_decode($response);

        $yt -> page = (object) [];
        $yt -> page = PlaylistModel::bakePL($ytdata);
    }
};