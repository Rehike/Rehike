<?php
namespace Rehike\Controller\ajax;

use Rehike\Controller\core\AjaxController;
use Rehike\i18n;
use Rehike\Request;
use Rehike\Model\AddTo\MAddTo as AddTo;

return new class extends AjaxController {
    public $useTemplate = true;
    public $template = "ajax/addto";
    public $contentType = "application/xml";

    public function onGet(&$yt, $request) {
        return $this->onPost($yt, $request);
    }

    public function onPost(&$yt, $request) {
        i18n::newNamespace("addto")->registerFromFolder("i18n/addto");

        // Because YouTube's own server is a bit weird, this
        // might go too fast and break everything.
        // Hence: very gross fix for a server-side bug
        sleep(3);

        $response = Request::innertubeRequest("playlist/get_add_to_playlist", (object)[
            "videoIds" => explode(",", $_POST["video_ids"]) ?? [""]
        ]);
        $response = json_decode($response);

        $lists = $response->contents[0]->addToPlaylistRenderer->playlists;

        $yt->page->addto = new AddTo($lists);
    }
};