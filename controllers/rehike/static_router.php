<?php
namespace Rehike\Controller\rehike;

class StaticRouter {
    public function get(&$yt, &$template, $request) {
        switch ($request->path[2])
        {
            case "logo.png":
                header("Content-Type: image/png");
                echo file_get_contents("static/version/logo.png");
                exit();
                break;
            case "logo_small_grey.png":
                header("Content-Type: image/png");
                echo file_get_contents("static/version/logo_small_grey.png");
                exit();
                break;
            case "branch_icon.png":
                header("Content-Type: image/png");
                echo file_get_contents("static/version/branch_icon.png");
                exit();
                break;
        }
    }

    public function post(&$yt, &$template, $request) {
        return $this->get($yt, $template, $request);
    }
}

return new StaticRouter();