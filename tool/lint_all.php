<?php
declare(strict_types=1);
namespace RehikeTool;

require "tool_base.php";
require "include_all.php";
require "lint.php";

includeAllFiles();

foreach (get_declared_classes() as $class)
{
    Linter::lintClass($class);
}