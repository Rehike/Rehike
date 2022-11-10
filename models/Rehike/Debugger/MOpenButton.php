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
        if ($condensed) $this->class[] = "condensed";

        if ($errorCount > 0) $this->class[] = "rebug-open-button-has-error";

        $this->setText(self::getTitle($errorCount, $condensed));
        $this->hasArrow = true;
    }

    public static function getTitle($errorCount = 0, $condensed = false)
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
        else
        {
            $label = "";
        }

        if ($errorCount > 0)
        {
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
        
        return $label;
    }
}