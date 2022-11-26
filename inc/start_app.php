<?php
   /*Object Instantiation*/
   ob_start();
   date_default_timezone_set("Africa/Accra");

   use SannyTech\DB as Database;
   use SannyTech\Session;

   $db = new Database();
   $session = new Session();