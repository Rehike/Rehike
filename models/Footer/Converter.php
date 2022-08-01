<?php
namespace Rehike\Model\Footer;

use \Rehike\Model\Footer\MFooter;
use \Rehike\Model\Footer\MFooterLink;
use \voku\helper\HtmlDomParser as Dom;

/**
 * Convert the footer data from InnerTu-
 * Oh wait. Sorry, I mean WEBFE HTML.
 * Convert the footer data from WebFE HTML
 * into the Rehike format.
 */
class Converter {
    protected static $dom;

    /**
     * Register the Voku DOM Parser
     * This should be run before any other function here
     * 
     * @param string $html WebFE response HTML
     */
    public static function register($html) {
        self::$dom = new Dom();
        self::$dom->loadHtml($html, LIBXML_PARSEHUGE);
    }

    /**
     * Build links array from WebFE HTML.
     * 
     * @param string $slot Slot param from links
     */
    public static function bakeLinks($slot) {
        $html = self::$dom;
        $links = $html -> find("ytd-app a[slot=\"" . $slot . "\"]");
        $response = [];

        for ($i = 0; $i < count($links); $i++) {
            $response[] = new MFooterLink((object) [
                "text" => str_replace("&amp;", "&", $links[$i] -> innerHtml),
                "href" => $links[$i] -> getAttribute("href")
            ]);
        }

        return $response;
    }

    /**
     * Bake primary links.
     */
    public static function bakePrimaryLinks() {
        return self::bakeLinks("guide-links-primary");
    }

    /**
     * Bake secondary links.
     */
    public static function bakeSecondaryLinks() {
        return self::bakeLinks("guide-links-secondary");
    }
}