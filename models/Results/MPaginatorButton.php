<?php
namespace Rehike\Model\Results;

use Rehike\Model\Common\MButton;

/**
 * A button in the pagination view.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */
class MPaginatorButton extends MButton
{
    public function __construct(string $text, bool $selected, string $url)
    {
        $this->setText($text);
        
        if ($selected)
        {
            $this->customAttributes["disabled"] = "True";
            $this->attributes["redirect-url"] = $url;
        }
        else
        {
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