<?php
namespace Rehike\Model\Rehike\Debugger;

use \Rehike\i18n\i18n;
use \Rehike\Debugger\ErrorWrapper;
use \Rehike\Model\Common\MButton;
use \Rehike\FileSystem;

/**
 * A general trait for rich text content as used by the
 * debugger.
 * 
 * @author The Rehike Developers
 */
trait RichContent
{
    /**
     * Stores rich content, which is a versatile way to implement debugger
     * content.
     * 
     * @param object[]
     */
    public $richDebuggerRenderer = [];

    /**
     * Add a rich text wrapper that can be typed.
     * 
     * @param string  $type   Type of the text to be tested upon.
     * @param ?string $text   Text to assign.
     * @param array   $custom Associative array of custom attributes.
     * 
     * @return void
     */
    public function addRichText($type, $text = null, $custom = [])
    {
        $obj = [];
        if (null != $text) $obj["text"] = $text;
        if (!empty($custom)) $obj += $custom;

        $this->richDebuggerRenderer[] = (object)[
            $type => (object)$obj
        ];
    }

    /**
     * Add a heading to the renderer.
     * 
     * @param string $text
     * @return void
     */
    public function addHeading($text)
    {
        $this->addRichText("heading", $text);
    }

    /**
     * Add a subheading to the renderer.
     * 
     * @param string $text
     * @return void
     */
    public function addSubheading($text)
    {
        $this->addRichText("subheading", $text);
    }

    /**
     * Add a generic text renderer to the renderer.
     * 
     * @param string $text
     * @return void
     */
    public function addText($text)
    {
        $this->addRichText("simpleText", $text);
    }

    /**
     * Add a generic code renderer to the renderer.
     * 
     * @param string $text
     * @return void
     */
    public function addCode($text)
    {
        $this->addRichText("code", $text);
    }

    /**
     * Add a button to the renderer.
     * 
     * @param MButton $button
     * @return void
     */
    public function addButton($button)
    {
        $this->richDebuggerRenderer[] = (object)[
            "button" => $button
        ];
    }

    /**
     * Add an error renderer to the rich content array.
     * 
     * @param ErrorWrapper $error
     * @return void
     */
    public function addError($error)
    {
        $i18n = i18n::getNamespace("rehike/debugger");

        // Get error information from the ID
        switch ($error->errno)
        {
            case E_ERROR:
            case E_USER_ERROR:
                $type = "error";
                $errorTypeText = $i18n->get("errorError");
                break;
            case E_WARNING:
            case E_USER_WARNING:
                $type = "warning";
                $errorTypeText = $i18n->get("errorWarning");
                break;
            case E_DEPRECATED:
            case E_USER_DEPRECATED:
                $type = "deprecated";
                $errorTypeText = $i18n->get("errorDeprecated");
                break;
            case E_NOTICE:
            case E_USER_NOTICE:
                $type = "notice";
                $errorTypeText = $i18n->get("errorNotice");
                break;
        }

        // Get relative rehike:// path
        $longFile = FileSystem::getRehikeRelativePath(
            $error->errfile
        );

        // Get the short filename
        $shortFile = $error->errfile;
        $shortFile = explode("/", str_replace("\\", "/", $shortFile));
        $shortFile = $shortFile[count($shortFile) - 1];

        $this->richDebuggerRenderer[] = (object)[
            "errorRenderer" => (object)[
                "number" => $error->errno,
                "file" => $longFile,
                "shortFile" => $shortFile,
                "message" => $error->errstr,
                "line" => $error->errline,
                "type" => $type,
                "errorTypeText" => $errorTypeText
            ]
        ];
    }

    /**
     * Add a nothing to see placeholder to the renderer.
     * 
     * @return void
     */
    public function addNothingToSee()
    {
        $this->richDebuggerRenderer[] = (object)[
            "nothingToSeeRenderer" => new MNothingToSee()
        ];
    }

    /**
     * Add a loading placeholder to the renderer.
     * 
     * @return void
     */
    public function addLoading()
    {
        $this->richDebuggerRenderer[] = (object)[
            "loadingRenderer" => true
        ];
    }
}