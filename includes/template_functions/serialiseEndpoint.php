<?php
use Rehike\Util\Base64Url;

use Com\Youtube\Innertube\Navigation\NavigationEndpoint;
use Com\Youtube\Innertube\Navigation\NavigationEndpoint\BrowseEndpoint;
use Com\Youtube\Innertube\Navigation\NavigationEndpoint\UrlEndpoint;

/**
 * Serialise a guide navigation endpoint in URL-base64 protobuf.
 * 
 * This is very accurate to the official Hitchhiker implementation.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 * 
 * @param object $endpoint
 * @return string
 */
\Rehike\TemplateFunctions::register('serialiseEndpoint', function($endpoint) {
    $pb = new NavigationEndpoint();

    if (isset($endpoint->browseEndpoint))
    {
        $pb->setBrowseEndpoint(new BrowseEndpoint([
            "browse_id" => $endpoint->browseEndpoint->browseId
        ]));
    }
    else if (isset($endpoint->urlEndpoint))
    {
        $pb->setUrlEndpoint(new UrlEndpoint([
            "url" => $endpoint->urlEndpoint->url
        ]));
    }

    $data = $pb->serializeToString();

    return Base64Url::encode($data);
});