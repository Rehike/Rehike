<?php
namespace Rehike\Model\Channels\Channels4;

use Rehike\TemplateFunctions as TF;

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
        $resources = include "resource_constants.php";

        $this->image = TF::imgPath(
            "channels/c4/default_banner",
            $resources
        );

        $this->hdImage = TF::imgPath(
            "channels/c4/default_banner_hq",
            $resources
        );
    }
}