<?php

/**
 * Script for getting the Rehike build number.
 * 
 * @author Isabella <kawapure@gmail.com>
 * @author The Rehike Maintainers
 */

use Rehike\Version\BuildNumber;

$_SERVER["DOCUMENT_ROOT"] = getcwd(); // Required for autoloader to work.
$includeStatus = @(include "includes/rehike_autoloader.php");

if (false === $includeStatus)
{
    fwrite(STDERR, "Failed to open autoloader");
    exit(1);
}

$buildNumber = BuildNumber::getBuildNumber();

fwrite(STDOUT, $buildNumber);