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
//    - Taniko Yamamoto (@kirasicecreamm or @YukisCoffee on everything)
//    - Aubrey Pankow (@aubymori on Twitter and YouTube)
//    - Daylin Cooper (@Nightlinbit on GitHub)
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

// PHP < 8.2 IDE fix
include "includes/polyfill/AllowDynamicProperties.php";

require "includes/constants.php";

// Include the Composer and Rehike autoloaders, respectively.
require "vendor/autoload.php";
require "includes/rehike_autoloader.php";
foreach (glob('includes/template_functions/*.php') as $file) include $file;

\Rehike\Boot\Bootloader::startSession();