<?php
namespace Rehike\Model\Masthead\AccountPicker;

use Rehike\Model\Common\MButton;
use Rehike\i18n;
use Rehike\Signin\API as SignIn;
use Rehike\Model\Common\Thumb\MThumbSquare;
use Rehike\Util\ImageUtils;

class MAccountPickerButton extends MButton {
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

    public function __construct() {
        $i18n = i18n::getNamespace("masthead");
        $signInInfo = (object) SignIn::getInfo();
        $this->thumb = new MThumbSquare([
            "image" => ImageUtils::changeGgphtImageSize($signInInfo->activeChannel["photo"], 27),
            "size" => 27,
            "delayload" => true
        ]);
        $this->accessibility = (object) [
            "accessibilityData" => (object) [
                "label" => $i18n->accountPickerLabel
            ]
        ];
        $this->clickcard = new MAccountPickerClickcard();
    }
}