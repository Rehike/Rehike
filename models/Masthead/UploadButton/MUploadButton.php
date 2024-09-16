<?php
namespace Rehike\Model\Masthead\UploadButton;

use Rehike\Model\Common\MButton;
use Rehike\i18n\i18n;
use Rehike\SignInV2\SignIn;

class MUploadButton extends MButton
{
    public $targetId = "upload-btn";

    public function __construct()
    {
        $i18n = i18n::getNamespace("masthead");

        $signInInfo = SignIn::getSessionInfo();
        $hasChannel = SignIn::isSignedIn() && !is_null($signInInfo->getUcid());
        
        if ($hasChannel)
        {
            $ucid = $signInInfo->getUcid();
        }

        $this->text = (object) [
            "simpleText" => $i18n->get("uploadButton")
        ];
        $this->navigationEndpoint = (object) [
            "commandMetadata" => (object) [
                "webCommandMetadata" => (object) [
                    "url" => $hasChannel ? "//studio.youtube.com/channel/$ucid/videos?d=ud" : "/create_channel?upsell=upload&next=/"
                ]
            ]
        ];
    }
}