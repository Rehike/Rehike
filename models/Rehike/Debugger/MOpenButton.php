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

    /**
     * Get the title to be used by the open button.
     * 
     * @param int $errorCount Number of errors that have occurred.
     * @param bool $condensed Different strings are used if the debugger is
     *                        condensed.
     * @return string
     */
    public static function getTitle($errorCount = 0, $condensed = false)
    {
        $i18n = &i18n::getNamespace("rebug");

        $label = "";

        if (!$condensed) 
        {
            /*
             * When the debugger is not in condensed mode, the title will
             * report version information and error information if it occurs,
             * as such:
             *    "Debugger@rehike.master.500 (2 errors)"
             */
            $label = $i18n->openButtonLabel;

            // Attempt to get version from version service
            $versionInfo = &VersionController::$versionInfo;

            if (isset($versionInfo))
            {
                // Append the @rehike string if version is available;
                //    "Debugger@rehike"
                $label .= "@rehike";

                $branch = $versionInfo["branch"] ?? null;
                $revId = $versionInfo["currentRevisionId"] ?? null;

                // Append the branch if it's available;
                //    "Debugger@rehike.master"
                if (null != $branch)
                {
                    $label .= ".$branch";
                }

                // Append the revision ID (commit index) if it is available;
                //    "Debugger@rehike.master.500"
                if (null != $revId)
                {
                    $label .= ".$revId";
                }
            }
        }
        else
        {
            /*
             * When the debugger is in condensed mode, the button should not
             * show at all. This is actually legacy behaviour since the button
             * is not shown at all in condensed mode if no errors have occurred.
             */
            $label = "";
        }

        /*
         * The button also must report the number of errors that have occurred
         * so that the developer can easily see it at a glance. 
         */
        if ($errorCount > 0)
        {
            if (!$condensed)
            {
                /*
                 * When not in condensed mode, append the error count to the
                 * end of the existing label;
                 *    "Debugger@rehike.master.500 (1 error)"
                 *    "Debugger@rehike.master.500 (6 errors)" 
                 */
                if (1 == $errorCount)
                {
                    $label .= " " . $i18n->openButtonErrorCountSingular;
                }
                else
                {
                    $label .= " " . $i18n->openButtonErrorCountPlural(
                        number_format($errorCount)
                    );
                }
            }
            else
            {
                /*
                 * Condensed mode should instead make a true label for the first
                 * time that indicates simply that an error has occurred;
                 *    "An error has occurred. Click to learn more."
                 *    "6 errors have occurred. Click to learn more."
                 */
                if (1 == $errorCount)
                {
                    $label = $i18n->condensedButtonLabelSingular;
                }
                else
                {
                    $label = $i18n->condensedButtonLabelPlural(
                        number_format($errorCount)
                    );
                }
            }
        }
        
        return $label;
    }
}