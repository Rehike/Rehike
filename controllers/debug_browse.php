<?php
namespace Rehike\Controller;

use Rehike\Controller\core\NirvanaController;

use Rehike\Request;

return new class extends NirvanaController {
    public $template = "debug_browse";

    public function onGet(&$yt, $request)
    {
        $this->useJsModule("www/feed");

        // Get the requested tab
        $tab = "featured";
        if (isset($request->path[2]) && "" != @$request->path[2])
        {
            $tab = $request->path[2];
        }

        // Perform InnerTube request
        $response = Request::innertubeRequest("browse", (object)[
            "browseId" => $request->params->browse_id
        ]);

        $page = json_decode($response);

        $yt->page = $page;
    }
};