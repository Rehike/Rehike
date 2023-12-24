<?php
namespace Rehike\Model\Dialog;

/**
 * Confirmation dialog model.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */
class MConfirmDialog extends MDialog
{
    public string $title;
    public array $dialogMessages = [];
    public object $cancelButton;
    public object $confirmButton;
    public object $confirmEndpoint;
    public bool $primaryIsCancel = false;
    public string $jsWrapperClassName = "yt-dialog";

    public function __construct(
            ?string $title = null,
            ?array $dialogMessages = null,
            ?object $cancelButton = null,
            ?object $confirmButton = null,
            ?bool $primaryIsCancel = false,
            ?string $description = null,
            ?string $jsWrapperClassName = null
    )
    {
        if (!is_null($title))
        {
            $this->title = $title;
        }
        if (!is_null($dialogMessages))
        {
            $this->dialogMessages = $dialogMessages;
        }
        else if (!is_null($description))
        {
            $this->dialogMessages = [
                (object)[
                    "runs" => [
                        (object)[
                            "text" => $description
                        ]
                    ]
                ]
            ];
        }
        if (!is_null($cancelButton))
        {
            $this->cancelButton = $cancelButton;
        }
        if (!is_null($confirmButton))
        {
            $this->confirmButton = $confirmButton;
        }
        if (!is_null($primaryIsCancel))
        {
            $this->primaryIsCancel = $primaryIsCancel;
        }
        if (!is_null($jsWrapperClassName))
        {
            $this->jsWrapperClassName = $jsWrapperClassName;
        }
    }
}