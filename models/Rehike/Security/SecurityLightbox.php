<?php
namespace Rehike\Model\Rehike\Security;

use Rehike\Model\Common\MButton;
use Rehike\Model\Traits\Runs;

/**
 * Informs the user of a security vulnerability via a lightbox.
 * 
 * Examples include having Apache installed as a service, which involves
 * misconfiguration of middleware and which can be exploited by malware.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */
class SecurityLightbox
{
    use Runs;

    public string $title;
    public array $message = [];
    public MButton $learnMoreButton;
    public MButton $dismissButton;
    public MButton $moreOptionsButton;
    public MButton $doNotShowAgainButton;

    public function __construct()
    {
        $this->title = "Security warning";

        $this->createMessage();

        $this->learnMoreButton = new MButton([
            "style" => "STYLE_PRIMARY",
            "targetId" => "rehike-security-notice-learn-more-button",
            "customAttributes" => [
                "target" => "_blank"
            ],
            "text" => (object)[
                "runs" => [
                    (object)[
                        "text" => "Learn more"
                    ]
                ]
            ],
            "navigationEndpoint" => (object)[
                "commandMetadata" => (object)[
                    "webCommandMetadata" => (object)[
                        "url" => "https://github.com/Rehike/Rehike/wiki/Running-Apache-as-user-on-Windows"
                    ]
                ]
            ]
        ]);

        $this->dismissButton = new MButton([
            "style" => "STYLE_DEFAULT",
            "targetId" => "rehike-security-notice-dismiss-button",
            "text" => (object)[
                "runs" => [
                    (object)[
                        "text" => "Dismiss"
                    ]
                ]
            ]
        ]);

        $this->moreOptionsButton = new MButton([
            "style" => "STYLE_OPACITY",
            "targetId" => "rehike-security-notice-more-options-button",
            "text" => (object)[
                "runs" => [
                    (object)[
                        "text" => "More options"
                    ]
                ]
            ],
            "hasArrow" => true
        ]);

        $this->doNotShowAgainButton = new MButton([
            "style" => "STYLE_DESTRUCTIVE",
            "targetId" => "rehike-security-notice-ignore-button",
            "text" => (object)[
                "runs" => [
                    (object)[
                        "text" => "Do not show this again"
                    ]
                ]
            ]
        ]);
    }

    private function createMessage(): void
    {
        $this->message[] = $this->createRun(
            "Rehike is running as SYSTEM. This can create a security " .
            "vulnerability, and it is recommended that you reconfigure " .
            "Apache to run as your own account. "
        );

        // $this->message[] = $this->createRun(
        //     "Learn more.",
        //     "https://github.com/Rehike/Rehike/wiki/Running-Apache-as-user-on-Windows"
        // );
    }

    private function createDismissButton(): MButton
    {
        $button = new MButton([
            "style" => "STYLE_DEFAULT",
            "id" => "rehike-security-notice-dismiss-button"
        ]);

        return $button;
    }
}