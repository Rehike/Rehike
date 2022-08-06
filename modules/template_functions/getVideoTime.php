<?php

\Rehike\TemplateFunctions::register('getVideoTime', function($obj) {
    if (isset($obj->lengthText)) {
        return $obj->lengthText->simpleText;
    } else if (isset($obj->thumbnailOverlays)) {
        for ($i = 0; $i < count($obj->thumbnailOverlays); $i++) {
            if (isset($obj->thumbnailOverlays[$i]->thumbnailOverlayTimeStatusRenderer)) {
                $lengthText = $obj->thumbnailOverlays[$i]->thumbnailOverlayTimeStatusRenderer->text->simpleText;
            }
        }

        if (!isset($lengthText)) {
            return;
        } else {
            if ($lengthText == "SHORTS") {
                // only match seconds, if the video has the shorts timestamp we can assume two things
                // - the video is at MOST 1 minute
                // - if it has no seconds in the accessibility label it is 100% exactly 1 minute long
                preg_match("/([0-9]?[0-9])( seconds)|(1 second)/", $obj->title->accessibility->accessibilityData->label, $matches);
                if (!isset($matches[0])) {
                    return "1:00";
                } else {
                    $lengthText = (int) preg_replace("/( seconds)|( second)/", "", $matches[0]);
                    if ($lengthText < 10) {
                        return "0:0" . $lengthText;
                    } else {
                        return "0:" . $lengthText;
                    }
                }
            } else if ($lengthText == "LIVE") { // some endpoints have LIVE timestamp instead of badge
                return null;
            } else {
                return $lengthText;
            }
        }
    }

    return null;
}); 