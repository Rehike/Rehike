<?php
namespace Rehike\Model\Rehike\Version;

use Rehike\i18n;

class MFailedNotice extends MNotice
{
    public function __construct()
    {
        $strings = i18n::getNamespace('rehike/version');

        $this -> text = $strings->failedNotice;
        $this -> description = $strings->noDotVersionNotice;
    }
}