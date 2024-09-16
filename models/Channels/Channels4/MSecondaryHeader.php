<?php
namespace Rehike\Model\Channels\Channels4;

use Rehike\i18n\i18n;
use Rehike\Model\Channels\Channels4\SecondaryHeader\MSecondaryHeaderLink;
use Rehike\SignInV2\SignIn;

class MSecondaryHeader
{
    /** @var MSecondaryHeaderLink[] */
    public array $links = [];

    public function __construct(object $data)
    {
        $i18n = i18n::getNamespace("channels");

        $ucid = SignIn::getSessionInfo()->getUcid();

        if (isset($data->subscribers))
        {
            $count = $data->subscribers;
            
            if ($count == 1)
            {
                $text = i18n::getFormattedString(
                    "misc", 
                    "subscriberTextSingular", 
                    $i18n->formatNumber($data->subscribers)
                );
            }
            else
            {
                $text = i18n::getFormattedString(
                    "misc", 
                    "subscriberTextPlural", 
                    $i18n->formatNumber($data->subscribers)
                );
            }

            $this->links[] = new MSecondaryHeaderLink(
                $text,
                "//studio.youtube.com/channel/$ucid"
            );
        }

        if (isset($data->views))
        {
            $this->links[] = new MSecondaryHeaderLink(
                $i18n->format("secondaryHeaderViews", $i18n->formatNumber($data->views)),
                "//studio.youtube.com/channel/$ucid/analytics",
                "analytics"
            );
        }

        $this->links[] = new MSecondaryHeaderLink(
            $i18n->get("secondaryHeaderManager"),
            "//studio.youtube.com/channel/$ucid/videos",
            "vm"
        );
    }
}