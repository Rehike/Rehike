<?php
namespace Rehike\Model\Comments;

use Rehike\i18n\i18n;

/**
 * As of February 2024, we make all comments API requests with the German
 * language for some small benefits.
 * 
 * Here is a translation manager to make the German -> user language translation
 * process simple.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */
class StringTranslationManager
{
    /**
     * Get a static translation.
     */
    public static function get(?string $in): ?string
    {
        $i18n = i18n::getNamespace("comments");

        $yt = \Rehike\YtApp::getInstance();
        if (!isset($yt->testAbc))
        {
            $yt->testAbc = [];
        }
        $yt->testAbc[] = trim($in);

        return match (trim($in))
        {
            "Kommentare" => $i18n->get("commentsHeader"),
            "Top-Kommentare" => $i18n->get("topComments"),
            "Neueste zuerst" => $i18n->get("newestFirst"),
            "Kommentar hinzufügen…" => $i18n->get("addAComment"),
            "Antwort hinzufügen…" => $i18n->get("addAReply"),
            "Mehr Antworten ansehen" => $i18n->get("loadMoreReplies"),
            "Antworten" => $i18n->get("replyButtonText"),
            "Mehr anzeigen" => $i18n->get("expandButtonText"),
            "Weniger anzeigen" => $i18n->get("collapseButtonText"),
            "Kommentieren" => $i18n->get("commentText"),

            default => $in
        };
    }

    public static function reformatNumber(?string $num): ?string
    {
        $i18n = i18n::getNamespace("comments");

        // German numbers are separated using reverse symbols from English,
        // which is the basis of PHP's number parser. So we have to do something
        // like this to parse it.
        $newNum = floatval(str_replace(",", ".", str_replace(".", "", $num)));

        return $i18n->formatNumber($newNum);
    }

    public static function convertDate(?string $date): ?string
    {
        $isEdited = str_contains($date, "(bearbeitet)");
        $number = explode(" ", $date)[1];
        $i18n = i18n::getNamespace("comments");

        // The format is consistent:
        // vor NUMBER UNIT
        $unit = match(explode(" ", $date)[2])
        {
            "Sekunde" => "secondsAgoSingular",
            "Sekunden" => "secondsAgoPlural",
            "Minute" => "minutesAgoSingular",
            "Minuten" => "minutesAgoPlural",
            "Stunde" => "hoursAgoSingular",
            "Stunden" => "hoursAgoPlural",
            "Tag" => "daysAgoSingular",
            "Tagen" => "daysAgoPlural",
            "Woche" => "weeksAgoSingular",
            "Wochen" => "weeksAgoPlural",
            "Monat" => "monthsAgoSingular",
            "Monaten" => "monthsAgoPlural",
            "Jahr" => "yearsAgoSingular",
            "Jahren" => "yearsAgoPlural"
        };

        if ($unit)
        {
            $result = $i18n->format($unit, $number);

            if ($isEdited)
            {
                $result = $i18n->format("timestampEditedText", $result);
            }

            return $result;
        }

        return $date;
    }

    public static function convertLikeCount(?string $text): ?string
    {
        // Parsing the like count is easy, but there are some cases where it
        // returns partial numbers even in German. These can be calculated
        // regardless.

        if (str_contains($text, "Mio") || str_contains($text, "Brd"))
        {
            $regexResult = preg_match("/([0-9,]*)/", $text, $matches);
            $num = str_replace(",", ".", $matches[1]);
            
            // PHP is insane for this being the way that you insert into the
            // middle of a string:
            $parsedNum = floatval($num);

            $figures = match(true)
            {
                // Millions
                str_contains($text, "Mio") => 6,
                // Billions must die
                str_contains($text, "Brd") => 9
            };

            $randomizedFigures = $figures - strlen(explode(".", $num)[0]);
            
            $base = $parsedNum * 10 ** $figures;
            if (false && $randomizedFigures > 0)
            {
                $rng = rand(0, 10 ** $randomizedFigures);
            }
            else
            {
                $rng = 0;
            }

            $finalNum = $base + $rng;

            return substr_replace($rng, (string)$finalNum, 0, strlen((string)$finalNum));
        }

        return preg_replace("/[^0-9]/", "", $text);
    }

    public static function convertHeart(?string $in): ?string
    {
        // "<3 von USERNAME"
        $i18n = i18n::getNamespace("comments");
        return $i18n->format(
            "heartTooltipText", 
            implode(" ", array_slice(explode(" ", $in), 2))
        );
    }

    public static function setText(mixed &$innertubeObject, string $text): void
    {
        if (isset($innertubeObject->runs))
        {
            $innertubeObject->runs[0]->text = $text;
        }
        else if (isset($innertubeObject->simpleText))
        {
            $innertubeObject->simpleText = $text;
        }
        else
        {
            $innertubeObject = $text;
        }
    }
}