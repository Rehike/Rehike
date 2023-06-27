<?php
namespace Rehike\Model\Channels\Channels4;

use Rehike\i18n;
use Rehike\Model\Channels\Channels4\SecondaryHeader\MSecondaryHeaderLink;
use Rehike\Signin\API as SignIn;

class MSecondaryHeader
{
    /** @var MSecondaryHeaderLink[] */
    public array $links = [];

    public function __construct(object $data)
    {
        $i18n = i18n::getNamespace("channels");

        $ucid = SignIn::getInfo()["ucid"];

        if (isset($data->subscribers))
        {
            $this->links[] = new MSecondaryHeaderLink(
                $i18n->secondaryHeaderSubs(number_format($data->subscribers)),
                "//studio.youtube.com/channel/$ucid"
            );
        }

        if (isset($data->views))
        {
            $this->links[] = new MSecondaryHeaderLink(
                $i18n->secondaryHeaderViews(number_format($data->views)),
                "//studio.youtube.com/channel/$ucid/analytics",
                "analytics"
            );
        }

        $this->links[] = new MSecondaryHeaderLink(
            $i18n->secondaryHeaderManager,
            "//studio.youtube.com/channel/$ucid/videos",
            "vm"
        );
    }
}