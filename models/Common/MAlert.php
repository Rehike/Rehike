<?php
namespace Rehike\Model\Common;

use \Rehike\Model\Common\MButton;

class MAlert {
    const TypeInformation = "info";
    const TypeWarning = "warn";
    const TypeError = "error";
    const TypeSuccess = "success";

    /**
     * What type the alert should be rendered in.
     * 
     * @var string
     */
    public $type = self::TypeInformation;

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
     * @return string
     */
    public static function parseInnerTubeType($type) {
        switch ($type) {
            case "INFO":
                return MAlert::TypeInformation;
                break;
            case "WARNING":
                return MAlert::TypeWarning;
                break;
            case "ERROR":
                return MAlert::TypeError;
                break;
            case "SUCCESS":
                return MAlert::TypeSuccess;
                break;
        }
    }
}