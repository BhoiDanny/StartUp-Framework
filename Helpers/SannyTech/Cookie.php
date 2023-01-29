<?php /** @noinspection PhpIllegalPsrClassPathInspection */

   namespace SannyTech;

   class Cookie
   {
      private mixed $user_id;
      private mixed $admin_id;
      private bool $signedIn = false;
      private bool $aSignedIn = false;
      private mixed $name;
      private mixed $aName;

      /**
       * Cookie Constructor
       * @return void
       */
      public function __construct()
      {
         $this->name = Helper::env('COOKIE_NAME');
         $this->aName = Helper::env('ADMIN_COOKIE_NAME');
         $this->checkLogin();
         $this->checkAdminLogin();
      }

      /**
       * Get Cookie by param
       * @param $param
       * @return mixed
       */
      public function get($param): mixed
      {
         return $_COOKIE[$param];
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

      /*Admin Section*/

      /**
       * Check if admin is logged in
       * @return bool
       */
      public function isASignedIn(): bool
      {
         return $this->aSignedIn;
      }

      /**
       * Return the admin login in
       * @return mixed
       */
      public function admin(): mixed
      {
         return $this->admin_id;
      }


      /**
       * Set Admin SignIn cookie
       * @param $user
       * @return void
       */
      public function setACookie($user): void
      {
         if($user) {
            $cookie = Helper::encrypt($user->id);
            setcookie($this->aName, $cookie, time() + Helper::env('COOKIE_EXPIRY'), '/');
         }
      }

      /**
       * Set SignIn cookie for admin
       * @param $admin
       * @return void
       */
      public function aSignIn($admin):void
      {
         if($admin) {
            $this->admin_id = $_SESSION[$this->name] = $admin->id;
            $this->aSignedIn = true;
         }
      }



      /**
       * Check if admin is logged in using cookie
       * @return void
       */
      private function checkAdminLogin(): void
      {
         if(Helper::cookie($this->aName) !== null) {
            $cookie = Helper::decrypt(Helper::cookie($this->aName));
            $this->admin_id = $_SESSION['admin_id'] = $cookie;
            $this->aSignedIn = true;
         } else {
            unset($this->admin_id);
            $this->aSignedIn = false;
         }
      }

      /**
       * Log admin out using cookie
       * @return void
       */
      public function aSignOut(): void
      {
         unset($_SESSION['admin_id']);
         unset($this->admin_id);
         $this->aSignedIn = false;
         $this->destroyCookie($this->aName);
      }

      /**
       * Destroy a cookie
       * @param string $cookieName
       * @return void
       */
      public function aDestroyCookie(string $cookieName): void
      {
         $this->aName = $cookieName;
         if($this->exists()) {
            unset($_COOKIE[$this->aName]);
            setcookie($this->aName, '', time() - 3600, '/');
         }
      }



   }