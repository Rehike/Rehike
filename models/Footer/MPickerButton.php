<?php
namespace Rehike\Model\Footer;

use Rehike\i18n\i18n;
use Rehike\Model\Common\MButton;

abstract class MPickerButton extends MButton
{
    public bool $hasArrow = true;
    
    /**
     * Gets a formatted text label for the frontend.
     */
    protected function getFormattedLabel(string $template, string $currentValue): object
    {
        $result = (object)[
            "runs" => []
        ];
        
        $labelContent = explode("%s", $template);
        
        
        if (isset($labelContent[0]) && !empty($labelContent[0]))
        {
            // If the label is left aligned (which is almost always the case), then we add a
            // run for it on the left.
            $result->runs = [
                (object)[
                    "text" => trim($labelContent[0]),
                    "class" => "yt-picker-button-label"
                ],
                (object)[
                    "text" => " " . $currentValue
                ]
            ];
        }
        else if (isset($labelContent[1]) && !empty($labelContent[1]))
        {
            // If the label is right aligned (will this ever be the case?), then we add a
            // run for it on the right.
            $result->runs = [
                (object)[
                    "text" => $currentValue
                ],
                (object)[
                    "text" => " " . trim($labelContent[0]),
                    "class" => "yt-picker-button-label"
                ]
            ];
        }
        else
        {
            // If there is content on both the left and right, then we just throw it as
            // one string.
            try
            {
                $result->runs[0] = (object)[
                    "text" => sprintf($template, $currentValue)
                ];
            }
            catch (\Throwable) {}
        }
        
        return $result;
    }
}