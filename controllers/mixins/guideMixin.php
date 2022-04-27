<?php
use \Rehike\Request;

Request::innertubeRequest("guide", "guide" , (object) []);
$yt -> guide = json_decode(Request::getInnertubeResponses()["guide"]);