<?php
namespace Rehike\Model\Masthead\AccountPicker;

use Rehike\Model\Common\MButton;
use Rehike\i18n\i18n;
use Rehike\SignInV2\SignIn;
use Rehike\Model\Common\Thumb\MThumbSquare;
use Rehike\Util\ImageUtils;

class MAccountPickerButton extends MButton
{
    public $noStyle = true;
    public $class = [
        "yt-masthead-user-icon"
    ];
    public $attributes = [
        "force-position" => "true",
        "position-fixed" => "true",
        "orientation" => "vertical",
        "position" => "bottomleft"
    ];

    public function __construct()
    {
        $i18n = i18n::getNamespace("masthead");
        $signInInfo = SignIn::getSessionInfo();
        $this->thumb = new MThumbSquare([
            "image" => ImageUtils::changeSize($signInInfo->getCurrentChannel()?->getAvatarUrl() ?? "", 27),
            "size" => 27,
            "delayload" => true
        ]);
        $this->accessibility = (object) [
            "accessibilityData" => (object) [
                "label" => $i18n->get("accountPickerLabel")
            ]
        ];
        $this->clickcard = new MAccountPickerClickcard();
    }
}