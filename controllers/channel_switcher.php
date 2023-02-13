<?php
namespace Rehike\Controller;

use Rehike\Controller\core\HitchhikerController;
use Rehike\Request;
use Rehike\Signin\API as SignIn;
use \Rehike\Model\ChannelSwitcher\ChannelSwitcherModel;

// TODO: send "X-Goog-AuthUser" header in innertube request
return new class extends HitchhikerController
{
    public $template = "channel_switcher";

    public function onGet(&$yt, $request)
    {
        if (!SignIn::isSignedIn())
        {
            header("Location: https://accounts.google.com/v3/signin/identifier?dsh=S369128673%3A1675950960460363&continue=https%3A%2F%2Fwww.youtube.com%2Fsignin%3Faction_handle_signin%3Dtrue%26app%3Ddesktop%26hl%3Den%26next%3Dhttps%253A%252F%252Fwww.youtube.com%252Fchannel_switcher%26feature%3Dredirect_login&hl=en&passive=true&service=youtube&uilel=3&flowName=GlifWebSignIn&flowEntry=ServiceLogin&ifkv=AWnogHdTaLSFWkbPzHGsk61TYFu3C76VEZLMz1uTSkocGsIfWWBDd8s0xL3geNfwrIMQ3RiPfuGgGg");
        }

        Request::queueInnertubeRequest("channels", "account/accounts_list", (object) [
            "requestType" => "ACCOUNTS_LIST_REQUEST_TYPE_CHANNEL_SWITCHER",
            "callCircumstance" => "SWITCHING_USERS_FULL"
        ]);
        $ytdata = json_decode(Request::getResponses()["channels"]);
        $channels = $ytdata->actions[0]->updateChannelSwitcherPageAction->page->channelSwitcherPageRenderer->contents ?? null;

        // TODO: Get from cache
        Request::queueUrlRequest("switcher", "https://www.youtube.com/getAccountSwitcherEndpoint");
        $switcher = json_decode(substr(Request::getResponses()["switcher"], 4));

        $yt->channels = $channels;

        $next = null;
        if (isset($request->params->next))
        {
            $next = $request->params->next;
        }

        $yt->page = ChannelSwitcherModel::bake($channels, $switcher, $next);

    }
};