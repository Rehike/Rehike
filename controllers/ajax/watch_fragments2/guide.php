<?php
$template = "pageframe/guide";

require "controllers/mixins/guideMixin.php";

$yt->spfIdListeners = [
    '@masthead_search<data-is-crosswalk>',
    'guide'
];