<?php
/**
 * A stub for loading the Rehike application.
 * 
 * Rehike comes pre-setup for the Apache web server,
 * however it may work with another one so long as all requests
 * direct to this file.
 * 
 * The Rehike application handles the parsing and handling of requests and such.
 */

// Require the main Rehike autoloader.
require 'autoloader.php';

// Start the Rehike application.
Rehike\Application::main();