<?php

   namespace SannyTech;

   class PasswordReset extends App
   {
      public string $email;
      public string $token;
      public string $password;
      public string $confirmPassword;
      public string $passwordResetError;
      public string $passwordResetSuccess;
      public string $passwordResetTokenError;
      public string $passwordResetTokenSuccess;
      public string $passwordResetToken;
      public string $passwordResetTokenEmail;
      public string $passwordResetTokenEmailError;
      public string $passwordResetTokenEmailSuccess;

      /**
       * PasswordReset Constructor
       * @return void
       */
      public function __construct(){}

      /**
       * Validate Password Reset
       * @return bool
       */
      public function validatePasswordReset(): bool
      {

         if($this->password !== $this->confirmPassword) {
            $this->passwordResetError = 'Passwords do not match';
            return false;
         }

         if(strlen($this->password) < 8) {
            $this->passwordResetError = 'Password must be at least 8 characters';
            return false;
         }

         if(strlen($this->password) > 32) {
            $this->passwordResetError = 'Password must be less than 32 characters';
            return false;
         }

         return true;
      }

      /**
       * Validate Password Reset Token
       * @return bool
       */
      public function validatePasswordResetToken(): bool
      {

         if(strlen($this->passwordResetToken) < 32) {
            $this->passwordResetTokenError = 'Invalid token';
            return false;
         }

         if(strlen($this->passwordResetToken) > 32) {
            $this->passwordResetTokenError = 'Invalid token';
            return false;
         }

         return true;
      }

      /**
       * Validate Password Reset Token Email
       * @return bool
       */
      public function validatePasswordResetTokenEmail(): bool
      {

            if(!filter_var($this->passwordResetTokenEmail, FILTER_VALIDATE_EMAIL)) {
               $this->passwordResetTokenEmailError = 'Invalid email';
               return false;
            }

            if(strlen($this->passwordResetTokenEmail) < 5) {
               $this->passwordResetTokenEmailError = 'Invalid email';
               return false;
            }

            if(strlen($this->passwordResetTokenEmail) > 255) {
               $this->passwordResetTokenEmailError = 'Invalid email';
               return false;
            }

            return true;
      }

      /**
       * Send Password Reset Token Email
       * @return bool
       */
      public function sendPasswordResetTokenEmail(): bool
      {

         if(!$this->validatePasswordResetTokenEmail()) {
            return false;
         }

         $this->passwordResetToken = $this->generateToken();

         $this->passwordResetTokenEmailSuccess = 'Password reset token sent';

         return true;
      }

      /**
       * Reset Password
       * @return bool
       */
      public function resetPassword(): bool
      {

            if(!$this->validatePasswordReset()) {
               return false;
            }

            $this->passwordResetSuccess = 'Password reset';

            return true;
      }

   }