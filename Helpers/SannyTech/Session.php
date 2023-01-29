<?php /** @noinspection PhpIllegalPsrClassPathInspection */

   namespace SannyTech;

   class Session
   {
      private mixed $user_id;
      private mixed $admin_id;
      public string $message;
      private bool $signedIn = false;
      private bool $aSignedIn = false;
      private mixed $savedId;
      private mixed $csrfToken;

      /**Session Constructor
       * @return void
       */
      public function __construct()
      {
         session_name(Helper::env('SESSION_NAME'));
         session_start();
         $this->checkLogin();
         $this->checkALogin();
         $this->checkMessage();
         $this->checkLifetime();
         $this->checkSavedId();
         $this->checkCsrfToken();
      }

      /**
       * Get Session by param
       * @param $param
       * @return mixed
       */
      public function get($param): mixed
      {
         return $_SESSION[$param];
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
       * Sign a user in
       * @param $user
       * @return void
       */
      public function signIn($user): void
      {
         if($user) {
            $this->user_id = $_SESSION['user_id'] = $user->id;
            $this->signedIn = true;
            $this->setTime();
         }
      }

      /**
       * Sign a user out
       * @return void
       */
      public function signOut(): void
      {
         unset($_SESSION['user_id']);
         unset($this->user_id);
         $this->signedIn = false;
      }

      /**
       * Check if user is logged in
       * @return void
       */
      private function checkLogin(): void
      {
         if(isset($_SESSION['user_id'])) {
            $this->user_id = $_SESSION['user_id'];
            $this->signedIn = true;
         } else {
            unset($this->user_id);
            $this->signedIn = false;
         }
      }

      /**
       * Set Session time
       * @return void
       */
      private function setTime(): void
      {
         $_SESSION['time'] = time();
      }

      /**
       * Get Session time
       * @return mixed
       */
      private function getTime(): mixed
      {
         return $_SESSION['time'] ?? false;
      }

      /**
       * Check if time is expired
       * @return bool
       */
      public function isExpired(): bool
      {
         if($this->getTime() + Helper::env('SESSION_LIFETIME') < time()) {
            return true;
         } else {
            return false;
         }
      }

      /**
       * Check Session lifetime
       * @return void
       */
      public function checkLifetime(): void
      {
         if($this->isExpired()) {
            $this->signOut();
            $this->aSignOut();
         }
      }

      /**
       * Set a message
       * @param string $msg
       * @return string
       */
      public function message(string $msg = ""): string
      {
         if(!empty($msg)) {
            return $_SESSION['message'] = $msg;
         } else {
            return $this->message;
         }
      }

      /**
       * Set Message
       * @param string $message
       * @return void
       */
      public function setMessage(string $message): void
      {
         $this->message = $_SESSION['message'] = $message;
      }

      /**
       * Check if a message is set
       * @return void
       */
      private function checkMessage(): void
      {
         if(isset($_SESSION['message'])) {
            $this->message = $_SESSION['message'];
            unset($_SESSION['message']);
         } else {
            $this->message = "";
         }
      }

      /**
       * Save an id
       * @param string $id
       * @return string
       */
      public function saveId(string $id=""): string
      {
         if(!empty($id)) {
            return $_SESSION['saveId'] = $id;
         } else {
            return $this->savedId;
         }
      }

      /**
       * Check if an id is saved
       * @return void
       */
      private function checkSavedId(): void
      {
         if(isset($_SESSION['saveId'])) {
            $this->savedId = $_SESSION['saveId'];
            unset($_SESSION['saveId']);
         } else {
            $this->savedId = "";
         }
      }

      /*New Methods*/

      /**
       * CSRF Token
       * @return string
       */
      #grepper How to create a CSRF token in PHP in a class
      public function csrfToken(): string
      {
         if(!isset($_SESSION['csrfToken']) || !empty($_SESSION['csrfToken'])) {
            try {
               $this->csrfToken = $_SESSION['csrfToken'] = bin2hex(random_bytes(32));
            } catch (\Exception $e) {
               echo $e->getMessage();
            }
         }
         return $this->csrfToken;
      }
      #end grepper

      /**
       * Check CSRF Token
       * @return void
       */
      private function checkCsrfToken(): void
      {
         if(isset($_SESSION['csrfToken']) && !empty($_SESSION['csrfToken'])) {
            $this->csrfToken = $_SESSION['csrfToken'];
         } else {
            $this->csrfToken = "";
         }
      }

      /**
       * Check if CSRF Token is valid
       * @param string $token
       * @return bool
       */
      public function isValidCsrfToken(string $token): bool
      {
         if($token === $this->csrfToken) {
            return true;
         } else {
            return false;
         }
      }

      /**
       * Destroy csrf token
       * @return void
       */
      public function destroyCsrfToken(): void
      {
         unset($_SESSION['csrfToken']);
         unset($this->csrfToken);
      }

      /**
       * Destroy Session
       * @return void
       */
      public function destroy(): void
      {
         session_destroy();
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
       * Sign a admin in
       * @param $admin
       * @return void
       */
      public function aSignIn($admin): void
      {
         if($admin) {
            $this->admin_id = $_SESSION['admin_id'] = $admin->id;
            $this->aSignedIn = true;
            $this->setTime();
         }
      }

      /**
       * Sign a admin out
       * @return void
       */
      public function aSignOut(): void
      {
         unset($_SESSION['admin_id']);
         unset($this->admin_id);
         $this->aSignedIn = false;
      }

      /**
       * Check if admin is logged in
       */
      private function checkALogin(): void
      {
         if(isset($_SESSION['admin_id'])) {
            $this->admin_id = $_SESSION['admin_id'];
            $this->aSignedIn = true;
         } else {
            unset($this->admin_id);
            $this->aSignedIn = false;
         }
      }


   }