<?php
function YcRehikeAutoloader($class)
{
    if (file_exists("mod/{$class}.php")) {
        require "mod/{$class}.php";
    }
}

function RehikeRegisterSharedFunction($name, $callback)
{
    \Rehike\SharedFunctions::addFunction($name, $callback);
    \Rehike\Yt\TemplateController::queueFunction($name, $callback);
}

function RehikeBoot()
{
    // Composer autoloader
    require "vendor/autoload.php";

    require('mod/spfPhp.php');
    require('mod/cacheUtils/cacheUtils.php');
    require('mod/playerCore.php');

    // Autoload YukisCoffee modules
    // (hey, that's me!)
    // (this also does Rehike modules but I'm more awesome <3)
    spl_autoload_register('YcRehikeAutoloader');
    require "mod/YukisCoffee/GetPropertyAtPath.php"; // Can't be autoloaded

    // Include old shared functions
    // These probably should become deprecated.
    foreach (glob('mod/functions/*.php') as $file) include $file;
}