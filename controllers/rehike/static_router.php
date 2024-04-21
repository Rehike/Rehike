<?php
namespace Rehike\Controller\rehike;

use Rehike\YtApp;
use Rehike\FileSystem;
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
            header("Content-Type: " . $this->getMimeType($filename));
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

    /**
     * Gets the MIME type for a filename.
     * 
     * The default PHP limitation is a little bit broken, so we only use it as a
     * fallback and try to detect the MIME type on our own.
     */
    private function getMimeType(string $filename): string
    {
        $ext = FileSystem::getExtension($filename);

        return match ($ext)
        {
            "js" => "text/javascript",
            "css" => "text/css",
            "json" => "application/json",
            "txt" => "text/plain",
            "html" => "text/html",
            "php" => "text/html",
            "xml" => "application/xml",
            "swf" => "application/x-shockwave-flash",
            "flv" => "video/x-flv",
            "png" => "image/png",
            "jpg" => "image/jpeg",
            "jpeg" => "image/jpeg",
            "gif" => "image/gif",
            "bmp" => "image/bmp",
            "svg" => "image/svg+xml",
            "zip" => "application/zip",
            "mp3" => "audio/mpeg",
            "mp4" => "video/mp4",
            default => mime_content_type($ext)
        };
    }
}

return new StaticRouter();