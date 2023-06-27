<?php
namespace Rehike\Model\Channels\Channels4\SecondaryHeader;

class MSecondaryHeaderLink
{
    public function __construct(
        public string $text,
        public string $url,
        public ?string $icon = null
    ) {}
}