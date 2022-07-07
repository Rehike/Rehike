<?php
namespace SpfPhp;

use voku\helper\HtmlDomParser as Dom;

/**
 * An adapter for voku's HtmlDomParser library.
 * 
 * @author The Rehike Maintainers
 * @license MIT
 */
class VokuParserAdapter implements IAdapter
{
    protected static $dom;

    public static function register(&$html)
    {
        self::$dom = new Dom();
        self::$dom->loadHtml($html, LIBXML_PARSEHUGE);
    }

    public static function getElementById($id)
    {
        $element = self::$dom->getElementById($id);

        // Premature return if null
        if (null == $element) return null;

        // Otherwise convert the element and return
        return self::convertElement($element);
    }

    public static function getHtmlTitle()
    {
        $title = self::$dom->find('head title');
        // title is an array (this is like querySelectorAll)
        if (isset($title[0])) {
            $text = htmlspecialchars_decode($title[0]->text, ENT_QUOTES);
            $title[0]->delete();
            unset($title);
            return $text;
        }
        return null;
    }

    public static function getHead($resourcesOnly = true)
    {
        $html = &self::$dom;
        $response = '';

        /** @var \Countable // intelephense hack */
        $head = $html->find('head'); // this is an array again
        if (count($head) == 0) return null; // skip if head unavailable

        if ($resourcesOnly) {
            /** @var \Countable // intelephense hack */
            $headItems = $html->find('head > style, head > link, head > script');
            for ($i = 0; $i < count($headItems); $i++) {
                $response .= $headItems[$i]->outerHtml();
            }
        } else {
            $response = $head[0]->innerHtml();
        }

        return $response;
    }

    public static function getBodyXtagged()
    {
        $html = &self::$dom;
        $response = [];

        // Get all elements with the capture x-tag
        $caps = $html->find("[" . XTag::XTAG_PREFIX . "capture]");
        
        // Add the new elements to the array
        if (is_array($caps) || $caps instanceof \voku\helper\SimpleHtmlDomNode)
        {
            foreach ($caps as $index => $element)
            {   
                $response[] = self::convertElement($element);
            }
        }
        else if (null != $caps)
        {
            $response[] = self::convertElement($caps);
        }
        
        // Return the array
        return $response;
    }

    public static function getFoot()
    {
        $html = &self::$dom;
        $response = '';

        /** @var \Countable // intelephense hack */
        $footItems = $html->find('body > style, body > link, body > script');
        $itemsCount = count($footItems);
        if ($itemsCount == 0) return null;

        for ($i = 0; $i < $itemsCount; $i++) {
            $response .= $footItems[$i]->outerHtml() ?? '';
        }

        return $response;
    }

    public static function getAttribute($id, $attribute)
    {
        $dom = &self::$dom;

        if (($element = $dom->getElementById($id)) && $element->getAttribute($attribute)) {
            return $element->getAttribute($attribute);
        } else {
            return '';
        }
    }

    /**
     * Convert a voku element to an Element instance
     * 
     * @param $element
     * @return Element
     */
    protected static function convertElement($element)
    {
        // Get the innerHTML of this node.
        $innerHTML = $element->innerHtml() ?? "";

        // Get attributes and separate x-tags
        if (method_exists($element, "getAllAttributes"))
        {
            $attributes = $element->getAllAttributes();

            // Split off x-tags
            XTag::splitAttributesArray($attributes, $xtags);
        }
        else
        {
            $attributes = [];
            $xtags = [];
        }

        // Return a new Element instance with this data.
        return new Element($innerHTML, $attributes, $xtags);
    }
}