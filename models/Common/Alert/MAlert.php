<?php
namespace Rehike\Model\Common\Alert;

use \Rehike\Model\Common\Alert\MAlertType;
use \Rehike\Model\Common\MButton;

class MAlert {
    /**
     * What type the alert should be rendered in.
     * See MAlertType above for more information.
     * 
     * @var MAlertType
     */
    public $type = MAlertType::Information;

    /**
     * Text displayed inside the alert.
     * 
     * Each text should be an object.
     * Use "text" property for text, "href" property for link
     * Use "jumpToNl" to jump to a new line
     * 
     * @var string[]
     */
    public $content = [];

    /**
     * Whether or not to render a close button
     * on the right side of the alert.
     * 
     * @var boolean
     */
    public $hasCloseButton = true;

    /**
     * Buttons to be shown on the right of the alert.
     * 
     * @var MAlertButton[]
     */
    public $buttons = [];

    public function __construct($data) {
        $this -> type = $data -> type;
        $this -> content = $data -> content ?? null;
        $this -> hasCloseButton = $data -> hasCloseButton ?? true;
        $this -> buttons = null;
        // TODO: Buttons
    }

    /**
     * Parse the alert type format returned from InnerTube
     * 
     * @param string $type Alert type returne from InnerTube.
     * 
     * @return MAlertType
     */
    public static function parseInnerTubeType($type) {
        switch ($type) {
            case "INFO":
                return MAlertType::Information;
                break;
            case "WARNING":
                return MAlertType::Warning;
                break;
            case "ERROR":
                return MAlertType::Error;
                break;
            case "SUCCESS":
                return MAlertType::Success;
                break;
        }
    }
}