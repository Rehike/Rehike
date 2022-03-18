<?php
header("Content-Type: application/json");

if (!isset($_GET["continuation"])) {
    http_response_code(400);
    die("{\"errors\":[\"Invalid Request\"]}");
}

$template = "ajax/browse/main";
$yt->continuation = $_GET["continuation"];
$yt->target = $_GET["target_id"];