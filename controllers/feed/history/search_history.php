<?php
$yt->spfEnabled = true;
$yt->useModularCore = true;
$template = 'feed/history/search_history';
$yt->modularCoreModules = ['www/feed'];

include "controllers/mixins/guideNotSpfMixin.php";
if (function_exists("legacySetEndpoint"))
{
    $yt->currentEndpoint = legacySetEndpoint("browse", "FEhistory");
}