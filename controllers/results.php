<?php
namespace Rehike\Controller;

use Rehike\Controller\core\NirvanaController;
use Rehike\Model\Results\ResultsModel;

use \Com\YouTube\Innertube\Request\SearchRequestParams;

use Rehike\Request;
use Rehike\i18n;
use Rehike\Util\Base64Url;

class ResultsController extends NirvanaController {
    public $template = "results";

    public static $query;
    public static $param;

    public function onGet(&$yt, $request) {
        // invalid request redirect
        if (!isset($_GET["search_query"])) {
            header("Location: /");
            die();
        }
        
        $this -> useJsModule("www/results");

        $i18n = &i18n::newNamespace("results");
        $i18n->registerFromFolder("i18n/results");
        
        $yt -> masthead -> searchbox -> query = $_GET["search_query"] ?? null;
        self::$query = &$yt -> masthead -> searchbox -> query;
        // used for filters
        $yt -> params = $_GET["sp"] ?? null;
        self::$param = &$yt -> params;

        $resultsIndex = self::getPaginatorIndex($yt->params);

        $response = Request::innertubeRequest("search", (object) [
            "query" => self::$query,
            "params" => $yt -> params
        ]);
        $ytdata = json_decode($response);

        $resultsCount = ResultsModel::getResultsCount($ytdata);

        $paginatorInfo = self::getPaginatorInfo($resultsCount, $resultsIndex);

        $yt -> page = ResultsModel::bake($ytdata, $paginatorInfo, self::$query);
    }

    public static function getPaginatorIndex($sp) {
        if ($sp == null) {
            return 0;
        } else {
            try {
                $parsed = new SearchRequestParams();
                $parsed->mergeFromString(
                    Base64Url::decode($sp)
                );

                if ($parsed->hasIndex()) {
                    $index = $parsed->getIndex();
                } else {
                    $index = 0;
                }

                return $index;
            } catch (\Throwable $e) {
                return 0;
            }
        }
    }

    public static function getPaginatorInfo($resultsCount, $index) {
        // youtube is 20 results/page
        $rpp = 20;
        $pageNo = ceil($index / $rpp) + 1;
        $pagesCount = ceil($resultsCount / $rpp);

        return (object) [
            "resultsPerPage" => $rpp,
            "pageNumber" => $pageNo,
            "pagesCount" => $pagesCount
        ];
    }

    public static function getPageParam($sp = null, $page = 1) {
        $parsed = new SearchRequestParams();

        if ($sp == null) {
            $parsed->setIndex(($page - 1) * 20);
            $parsed->setSomething("");
        } else {
            try {
                $parsed->mergeFromString(Base64Url::decode($sp));
            } catch (\Throwable $e) {}
            $parsed->setIndex(($page - 1) * 20);
        }

        return str_replace(
            ["+","/","="],
            ["-","_","%3D"],
            base64_encode($parsed->serializeToString())
        );
    }

    public static function getPageParamUrl($sp = null, $page = 1) {
        $query = urlencode(self::$query);
        $param = self::getPageParam($sp, $page);

        return "/results?search_query=$query&sp=$param";
    }
};

return new ResultsController;