<?php
namespace Rehike\Model\Rehike\Version;

class MNonGitNotice extends MNotice
{
    public function __construct()
    {
        $strings = i18n::getNamespace('rehike/version');

        $this -> text = $strings->nonGitNotice;
        $this -> description = $strings->nonGitExtended;
    }
}