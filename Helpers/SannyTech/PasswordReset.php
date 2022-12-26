<?php

   namespace SannyTech;


   use Exception;

   class ResetPassword extends App
   {
      public static $dbTable = "";
      public static $dbFields = array('', '', '', '');
      private static $emailColumn = "";
      private static $selector_column = "";
      private static $pwd_expire_column = "";
      public $pwdResetEmail;
      public $pwdResetSelector;
      public $pwdResetToken;
      public $pwdResetExpires;
      public $validator;
      public $hashed_token;

      /**
       * ResetPassword Constructor
       * @return void
       * @throws Exception
       */
      public function __construct()
      {
         $this->pwdResetExpires = date("U") + Helper::env('PASSWORD_RESET_EXPIRY');// minutes
         $this->pwdResetSelector = bin2hex(openssl_random_pseudo_bytes(9));
         $this->pwdResetToken = openssl_random_pseudo_bytes(32);
         $this->validator = bin2hex($this->pwdResetToken);
         $this->hashed_token = password_hash($this->pwdResetToken, PASSWORD_DEFAULT);
      }

      /**
       * Delete Password Reset If Exists
       * @param $email
       * @return bool
       */
      public static function deletePwdReset($email): bool
      {
         global $db;
         $sql = "DELETE FROM " . static::$dbTable ." WHERE " . static::$emailColumn . " = :email";
         $stmt = $db->prepare($sql);
         $stmt->execute([':email' => $email]);
         return $stmt->rowCount() > 0;
      }

      /**
       * Select Email From Users
       * @param $userTable
       * @param $email
       * @return bool
       */
      public static function selectEmail($userTable, $email): bool
      {
         global $db;
         $userTable = Helper::secure($userTable);
         $email     = Helper::secure($email);
         $sql = "SELECT * FROM " . $userTable . " WHERE email = :email";
         $stmt = $db->prepare($sql);
         $stmt->execute([':email' => $email]);
         return $stmt->rowCount() > 0;
      }

      /**
       * Update Password Reset
       * @param $userTable
       * User Table
       * @param $email
       * Email to update
       * @param $password
       * Password to update
       * @return bool
       */
      public static function updatePwdReset($userTable, $email, $password): bool
      {
         global $db;
         $userTable = Helper::secure($userTable);
         $email     = Helper::secure($email);
         $password  = Helper::hashString($password);
         $sql = "UPDATE " . $userTable . " SET password = :password WHERE email = :email";
         $stmt = $db->prepare($sql);
         $stmt->execute([':password' => $password, ':email' => $email]);
         return $stmt->rowCount() > 0;
      }

      /**
       * Test Selector And Validator hash match
       * @return bool
       */
      public function matchType():bool
      {
         if(!ctype_xdigit($this->pwdResetSelector) && !ctype_xdigit($this->pwdResetToken)) {
            return false;
         }
         return true;
      }

      /**
       * Check If Reset not expired
       * @param $selector
       * the column in the database for selector
       * @param $expire
       * the column in the database for expiry
       * @return bool
       */
      public static function checkTime($selector, $expire):bool|object|array
      {
         global $db;
         $sql = "SELECT * FROM " . static::$dbTable . " WHERE " . static::$selector_column . " = :selector AND " . static::$pwd_expire_column . " >= :expires";
         $stmt = $db->prepare($sql);
         $stmt->bindParam(":selector", $selector);
         $stmt->bindParam(":expires", $expire);
         $stmt->execute();
         if($stmt->rowCount() > 0) {
            return $stmt->fetch();
         }
         return false;
      }

      /**
       * Check Token And Validator
       * @param $validator
       * validator from the url
       * @param $token
       * Token From Database
       * @return bool
       */
      public static function checkToken($validator, $token): bool
      {
         if(!ctype_xdigit($validator) && !ctype_xdigit($token)) {
            return false;
         }
         if(password_verify(hex2bin($validator), $token)) {
            return true;
         }
         return false;
      }

      /**
       * Set Selector Column
       * @param $selector
       * @return void
       */
      public function setSelector($selector):void
      {
         static::$selector_column = $selector;
      }

      /**
       * Set Password Expiry Column
       * @param $pwd_expire
       * @return void
       */
      public function setPwdExpire($pwd_expire):void
      {
         static::$pwd_expire_column = $pwd_expire;
      }

      /**
       * Verify Token
       * @param $validator
       * @return bool
       */
      public function verifyToken($validator):bool
      {
         $token = hex2bin($this->validator);
         if(password_verify($token, $this->hashed_token)) {
            return true;
         }
         return false;
      }

   }