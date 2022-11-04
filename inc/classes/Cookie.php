<?php

   namespace SannyTech;

   class Cookie
   {
      public mixed $value;
      public string $name;
      public int $expiry;
      public string $path;
      public string $domain;
      public bool $secure;
      public bool $httpOnly;

      /**
       * Cookie Constructor
       * @param $name
       * @param string $value
       * @param int $expiry
       * @param string $path
       * @param string $domain
       * @param bool $secure
       * @param bool $httpOnly
       * @return void
       */
      public function __construct(
         $name,
         string $value = '',
         int $expiry = 0,
         string $path = '',
         string $domain = '',
         bool $secure = false,
         bool $httpOnly = true,
      )
      {
         $this->name = $name;
         $this->value = $value;
         $this->expiry = $expiry;
         $this->path = $path;
         $this->domain = $domain;
         $this->secure = $secure;
         $this->httpOnly = $httpOnly;

      }

      /**
       * Set a cookie
       * @return void
       */
      public function set(): void
      {
         setcookie(
            $this->name,
            $this->value,
            $this->expiry,
            $this->path,
            $this->domain,
            $this->secure,
            $this->httpOnly
         );
      }

      /**
       * Get a cookie
       * @return mixed
       */
      public function get(): mixed
      {
         return $_COOKIE[$this->name];
      }

      /**
       * Delete a cookie
       * @return void
       */
      public function delete(): void
      {
         $this->expiry = time() - 3600;
         $this->set();
      }

      /**
       * Check if a cookie exists
       * @return bool
       */
      public function exists(): bool
      {
         return isset($_COOKIE[$this->name]);
      }

      /**
       * Check if a cookie is empty
       * @return bool
       */
      public function isEmpty(): bool
      {
         return empty($_COOKIE[$this->name]);
      }



   }