<?php
namespace Rehike\Model\ChannelSwitcher;

use Rehike\TemplateFunctions as TF;
use Rehike\i18n;

// TODO: video counts, which can be done like so:
/*
    Request::queueInnertubeRequest("id", ""creator/get_creator_channels", (object) [
        "channel_array" => channelIds
    ])
*/
class ChannelSwitcherModel
{
    public static function bake(?array $channels, ?object $switcher, ?string $next)
    {
        $response = (object) [];
        $response->channels = [];

        $i18n = i18n::newNamespace("channel_switcher");
        $i18n->registerFromFolder("i18n/channel_switcher");

        $response->headerTextPrefix = $i18n->pageHeaderPrefix;
        $response->learnMoreLinkText = $i18n->learnMoreLink;

        foreach ($channels as $channel) 
        {
            if (isset($channel->accountItemRenderer))
            {
                $response->channels[] = (object) [ 
                    "accountItemRenderer" => new MChannelItem($channel->accountItemRenderer, $next)
                ];
            }
            elseif (isset($channel->buttonRenderer))
            {
                $response->channels[] = (object) [
                    "createChannelItemRenderer" => new MCreateChannelItem($channel->buttonRenderer)
                ];
            }
        }

        $response->email = TF::getText(
            @$switcher->data->actions[0]->getMultiPageMenuAction
            ->menu->multiPageMenuRenderer->sections[0]
            ->accountSectionListRenderer->header->googleAccountHeaderRenderer->email
        );

        return $response;
    }
}