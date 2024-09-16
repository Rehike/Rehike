<?php
namespace Rehike\Model\Guide;

use Rehike\Network;
use Rehike\FileSystem;
use Rehike\SignInV2\SignIn;

// Async imports:
use function Rehike\Async\async;
use Rehike\Async\Promise;

/**
 * Manages the playlist items that appear in the guide when signed in.
 *
 * @author Isabella <kawapure@gmail.com>
 * @author The Rehike Maintainers
 */
class GuidePlaylistsManager
{
    const CACHE_FILE = "guide_playlists.json";
    const EXPIRE_TIME = 18000; // 5 hours

    /**
     * Gets all playlists on the guide.
     *
     * This attempts to read from the cache first for performance reasons, and if the cache
     * has expired, then we attempt to retrieve fresh data from InnerTube.
     *
     * @return Promise<object[]>
     */
    public function getPlaylists(): Promise/*<array>*/
    {
        $cacheFilePath = "cache/" . self::CACHE_FILE;
        $cacheExists = FileSystem::fileExists($cacheFilePath);

        if ($cacheExists)
        {
            // If the cache file exists, then we can just read the result from it.
            $cacheContents = FileSystem::getFileContents($cacheFilePath);
            $cacheObject = json_decode($cacheContents);
            $validation = $this->validateCache($cacheObject);

            if ($validation->isValid)
            {
                // Return an automatically-resolving promise with the items.
                return new Promise(fn($r) => $r($validation->items));
            }
        }

        return async(function() use ($cacheFilePath) {
            // Request fresh data from InnerTube.
            $freshData = yield $this->requestFromInnertube();

            // Build the cache object.
            $cacheObject = $this->buildCache($freshData);

            // Write to the cache file.
            FileSystem::writeFile($cacheFilePath, json_encode($cacheObject));

            // Return what we already have in memory.
            return $freshData;
        });
    }

    /**
     * Request user playlists from InnerTube and get the processed version.
     *
     * @return Promise<object[]>
     */
    protected function requestFromInnertube(): Promise/*<array>*/
    {
        return async(function() {
            $response = yield Network::innertubeRequest(
                action: "browse",
                body: [
                    "browseId" => "FEplaylist_aggregation"
                ]
            );

            $data = $response->getJson();

            // Find the guide items.
            if (isset($data->contents->twoColumnBrowseResultsRenderer->tabs[0]->tabRenderer->content
                    ->richGridRenderer->contents
            ))
            {
                $items = $data->contents->twoColumnBrowseResultsRenderer->tabs[0]->tabRenderer->content
                    ->richGridRenderer->contents;
            }

            return $this->processInnertubeData($items);
        });
    }

    /**
     * Processes the playlists feed response from InnerTube and normalizes the
     * result.
     */
    protected function processInnertubeData(array $baseItems): array
    {
        $resultItems =  [];

        foreach ($baseItems as $item)
        {
            if (isset($item->richItemRenderer->content->lockupViewModel))
            {
                $model = $item->richItemRenderer->content->lockupViewModel;
                $mdModel = $model->metadata->lockupMetadataViewModel;

                // Create a new item.
                $curItem = (object)[];

                // All guide playlist items have at least a title and a playlist ID for
                // generating the link.
                $curItem->playlistId = $model->contentId;
                $curItem->title = $mdModel->title->content;

                // Saved playlists have an author attribution, so we replicate this.
                if (isset($mdModel->metadata->contentMetadataViewModel->metadataRows))
                {
                    $firstMdRow = $mdModel->metadata->contentMetadataViewModel->metadataRows[0];

                    if (isset($firstMdRow->metadataParts[0]->text->commandRuns))
                    {
                        $curItem->authorName = $firstMdRow->metadataParts[0]->text->content;
                    }
                }

                // Push this new item to the results:
                $resultItems[] = $curItem;
            }
        }

        return $resultItems;
    }

    /**
     * Builds the cache for multi-user support.
     */
    protected function buildCache(array $items): object
    {
        $accountId = SignIn::getSessionInfo()->getCurrentGoogleAccount()->getGaiaId();
        $expire = time() + self::EXPIRE_TIME;

        return (object)[
            "accountId" => $accountId,
            "expire" => $expire,
            "items" => $items
        ];
    }

    /**
     * Performs cache validation functions.
     */
    protected function validateCache(mixed $cacheObj): object
    {
        // All of these properties must exist on the cache object.
        if (is_object($cacheObj) && isset($cacheObj->expire) && isset($cacheObj->accountId) && isset($cacheObj->items))
        {
            // The current time must be less than the expire time.
            if (time() > $cacheObj->expire)
            {
                // The account ID must match, meaning the user has not switched accounts.
                $currentGaiaId = SignIn::getSessionInfo()->getCurrentGoogleAccount()->getGaiaId();
                if ($currentGaiaId == $cacheObj->accountId)
                {
                    return (object)[
                        "isValid" => true,
                        "items" => $cacheObj->items
                    ];
                }
            }
        }

        return (object)[
            "isValid" => false
        ];
    }
}