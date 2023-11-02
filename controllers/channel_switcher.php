<?php
namespace Rehike\Controller;

use Rehike\YtApp;
use Rehike\ControllerV2\RequestMetadata;

use Rehike\Controller\core\HitchhikerController;
use Rehike\Network;
use Rehike\Signin\API as SignIn;
use \Rehike\Model\ChannelSwitcher\ChannelSwitcherModel;

use function Rehike\Async\async;

// TODO: send "X-Goog-AuthUser" header in innertube request
return new class extends HitchhikerController
{
    public string $template = "channel_switcher";

    /**
     * Configures view properties for the page.
     */
    public function setupViewProps(\Rehike\ViewProperties $vp): void
    {
        $vp->leftAlignPage = true;
    }

    public function onGet(YtApp $yt, RequestMetadata $request): void
    {
        async(function() use (&$yt, &$request) {
            if (!SignIn::isSignedIn())
            {
                header("Location: https://accounts.google.com/v3/signin/identifier?dsh=S369128673%3A1675950960460363&continue=https%3A%2F%2Fwww.youtube.com%2Fsignin%3Faction_handle_signin%3Dtrue%26app%3Ddesktop%26hl%3Den%26next%3Dhttps%253A%252F%252Fwww.youtube.com%252Fchannel_switcher%26feature%3Dredirect_login&hl=en&passive=true&service=youtube&uilel=3&flowName=GlifWebSignIn&flowEntry=ServiceLogin&ifkv=AWnogHdTaLSFWkbPzHGsk61TYFu3C76VEZLMz1uTSkocGsIfWWBDd8s0xL3geNfwrIMQ3RiPfuGgGg");
            }

            $ytdata = (yield Network::innertubeRequest(
                action: "account/accounts_list",
                body: [
                    "requestType" => "ACCOUNTS_LIST_REQUEST_TYPE_CHANNEL_SWITCHER",
                    "callCircumstance" => "SWITCHING_USERS_FULL"
                ]
            ))->getJson();

            $channels = $ytdata->actions[0]->updateChannelSwitcherPageAction->page->channelSwitcherPageRenderer->contents ?? null;

            // TODO: Get from cache
            $switcherOriginal = (yield Network::urlRequestFirstParty(
                "https://www.youtube.com/getAccountSwitcherEndpoint",
            ))->getText();
            $switcher = json_decode(substr($switcherOriginal, 4));

            $yt->channels = $channels;

            $next = null;
            if (isset($request->params->next))
            {
                $next = $request->params->next;
            }

            $yt->page = ChannelSwitcherModel::bake($channels, $switcher, $next);
        });
    }
};