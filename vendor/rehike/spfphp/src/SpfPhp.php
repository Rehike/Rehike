<?php
namespace SpfPhp;

/**
 * A PHP library for generating SPF.js-compatible output.
 *
 * This library transforms a HTML string (i.e. from output buffering)
 * into JSON that is compatible with YouTube's SPF.js library for
 * dynamic page navigation using server-side rendered content.
 *
 * @author      The Rehike Maintainers
 * @license     MIT
 */
class SpfPhp
{
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
     * @param string[]|string $listenerIds   HTML element IDs to listen for.
     * @param array|object|null $params   (Optional) Configuration options.
     * @return string|object
     */
    public static function parse($html, $listenerIds = [], $params = null)
    {
        $response = (object) [];

        // Convert a string-encoded listener ID list to a string array.
        if (is_string($listenerIds))
        {
            $listenerIds = self::parseListenerIdsString($listenerIds);
        }

        // While we're at it, some people may not want to use listenerIds
        // at all. In case x-tags are being used exclusively, but params
        // are being passed, allow the listenerIds argument be used for
        // params too.
        // To do this, just assume that the array should be numerically
        // iterated usually and assume they are params if they are not
        // (therefore likely an associative array).
        if (is_array($listenerIds) && !isset($listenerIds[0]))
        {
            $params = $listenerIds;
            $listenerIds = [];
        }

        // Handle configuration.
        if (is_object($params)) $params = (array)$params;

        if (null != $params)
            $params += self::getDefaultParams(); // Merge default parameters
        else
            $params = self::getDefaultParams();

        if (isset($params["skipSerialization"]))
        {
            $params["skipSerialisation"] = $params["skipSerialization"];
        }

        // Register the voku parser adapter
        VokuParserAdapter::register($html);

        // Set response title
        if ($responseTitle = VokuParserAdapter::getHtmlTitle()) {
            $response->title = $responseTitle;
        }

        // Create url field
        if (null != $params["url"]) {
            $response->url = $params["url"];
        }

        // Create head field
        $useFullHead = (isset($params["useFullHead"]) && ($params["useFullHead"]));

        if ($responseHead = VokuParserAdapter::getHead($useFullHead)) {
            $response->head = $responseHead;
        }

        // Create attr field (god this is ugly)
        $attributes = [];

        $idsCount = count($listenerIds);
        for ($i = 0; $i < $idsCount; $i++) {
            $id = $listenerIds[$i];

            if (strstr($id, '<')) {
                $attributes += self::parseAttributes($id);
            }
        }

        // Create body field and push attributes
        $xtaggedIds = VokuParserAdapter::getBodyXtagged();

        $response->body = (object) [];
        $body = $response->body;

        // Iterate both listenerIds and xtaggedIds and return their contents
        foreach (\array_merge($listenerIds, $xtaggedIds) as $index => $id) {
            $iterationIsXtagged = false;
            $skip = false;

            // Differentiate and retrieve the missing information between
            // listenerIds and xtaggedIds
            if ($id instanceof Element)
            {
                $element = $id;
                $id = $element->getAttribute("id");
                $iterationIsXtagged = true;
            }

            if (null == $id) continue;

            // handle @ prefixed selectors
            if (substr($id, 0, 1) == '@')
            {
                $skip = true;
                $id = substr($id, 1);
            }

            // remove attribute delimiter
            if (strstr($id, '<')) {
                $id = explode('<', $id)[0];
            }

            // If the element isn't already set, set it now
            if (!$iterationIsXtagged)
                $element = VokuParserAdapter::getElementById($id);

            // Add x-spfphp-listener-attributes to the attributes array
            if (($xt = $element->getXtag("listener-attributes")) && null != $xt)
            {
                // Remove spaces and separate by commas.
                $attributes[$element->getId()] = explode(",", str_replace(" ", "", $xt));
            }

            // Push attributes into the attributes array.
            self::pushAttributes($response, $attributes, $element);

            // skip @ prefixed selectors
            if ($skip) continue;

            // Skip x-spfphp-ignore-body attribute
            if (($xt = $element->getXtag("ignore-body")) || !is_null($xt) && "false" !== $xt)
                continue;

            // Contents to add to the ID.
            $content = Xtag::erradicate($element->innerHTML);

            // Use a direct render callback (if it is set)
            if (null != ($cbName = $element->getXtag("direct-render-callback")))
            {
                // Get the response of the callback.
                // The callback is passed the xtags of the element as its only
                // argument, so it may obtain additional data from that.
                $content = DirectRender::getCallback($cbName)->call($element->xtags);
            }

            $body->{$id} = $content;
        }

        // Create foot field
        if ($responseFoot = VokuParserAdapter::getFoot()) {
            $response->foot = $responseFoot;
        }

        // Finalisation of response
        // Skip serialisation (with aliases)
        if (!isset($params["skipSerialisation"]) || !$params["skipSerialisation"])
        {
            $response = json_encode($response);
        }

        return $response;
    }
    
