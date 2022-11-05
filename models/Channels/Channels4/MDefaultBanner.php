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

    public function __construct()
    {
        $resources = json_decode(file_get_contents("resource_constants.json"));

        $this -> image = TF::imgPath(
            "channels/c4/default_banner",
            $resources
        );

        $this -> hdImage = TF::imgPath(
            "channels/c4/default_banner_hq",
            $resources
        );
    }
}