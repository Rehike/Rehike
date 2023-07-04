<?php
namespace Rehike\Model\Watch\Watch8\PrimaryInfo;

use Rehike\TemplateFunctions;

/**
 * Define the super title (links that appear above the title, such as hashtags).
 */
class MSuperTitle
{
    public $items = [];

    public function __construct($superTitleLink)
    {
        if (isset($superTitleLink->runs))
        foreach ($superTitleLink->runs as $run)
        if (" " != $run->text)
        {
            $this->items[] = (object)[
                "text" => $run->text,
                "url" => TemplateFunctions::getUrl($run)
            ];
        }
    }
}