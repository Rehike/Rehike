<?php
declare(strict_types=1);
namespace RehikeTool;

function includeAllFiles(): void
{
    foreach (glob("../models/*") as $folder)
        includeAllFromTree($folder);
}

function includeAllFromTree(string $root): void
{
    if (str_ends_with($root, ".php"))
    {
        echo "Including \"$root\"..." . PHP_EOL;
        include_once $root;
    }
    else if (is_dir($root))
    {
        foreach (glob("$root/*") as $nextRoot)
            includeAllFromTree($nextRoot);
    }
}