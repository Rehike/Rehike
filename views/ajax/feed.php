<?php
header("Content-Type: application/json");

if (isset($_GET["action_get_unseen_notification_count"])) {
    include "views/ajax/feed/get_unseen_notification_count.php";
} else {
    die("{\"errors\":[]}");
}