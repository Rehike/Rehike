<?php
$yt->experiment = (object) [
    'ringo' => true, // true: new logo; false: old logo before 2017
    'creatorMenu' => true, // true: creation menu with upload and go live; false: upload icon
    'oldRoboto' => false, // true: older version of roboto from 2016; false: normal roboto from 2017+
    'timeOnRecoms' => false, // true: show relative upload date on recommendations; false: don't show, like regular hitchhiker
    'oldUploadBtn' => false, // true: old upload button with "Upload" text; false: upload material icon; NOTE: 'creatorMenu' must be set to false for this to apply
];

$yt->debugger = false; // does nothing at the moment, in the future this should provide tools for debugging such as viewing raw innertube responses