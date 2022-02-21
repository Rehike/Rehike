<?php
function main()
{
    ob_start();
    set_include_path($_SERVER['DOCUMENT_ROOT']);

    require "boot.php";
    RehikeBoot();

    // Move to Rehike main controller
    Rehike\Main::main();
}

main();