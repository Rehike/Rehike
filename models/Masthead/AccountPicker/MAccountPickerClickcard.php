<?php
namespace Rehike\Model\Masthead\AccountPicker;

use Rehike\i18n;
use Rehike\Signin\API as SignIn;
use Rehike\Model\Common\Thumb\MThumbSquare;
use Rehike\Util\ImageUtils;

class MAccountPickerClickcard {
    public $template = "masthead_account_picker";
    public $id = "yt-masthead-account-picker";
    public $cardAction = "yt.www.masthead.handleAccountPickerClick";
    public $cardClass = [
        "yt-masthead-account-picker-card",
    ];
    public $class = "yt-masthead-account-picker";

    public function __construct() {
        $i18n = i18n::getNamespace("masthead");
        $signInInfo = (object) SignIn::getInfo();
        $activeChannel = $signInInfo->activeChannel;

        $this->content = (object) [];
        $content = &$this->content;

        $content->email = (object) [
            "simpleText" => $signInInfo->googleAccount["email"],
            "navigationEndpoint" => (object) [
                "commandMetadata" => (object) [
                    "webCommandMetadata" => (object) [
                        "url" => "//myaccount.google.com/u/0"
                    ]
                ]
            ]
        ];
        $content->username = $activeChannel["name"];
        $content->subCount = $activeChannel["byline"];
        $content->photo = (object) [
            "simpleText" => $i18n->accountPickerPhotoChange,
            "navigationEndpoint" => (object) [
                "commandMetadata" => (object) [
                    "webCommandMetadata" => (object) [
                        "url" => "//myaccount.google.com/u/0/profile#profile_photo"
                    ]
                ]
            ],
            "thumb" => new MThumbSquare([
                "image" => ImageUtils::changeGgphtImageSize($activeChannel["photo"], 64),
                "size" => 64,
                "delayload" => true
            ])
        ];
        $content->buttons = [];
        $content->buttons[] = new MAccountPickerStudioButton();
        $content->buttons[] = new MAccountPickerSettingsButton();

        $content->footer = [];
        $content->footer[] = new MAccountPickerAddButton();
        $content->footer[] = new MAccountPickerSignOutButton();
    }
}