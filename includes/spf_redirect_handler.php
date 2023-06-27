<?php
use SpfPhp\SpfPhp;

/**
 * A custom redirect handler that takes SPF into account.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */
return function($url) {
    if (SpfPhp::isSpfRequested())
    {
        $spfRegexp = '/(\?|&)spf=[A-Za-z0-9]*/';

        http_response_code(200);
        echo json_encode((object)[
            "redirect" => preg_replace($spfRegexp, "", $url)
        ]);
        die();
    }
    else
    {
        http_response_code(302);
        header("Location: $url");
        die();
    }
};