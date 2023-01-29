<?php

   /**
    * Calls the Base url of the application
    * @param bool $https
    * @return string
    */
   function base_url(bool $https = false): string
   {
      $path = array_filter(explode('/', $_SERVER['REQUEST_URI']));
      $path = array_shift($path);
      return 'http'. ($https ? 's' : '') . '://' . $_SERVER['SERVER_NAME'] . '/' . $path;
   }
