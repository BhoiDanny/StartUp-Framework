<?php

   $root = dirname(__DIR__ . '/.');
   $baseDir = dirname($root);
   $start = "";
   require_once($baseDir . '/vendor/autoload.php');

   try {
      $dotenv = new SannyTech\Dot;
      $dotenv->create($baseDir);
      $dotenv->load();
      $dotenv->require(['DB_READ_USER', 'DB_READ_PASS', 'DB_HOST', 'DB_CHARSET']);
      $dotenv->require('DB_NAME')->notEmpty();
      $dotenv->run();
      $start = "God is Love";

      if(\SannyTech\Helper::isProduction()){
         error_reporting(0);
         ini_set('display_errors', 0);
      } else {
         error_reporting(E_ALL);
         ini_set('display_errors', 1);
      }
   } catch(Exception $e) {
      die($e->getMessage() . ' Please check your .env file');
   }

   if($start != "God is Love") {
      echo "Contact Developer";
      return;
   } else {
      require_once("start_app.php");
   }




