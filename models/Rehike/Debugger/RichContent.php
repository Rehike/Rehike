<?php
namespace Rehike\Model\Rehike\Debugger;

use \Rehike\i18n;

/**
 * A general trait for rich text content as used by the
 * debugger.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Developers
 */
trait RichContent
{
    public $richDebuggerRenderer = [];

    public function addRichText($type, $text = null, $custom = [])
    {
        $obj = [];
        if (null != $text) $obj["text"] = $text;
        if (!empty($custom)) $obj += $custom;

        $this->richDebuggerRenderer[] = (object)[
            $type => (object)$obj
        ];
    }

    public function addHeading($text)
    {
        $this->addRichText("heading", $text);
    }

    public function addSubheading($text)
    {
        $this->addRichText("subheading", $text);
    }

    public function addText($text)
    {
        $this->addRichText("simpleText", $text);
    }

    public function addCode($text)
    {
        $this->addRichText("code", $text);
    }

    public function addButton($button)
    {
        $this->richDebuggerRenderer[] = (object)[
            "button" => $button
        ];
    }

    public function addError($error)
    {
        $i18n = &i18n::getNamespace("rebug");

        // Get error information from the ID
        switch ($error->errno)
        {
            case E_ERROR:
            case E_USER_ERROR:
                $type = "error";
                $errorTypeText = $i18n->errorError ?? "Error";
                break;
            case E_WARNING:
            case E_USER_WARNING:
                $type = "warning";
                $errorTypeText = $i18n->errorWarning ?? "Warning";
                break;
            case E_DEPRECATED:
            case E_USER_DEPRECATED:
                $type = "deprecated";
                $errorTypeText = $i18n->errorDeprecated ?? "Deprecated";
                break;
            case E_NOTICE:
            case E_USER_NOTICE:
                $type = "notice";
                $errorTypeText = $i18n->errorNotice ?? "Notice";
                break;
        }

        // Get the short filename
        $shortFile = $error->errfile;
        $shortFile = explode("/", str_replace("\\", "/", $shortFile));
        $shortFile = $shortFile[count($shortFile) - 1];

        $this->richDebuggerRenderer[] = (object)[
            "errorRenderer" => (object)[
                "number" => $error->errno,
                "file" => $error->errfile,
                "shortFile" => $shortFile,
                "message" => $error->errstr,
                "line" => $error->errline,
                "type" => $type,
                "errorTypeText" => $errorTypeText
            ]
        ];
    }

    public function addNothingToSee()
    {
        $this->richDebuggerRenderer[] = (object)[
            "nothingToSeeRenderer" => new MNothingToSee()
        ];
    }

    public function addLoading()
    {
        $this->richDebuggerRenderer[] = (object)[
            "loadingRenderer" => true
        ];
    }
}