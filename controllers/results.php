<?php
namespace Rehike\Controller;

use Rehike\YtApp;
use Rehike\ControllerV2\RequestMetadata;

use Rehike\Controller\core\NirvanaController;
use Rehike\Model\Results\ResultsModel;

use \Com\Youtube\Innertube\Request\SearchRequestParams;

use Rehike\Network;
use Rehike\Util\Base64Url;
use function Rehike\Async\async;

/**
 * Controller for the results (search) page.
 * 
 * This handles the base logic for directing to the search page, including
 * pagination, which doesn't exist in any other client but is still supported
 * by the InnerTube API.
 * 
 * @author Aubrey Pankow <aubyomori@gmail.com>
 * @author Daylin Cooper <dcoop2004@gmail.com>
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */
class ResultsController extends NirvanaController {
    public string $template = "results";

    // No clue why these are static.
    public static ?string $query;
    public static ?string $param;

    public function onGet(YtApp $yt, RequestMetadata $request): void
    {
        async(function() use ($yt, $request) {
            // invalid request redirect
            if (!isset($_GET["search_query"]))
            {
                header("Location: /");
                die();
            }
            
            // Seemingly unused on the client-side (?), but this should still be
            // declared regardless.
            $this->useJsModule("www/results");
            
            // Setup search query internally
            $query = $_GET["search_query"] ?? null;
            self::$query = $query;

            // Display query in the searchbox.
            $yt->masthead->searchbox->query = $query;
            $this->setTitle($query);

            // used for filters
            $yt->params = $_GET["sp"] ?? null;
            self::$param = &$yt->params;

            // Calculates the offset to give the InnerTube server.
            $resultsIndex = self::getPaginatorIndex($yt->params);

            $response = yield Network::innertubeRequest(
                action: "search",
                body: [
                    "query"  => self::$query,
                    "params" => $yt->params
                ]
            );
            $ytdata = $response->getJson();

            // Collect channel UCIDs for data API request (used for video count)
            $ucids = [];

            if (@$contents = $ytdata->contents->twoColumnSearchResultsRenderer->primaryContents->sectionListRenderer->contents)
            foreach ($contents as $data)
            {
                if (isset($data->itemSectionRenderer->contents))
                foreach ($data->itemSectionRenderer->contents as $cdata)
                {
                    if ($ucid = @$cdata->channelRenderer->channelId)
                    {
                        $ucids[] = $ucid;
                    }
                }
            }

            if (count($ucids) > 0)
            {
                $dresponse = yield Network::dataApiRequest(
                    action: "channels",
                    params: [
                        "part" => "id,statistics",
                        "id" => implode(",", $ucids)
                    ]
                );
            }

            $dapidata = [];
            if (isset($dresponse))
            {
                $dapijson = $dresponse->getJson();

                if (@$dapijson->items)
                foreach ($dapijson->items as $item)
                {
                    $dapidata[$item->id] = $item->statistics;
                }
            }
        
            $resultsCount = ResultsModel::getResultsCount($ytdata);
    
            $paginatorInfo = self::getPaginatorInfo(
                $resultsCount, $resultsIndex
            );
    
            $yt->page = ResultsModel::bake(
                data:           $ytdata, 
                paginatorInfo:  $paginatorInfo, 
                query:          self::$query,
                dapidata:       $dapidata
            );
        });
    }

    /**
     * Get the index at which the page starts.
     * 
     * This is *not* the page number. This is the index by which to shift the
     * given results from the start, i.e. an index of 20 would start 20 results
     * after the first result.
     * 
     * @param $sp  Base64-encoded search parameter provided by the YT server.
     */
    public static function getPaginatorIndex(?string $sp): int {
        if ($sp == null)
        {
            return 0;
        }
        else
        {
            try
            {
                $parsed = new SearchRequestParams();
                $parsed->mergeFromString(
                    Base64Url::decode($sp)
                );

                if ($parsed->hasIndex())
                {
                    $index = $parsed->getIndex();
                }
                else
                {
                    $index = 0;
                }

                return $index;
            }
            catch (\Throwable $e)
            {
                return 0;
            }
        }
    }

    /**
     * Get information for the paginator at the bottom of the search page.
     * 
     * @param int $resultsCount  The number of results for the query.
     * @param int $index         Index at which to start the first result.
     */
    public static function getPaginatorInfo(int $resultsCount, int $index): object
    {
        // youtube is 20 results/page
        $resultsPerPage = 20;

        $pageNo = ceil($index / $resultsPerPage) + 1;
        $pagesCount = ceil($resultsCount / $resultsPerPage);

        return (object) [
            "resultsPerPage" => $resultsPerPage,
            "pageNumber" => $pageNo,
            "pagesCount" => $pagesCount
        ];
    }

    /**
     * Get the URL parameter that indicates the search page to the server.
     * 
     * @param string $sp    Standard base64-encoded parameter to be modified.
     * @param int    $page  The page number to encode.
     * 
     * @return string  A modified search parameter that uses the page.
     */
    public static function getPageParam(?string $sp = null, int $page = 1): string
    {
        $parsed = new SearchRequestParams();

        if ($sp == null)
        {
            $parsed->setIndex(($page - 1) * 20);
            $parsed->setSomething("");
        }
        else
        {
            try
            {
                $parsed->mergeFromString(Base64Url::decode($sp));
            }
            catch (\Throwable $e) {} // consume any exception

            $parsed->setIndex(($page - 1) * 20);
        }

        return Base64Url::encode($parsed->serializeToString());
    }

    /**
     * Returns the URL for a page's index.
     * 
     * @param string $sp    Standard base64 encoded parameter to be modified.
     * @param int    $page  The page number to encode.
     * 
     * @return string  URL for that page.
     */
    public static function getPageParamUrl(?string $sp = null, int $page = 1): string
    {
        $query = urlencode(self::$query);
        $param = self::getPageParam($sp, $page);

        return "/results?search_query=$query&sp=$param";
    }
};

return new ResultsController;
