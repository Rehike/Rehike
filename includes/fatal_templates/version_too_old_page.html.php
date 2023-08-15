<?php
    namespace Rehike\ErrorHandler\FatalErrorTemplate;

    $minimumPhpVersion = "8.0";
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Rehike startup error</title>
        <?php include "fatal.css.php" ?>
    </head>
    <body>
        <div id="rehike-fatal-error">
            <div class="header">
                <svg xmlns="http://www.w3.org/2000/svg" height="48px" viewBox="0 0 24 24" width="48px" fill="#cc181e">
                    <path d="M0 0h24v24H0z" fill="none" />
                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z" />
                </svg>
                <h1>Your version of PHP is too old!</h1>
            </div>
            <p>
                Rehike requires at least <b>PHP <?php echo $minimumPhpVersion ?></b>. You are using
                <b>PHP <?php echo PHP_VERSION ?></b>.
            </p>
            <p>
                Please update PHP and try again.
            </p>
        </div>
    </body>
</html>