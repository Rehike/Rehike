<?php
namespace Rehike\Signin;

/**
 * A really nasty switcher parser, wrote by Taniko circa March 2022.
 * 
 * Needless to say, it's one of the messier Rehike modules.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */
class Switcher
{
    /**
     * Parse a getAccountSwitcherEndpoint response.
     * 
     * @param string $response Raw response (not JSON decoded).
     * @return array Associative array of wrappers.
     */
    public static function parseResponse(string $response): array
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

    /**
     * Get Google account information from the header.
     * 
     * @param object $data Decoded from the response.
     * @return array Associative array of parsed data.
     */
    public static function getGoogAccInfo(object $data): array
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

    /**
     * Get the available channels on a Google account.
     * 
     * @param object $data Decoded from the response.
     * @return array Iterative array of channel information.
     */
    public static function getChannels(object $data): array
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

    /**
     * Get information about the active channel.
     * 
     * @param object $data Decoded from the response.
     * @return ?array Channel item data
     */
    public static function getActiveChannel(object $data): ?array
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

    /**
     * Parse information about an account item.
     * 
     * @param object $account From the original response.
     * @return array Parsed associative array.
     */
    public static function accountItem(object $account): array
    {
        if (isset($account->serviceEndpoint->selectActiveIdentityEndpoint->supportedTokens))
        foreach ($account->serviceEndpoint->selectActiveIdentityEndpoint->supportedTokens as $token)
        {
            if (isset($token->pageIdToken))
            {
                $gaiaId = $token->pageIdToken->pageId;
            }
            elseif (isset($token->accountSigninToken))
            {
                $switchUrl = $token->accountSigninToken->signinUrl;
            }
        }

        return
            [
                "name" => $account->accountName->simpleText,
                "photo" => $account->accountPhoto->thumbnails[0]->url,
                "byline" => $account->accountByline->simpleText,
                "selected" => $account->isSelected,
                "hasChannel" => $account->hasChannel ?? false,
                "gaiaId" => $gaiaId ?? "",
                "switchUrl" => $switchUrl ?? ""
            ];
    }
}