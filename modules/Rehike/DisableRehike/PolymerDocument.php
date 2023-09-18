<?php
namespace Rehike\DisableRehike;

use Rehike\Async\Promise;
use Rehike\Exception\FileSystem\FsFileDoesNotExistException;
use Rehike\Exception\FileSystem\FsFileReadFailureException;
use Rehike\SimpleFunnel;
use Rehike\FileSystem;
use Rehike\i18n;
use function Rehike\Async\async;

use YukisCoffee\CoffeeRequest\Network\Response;
use YukisCoffee\CoffeeRequest\Network\ResponseHeaders;

/**
 * Responsible for requesting and modifying the Polymer document to inject the
 * custom script.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */
class PolymerDocument
{
    public function __construct(
        public string $response,
        public ResponseHeaders $headers,
        public int $status
    ) {}

    /**
     * Retrieves the Polymer document from the server.
     */
    public static function getPolymerDocument(): Promise/*<self>*/
    {
        return async(function () {
            $polymerResult = yield SimpleFunnel::funnelCurrentPage();
            $resultText = $polymerResult->getText();
            $document = $resultText;

            if (0 === strpos($polymerResult->headers->contentType, "text/html"))
            {
                try
                {
                    $script = FileSystem::getFileContents("modules/Rehike/DisableRehike/polymer_script.js");
                    $nonce = self::findScriptNonce($resultText);

                    $firstScriptIndex = strpos($resultText, "<script");

                    $document = substr($resultText, 0, $firstScriptIndex) .
                        "<script nonce=\"$nonce\">" .
                            str_replace(
                                "PREPROCESSOR_DISABLE_POLYMER_CONFIG",
                                self::getDisablePolymerJsConfig(),
                                $script
                            ) .
                        "</script>" .
                        substr($resultText, $firstScriptIndex);
                }
                catch (FsFileDoesNotExistException $e)
                {
                    // Swallow exception, should just not modify document.
                }
                catch (FsFileReadFailureException $e)
                {
                    // Swallow exception, should just not modify document.
                }
            }

            return new self(
                response: $document, 
                headers: $polymerResult->headers,
                status: $polymerResult->status
            );
        });
    }

    /**
     * Finds the script nonce for use in the custom script tag.
     * 
     * I'm not actually sure if this was required, but I decided to do it just
     * in case.
     */
    private static function findScriptNonce(string $text): ?string
    {
        $nonceIndex = preg_match("/nonce=\"([A-Za-z0-9-_]+)\"/", $text, $matches);
        $nonce = $matches[1];

        return $nonce ?? null;
    }

    /**
     * Gets the JS config in JSON format (a JS object).
     */
    private static function getDisablePolymerJsConfig(): string
    {
        if (!i18n::namespaceExists("disable_rehike"))
        {
            DisableRehike::initI18n();
        }

        $i18n = i18n::getNamespace("disable_rehike");
        $strings = $i18n->getStrings()[$i18n->getLanguage()];

        return json_encode((object)[
            "strings" => $strings
        ]);
    }
}