    /**
     * Parse a HTML string and echo its contents.
     * 
     * @see parse()
     */
    public static function display($html, $listenerIds = [], $params = null)
    {
        @header("Content-Type: application/json");

        if (!is_array($params)) $params = [];
        $params["skipSerialisation"] = false; // This may never happen in this case.

        echo self::parse($html, $listenerIds, $params);
    }

    /**
     * Begin capturing HTML contents (to be used with autoRender method)
     * 
     * @return void
     */
    public static function beginCapture()
    {
        ob_start();
    }

    /**
     * Attempt to automatically control the render of a page.
     * 
     * This may wrap any template engine, as well as any other render
     * functionality. However, it may conflict with other libraries that
     * control output buffering. Overall, it should provide an easy means
     * for integration with pre-existing projects.
     * 
     * This assumes a dependency on the x-tag functionality, which
     * are accounted for and automatically removed in both SPF and HTML 
     * output. However, listener IDs still may be used as another solution.
     */
    public static function autoRender($listenerIds = [], $params = null)
    {
        // Attempt to get contents from the output buffer
        $ob = @ob_get_clean();

        // If there is an error, throw an exception:
        if (false == $ob) throw new Exception\RenderCaptureException(
            "Failed to capture output buffering contents. Did you forget to run " .
            "beginCapture()? This may also be triggered by some libraries, such as " .
            "PHP Template Inheritance."
        );

        // Check if SPF is requested
        if (self::isSpfRequested())
        {
            // I am using an SPF request, therefore parse the response
            // and send it.
            return self::display($ob, $listenerIds, $params);
        }
        else
        {
            // I am not using an SPF request, therefore remove all x-tags
            // from the HTML response and send it.
            echo XTag::erradicate($ob);
        }
    }

    /**
     * Look at the request parameters and determine if SPF
     * was requested (over the normal state).
     * 
     * @return string|null if string, the SPF state (navigate/etc.)
     */
    public static function isSpfRequested()
    {
        if (isset($_GET["spf"]))
        {
            switch ($_GET["spf"])
            {
                case "navigate":
                case "navigate-back":
                case "navigate-forward":
                case "load":
                    return $_GET["spf"];
            }
        }

        return false;
    }

    /** @see DirectRender::registerCallback */
    public static function registerDirectRenderCallback($name, $cb)
    {
        return DirectRender::registerCallback($name, $cb);
    }

    /**
     * Get the default parameters for a request.
     * 
     * @return array
     */
    protected static function getDefaultParams()
    {
        return [
            "skipSerialisation" => false,
            "useFullHead" => false,
            "url" => null
        ];
    }

    /**
     * Parse serialised attribute data into an object.
     * 
     * @param string $data
     * @return string[]
     */
    protected static function parseAttributes($data)
    {
        $frags = explode('<', $data);
        $id = str_replace('@', '', $frags[0]); // id should always preceed definitions
        $attrs = str_replace(['>', ' '], '', $frags[1]);
        $attrs = explode(',', $attrs);

        return [$id => $attrs];
    }

    /**
     * Push serialised attributes to an object.
     * 
     * @param object $data or the ultimate object to return...
     * @param array $attributes of the element
     * @param Element $element
     * @return void
     */
    protected static function pushAttributes(&$data, &$attributes, &$element)
    {
        // Premature return if the element doesn't exist.
        if (!($element instanceof Element)) return;

        $elmid = $element->getId();

        // Check if the element ID is present within the attributes array.
        // Premature return if it isn't.
        if (!isset($attributes[$elmid])) return;

        // If I get this far, try to iterate the attributes and check if they're on
        // the element.
        foreach ($attributes[$elmid] as $name)
        {
            // Create the attr field if it doesn't already exist
            if (!isset($data->attr))
            {
                $data->attr = (object) [];
            }

            // Create a field for this element if it doesn't already exist
            if (!isset($data->attr->{$elmid}))
            {
                $data->attr->{$elmid} = (object)[];
            }

            $data->attr->{$elmid}->{$name} = $element->getAttribute($name) ?? "";
        }
    }

    /**
     * Parse a semicolon separated listener ID string
     * into a string array.
     * 
     * @param string $ids
     * @return string[]
     */
    protected static function parseListenerIdsString($ids)
    {
        return (
            explode(";", str_replace(
                [" ", "\r\n", "\n"], "", $ids
            ))
        );
    }
}
