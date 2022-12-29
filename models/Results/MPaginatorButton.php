<?php
namespace Rehike\Model\Results;

use Rehike\Model\Common\MButton;

class MPaginatorButton extends MButton {
    public function __construct($text, $selected, $url) {
        $this->setText($text);
        
        if ($selected) {
            $this->customAttributes["disabled"] = "True";
            $this->attributes["redirect-url"] = $url;
        } else {
            $this->navigationEndpoint = (object) [
                "commandMetadata" => (object) [
                    "webCommandMetadata" => (object) [
                        "url" => $url
                    ]
                ]
            ];
        }
    }
}