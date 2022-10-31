<?php
namespace Rehike\Controller;

use Rehike\Controller\core\NirvanaController;
use Rehike\Signin\API as SignIn;
use Rehike\Request;
use Rehike\Model\Feed\MFeedAppbarNav;

return new class extends NirvanaController {
    public $template = "feed/subscriptions";

    public function onGet(&$yt, $request) {
        $this->useJsModule('www/feed');
        $this->setEndpoint("browse", "FEsubscriptions");
        $yt->appbar->nav = new MFeedAppbarNav("FEsubscriptions");

        if (!SignIn::isSignedIn()) {
            header("Location: /");
        }

        $useListFlow = $request -> params -> flow == "2";
        $requestBody = [
            "browseId" => "FEsubscriptions"
        ];
        if ($useListFlow) $requestBody += [
            "params" => "MAI%3D",
            "subscriptionsSettingsState" => "MY_SUBS_SETTINGS_STATE_LAYOUT_FORMAT_LIST"
        ];

        Request::queueInnertubeRequest("subscriptions", "browse", (object) $requestBody);
        $response = Request::getResponses()["subscriptions"];
        $ytdata = json_decode($response);
        $yt -> page -> content = $ytdata -> contents -> twoColumnBrowseResultsRenderer -> tabs[0] -> tabRenderer -> content ?? null;
    }
};