<?php
$yt->spfEnabled = true;
$yt->useModularCore = true;
$template = 'feed/history/history';
$yt->modularCoreModules = ['www/feed'];

include "controllers/mixins/guideNotSpfMixin.php";