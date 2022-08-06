<?php
namespace Rehike\Model\Picker;
use \Rehike\Model\Picker\MPickerOption;

class PickerModel {
    public function bake($dataHost, $action) {
        $response = (object) [];
        $response -> type = $action;
        switch ($action) {
            case "language":
                $response -> title = "Choose your language";
                $response -> notes = "This changes the language of the site. It won't change any text entered by users.";
                $iconSearch = "TRANSLATE";
                break;
            case "country":
                $response -> title = "Choose your location";
                $response -> notes = "This changes the videos and channels shown to you. It won't change the language of the site.";
                $iconSearch = "LANGUAGE";
                break;
            case "safetymode":
                $response -> title = "Restricted Mode";
                $response -> notes = [
                    "Restricted Mode hides videos that may contain inappropriate content flagged by users and other signals. No filter is 100% accurate, but it should help you avoid most inappropriate content.",
                    "Your Restricted Mode setting will apply to this browser only."
                ];
                $iconSearch = "ADMIN_PANEL_SETTINGS";
        }
    }
}