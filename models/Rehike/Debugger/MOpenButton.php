<?php
namespace Rehike\Model\Rehike\Debugger;

use \Rehike\i18n;
use \Rehike\Version\VersionController;
use \Rehike\Model\Common\MButton;

/**
 * Implements the Rehike debugger popup open button.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Developers
 */
class MOpenButton extends MButton
{
    public $style = "STYLE_OPACITY";

    public $class = [
        "yt-uix-button-reverse",
        "rebug-open-button"
    ];

    public function __construct($errorCount, $condensed)
    {
        $i18n = &i18n::getNamespace("rebug");

        $label = "";

        if (!$condensed) 
        {
            $label = $i18n->openButtonLabel;

            // Attempt to get version from version service
            $versionInfo = &VersionController::$versionInfo;

            if (isset($versionInfo))
            {
                $label .= "@rehike";

                $branch = $versionInfo["branch"] ?? null;
                $revId = $versionInfo["currentRevisionId"] ?? null;

                if (null != $branch)
                {
                    $label .= ".$branch";
                }

                if (null != $revId)
                {
                    $label .= ".$revId";
                }
            }
        }

        if ($errorCount > 0)
        {
            $this->class[] = "rebug-open-button-has-error";

            if (!$condensed)
            {
                if (1 == $errorCount)
                {
                    $label .= " " . $i18n->openButtonErrorCountSingular;
                }
                else
                {
                    $label .= " " . $i18n->openButtonErrorCountPlural(number_format($errorCount));
                }
            }
            else
            {
                if (1 == $errorCount)
                {
                    $label = $i18n->condensedButtonLabelSingular;
                }
                else
                {
                    $label = $i18n->condensedButtonLabelPlural(number_format($errorCount));
                }
            }
        }

        $this->setText($label);
        $this->hasArrow = true;
    }
}