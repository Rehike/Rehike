<?php
$yt->spfEnabled = true;
$yt->useModularCore = true;
$template = 'feed/history/history';
$yt->modularCoreModules = ['www/feed'];

if(!isset($yt->spf) or $yt->spf == false) {
    require "mod/getGuide.php";
}