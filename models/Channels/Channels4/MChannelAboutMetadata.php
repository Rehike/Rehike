<?php
namespace Rehike\Model\Channels\Channels4;

use Rehike\Util\ExtractUtils;
use Rehike\i18n;
use Rehike\TemplateFunctions as TF;

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
        $regexs = &i18n::getNamespace("main/regex");
        $miscStrings = &i18n::getNamespace("main/misc");

        $this->subscriberCountText = self::getRichStat(
            $subCount,
            $regexs->subscriberCountIsolator
        );

        $viewCountText = $miscStrings->viewTextPlural("0");

        if (isset($data->viewCountText))
            $viewCountText = TF::getText(@$data->viewCountText);

        $this->viewCountText = self::getRichStat(
            $viewCountText,
            $regexs->viewCountIsolator
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