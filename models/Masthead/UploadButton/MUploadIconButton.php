<?php
namespace Rehike\Model\Masthead\UploadButton;

use Rehike\Model\Common\MButton;
use Rehike\i18n\i18n;
use Rehike\Signin\API as SignIn;

class MUploadIconButton extends MButton {
    public $targetId = "upload-btn";
    public $style = "STYLE_OPACITY";

    public function __construct() {
        $i18n = i18n::getNamespace("masthead");

        $signInInfo = (object) SignIn::getInfo();
        $hasChannel = SignIn::isSignedIn() && isset($signInInfo->ucid);
        if ($hasChannel) $ucid = $signInInfo->ucid;

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