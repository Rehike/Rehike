<?php
// ,                                                 
// ,,,,,                                             
// ,,,,,,,,,.                                        
// ,,,,,,,,,,,,,,                                    
// ,,,,,,,,,,,,,,,,,,                                
// ,,,,,,,,,,,,,,,,,,,,,,,                           
// ,,,,,,,,,,,##,,,,,,,,,,,,,,                       
// ,,,,,,,,,@@@@@%,,,,,,,,,,,,,,,,.                  
// ,,,,,,,,,,*&&*,,,,,,,,,,,,,,,,,,,,,,              
// ,,,,,,,,,,,@@@@@%,,,,@@*,,,,,,,,,,,,,,,,          
// ,,,,,,@@@,,@@@@@@@@@@@@@@,,,,,,,,,,,,,,,,,,,.     
// ,,,,,&@@@@,,@@@@@*,,,,,,@@,,,,,,,,,,,,,,,,,,,,,,. 
// ,,,,,,@@@@&,*@@@@@@@#*,,,%@@,,,,,,,,,,,,,,,,,,,,  
// ,,,,,,,@@@@(,(@@@&@@@@@,,,,@@,,,,,,,,,,,,,,,      
// ,,,,,,,,,,,,,,%@@%,,,#@@#,,,%@&,,,,,,,,.          
// ,,,,,,,,,,,,,,,&@@*,,,,@@@,,,,,,,,,               
// ,,,,,,,,,,,,,,,,@@@,,,,,#,,,,,,                   
// ,,,,,,,,,,,,,,,,,@@@,,,,,,.                       
// ,,,,,,,,,,,,,,,,,,,,,,                            
// ,,,,,,,,,,,,,,,,,,                                
// ,,,,,,,,,,,,,.                                    
// ,,,,,,,,,                                         
// ,,,,, 
//
//
// Rehike:
//
// A classic YouTube server emulator written in PHP.
// See the README.md file for installation instructions and more.
//
// If you wish to contribute, please see CONTRIBUTING.md.
//
// And check out some of the cool people that brought it to you!
//    - Aubrey Pankow (@aubymori on Twitter and YouTube)
//    - Isabella Lulamoon (@kawapure on everything)
//
//-----------------------------------------------------------------------------
//
// This file is the insertion point for the Rehike server. Every request
// to YouTube should go through this file.
//
// This is responsible for early imports and starting the Rehike session.
//
//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

ob_start();
set_include_path($_SERVER['DOCUMENT_ROOT']);

(include "integrity_check.php") or die(
    "<h1>Rehike fatal error</h1> " .
    "Failed to run integrity check. Something is desperately wrong. " .
    "Verify your server configuration and try again. Try chmod on Unix (Linux, macOS, etc.) " .
    "as file system permissions can be hard to debug. " .
    "<a href=\"//github.com/Rehike/Rehike/wiki/Common_errors#Failed_to_run_integrity_check\">" .
        "For more information, please see our wiki page on this error message." .
    "</a>"
);

require "includes/startup_config.php";
require "includes/fatal_handler.php";

// PHP < 8.2 IDE fix
include "includes/polyfill/AllowDynamicProperties.php";

require "includes/constants.php";

// Include the Composer and Rehike autoloaders, respectively.
require "vendor/autoload.php";
require "includes/rehike_autoloader.php";

\Rehike\Boot\Bootloader::startSession();