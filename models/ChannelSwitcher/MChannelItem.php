<?php

namespace Rehike\Model\ChannelSwitcher;

use Rehike\i18n;
use Rehike\TemplateFunctions as TF;

class MChannelItem
{
    public bool $selected = false;
    public string $ucid;
    public string $url;
    public string $avatar;
    public string $title;
    public string $subscriberCountText;

    
    public function __construct(object $data)
    {
        $i18n = i18n::getNamespace("channel_switcher");

        $this->selected = $data->isSelected;
        $this->avatar = TF::getThumb($data->accountPhoto, 56);
        $this->title = TF::getText(@$data->accountName);

        $this->subscriberCountText = $data->hasChannel
        ? TF::getText(@$data->accountByline)
        : $i18n->ownerAccountNoChannel;

        $tokenRoot = $data->serviceEndpoint->selectActiveIdentityEndpoint->supportedTokens;

        foreach($tokenRoot as $token)
        {
            if (isset($token->offlineCacheKeyToken))
            {
                $this->ucid = "UC" . $token->offlineCacheKeyToken->clientCacheKey;
            }
            elseif (isset($token->accountSigninToken))
            {
                $this->url = $token->accountSigninToken->signinUrl;
            }
        }
    }
}