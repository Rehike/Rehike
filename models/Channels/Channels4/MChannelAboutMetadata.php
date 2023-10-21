<?php
namespace Rehike\Model\Channels\Channels4;

use Rehike\Util\ExtractUtils;
use Rehike\i18n\i18n;
use Rehike\TemplateFunctions as TF;
use Rehike\Model\Traits\NavigationEndpoint;

class MChannelAboutMetadata
{
    public $subscriberCountText;
    public $viewCountText;
    public $joinedDateText;
    public $descriptionLabel;
    public $detailsLabel;
    public $linksLabel;
    public $description;
    public $country;
    public $countryLabel;
    public $primaryLinks;

    public function __construct($subCount, $data)
    {
        $regexs = i18n::getNamespace("regex");
        $miscStrings = i18n::getNamespace("misc");

        $this->subscriberCountText = self::getRichStat(
            $subCount,
            $regexs->get("subscriberCountIsolator")
        );

        $viewCountText = $miscStrings->format("viewTextPlural", "0");

        if (isset($data->viewCountText))
            $viewCountText = TF::getText(@$data->viewCountText);

        $this->viewCountText = self::getRichStat(
            $viewCountText,
            $regexs->get("viewCountIsolator")
        );

        $this->joinedDateText = TF::getText(@$data->joinedDateText);

        if (isset($data->descriptionLabel))
            $this->descriptionLabel = TF::getText($data->descriptionLabel);

        if (isset($data->detailsLabel))
            $this->detailsLabel = TF::getText($data->detailsLabel);

        if (isset($data->primaryLinksLabel))
            $this->linksLabel = TF::getText($data->primaryLinksLabel);
        
        if (isset($data->description))
            $this->description = $data->description;

        if (isset($data->country))
            $this->country = TF::getText($data->country);

        if (isset($data->countryLabel))
            $this->countryLabel = TF::getText($data->countryLabel);

        if (isset($data->primaryLinks))
            $this->primaryLinks = $data->primaryLinks;
        else if (isset($data->links))
            $this->primaryLinks = self::convertLinks($data->links);
    }

    /**
     * The monkeys have done it AGAIN!
     * This among the other new things from channel page use the same commandRuns
     * bullshit that the description does. Fortunately, here it isn't actually
     * used to split apart strings, so it's pretty painless to parse it.
     * 
     * Anyways, this function converts the new link format back to the old one.
     * Icons and everything.
     * 
     * @param array $links  channelAboutFullMetaDataRenderer.links object
     * @return array
     */
    private static function convertLinks(array $links): array
    {
        $out = [];
        foreach ($links as $link)
        {
            $nlink = (object) [];
            $link = $link->channelExternalLinkViewModel;

            $nlink->title = (object) [
                "simpleText" => $link->title->content
            ];

            $nlink->navigationEndpoint = $link->link->commandRuns[0]->onTap->innertubeCommand;

            /**
             * Google removed the icon markup, so we have to provide it ourself.
             * Luckily, they provide a simple API for favicons, which just so
             * happens to be the exact one which these actually used before the
             * update.
             */
            $nlink->icon = (object) [
                "thumbnails" => [
                    (object) [
                        "url" =>
                        sprintf(
                            "https://t0.gstatic.com/faviconV2?client=SOCIAL&type=FAVICON&fallback_opts=TYPE,SIZE,URL&url=https://%s&size=16",
                            strstr($link->link->content, "/", true)
                        )
                    ]
                ]
            ];
            
            $out[] = $nlink;
        }
        return $out;
    }

    public static function getRichStat($text, $isolator)
    {
        if ("" == $text) return;

        $number = preg_replace(
            str_replace("/g", "/", $isolator), "", $text
        );
        $string = str_replace($number, "<b>$number<b>", $text);
        $string = explode("<b>", $string);

        $response = (object)["runs" => []];

        for ($i = 0; $i < count($string); $i++)
        {
            $response->runs[$i] = (object)[
                "text" => $string[$i]
            ];

            if ($number == $string[$i])
            {
                $response->runs[$i]->bold = true;
            }
        }

        return $response;
    }
}