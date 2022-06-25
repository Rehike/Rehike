<?php
use \Rehike\Request;

$yt -> guide = json_decode(Request::innertubeRequest("guide" , (object) []));
