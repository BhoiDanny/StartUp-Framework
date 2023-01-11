<?php

   namespace SannyTech;

   class Cookie
   {
      private mixed $user_id;
      private bool $signedIn = false;
      private mixed $name;

      /**
       * Cookie Constructor
       * @return void
       */
      public function __construct()
      {
         $this->name = Helper::env('COOKIE_NAME');
         $this->checkLogin();
      }

      /**
       * Check if user is logged in
       * @return bool
       */
      public function isSignedIn(): bool
      {
         return $this->signedIn;
      }

      /**
       * Return the user login in
       * @return mixed
       */
      public function user(): mixed
      {
         return $this->user_id;
      }


      /**
       * Set SignIn cookie
       * @param $user
       * @return void
       */
      public function setCookie($user): void
      {
         if($user) {
            $cookie = Helper::encrypt($user->id);
            setcookie($this->name, $cookie, time() + Helper::env('COOKIE_EXPIRY'), '/');
         }
      }

      /**
       * SignIn with cookie
       * @param $user
       * @return void
       */
      public function signIn($user):void
      {
         if($user) {
            $this->user_id = $_SESSION[$this->name] = $user->id;
            $this->signedIn = true;
         }
      }

      /**
       * Check if user is logged in using cookie
       * @return void
       */
      private function checkLogin(): void
      {
         if(Helper::cookie($this->name) !== null) {
            $cookie = Helper::decrypt(Helper::cookie($this->name));
            $this->user_id = $_SESSION['user_id'] = $cookie;
            $this->signedIn = true;
         } else {
            unset($this->user_id);
            $this->signedIn = false;
         }
      }

      /**
       * Log user out using cookie
       * @return void
       */
      public function signOut(): void
      {
         unset($_SESSION['user_id']);
         unset($this->user_id);
         $this->signedIn = false;
         $this->destroyCookie($this->name);
      }

      /**
       * Destroy a cookie
       * @param string $cookieName
       * @return void
       */
      public function destroyCookie(string $cookieName): void
      {
         $this->name = $cookieName;
         if($this->exists()) {
            unset($_COOKIE[$this->name]);
            setcookie($this->name, '', time() - 3600, '/');
         }
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