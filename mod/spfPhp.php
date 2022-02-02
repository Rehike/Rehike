<?php
namespace SpfPhp;

require_once('vendor/autoload.php');
use voku\helper\HtmlDomParser as Dom;

/**
 * PHP library for SPF.js-compatible output.
 *
 * This library transforms a HTML string (i.e. from output buffering)
 * into JSON that is compatible with YouTube's SPF.js library for
 * dynamic page navigation using server-side rendered content.
 *
 * @author      Nightlinbit (Daylin Cooper)
 * @version     1.0.20220120
 * @license     CC0
 */
class SpfPhp {
    /**
     * Main function
     *
     * SPF restructures HTML into JSON, mapping element contents and
     * attributes with the element's id. This structure works in a few ways.
     *
     * A standard SPF response contains a few parentmost fields. These are:
     *      "title": Updates the document's title.
     *      "url": If specified, updates the address seen by the end-user.
     *             If not specified, SPF uses the request URI.
     *      "head": HTML content of the head element.
     *      "attr": Sets attributes of all specified element IDs.
     *      "body": Tree containing an element ID to HTML map.
     *      "foot": Field specifying end-of-body resources (CSS/JS)
     * SPF processes fields in this order and each field is optional.
     * 
     * SpfPhp uses angle brackets alongside listener ids to specify attributes to listen for.
     * For example, "page<class>" adds a listener for both page and its class attribute.
     * Multiple attributes should be delimited by commas: "page<class, name>".
     * For isolated attributes, prefix the selector with '@'. This ignores the selector when
     * building the body section.
     *
     * @param string $html   Input HTML string to transform.
     * @param string[] $listenerIds   HTML element IDs to listen for.
     * @param object|null $params   (Optional) Configuration options.
     * @return string|object
     */
    public static function build(string $html, array $listenerIds, ?object $params = null) {
        $response = (object) [];
        $dom = new Dom();
        $dom->loadHtml($html);

        // Set response title
        if ($responseTitle = self::getHtmlTitle($dom)) {
            $response->title = $responseTitle;
        }

        // Create url field
        if (isset($params->url)) {
            $response->url = $params->url;
        }

        // Create head field
        $useFullHead = (isset($params->useFullHead) && ($params->useFullHead));
        if ($responseHead = self::getHead($dom, $useFullHead)) {
            $response->head = $responseHead;
        }
        
        // Create attr field (god this is ugly)
        $hasAttrField = false;
        $idsCount = count($listenerIds);
        for ($i = 0; $i < $idsCount; $i++) {
            $id = $listenerIds[$i];

            if (strstr($id, '<')) {
                if (!$hasAttrField) {
                    $response->attr = (object) [];
                    $hasAttrField = true;
                }
                self::pushAttributes($dom, $response->attr, $id);
            }
        }

        // Create body field
        $response->body = (object) [];
        $body = $response->body;

        for ($i = 0; $i < $idsCount; $i++) {
            $id = $listenerIds[$i];

            // skip @ prefixed selectors
            if (substr($id, 0, 1) == '@') {
                continue;
            }
            // remove attribute delimiter
            if (strstr($id, '<')) {
                $id = explode('<', $id)[0];
            }

            if ($element = $dom->getElementById($id)) {
                $body->{$id} = $element->innerHtml() ?? '';
            } else {
                $body->{$id} = '';
            }
        }

        // Create foot field
        if ($responseFoot = self::getFoot($dom)) {
            $response->foot = $responseFoot;
        }

        // Finalisation of response

        if (!isset($params->skipSerialisation) || !$params->skipSerialisation) {
            $response = json_encode($response);
        }

        return $response;
    }

    /**
     * Extract document title.
     *
     * @param Dom $html     Writable so we can nuke the title lol
     * @return string|null
     */
    public static function getHtmlTitle(Dom &$html): ?string {
        $title = $html->find('head title');
        // title is an array (this is like querySelectorAll)
        if ($title[0]) {
            $text = $title[0]->text;
            $title[0]->delete();
            unset($title);
            return $text;
        }
        return null;
    }

    /**
     * Extract HTML head
     * 
     * @param Dom $html
     * @param bool $resourcesOnly   Delimit head SPF response to only resources. Default: true
     * @return string|null
     */
    public static function getHead(Dom $html, bool $resourcesOnly = true): ?string {
        $response = '';

        $head = $html->find('head'); // this is an array again
        if (count($head) == 0) return null; // skip if head unavailable

        if ($resourcesOnly) {
            $headItems = $html->find('head > style, head > link, head > script');
            for ($i = 0; $i < count($headItems); $i++) {
                $response .= $headItems[$i]->outerHtml();
            }
        } else {
            $response = $head[0]->innerHtml();
        }

        return $response;
    }

    /**
     * Extract footer resources.
     * 
     * @param Dom $html
     * @return string|null
     */
    public static function getFoot(Dom $html): ?string {
        $response = '';

        $footItems = $html->find('body > style, body > link, body > script');
        $itemsCount = count($footItems);
        if ($itemsCount == 0) return null;

        for ($i = 0; $i < $itemsCount; $i++) {
            $response .= $footItems[$i]->outerHtml() ?? '';
        }

        return $response;
    }

    /**
     * Parse serialised attribute data into an object.
     * 
     * @param string $data
     * @return object
     */
    public static function parseAttributes(string $data): object {
        $response = (object) [];

        $frags = explode('<', $data);
        $id = str_replace('@', '', $frags[0]); // id should always preceed definitions
        $attrs = str_replace(['>', ' '], '', $frags[1]);
        $attrs = explode(',', $attrs);

        $response->id = $id;
        $response->attrs = $attrs;

        return $response;
    }

    /**
     * Push serialised attributes to an object.
     * 
     * @param Dom $dom
     * @param object $attibutes
     * @param string $serialisedData    Element ID and serialised attributes information.
     * @return void
     */
    public static function pushAttributes(Dom $dom, object &$attributes, string $serialisedData): void {
        $data = self::parseAttributes($serialisedData);
        $id = $data->id;

        $attributes->{$id} = (object) [];

        for ($i = 0; $i < count($data->attrs); $i++) {
            $curAttr = $data->attrs[$i];

            if (($element = $dom->getElementById($id)) && $element->getAttribute($curAttr)) {
                $attributes->{$id}->{$curAttr} = $element->getAttribute($curAttr);
            } else {
                $attributes->{$id}->{$curAttr} = '';
            }
        }
    }
}