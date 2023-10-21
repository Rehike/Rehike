<?php
require "autoloader.php";

use YukisCoffee\CoffeeTranslation\Lang\Parser\RecordFileParser;
use YukisCoffee\CoffeeTranslation\Lang\SourceInfo;


$files = glob("C:\\xampp\\Rehike07Dev\\i18n_new\\en-US\\rehike\**.i18n");

echo "---- BEGINNING OF STREAM -----\n\n";
foreach ($files as $file)
{
    $data = file_get_contents($file);
    $fileInfo = new SourceInfo($file, "utf-8", $data);
    try
    {
        $info = RecordFileParser::parse($fileInfo);
        $result = true;
    }
    catch (Throwable $e)
    {
        $result = false;
        $failureReason = $e;
    }

    echo "=============================================================================\n";
    echo "  FILENAME: $file\n";
    echo "  SUCCESS: " . ($result ? "Success!\n" : "Failure... :(\n");
    
    if ($result == true)
    {
        echo "\n";
        echo "  PAYLOAD:\n";
        echo preg_replace("/^/m", "    ", json_encode($info->toObject(), JSON_PRETTY_PRINT));
        echo "\n";
    }
    else
    {
        echo " FAILURE REASON:\n";
        echo preg_replace("/^/m", "    ", $failureReason);
    }
    echo "=============================================================================\n";
    echo "\n";
    echo "\n";
}
echo "\n\n---- END OF STREAM -----";