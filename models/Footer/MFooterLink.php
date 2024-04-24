<?php
namespace Rehike\Model\Footer;

class MFooterLink
{
    /** @var string */
    public $simpleText;

    /** @var object */
    public $navigationEndpoint;

    public function __construct($text, $url)
    {
        $this->simpleText = $text;
        $this->navigationEndpoint = (object) [
            "commandMetadata" => (object) [
                "webCommandMetadata" => (object) [
                    "url" => $url
                ]
            ]
        ];
    }
}