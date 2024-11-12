<?php

/**
 * Proxy for the messy JSON parser for reading the Rehike version.
 * 
 * This script will output valid JSON on success.
 * 
 * @author Isabella Lulamoon <kawapure@gmail.com>
 * @author The Rehike Maintainers
 */

use Rehike\Version\MessyJsonParser;

$includeStatus = @(include "modules/Rehike/Version/MessyJsonParser.php");

if (false === $includeStatus)
{
    fwrite(STDERR, "Failed to open MessyJsonParser.php");
    exit(1);
}

$version = @file_get_contents(".version");

if (false === $version)
{
    fwrite(STDERR, "Failed to open .version");
    exit(1);
}

$properJson = json_encode(MessyJsonParser::parse($version));

fwrite(STDOUT, $properJson);