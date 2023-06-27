<?php
namespace Rehike\Model\ChannelSwitcher;

use Rehike\Util\ParsingUtils;

class MCreateChannelItem
{
    public string $text;

    public function __construct(object $data)
    {
        $this->text = ParsingUtils::getText($data->text);
    }
}