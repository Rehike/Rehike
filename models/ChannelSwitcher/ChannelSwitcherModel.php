<?php
namespace Rehike\Model\ChannelSwitcher;

use Rehike\TemplateFunctions as TF;
use Rehike\i18n;

// TODO: i18n
// TODO: video counts, which can be done like so:
/*
    Request::queueInnertubeRequest("id", ""creator/get_creator_channels", (object) [
        "channel_array" => channelIds
    ])
*/
class ChannelSwitcherModel {

    public static function bake($channelsData, $switcherData) {
        $response = (object) [];
        $channels = [];


        for ($i = 0; $i < count($channelsData); $i++) {

            if (!isset($channelsData[$i]->accountItemRenderer)) continue;
            $channels[] = new MChannelItem($channelsData[$i]->accountItemRenderer);

        }

        $email = TF::getText(@$switcherData->data->actions[0]->getMultiPageMenuAction->menu->multiPageMenuRenderer->sections[0]->accountSectionListRenderer->header->googleAccountHeaderRenderer->email);


        $response->channels = $channels;
        $response->email = $email;
        return $response;
    }
}