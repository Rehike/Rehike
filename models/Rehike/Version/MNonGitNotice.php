<?php
namespace Rehike\Model\Rehike\Version;
use Rehike\i18n;

class MNonGitNotice extends MNotice
{
    public function __construct()
    {
        $strings = i18n::getNamespace('rehike/version');

        $this -> text = $strings->nonGitNotice;
        $this -> description = $strings->nonGitExtended;
    }
}