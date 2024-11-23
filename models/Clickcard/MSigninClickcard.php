<?php
namespace Rehike\Model\Clickcard;

use Rehike\Model\Common\MAbstractClickcard;
use Rehike\Util\ParsingUtils;
use Rehike\Model\Common\MButton;

/**
 * Implements a common model for the signin clickcard.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */
class MSigninClickcard extends MAbstractClickcard
{
    public $template = "signin_clickcard";
    public $class = "signin-clickcard";

    public function __construct($heading, $message, $button)
    {
        $this->content = (object)[
            "heading" => $heading,
            "message" => $message,
            "button" => new MButton([
                "style" => "STYLE_PRIMARY",
                "class" => ["signin-button"],
                "navigationEndpoint" => (object) [
                    "commandMetadata" => (object) [
                        "webCommandMetadata" => (object) [
                            "url" => $button["href"] ?? ""
                        ]
                    ]
                ],
                "text" => (object) [
                    "simpleText" => $button["text"] ?? ""
                ]
            ])
        ];
    }

    public static function fromData($data)
    {
        $heading = $data->title ?? null;
        $message = $data->content ?? null;
        $button = $data->button->buttonRenderer ?? null;
        
        return new self(
            ParsingUtils::getText($heading) ?? null,
            ParsingUtils::getText($message) ?? null,
            [
                "text" => ParsingUtils::getText($button->text) ?? null,
                "href" => ParsingUtils::getUrl($button) ?? null
            ]
        );
    }
}