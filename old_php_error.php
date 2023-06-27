<?php
require_once("min_php_version.php");
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Outdated PHP!</title>
        <meta charset="utf-8">
    </head>
    <body>
        <h1>Outdated PHP!</h1>
        <p>Rehike requires at least PHP <b><?= implode(".", MIN_PHP_VERSION) ?></b>. You are running PHP <b><?= phpversion() ?></b>.</p>
    </body>
</html>