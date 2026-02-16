<?php
namespace Rehike\Model\Channels\Channels4;

use Rehike\Util\ResourceUtils;

/**
 * Implements the (very hacky) Channels4 default banner.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */
class MDefaultBanner
{
    public $image;
    public $hdImage;
    public $isCustom = false;

    public function __construct()
    {
        $this->image = ResourceUtils::imgPath(
            "channels/c4/default_banner",
        );

        $this->hdImage = ResourceUtils::imgPath(
            "channels/c4/default_banner_hq",
        );
    }
}