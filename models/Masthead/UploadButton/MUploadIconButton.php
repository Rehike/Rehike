<?php
namespace Rehike\Model\Masthead\UploadButton;

use Rehike\Model\Common\MButton;
use Rehike\i18n\i18n;
use Rehike\SignInV2\SignIn;

class MUploadIconButton extends MButton
{
    public string $targetId = "upload-btn";
    public string $style = "STYLE_OPACITY";

    public function __construct()
    {
        $i18n = i18n::getNamespace("masthead");

        $signInInfo = SignIn::getSessionInfo();
        $hasChannel = SignIn::isSignedIn() && !is_null($signInInfo->getUcid());
        
        if ($hasChannel)
        {
            $ucid = $signInInfo->getUcid();
        }

        $this->tooltip = $i18n->get("uploadButton");
        $this->navigationEndpoint = (object) [
            "commandMetadata" => (object) [
                "webCommandMetadata" => (object) [
                    "url" => $hasChannel ? "//studio.youtube.com/channel/$ucid/videos?d=ud" : "/create_channel?upsell=upload&next=/"
                ]
            ]
        ];
        $this->icon = (object) [
            "iconType" => "MATERIAL_UPLOAD"
        ];
    }
}