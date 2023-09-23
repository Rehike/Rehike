<?php
namespace Rehike\Controller\rehike;

use Rehike\YtApp;
use Rehike\ControllerV2\RequestMetadata;

class StaticRouter
{
    public function get(YtApp $yt, string &$template, RequestMetadata $request)
    {
        $filename = "static/";
        for ($i = 2; $i < count($request->path); $i++)
        {
            if ($i == count($request->path) - 1)
            {
                $filename .= $request->path[$i];
            }
            else 
            {
                $filename .= $request->path[$i] . "/";
            }
        }

        if (file_exists($filename)) 
        {
            header("Content-Type: " . mime_content_type($filename));
            echo file_get_contents($filename);
            exit();
        } 
        else 
        {
            http_response_code(404);
        }
    }

    public function post(YtApp &$yt, string &$template, RequestMetadata $request)
    {
        return $this->get($yt, $template, $request);
    }
}

return new StaticRouter();