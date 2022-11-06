<?php
namespace Rehike\Controller;

use Rehike\Request;
use Rehike\Signin\API as SignIn;

return new class extends \Rehike\Controller\core\HitchhikerController {
    public $useTemplate = false;

    public function onGet(&$yt, $request) {
        if (!SignIn::isSignedIn()) {
            header("Location: /");
        }

        Request::queueInnertubeRequest("resolve", "navigation/resolve_url", (object) [
            "url" => "https://www.youtube.com/profile"
        ]);
        $ytdata = json_decode(Request::getResponses()["resolve"]);

        if ($a = @$ytdata -> endpoint -> urlEndpoint -> url) {
            header("Location: " . str_replace("https://www.youtube.com", "", $a));
        }
    }
};