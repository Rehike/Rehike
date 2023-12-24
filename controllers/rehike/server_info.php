<?php
namespace Rehike\Controller\rehike;

use Rehike\YtApp;
use Rehike\ControllerV2\RequestMetadata;

return new class
{
    public function get(YtApp $yt, string &$template, RequestMetadata $request)
    {
        phpinfo();
    }

    public function post(YtApp &$yt, string &$template, RequestMetadata $request)
    {
        return $this->get($yt, $template, $request);
    }
};