<?php
namespace Rehike\Controller;

use Rehike\Controller\core\HitchhikerController;
use Rehike\Network;
use Rehike\Model\Attribution\AttributionModel;

/**
 * Controller for the video attribution information page.
 * 
 * Technically, this page doesn't exist anymore. Rehike includes it for two
 * reasons:
 *   1. As a homage to it being the last true Hitchhiker page online.
 *   2. For compatibility with Shorts attributions, a new feature that does
 *      exist.
 * 
 * @author Aubrey Pankow <aubyomori@gmail.com>
 * @author The Rehike Maintainers
 */
return new class extends HitchhikerController {
    public $template = "attribution";

    public function onGet(&$yt, $request) {
        if (!isset($request->params->v)) {
            $this->template = "oops";
            return;
        }
        
        $videoId = $request->params->v;

        Network::innertubeRequest(
            action: "navigation/resolve_url",
            body: [
                "url" => "https://www.youtube.com/source/" . $videoId . "/shorts"
            ]
        )->then(function ($resolve) {
            $resolveData = $resolve->getJson();

            if (!isset($resolveData->endpoint->browseEndpoint->params)) {
                $this->template = "oops";
                return;
            }
            
            return Network::innertubeRequest(
                action: "browse",
                body: [
                    "browseId" => "FEsfv_audio_pivot",
                    "params" => $resolveData->endpoint->browseEndpoint->params
                ]
                );
        })->then(function ($response) use ($yt, $videoId) {
            $ytdata = $response->getJson();

            $yt->page = AttributionModel::bake($ytdata, $videoId);
        });
    }
};