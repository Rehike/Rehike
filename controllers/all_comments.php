<?php
use \Rehike\Controller\core\NirvanaController;
use \Rehike\Model\AllComments\AllCommentsModel;
use \Rehike\Request;

use Com\Youtube\Innertube\Request\NextRequestParams;
use Com\Youtube\Innertube\Request\NextRequestParams\UnknownThing;


return new class extends NirvanaController {
    public $template = "all_comments";

    public function onGet(&$yt, $request) {
        // invalid request redirect
        if (!isset($_GET['v'])) {
            header('Location: /');
            die();
        }

        $yt->videoId = $request->params->v;

        // Generate LC (local comment) param
        if (isset($request->params->lc)) {
            $param = new NextRequestParams();
            
            // I don't know if this is needed, but I want to include it
            // anyways.
            $param->setUnknownThing(new UnknownThing(["a" => 0]));

            $param->setLinkedCommentId($request->params->lc);

            $lcParams = Base64Url::encode($param->serializeToString());
        }

        $videoResponse = Request::innertubeRequest("next", (object) [
            "videoId" => $yt->videoId,
            "params" => $lcParams ?? null
        ]);
        $videoData = json_decode($videoResponse);

        $yt->page = AllCommentsModel::bake($yt, $videoData);
    }
};