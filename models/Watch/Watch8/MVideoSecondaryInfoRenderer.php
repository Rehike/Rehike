<?php
namespace Rehike\Model\Watch\Watch8;

use Rehike\Util\ExtractUtils;
use Rehike\Util\ParsingUtils;
use Rehike\Model\Watch\Watch8\SecondaryInfo\MMetadataRowContainer;
use Rehike\Model\Watch\WatchBakery;

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

    public const REDIRECT_URL_REGEX = "/(?<=\?q=|&q=)(?=(.*?)&|$)/";

    public function __construct(WatchBakery $bakery)
    {
        if (!is_null($bakery->secondaryInfo))
        {
            $info = &$bakery->secondaryInfo;
            $primaryInfo = &$bakery->primaryInfo;
            
            if (isset($info->attributedDescription))
            {
                $this->description = ParsingUtils::indexedRunsToRuns($info->attributedDescription);
                self::fixDescLinks($this->description->runs);
            }

            $isPrivate = false;
            if (isset($primaryInfo->badges))
            foreach ($primaryInfo->badges as $badge)
            {
                if ($icon = @$badge->metadataBadgeRenderer->icon->iconType)
                {
                    if (str_starts_with($icon, "PRIVACY_"))
                    {
                        $isPrivate = true;
                    }
                }
            }
            
            $this->defaultExpanded = $info->defaultExpanded ?? false;
            $this->dateText = isset($primaryInfo->dateText)
                ? ExtractUtils::resolveDate($primaryInfo->dateText, $isPrivate)
                : null;
            $this->metadataRowContainer = new MMetadataRowContainer(
                $bakery,
                $info->metadataRowContainer->metadataRowContainerRenderer->rows
            );
            $this->showMoreText = $info->showMoreText ?? null;
            $this->showLessText = $info->showLessText ?? null;
        }
    }

    /**
     * Fix description link text.
     * 
     * @param object $runs  runs object
     * @param 
     */
    private static function fixDescLinks(array &$runs): void
    {
        foreach ($runs as &$run)
        if (isset($run->navigationEndpoint))
        {
            // Video links
            if (isset($run->navigationEndpoint->watchEndpoint)
            &&  !preg_match("/^([0-9]{1,2}(:)?)+$/", $run->text)) // Prevent replacing timestamps
            {
                $run->text = self::truncate(
                    $run->navigationEndpoint->commandMetadata->webCommandMetadata->url,
                    true
                );
            }
            // Channel links
            else if (isset($run->navigationEndpoint->browseEndpoint))
            {
                switch (substr($run->navigationEndpoint->browseEndpoint->browseId, 0, 2))
                {
                    case "UC":
                        $count = 1; // This has to be a variable for some reason
                        $run->text = str_replace("\xc2\xa0", "", str_replace("/", "", $run->text, $count));
                        // Add @ if it isn't there
                        if (substr($run->text, 0, 1) != "@")
                        {
                            $run->text = "@" . $run->text;
                        }
                        break;
                    case "FE":
                        break;
                    default:
                        $run->text = self::truncate(
                            $run->navigationEndpoint->commandMetadata->webCommandMetadata->url,
                            true
                        );
                        break;
                }
            }
            // Other links which have custom styling
            else if (str_contains($run->text, "\xC2\xA0"))
            {
                $url = $run->navigationEndpoint->commandMetadata->webCommandMetadata->url;

                // Some external links (e.g. Twitter) have custom styles applied to them
                // like channel links and such. Just using the regular URL from these
                // results in a redirect link being directly displayed, so if that occurs,
                // we just extract the actual URL from the redirect URL.
                if (str_starts_with($url, "https://www.youtube.com/redirect"))
                {
                    $matches = [];
                    preg_match(self::REDIRECT_URL_REGEX, $url, $matches);
                    if (isset($matches[1]))
                    {
                        $url = urldecode($matches[1]);
                    }
                }

                $run->text = self::truncate($url);
            }
        }
    }

    /**
     * Truncate a string for displaying as a description link.
     */
    private static function truncate(?string $string, bool $prefix = false): ?string
    {
        if (is_null($string)) return null;
        if ($prefix) $string = "https://www.youtube.com" . $string;
        if (mb_strlen($string) <= 37)
        {
            return $string;
        }
        else
        {
            return ParsingUtils::mb_substr_ex($string, 0, 37) . "...";
        }
    }
}