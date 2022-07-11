<?php
namespace Rehike\Signin;

class Switcher
{
    // Parse getAccountSwitcherEndpoint response

    public static function parseResponse($response)
    {
        $response = json_decode(substr($response, 4, strlen($response)));
        $info =
            [
                "googleAccount" => self::getGoogAccInfo($response),
                "activeChannel" => self::getActiveChannel($response),
                "channelPicker" => self::getChannels($response)
            ];
        
        return $info;
    }

    public static function getGoogAccInfo($data)
    {
        $header = $data->data->actions[0]->getMultiPageMenuAction->
            menu->multiPageMenuRenderer->sections[0]->accountSectionListRenderer->
            header->googleAccountHeaderRenderer;
        
        return
            [
                "email" => $header->email->simpleText,
                "name" => $header->name->simpleText
            ];
    }

    public static function getChannels($data)
    {
        $items = $data->data->actions[0]->getMultiPageMenuAction->
            menu->multiPageMenuRenderer->sections[0]->accountSectionListRenderer->
            contents[0]->accountItemSectionRenderer->contents;
        
        $channels = [];

        for ($i = 0, $c = count($items); $i < $c; $i++)
        if (isset($items[$i]->accountItem))
        {
            $channels[] = self::accountItem($items[$i]->accountItem);
        }

        return $channels;
    }

    public static function getActiveChannel($data)
    {
        $channels = self::getChannels($data);

        foreach ($channels as $index => $channel)
        {
            if ($channel["selected"])
            {
                return $channel;
            }
        }
        
        return null;
    }

    public static function accountItem($account)
    {
        return
            [
                "name" => $account->accountName->simpleText,
                "photo" => $account->accountPhoto->thumbnails[0]->url,
                "byline" => $account->accountByline->simpleText,
                "selected" => $account->isSelected,
                "hasChannel" => $account->hasChannel,
                "gaiaId" => $account->serviceEndpoint->selectActiveIdentityEndpoint->supportedTokens[0]->pageIdToken->pageId ?? ""
            ];
    }
}