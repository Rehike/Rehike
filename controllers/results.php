<?php
use Rehike\Controller\core\NirvanaController;
use Rehike\Model\Results\ResultsModel;

use \Com\YouTube\Innertube\Request\SearchRequestParams;

use Rehike\Request;
use Rehike\i18n;

return new class extends NirvanaController {
    public $template = "results";

    public function onGet(&$yt, $request) {
        // invalid request redirect
        if (!isset($_GET['search_query'])) {
            header('Location: /');
            die();
        }
        
        $this -> useJsModule("www/results");
        // Remove when guide implemented into NirvanaController base.
        include "controllers/mixins/guideNotSpfMixin.php";

        $i18n = &i18n::newNamespace("results");
        $i18n->registerFromFolder("i18n/results");
        
        $yt -> query = $_GET["search_query"] ?? null;
        // used for filters
        $yt -> params = $_GET["sp"] ?? null;
        $yt -> pageNo = $_GET["page"] ?? 1;

        $response = Request::innertubeRequest("search", (object) [
            "query" => $yt -> query,
            "params" => $yt -> params
        ]);
        $ytdata = json_decode($response);

        $yt -> page = ResultsModel::bake($yt, $ytdata);
    }
};