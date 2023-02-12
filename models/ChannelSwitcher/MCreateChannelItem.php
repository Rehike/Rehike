<?php
namespace Rehike\Model\ChannelSwitcher;

use Rehike\TemplateFunctions;

class MCreateChannelItem
{
    public string $text;

    public function __construct(object $data)
    {
        $this->text = TemplateFunctions::getText($data->text);
    }
}