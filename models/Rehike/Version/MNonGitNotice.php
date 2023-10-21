<?php
namespace Rehike\Model\Rehike\Version;
use Rehike\i18n\i18n;

class MNonGitNotice extends MNotice
{
    public function __construct()
    {
        $strings = i18n::getNamespace('rehike/version');

        $this->text = $strings->get("nonGitNotice");
        $this->description = $strings->get("nonGitExtended");
    }
}