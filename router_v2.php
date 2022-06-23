<?php
use Rehike\ControllerV2\Router;

Router::get([
    "/watch" => "watch",
    "/live_chat" => "special/get_live_chat"
]);