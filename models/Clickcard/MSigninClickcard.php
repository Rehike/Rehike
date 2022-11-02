<?php
namespace Rehike\Model\Clickcard;

use Rehike\Model\Common\MAbstractClickcard;
use Rehike\TemplateFunctions;
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
                "anchor" => true,
                "href" => $button["href"] ?? null,
                "content" => (object) [
                    "runs" => [
                        (object) [
                            "text" => @$button["text"] ?? "Sign in"
                        ]
                    ]
                ]
            ])
        ];
    }

    public static function fromData($data) {
        $heading = $data -> title ?? null;
        $message = $data -> content ?? null;
        $button = $data -> button -> buttonRenderer ?? null;
        
        return new self(
            TemplateFunctions::getText($heading) ?? null,
            TemplateFunctions::getText($message) ?? null,
            [
                "text" => TemplateFunctions::getText($button -> text) ?? null,
                "href" => TemplateFunctions::getUrl($button) ?? null
            ]
        );
    }
}