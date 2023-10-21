<?php
    namespace Rehike\ErrorHandler\FatalErrorTemplate;

    use Rehike\ErrorHandler\ErrorHandler;
    use Rehike\ErrorHandler\ErrorPage\UncaughtExceptionPage;
    use Rehike\ErrorHandler\ErrorPage\InnertubeFailedRequestPage;
    use Rehike\ErrorHandler\ErrorPage\FatalErrorPage;

    use const Rehike\Constants\GH_ENABLED;
    use const Rehike\Constants\GH_REPO;

    include_once "includes/fatal_templates/fatal_template_functions.php";

    $page = ErrorHandler::getErrorPageModel();
?>
<!DOCTYPE html>
<!-- thanks aubrey <33 -->
<html>
    <head>
        <title>Rehike fatal error</title>
        <?php include "fatal_roboto.css.php" ?>
        <?php include "fatal.css.php" ?>
        <?php include "fatal.js.php" ?>
    </head>
    <body>
        <div id="rehike-fatal-error">
            <div class="header">
                <svg xmlns="http://www.w3.org/2000/svg" height="48px" viewBox="0 0 24 24" width="48px" fill="#cc181e">
                    <path d="M0 0h24v24H0z" fill="none" />
                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z" />
                </svg>
                <h1><?= $page->getTitle() ?></h1>
            </div>
            <p>
                A fatal error has occurred and Rehike cannot continue. Sorry for the inconvenience.
                <?php if (true == GH_ENABLED): ?>
                    <div class="github-link">
                        <a href="//github.com/<?= GH_REPO ?>/issues/new">
                            Please submit an issue to the GitHub repository, including the crash log.
                        </a>
                    </div>
                <?php endif ?>

                <div>
                    <h3 class="section-header">Actions</h3>
                    <button class="fatal-button" onclick="window.location.reload()">Refresh page</button>
                    <button class="fatal-button" onclick="fatalDisableRehikeOnce()">Disable Rehike (just this time)</button>
                    <button class="fatal-button" onclick="fatalDisableRehike()">Disable Rehike (persistently)</button>
                </div>

                <?php if ($page instanceof InnertubeFailedRequestPage): ?>
                    <?php $e = $page->getInnertubeFailedException()->failedResponse ?>
                    <h3 class="section-header">
                        Failed request information
                    </h3>
                    <ul class="failed-request-info">
                        <li>
                            <span class="section-title">
                                Response status: 
                            </span>
                            <?= $e->status ?>
                        </li>
                        <li>
                            <span class="section-title">
                                Response content:
                            </span>
                            <div class="failed-request-text">
                                <?= htmlspecialchars($e->getText()) ?>
                            </div>
                        </li>
                        <li>
                            <span class="section-title">
                                Response headers:
                            </span>
                            <ul class="response-headers-list">
                                <?php foreach ($e->headers as $name => $value): ?>
                                    <li>
                                        <?= "$name: $value" ?>
                                    </li>
                                <?php endforeach ?>
                            </ul>
                        </li>
                    </ul>
                <?php endif ?>

                <?php if ($page instanceof UncaughtExceptionPage): ?>
                    <pre class="exception-log"><?= 
                        simpleFormattedStringToHtml($page->getExceptionLog())
                    ?></pre>
                <?php elseif ($page instanceof FatalErrorPage): ?>
                    <ul class="fatal-error-info">
                        <li class="fatal-error-type">
                            <span class="section-title">
                                Error type: 
                            </span>
                            <?= htmlspecialchars($page->getType()) ?>
                        </li>
                        <li class="fatal-error-file">
                            <span class="section-title">
                                File: 
                            </span>
                            <?= htmlspecialchars($page->getFile()) ?>
                        </li>
                        <li class="fatal-error-message">
                            <span class="section-title">
                                Message: 
                            </span>
                            <?php if ($page->hasMessage()): ?>
                                <?= htmlspecialchars($page->getMessage()) ?>
                            <?php else: ?>
                                <span class="no-message">
                                    No message provided.
                                </span>
                            <?php endif ?>
                        </li>
                    </ul>
                <?php endif ?>
                <div>
                    <h3 class="section-header">
                        Message logs
                    </h3>
                    <ul>
                        <?php foreach ($page->getDebugLog() as $msg): ?>
                            <li>
                                <?= htmlspecialchars($msg) ?>
                            </li>
                        <?php endforeach ?>
                    </ul>
                </div>
            </p>
        </div>
    </body>
</html>