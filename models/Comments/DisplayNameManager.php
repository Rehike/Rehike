<?php
namespace Rehike\Model\Comments;

use Rehike\Network;

use Rehike\Async\Promise;
use function Rehike\Async\async;

/**
 * Manager for display names.
 * 
 * @author Aubrey Pankow <aubyomori@gmail.com>
 * @author Isabella Lulamoon <kawapure@gmail.com>
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */
class DisplayNameManager
{
    protected array $remoteData = [];
    
    protected ?object $displayNameMap = null;
    
     /**
     * Supplies a display name map to the comment bakery.
     * 
     * This may be supplied from a custom continuation.
     */
    public function supplyDisplayNameMap(object $displayNameMap): void
    {
        $this->displayNameMap = $displayNameMap;
    }
    
    /**
     * Get the display name of a commenter.
     */
    public function getDisplayName(string $ucid): ?string
    {
        if (DisplayNameCacheManager::has($ucid))
        {
            return DisplayNameCacheManager::get($ucid);
        }
        else if (!is_null($this->displayNameMap) && isset($this->displayNameMap->{$ucid}))
        {
            return $this->displayNameMap->{$ucid};
        }
        else if (isset($this->remoteData[$ucid]))
        {
            return $this->remoteData[$ucid];
        }
        
        return null;
    }
    
    /**
     * Ensures that data is available.
     */
    public function ensureDataAvailable(array $channelIds): Promise/*<void>*/
    {
        return async(function() use ($channelIds) { 
            $remoteCids = $this->filterKnownChannelIds($channelIds);
            
            // Ensure that we need to make a request in the first place. If we
            // already have all UCIDs populated, then we just avoid making any
            // request at all.
            if (empty($remoteCids))
            {
                return new Promise(fn($r) => $r());
            }
            
            // Try to request from data API:
            $dataApiResponse = yield $this->requestFromDataApi($remoteCids);
            
            if (!$dataApiResponse)
            {
                $embedMap = yield $this->requestFromSubscribeButtonEmbed($remoteCids);
                
                foreach ($embedMap as $key => $value)
                {
                    $this->remoteData[$key] = $value;
                }
            }
        });
    }

    /**
     * Populate $dataApiData with channel data.
     * 
     * @param string[] $cids  List of channel IDs to get display names for.
     * 
     * @return Promise<bool> True on success, false on failure
     */
    public function requestFromDataApi(array $cids): Promise/*<bool>*/
    {
        return async(function() use ($cids) {
            $response = yield Network::dataApiRequest(
                action: "channels",
                params: [
                    "part" => "id,snippet",
                    "id" => implode(",", $cids)
                ]
            );
            $data = $response->getJson();
            
            if (!$data)
            {
                return false;
            }
            
            $dataApiData = [];

            if (isset($data->items))
            {
                foreach ($data->items as $item)
                {
                    $dataApiData += [
                        $item->id => $item->snippet
                    ];
                }
            }
            else
            {
                return false;
            }
            
            foreach ($dataApiData as $ucid => $data)
            {
                $this->remoteData[$ucid] = $data->title;
                
                DisplayNameCacheManager::insert(
                    ucid: $ucid,
                    displayName: $data->title
                );
            }
            
            return true;
        });
    }
    
    /**
     * Get the title of the channel from the subscribe button embed page.
     * 
     * This is a very lightweight page to request when the data API is unavailable.
     * 
     * @return Promise<string[]>
     */
    public function requestFromSubscribeButtonEmbed(array $cids): Promise/*<array>*/
    {
        return async(function() use ($cids) {
            $requestPromises = [];
            
            foreach ($cids as $channelId)
            {
                $requestPromises[$channelId] = Network::urlRequestFirstParty(
                    "https://www.youtube.com/subscribe_embed?channelid=$channelId&layout=full"
                );
            }
            
            $responses = yield Promise::all($requestPromises);
            
            $out = [];
            
            foreach ($responses as $ucid => $response)
            {
                $responseText = $response->getText();
                
                $rightOfClass = explode("class=\"yt-username\"", $responseText)[1];
                $rightOfTagEnd = explode(">", $rightOfClass)[1];
                $isolatedDisplayName = explode("<", $rightOfTagEnd)[0];
                
                $out[$ucid] = $isolatedDisplayName;
                
                DisplayNameCacheManager::insert(
                    ucid: $ucid,
                    displayName: $isolatedDisplayName
                );
            }
            
            return $out;
        });
    }
    
    public function filterKnownChannelIds(array $cids): array
    {
        return array_filter($cids, fn($item) => 
            !isset($this->displayNameMap?->{$item}) &&
            !DisplayNameCacheManager::has($item)
        );
    }
    
    /**
     * Creates a display name map from remote data.
     */
    public function createDisplayNameMap(): object
    {
        $out = (object)[];
        
        foreach ($this->remoteData as $ucid => $displayName)
        {
            $out->{$ucid} = $displayName;
        }
        
        return $out;
    }
}