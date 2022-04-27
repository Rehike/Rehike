<?php
$template = "pageframe/guide";

require "mod/getGuide.php";

$yt->spfIdListeners = [
    '@masthead_search<data-is-crosswalk>',
    'guide'
];