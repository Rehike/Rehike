<?php
namespace Rehike\Model\Watch\AgeGate;

use Rehike\Util\ParsingUtils;
use Rehike\Model\Common\MButton;
use Rehike\i18n\i18n;

/**
 * Button that shows on the Age Gate and Content Gate screens.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author Isabella <kawapure@gmail.com>
 * @author The Rehike Maintainers
 */
class MPlayerAgeGateButton extends MButton
{
    public $style = "STYLE_PRIMARY";
    public $role = "link";

    public object $commandMetadata;

    public function __construct()
    {
        $this->commandMetadata = (object)[
            "webCommandMetadata" => (object)[
                "url" => "https://accounts.google.com/ServiceLogin?hl=en&service=youtube&uilel=3&continue=http%3A%2F%2Fwww.youtube.com%2Fsignin%3Fcontinue_action%3DQUFFLUhqbm5YUkxYSGRlWHphMjAwczlsLTBlcUFzTmpnQXxBQ3Jtc0trV2hlS1FyeWExa3hJQWtuRTB5TXEyckFwVGNuajAwZU5UWXZzM0ZRR0F5X1hISm8ybmczbUdqQkp6VGExTEhrRXdLOG94NmRlbWhYQ3FQcjRiSHFNbkhWV0dBZHdyNzJ3LW9PRFcwd21sQ0dWY05OemRFZV9hZUo2TGFlY0pjaXAyMEp2aEFPcEVBSHktU3d0dEdhdy1JaWhFUU1SWVVKcm9OUGNjSHc2Sm4yZ2t2Rmx4V2NOTm1MT1NQX1lLaEZ2RjZCMGk%253D%26feature%3Dsubscribe%26action_handle_signin%3Dtrue%26next%3D%252Fchannel%252FUCuAXFkgsw1L7xaCfnd5JJOw%26hl%3Den%26app%3Ddesktop&passive=true"
            ]
        ];
    }

    public static function constructSignedOut(): self
    {
        $pThis = new self;

        $text = i18n::getRawString(
            "watch",
            "playerBlockadeAgeRestrictedSignin"
        );
        $pThis->setText($text);

        return $pThis;
    }

    public static function constructFromYti(object $data): self
    {
        $pThis = new self;

        $pThis->setText(
            ParsingUtils::getText(
                $data->proceedButton->buttonRenderer->text
            ) ?? ""
        );

        // TODO(ev): Write a function for this.
        $appendedParam = strpos($_SERVER["REQUEST_URI"], "?") !== false
            ? "&has_verified=1"
            : "?has_verified=1";

        $pThis->commandMetadata->webCommandMetadata->url
            = $_SERVER["REQUEST_URI"] . $appendedParam;

        return $pThis;
    }
}