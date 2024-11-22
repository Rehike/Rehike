<?php
namespace Rehike\Model\Rehike\Security;

use Rehike\Model\Common\MButton;
use Rehike\Model\Traits\Runs;
use Rehike\i18n\i18n;

use Rehike\i18n\Internal\Lang\NamespaceBoundLanguageApi;

/**
 * Informs the user of a security vulnerability via a lightbox.
 * 
 * Examples include having Apache installed as a service, which involves
 * misconfiguration of middleware and which can be exploited by malware.
 * 
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

    private NamespaceBoundLanguageApi $i18n;

    public function __construct()
    {
        $this->i18n = i18n::getNamespace('rehike/security_lightbox');
        $this->title = $this->i18n->get("securityWarning");

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
                        "text" => $this->i18n->get("learnMoreButton")
                    ]
                ]
            ],
            "navigationEndpoint" => (object)[
                "commandMetadata" => (object)[
                    "webCommandMetadata" => (object)[
                        "url" => $this->i18n->get("languageRespectiveWikiLink")
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
                        "text" => $this->i18n->get("dismissButton")
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
                        "text" => $this->i18n->get("moreOptionsButton")
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
                        "text" => $this->i18n->get("doNotShowAgainButton")
                    ]
                ]
            ]
        ]);
    }

    private function createMessage(): void
    {
        $this->message[] = $this->createRun(
            $this->i18n->get("runningAsSystemMessage")
        );
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