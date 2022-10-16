<?php
namespace Rehike\Util;

use \Rehike\Util\Base64Url;

use \Com\Google\Protos\Youtube\Api\Innertube\BrowseContinuation;
use \Com\Google\Protos\Youtube\Api\Innertube\BrowseContinuationAppendAction;
use \Com\Google\Protos\Youtube\Api\Innertube\BrowseContinuationWrapper;
use \Com\Google\Protos\YouTube\Api\Innertube\ContinuationTypeWrapper;
use \Com\Google\Protos\YouTube\Api\Innertube\ContinuationWrapper;

class WebV2Shelves {
    /**
     * Convert a shelves client continuation to a WEB-compatible one.
     * 
     * The code is quite disgusting for this, however it's a quite simple modification. This digs
     * into the inner layers of this code to change two variables to convert the type, then returns
     * a new valid continuation.
     * 
     * @param string $continuation A continuation from a client with shelves, i.e. ANDROID, IOS, TVHTML5.
     * @return string A web continuation.
     */
    public static function continuationToWeb(string $continuation): string {
        $decoded = Base64Url::decode($continuation);
        $contWrapper = new ContinuationWrapper();
        $contTypeWrapper = new ContinuationTypeWrapper();
        $browseCont = new BrowseContinuation();

        // Unwrap in order to modify the class.
        $contWrapper -> mergeFromString($decoded);
        $innerWrapper = $contWrapper -> getBrowseContinuation();
        
        $rawTypeWrapper = Base64Url::decode($innerWrapper -> getEncodedAction());
        $contTypeWrapper -> mergeFromString($rawTypeWrapper);

        $rawBrowseContinuation = Base64Url::decode($contTypeWrapper -> getReloadContinuation());
        $browseCont -> mergeFromString($rawBrowseContinuation);

        // Now work in reverse to form a new continuation token.
        $browseCont->clearReloadAction();
        $browseCont->setAppendAction(new BrowseContinuationAppendAction(["a" => 0]));
        $newBrowseContinuation = Base64Url::encode($browseCont->serializeToString());

        $ctwBuilder = new ContinuationTypeWrapper();
        $ctwBuilder -> setAppendContinuation($newBrowseContinuation);
        $newTypeWrapper = Base64Url::encode($ctwBuilder -> serializeToString());

        $bcwBuilder = new BrowseContinuationWrapper();
        $bcwBuilder -> setBrowseId("FEwhat_to_watch");
        $bcwBuilder -> setEncodedAction($newTypeWrapper);
        $bcwBuilder -> setTargetId("browse-feedFEwhat_to_watch");
        $newInnerWrapper = $bcwBuilder -> serializeToString();

        $cwBuilder = new ContinuationWrapper();
        $cwBuilder -> setBrowseContinuation($bcwBuilder);
        $newOuterWrapper = Base64Url::encode($cwBuilder -> serializeToString());

        return $newOuterWrapper;
    }
}