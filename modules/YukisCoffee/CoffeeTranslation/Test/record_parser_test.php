<?php
require "autoloader.php";

use YukisCoffee\CoffeeTranslation\I18nRecord\Parser\I18nRecordParser;

$file = file_get_contents($argv[1]);

$result = I18nRecordParser::parse($file);

echo json_encode($result->toObject());