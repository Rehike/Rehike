<?php
namespace YukisCoffee\CoffeeTranslation\Router;

/**
 * Defines the base schema for a Router.
 * 
 * The router system is simple: a common URI pattern is used by all language
 * files, and a lookup is performed for the user languages.
 * 
 * The router is ultimately responsible for producing a language SourceInfo
 * object, which is sent to the parser and used to produce the final data
 * record.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 */
interface IRouter
{
    /**
     * Resolve a given language URI.
     */
    public function resolveLocation(string $langId, string $url): ResourceInfo;

    /**
     * Check if a given language ID exists in the current context.
     */
    public function languageExists(string $langId): bool;

    /**
     * Check if valid content exists at the requested URI.
     */
    public function locationExists(string $langId, string $uri): bool;

    /**
     * Resolve a given language URI with a given character encoding scheme.
     */
    public function resolveLocationAsEncoding(
            string $langId,
            string $uri,
            string $encoding
    ): ResourceInfo;
}