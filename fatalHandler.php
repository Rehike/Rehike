<?php

register_shutdown_function("fatalHandler");

// wow this code really fucking sucks

function fatalHandler() {
   global $buildFatalPage;
   $e = error_get_last();
   if ($e !== null && ($e['type'] == E_ERROR || $e['type'] == E_CORE_ERROR)) {
      ob_end_clean(); ob_start(); // wipe output buffer and restart
      
      try {
         $buildFatalPage($e);
      } catch (Error $err) {
         try {
            $buildFatalPage($e, true);
         } catch (Error $err) {
            echo 'Fatal error trying to catch fatal error trying to catch fatal error. Fuck.';
         }
      }
      
      ob_end_flush();
   }
}

$buildFatalPage = function ($e, $simple = false) use (&$twig, $yt) {
   /*
   if (!$simple) {
      $twig = new \Twig\Environment(
         new \Twig\Loader\FilesystemLoader(
            $_SERVER['DOCUMENT_ROOT'] . '/template/hitchhiker'
         )
      );
   }
   // */
   
   $errInfo = (object) [];
   $errInfo->type = $e['type'] ?? E_CORE_ERROR;
   $errInfo->file = $e['file'] ?? '(unknown file)';
   $errInfo->line = $e['line'] ?? 0;
   $errInfo->message = $e['message'] ?? '(no message)';
   $errInfo->messagePreview = fatalPreviewify($errInfo->message);
   
   if (!$simple) {
      //$twig->addGlobal('errInfo', $errInfo);
      $yt->errInfo = $errInfo;
      $twig->addGlobal('yt', $yt);
      echo $twig->render('fatal.twig', [$yt]);
   } else {
      header('Content-Type: application/json');
      echo json_encode($errInfo);
   }
};

function fatalPreviewify($msg) {
   $response = "";
   if (substr($msg, 0, 19) == "Uncaught Twig\Error") {
      return simplifyTwigError($msg);
   } else {
      $msg = 'Fatal error: ' . $msg;
      if (strlen($msg) > 40) {
         return substr($msg, 0, 37) . '...';
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