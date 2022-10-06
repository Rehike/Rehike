<?php
namespace Rehike\Controller;

use Rehike\Controller\core\HitchhikerController;
use Rehike\Request;
use Rehike\Model\Attribution\AttributionModel;

return new class extends HitchhikerController {
    public $template = "attribution";

    public function onGet(&$yt, $request) {
        if (!isset($request -> params -> v)) {
            $this -> template = "oops";
            return;
        }
        
        $videoId = $request -> params -> v;
        
        $resolve = Request::innertubeRequest("navigation/resolve_url", (object) [
            "url" => "https://www.youtube.com/source/" . $videoId . "/shorts"
        ]);
        $resolveData = json_decode($resolve);
        if (!isset($resolveData -> endpoint -> browseEndpoint -> params)) {
            $this -> template = "oops";
            return;
        }
        
        $response = Request::innertubeRequest("browse", (object) [
            "browseId" => "FEsfv_audio_pivot",
            "params" => $resolveData -> endpoint -> browseEndpoint -> params
        ]);
        $ytdata = json_decode($response);

        $yt -> page = AttributionModel::bake($ytdata, $videoId);
    }
};