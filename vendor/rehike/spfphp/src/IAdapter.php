<?php
namespace SpfPhp;

/**
 * Implements the interface for all adapters that SpfPhp
 * can use.
 * 
 * @author The Rehike Maintainers
 * @license MIT
 */
interface IAdapter
{
    /** 
     * Initialize the library and bind the input document.
     * 
     * @param string $html
     * @return void
     */
    public static function register(&$html);

    /**
     * Get an element by ID and return a unique instance.
     * 
     * @return Element|null
     */
    public static function getElementById($id);

    /**
     * Extract document title.
     * 
     * @return string|null
     */
    public static function getHtmlTitle();

    /**
     * Extract HTML head
     * 
     * @param bool $resourcesOnly   Delimit head SPF response to only resources. Default: true
     * @return string|null
     */
    public static function getHead($resourcesOnly = true);

    /**
     * Get all elements with the attribute `x-spfphp-capture`
     * that also have their ID attribute set.
     * 
     * @return Element[]
     */
    public static function getBodyXtagged();

    /**
     * Extract footer resources.
     * 
     * @return string|null
     */
    public static function getFoot();

    /**
     * Get the attribute of an element by ID and return its value. Otherwise,
     * return an empty string.
     * 
     * @param string $id of the element
     * @param string $attribute name
     * @return string
     */
    public static function getAttribute($id, $attribute);
}