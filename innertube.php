<?php

namespace Innertube;

use YtSigninCore;

class Context {
   public object $context;

   public function json(?object $append = null): string {
      return "";
   }

   public function __construct(){

   }
}


class Request {
   private ?YtSigninCore $sc;

   // TODO: experiment with googleapis instead of youtube domain
   // for localhost forwarding without proxy software
   public const apiURI = 'https://www.youtube.com/youtubei/v1/'; // please maintain the leading slash
   public const apiKey = 'AIzaSyAO_FJ2SlqU8Q4STEHLGCilw_Y9_11qcW8';
   public string $api;
   public Context $context;
   public object $params;
   public object $cookies;
   public bool $continuative;

   public static function getCookies(): array {
      if (isset($_SERVER['COOKIE'])) {
         return $_SERVER['COOKIE'];
      } else {
         return [];
      }
   }

   public static function jsonDecodeError(): object {
      return (object) [
         'error' => 'InnerTube response was not valid JSON.'
      ];
   }

   public static function jsonDecode(mixed $response): object {
      try {
         return json_decode($response);
      } catch (\Exception $e) {
         return self::jsonDecodeError();
      }
   }

   public function request(): object {
      $ch = curl_init( $this->getUrl() );

      curl_setopt_array($ch, [
         CURLOPT_HTTPHEADER => [
            'Cookie: ' . $this->cookies
         ],
         CURLOPT_POST => true,
         CURLOPT_RETURNTRANSFER => true
      ]);

      $response = curl_exec($ch);

      return self::jsonDecode($response);
   }

   public function continue(string $continuation): object {
      $this->params = (object) ['continuation' => $continuation];
      return $this->request();
   }

   public function getBody(object $body): string {
      return $this->context->json($body);
   }

   public function getUrl(): string {
      return self::apiURI . $this->api . '?key=' . self::apiKey;
   }

   public function __construct(?YtSigninCore $sc = null, string $api, object $context, object $params) {
      $this->sc = $sc;
      $this->api = $api;
      $this->params = $params;
      $this->cookies = self::getCookies();
      $this->context = new Context($context);

      $this->continuative = isset($this->params->continuation);

      if ($this->continuative) {
         return $this->continue( $this->params->continuation );
      } else {
         return $this->request();
      }
   }
}


class Environment {
   private ?YtSigninCore $sc;

   public function request(...$args): Request {
      return new Request($this->sc, ...$args);
   }

   public function __construct(?YtSigninCore $sc = null) {
      $this->sc = $sc;
   }
}