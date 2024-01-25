<?php
namespace Rehike\ErrorHandler\FatalErrorTemplate;

use Rehike\Logging\Common\FormattedString;

function simpleFormattedStringToHtml(FormattedString $fs): string
{
    $out = "";

    foreach ($fs->getRuns() as $run)
    {
        if (isset($run["tag"]))
        {
            $out .= 
                "<span class=\"" . htmlspecialchars($run["tag"]) . "\">" .
                htmlspecialchars($run["text"], ENT_QUOTES | ENT_SUBSTITUTE, "utf-8") .
                "</span>";
        }
        else
        {
            $out .= htmlspecialchars($run["text"]);
        }
    }

    return $out;
}