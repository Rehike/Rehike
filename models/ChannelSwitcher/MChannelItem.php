<?php

namespace Rehike\Model\ChannelSwitcher;

use Rehike\TemplateFunctions as TF;

// TODO: i18n
class MChannelItem {

    public $isSelected = false;
    public $uChannelId;
    public $signInToken;
    public $accountPhoto;
    public $accountName;
    public $accountSubscribers;

    
    public function __construct($channelJson) {


        $this->isSelected = $channelJson->isSelected;
        $this->accountPhoto = $channelJson->accountPhoto->thumbnails[0]->url;
        $this->accountName = TF::getText(@$channelJson->accountName);

        $this->accountSubscribers = $channelJson->hasChannel ? str_replace("No", "0", TF::getText(@$channelJson->accountByline)) 
                                                             : "Owner account, no channel";

        $tokenRoot = $channelJson->serviceEndpoint->selectActiveIdentityEndpoint->supportedTokens;


        foreach($tokenRoot as $token) {
            
            if (isset($token->offlineCacheKeyToken)) {
                $this->uChannelId = "UC" . $token->offlineCacheKeyToken->clientCacheKey;
            }

            if (isset($token->accountSigninToken)) {
                $this->signInToken = str_replace("skip_identity_prompt=true", "skip_identity_prompt=False", $token->accountSigninToken->signinUrl);
            }

        }


    }
}