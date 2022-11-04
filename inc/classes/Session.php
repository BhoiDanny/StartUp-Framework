<?php

   namespace SannyTech;

   class Session
   {
      public mixed $user_id;
      public string $message;
      private bool $signedIn = false;
      public mixed $savedId;

      /**Session Constructor
       * @return void
       */
      public function __construct()
      {
         session_name(Helper::env('SESSION_NAME'));
         session_start();
         $this->checkLogin();
         $this->checkMessage();
         $this->checkSavedId();
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
       * Sign a user in
       * @param $user
       * @return void
       */
      public function signIn($user): void
      {
         if($user) {
            $this->user_id = $_SESSION['user_id'] = $user->id;
            $this->signedIn = true;
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



   }