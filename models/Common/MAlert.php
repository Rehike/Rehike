<?php
namespace Rehike\Model\Common;

use Rehike\Model\Common\MButton;
use Rehike\TemplateFunctions;

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
     * @var string
     */
    public $content = "";

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
        $this -> type = $data["type"];
        $this -> text = $data["text"] ?? null;
        $this -> hasCloseButton = $data["hasCloseButton"] ?? true;
        $this -> buttons = null;
        // TODO: Buttons
    }

    /**
     * Build an alert from InnerTube data.
     * 
     * @param object $data  Data.
     * @return MAlert
     */
    public static function fromData($data) {
        return new self([
            "type" => MAlert::parseInnerTubeType($data -> type),
            "hasCloseButton" => (isset($data -> dismissButton)),
            "text" => TemplateFunctions::getText($data -> text)
        ]);
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
                return self::TypeInformation;
                break;
            case "WARNING":
                return self::TypeWarning;
                break;
            case "ERROR":
                return self::TypeError;
                break;
            case "SUCCESS":
                return self::TypeSuccess;
                break;
        }
    }
}