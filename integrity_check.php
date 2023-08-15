<?php
// Performs some quick integrity checks to verify that Rehike is even able to
// run in the first place. This helps users catch early mistakes more easily.

// Minimum PHP version:
if (PHP_VERSION_ID < 80000)
{
    require "includes/fatal_templates/version_too_old_page.html.php";
    exit();
}