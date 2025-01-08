<?php
namespace Rehike\Controller\rehike;

use Rehike\ControllerV2\BaseController;
use Rehike\ControllerV2\IGetController;
use Rehike\ControllerV2\IPostController;
use Rehike\YtApp;
use Rehike\FileSystem;
use Rehike\ResourceConstantsStore;
use Rehike\ControllerV2\RequestMetadata;

/**
 * Static content router stub.
 * 
 * @author Aubrey Pankow <aubyomori@gmail.com>
 * @author Isabella Lulamoon <kawapure@gmail.com>
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */
class StaticRouter extends BaseController implements IGetController, IPostController
{
    public function get(): void
    {
        $baseMap = ResourceConstantsStore::getVersionMap();
        $request = $this->getRequest();
        
        // We reverse the version map because we're performing a VFL-to-original
        // lookup in this case.
        $map = $this->reverseObject($baseMap);
        
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
        
        // If we're requesting a VFL version, then we map it back to the original.
        if (isset($map->{$filename}))
        {
            $filename = $map->{$filename};
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

    public function post(): void
    {
        $this->get();
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
    
    private function reverseObject(object $in): object
    {
        $out = (object)[];
        
        foreach ($in as $key => $value)
        {
            $out->{$value} = $key;
        }
        
        return $out;
    }
}