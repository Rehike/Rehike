<?php
namespace Rehike\Model\AddTo;

use Rehike\i18n;

class MCreatePlaylist
{
    public $label;
    public $publicText;
    public $unlistedText;
    public $privateText;
    public $playlistPrivacyText;
    public $isCompact = false;

    public function __construct($compact = false)
    {
        $strs = i18n::getNamespace("addto");

        $this->label = $strs->playlistTitle;
        $this->publicText = $strs->privacyPublic;
        $this->unlistedText = $strs->privacyUnlisted;
        $this->privateText = $strs->privacyPrivate;
        $this->playlistPrivacyText = $strs->playlistPrivacy;

        $this->isCompact = $compact;
    }
}