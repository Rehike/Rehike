<?php
namespace Rehike\Model\Watch\Watch8\PrimaryInfo;

use Rehike\i18n;

class MPrivacyBadge
{
    public string $privacy;
    public string $tooltip;

    public function __construct(string $privacy)
    {
        $i18n = i18n::getNamespace("watch");

        $this->privacy = strtolower(str_replace("PRIVACY_", "", $privacy));

        $this->tooltip = match ($privacy)
        {
            "PRIVACY_UNLISTED" => $i18n->privacyUnlisted,
            "PRIVACY_PRIVATE" => $i18n->privacyPrivate,
            default => ""
        };
    }
}