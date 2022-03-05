<?php
$yt->spfEnabled = true;
$yt->useModularCore = true;
$template = 'feed/history/search_history';
$yt->modularCoreModules = ['www/feed'];

if(!isset($yt->spf) or $yt->spf == false) {
    require "mod/getGuide.php";
}