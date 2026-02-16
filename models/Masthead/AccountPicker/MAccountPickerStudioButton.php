<?php
namespace Rehike\Model\Masthead\AccountPicker;

use Rehike\Model\Common\MButton;
use Rehike\i18n\i18n;
use Rehike\SignInV2\SignIn;

class MAccountPickerStudioButton extends MButton
{
    /**
     * @inheritDoc
     */
    public array $class = [
        "yt-masthead-picker-button",
        "yt-masthead-picker-button-primary"
    ];

    public function __construct()
    {
        $i18n = i18n::getNamespace("masthead");
        $signInInfo = SignIn::getSessionInfo();
        $hasChannel = SignIn::isSignedIn() && !is_null($signInInfo->getUcid());

        if ($hasChannel)
        {
            $this->text = (object) [
                "simpleText" => $i18n->get("accountPickerStudio")
            ];
            $this->navigationEndpoint = (object) [
                "commandMetadata" => (object) [
                    "webCommandMetadata" => (object) [
                        "url" => "//studio.youtube.com/"
                    ]
                ]
            ];
        }
        else
        {
            $this->text = (object) [
                "simpleText" => $i18n->get("accountPickerCreate")
            ];
            $this->navigationEndpoint = (object) [
                "commandMetadata" => (object) [
                    "webCommandMetadata" => (object) [
                        "url" => "//www.youtube.com/create_channel"
                    ]
                ]
            ];
        }

        $this->customAttributes = [
            "target" => "_blank"
        ];
    }
}