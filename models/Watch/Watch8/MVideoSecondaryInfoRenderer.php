<?php
namespace Rehike\Model\Watch\Watch8;

use Rehike\Util\ExtractUtils;
use Rehike\i18n;

use Rehike\Model\Watch\Watch8\{
    SecondaryInfo\MMetadataRowContainer
};

/**
 * Implement the model used by the video's secondary info renderer.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */
class MVideoSecondaryInfoRenderer
{
    public $description;
    public $defaultExpanded;
    public $dateText;
    public $metadataRowContainer;
    public $showMoreText;
    public $showLessText;

    public function __construct($dataHost)
    {
        if (!is_null($dataHost::$secondaryInfo))
        {
            $info = &$dataHost::$secondaryInfo;
            $primaryInfo = &$dataHost::$primaryInfo;

            // Legacy (COMPETENT) description
            if (isset($info->description))
            {
                $this->description = $info->description;
            }
            // "Modern" (RETARDED) description
            elseif (isset($info->attributedDescription))
            {
                $this->description = self::convertDescription($info->attributedDescription);
            }
            
            $this->defaultExpanded = $info->defaultExpanded ?? false;
            $this->dateText = isset($primaryInfo->dateText)
                ? ExtractUtils::resolveDate($primaryInfo->dateText)
                : null;
            $this->metadataRowContainer = new MMetadataRowContainer(
                $info->metadataRowContainer->metadataRowContainerRenderer->rows,
                $dataHost
            );
            $this->showMoreText = $info->showMoreText ?? null;
            $this->showLessText = $info->showLessText ?? null;
        }
    }

    
    /**
     * Welp, the fucktards have done it again. The shitty description experiment is back,
     * and it's just as painful as it was before. Do they even have any fucking idea what
     * they're doing? The link color is fucking hardcoded blue for god's sake. There is
     * no fucking way they can be serious about this. They have to be fucking with us at 
     * this point. What a bunch of fucking retards.
     *     - Love, Aubrey <3
     * 
     * Anyways, this function just converts it back to the standard runs format.
     * 
     * We use mb_substr with UTF-8 here, because the indices are set up for a JS environment.
     * 
     * @param object $description  videoSecondaryInfoRenderer.attributedDescription
     */
    private static function convertDescription(object $description): object
    {
        // If there's no links
        if (!isset($description->commandRuns))
        {
            return (object) [
                "runs" => [
                    (object) [
                        "text" => $description->content
                    ]
                ]
            ];
        }

        $runs = [];

        // Start at the beginning of the string
        $start = 0;

        foreach ($description->commandRuns as $run)
        {
            // Text from before the link
            $beforeText = self::telegram_substr($description->content, $start, $run->startIndex - $start);

            if (!empty($beforeText))
            {
                $runs[] = (object) [
                    "text" => $beforeText
                ];
            }

            // Add the actual link
            $text = self::telegram_substr($description->content, $run->startIndex, $run->length);
            $endpoint = $run->onTap->innertubeCommand;
            $runs[] = (object) [
                "text" => $text,
                "navigationEndpoint" => $endpoint
            ];

            $start = $run->startIndex + $run->length;
        }

        // Add the text after the last link
        $lastText = self::telegram_substr($description->content, $start, null);
        if (!empty($lastText))
        {
            $runs[] = (object) [
                "text" => $lastText
            ];
        }

        // Fix link text
        foreach ($runs as &$run)
        if (isset($run->navigationEndpoint))
        {
            // Video links
            if (isset($run->navigationEndpoint->watchEndpoint)
            &&  !preg_match("/^([0-9]{1,2}(:)?)+$/", $run->text)) // Prevent replacing timestamps
            {
                $run->text = self::truncate("https://www.youtube.com" . $run->navigationEndpoint->commandMetadata->webCommandMetadata->url);
            }
            // Channel links
            elseif (isset($run->navigationEndpoint->browseEndpoint))
            {
                switch (substr($run->navigationEndpoint->browseEndpoint->browseId, 0, 2))
                {
                    case "UC":
                        $count = 1; // This has to be a variable for some reason
                        $run->text = str_replace("\xc2\xa0", "", str_replace("/", "", $run->text, $count));
                        break;
                    case "FE":
                        break;
                    default:
                        $run->text = self::truncate("https://www.youtube.com" . $run->navigationEndpoint->commandMetadata->webCommandMetadata->url);
                        break;
                }
            }
            // Weird fake channel links
            else if (str_contains($run->text, "\xc2\xa0"))
            {
                $run->text = self::truncate($run->navigationEndpoint->commandMetadata->webCommandMetadata->url);
            }
        }

        return (object) [
            "runs" => $runs
        ];
    }

    /**
     * Truncate link texts
     */
    private static function truncate(?string $string): ?string
    {
        if (is_null($string)) return null;
        if (strlen($string) <= 37)
        {
            return $string;
        }
        else
        {
            return substr($string, 0, 37) . "...";
        }
    }

    // FUCKING THANK YOU SO MUCH
    // THIS FIXED EMOJI PROBLEM
    // https://stackoverflow.com/a/66878985
    private static function telegram_substr($str, $offset, $length) {
        $bmp = [];
        for( $i = 0; $i < mb_strlen($str); $i++ )
        {
            $mb_substr = mb_substr($str, $i, 1);
            $mb_ord = mb_ord($mb_substr);
            $bmp[] = $mb_substr;
            if ($mb_ord > 0xFFFF)
            {
                $bmp[] = "";
            }
        }
        return implode("", array_slice($bmp, $offset, $length));
    }
}