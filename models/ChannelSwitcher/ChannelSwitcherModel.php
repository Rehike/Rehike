<?php
namespace Rehike\Model\ChannelSwitcher;

use Rehike\Util\ParsingUtils;
use Rehike\i18n\i18n;
use Rehike\FormattedString;

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

        $i18n = i18n::getNamespace("channel_switcher");

        $response->learnMoreLinkText = $i18n->get("learnMoreLink");

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

        $response->email = ParsingUtils::getText(
            @$switcher->data->actions[0]->getMultiPageMenuAction
            ->menu->multiPageMenuRenderer->sections[0]
            ->accountSectionListRenderer->header->googleAccountHeaderRenderer->email
        );

        $response->headerText = FormattedString::fromTemplate(
            $i18n->format("pageHeader", $response->email)
        );

        return $response;
    }
}