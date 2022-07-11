<?php
use \Rehike\Request;
use \Rehike\i18n;
use Rehike\Model\Guide\MGuide;

$response = Request::innertubeRequest("guide", (object)[]);

$guide = json_decode($response);

// Guide update moves guide into appbar
if (!isset($yt->appbar))
{
    $yt->appbar = (object)[];
}
$yt->appbar->guide = MGuide::fromData($guide);
$yt->appbar->guideNotificationStrings =
    i18n::getNamespace("main/guide")->notifications
;

/**
 * Copy of the HitchhikerController endpoint set
 * function for legacy Controller V1 controllers.
 */
function legacySetEndpoint($type, $a)
{
    $type = strtolower($type);

    // Will be casted to an object
    $data = [];

    switch ($type)
    {
        case "browse":
            $data["browseEndpoint"] = (object)[
                "browseId" => $a
            ];
            break;
        case "url":
            $data["urlEndpoint"] = (object)[
                "url" => $a
            ];
            break;
    }

    $data = (object)$data;

    return $data;
}