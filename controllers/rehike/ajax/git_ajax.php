<?php
namespace Rehike\Controller\rehike\ajax;

use Rehike\Controller\core\AjaxController;

use Rehike\YtApp;
use Rehike\ControllerV2\RequestMetadata;
use Rehike\TemplateManager;
use Rehike\i18n\i18n;

use Rehike\Model\Dialog\MConfirmDialog;
use Rehike\Model\Common\MButton;

return new class extends AjaxController
{
    public bool $useTemplate = false;

    public function onGet(YtApp $yt, RequestMetadata $request): void 
    {
        $action = self::findAction();

        $result = (object)[];

        switch ($action)
        {
            case "pull":
                $result = $this->gitPull();
                break;
        }

        echo json_encode($result);
    }

    protected function gitPull(): object
    {
        $result = $this->gitExec("git pull --rebase");

        if ($result["code"] == 0)
        {
            return (object)[
                "status" => "SUCCESS"
            ];
        }
        else
        {
            $i18n = i18n::getNamespace("rehike/version");

            $dialogHtml = self::makeDialog(
                $i18n->get("gitManagerDialogFailedPullTitle"),
                $this->getDialogMessage(
                    $i18n->get("gitManagerDialogFailedPullMessage"),
                    $result["err"]
                )
            );

            return (object)[
                "status" => "FAILED",
                "dialogHtml" => $dialogHtml,
                "dialogRid" => $this->yt->page->dialogRid
            ];
        }
    }

    protected function makeDialog(string $title, array $messages): string
    {
        $rid = rand(10000, 99999);

        $i18n = i18n::getNamespace("rehike/version");

        $this->yt->page = (object)[
            "dialogRid" => $rid,
            "dialog" => new MConfirmDialog(
                title: $title,
                dialogMessages: $messages,
                cancelButton: new MButton([
                    "class" => [ "cancel-button" ],
                    "text" => (object)[
                        "simpleText" => $i18n->get("gitManagerDialogDismissButton")
                    ]
                ]),
                confirmButton: new MButton([
                    "class" => [ "confirm-button" ],
                    "style" => "primary",
                    "text" => (object)[
                        "simpleText" => $i18n->get("gitManagerDialogRetryButton")
                    ]
                ])
            )
        ];

        return TemplateManager::render([], "rehike/version/ajax/git_dialog");
    }

    protected function getDialogMessage(string $header, string $result): array
    {
        $output = [];

        $output[] = (object)[
            "runs" => [
                (object)[
                    "text" => $header
                ]
            ]
        ];

        $output[] = (object)[
            "runs" => [
                (object)[
                    "text" => ""
                ]
            ]
        ];

        $output[] = (object)[
            "runs" => [
                (object)[
                    "text" => $result
                ]
            ]
        ];

        \Rehike\YtApp::getInstance()->aaaa = $result;

        return $output;
    }

    /**
     * Executes a Git command.
     */
    protected function gitExec(string $cmd): array
    {
        $workingDir = $_SERVER["DOCUMENT_ROOT"];

        $descriptorSpec = [
            0 => ["pipe", "r"], // stdin
            1 => ["pipe", "w"], // stdout
            2 => ["pipe", "w"]  // stderr
        ];

        $process = proc_open($cmd, $descriptorSpec, $pipes, $workingDir, null);

        $stdout = stream_get_contents($pipes[1]);
        fclose($pipes[1]);

        $stderr = stream_get_contents($pipes[2]);
        fclose($pipes[2]);

        return [
            "code" => proc_close($process),
            "out" => trim($stdout),
            "err" => trim($stderr)
        ];
    }
};