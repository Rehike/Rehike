<?php
namespace Rehike\Model\Channels\Channels4;

use Rehike\ResourceConstants\ResourceContentsStore as Resources;
use Rehike\TemplateFunctions as TF;

/**
 * Implements the (very hacky) Channels4 default banner.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */
class MDefaultBanner
{
    public $thumbnails;

    public function __construct()
    {
        $this->thumbnails[] = (object)[
            "url" => TF::resourcePath(
                Resources::$resourceConstants,
                "img", "channels/c4/default_banner"
            )
        ];

        $this->thumbnails[] = (object)[
            "url" => TF::resourcePath(
                Resources::$resourceConstants,
                "img", "channels/c4/default_banner_hq"
            )
        ];
    }
}