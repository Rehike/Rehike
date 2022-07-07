<?php
namespace Rehike\Model\Channels\Channels4\BrandedPageV2;

class MSubnavBackButton
{
    public $accessibilityLabel;
    public $href;

    public function __construct($href)
    {
        $this->accessibilityLabel = "Back";
        
        $this->href = $href;
    }
}
