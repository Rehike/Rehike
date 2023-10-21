<?php
require "autoloader.php";

use YukisCoffee\CoffeeTranslation\Lang\Parser\RecordFileParser;
use YukisCoffee\CoffeeTranslation\Lang\SourceInfo;


$files = glob("C:\\xampp\\Rehike07Dev\\i18n_new\\en-US\\**.i18n");
$files = array_merge($files, glob("C:\\xampp\\Rehike07Dev\\i18n_new\\en-US\\rehike\\**.i18n"));

foreach ($files as $file)
{
    $expectedCorrespondingFile = str_replace(".i18n", "\\en.json", str_replace("i18n_new\\en-US\\", "i18n\\", $file));

    $data = @file_get_contents($file);
    if ($data)
    {
        echo "Successfully loaded new i18n file \"$file\"\n";
    }
    else
    {
        echo "Failed to load new i18n file \"$file\"\n";
    }

    $oldi18n = @file_get_contents($expectedCorrespondingFile);
    if ($oldi18n)
    {
        echo "Successfully loaded old counterpart file \"$expectedCorrespondingFile\"\n";
    }
    else
    {
        echo "Failed to load old counterpart file \"$expectedCorrespondingFile\"\n";
    }

    $fileInfo = new SourceInfo($file, "utf-8", $data);
    try
    {
        $info = RecordFileParser::parse($fileInfo);
        $result = true;

        echo "Successfully parsed new i18n file.\n";
    }
    catch (Throwable $e)
    {
        echo "Failed to parse new file \"$file\".\n\n";
        $result = false;
        $failureReason = $e;
    }

    if ($oldi18n)
    {
        $json = json_decode($oldi18n);

        if ($json)
        {
            echo "Successfully parsed old JSON file.\n";
            deepCompare($info->toObject(), $json);
        }
        else
        {
            echo "Failed to parse old JSON file \"$expectedCorrespondingFile\"???\n";
        }
    }
}

function deepCompare(object $new, object $orig, string $root = ""): void
{
    $refA = new ReflectionObject($new);
    $refB = new ReflectionObject($orig);

    $combinedPropNames = array_unique(array_merge(
        array_map(fn($fuck) => $fuck->getName(), $refA->getProperties()),
        array_map(fn($fuck) => $fuck->getName(), $refB->getProperties())
    ));

    foreach ($combinedPropNames as $propName)
    {
        if (!$refA->hasProperty($propName))
        {
            echo sprintf("New doesn't have property %s of old.\n", prefixName($propName, $root));
            continue;
        }

        if (!$refB->hasProperty($propName))
        {
            // doesn't matter if other doesn't have it
            continue;
        }

        $propA = $refA->getProperty($propName)->getValue($new);
        $propB = $refB->getProperty($propName)->getValue($orig);

        if (!is_object($propA) && !is_object($propB) && $propA != $propB)
        {
            echo "\n-------------------------------------------------\n";
            echo sprintf(
                "Value of new property %s is different from old one.\n",
                prefixName($propName, $root)
            );
            echo "\nNew value:\n\n";
            echo (string)$propA;
            echo "\n\n";
            echo "\nOld value:\n\n";
            echo (string)$propB;
            echo "\n\n";
            echo "-------------------------------------------------\n";
        }
        else if (is_object($propA) && is_object($propB))
        {
            deepCompare($propA, $propB, prefixName($propName, $root));
        }
    }
}

function prefixName(string $name, string $prefix = ""): string
{
    if (!empty($prefix))
    {
        return "$prefix.$name";
    }

    return $name;
}