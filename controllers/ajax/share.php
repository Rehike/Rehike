<?php
foreach ($_GET as $key => $val) {
    if (str_starts_with($_GET[$key], "action_") and $val = "1") {
        $action = $key;
        break;
    }
}