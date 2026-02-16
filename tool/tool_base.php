<?php
declare(strict_types=1);
namespace RehikeTool;

$g_toolFilePath = __FILE__;
$g_rehikeBaseFolder = dirname(dirname($g_toolFilePath));

set_include_path($g_rehikeBaseFolder);
require_once "includes/rehike_autoloader.php";