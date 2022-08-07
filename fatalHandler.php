<?php
/**
 * This should be completely redone
 *
 * love taniko
 */

register_shutdown_function("fatalHandler");

// wow this code really fucking sucks

function fatalHandler() {
   global $buildFatalPage;
   $e = error_get_last();
   if ($e !== null && ($e['type'] == E_ERROR || $e['type'] == E_CORE_ERROR)) {
      ob_end_clean(); ob_start(); // wipe output buffer and restart

      try {
         $buildFatalPage($e);
      } catch (Throwable $err) {
         try {
            $buildFatalPage($e, true);
         } catch (Throwable $err) {
            echo 'Fatal error trying to catch fatal error trying to catch fatal error. Fuck.';
            echo "<br><br>";
            echo $e["message"] . " in " . $e["file"] . ":" . $e["line"] . " <b><". $e["type"] ."></b>";
         }
      }

      ob_end_flush();
   }
}

$buildFatalPage = function ($e, $simple = false) use ($yt) {
   $errInfo = (object) [];
   $errInfo->type = $e['type'] ?? E_CORE_ERROR;
   $errInfo->file = $e['file'] ?? '(unknown file)';
   $errInfo->line = $e['line'] ?? 0;
   $errInfo->message = $e['message'] ?? '(no message)';
   $errInfo->messagePreview = fatalPreviewify($errInfo->message);

   if (!$simple) {
      //$twig->addGlobal('errInfo', $errInfo);
      $yt->errInfo = $errInfo;
      echo Rehike\TemplateManager::render([], 'rehike/fatal');
   } else {
      header('Content-Type: text/html');
      echo "<h1>Rehike pre-init error occurred</h1>";
      echo "<h2>Here are the details:</h2>";
      echo "<p>$errInfo->message</p>";
      echo "<h2>Technical details</h2>";
      echo "<pre>";
      echo json_encode($errInfo, JSON_PRETTY_PRINT);
      echo "</pre>";
   }
};

function fatalPreviewify($msg) {
   $response = "";
   if (substr($msg, 0, 19) == "Uncaught Twig\Error") {
      return simplifyTwigError($msg);
   } else {
      $msg = 'Fatal error: ' . $msg;
      $msg = explode("Stack trace", $msg)[0];
      if (strlen($msg) > 90) {
         return substr($msg, 0, 87) . '...';
      } else {
         return $msg;
      }
   }
}

function simplifyTwigError($msg) {
   $response = 'Fatal error (in Twig): ';
   $re = '/(Uncaught Twig\\\\Error\\\\(.*?): )|( \()/';
   preg_match_all($re, $msg, $matches, PREG_OFFSET_CAPTURE);

   $newmsg = substr(
      $msg,
      strlen($matches[0][0][0]),
      $matches[0][1][1] - strlen($matches[0][0][0])
   );

   return $response . ' ' . $newmsg;
}