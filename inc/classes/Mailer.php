<?php /** @noinspection PhpUndefinedConstantInspection */

   namespace SannyTech;

   use http\Header;
   use PHPMailer\PHPMailer\PHPMailer;

   use PHPMailer\PHPMailer\SMTP;

   use PHPMailer\PHPMailer\OAuth;

   use PHPMailer\PHPMailer\Exception;

   use League\OAuth2\Client\Provider\Google;

   use League\OAuth2\Client\Grant\RefreshToken;

   class Mailer extends PHPMailer
   {
      private static string $PHPMailer    = PHPMailer::class;
      private static string $SMTP         = SMTP::class;
      private static string $OAuth        = OAuth::class;
      private static string $Exception    = Exception::class;
      private static string $Google       = Google::class;
      private static string $RefreshToken = RefreshToken::class;

      private $mail;
      public string $error;

      public function __construct($bool=false)
      {
         try {

            parent::__construct();

            $this->mail = new static::$PHPMailer($bool);
            $this->mail->isSMTP();
            $this->mail->Host       = Helper::env('MAIL_HOST');
            $this->mail->Port       = Helper::env('MAIL_PORT');
            $this->mail->SMTPSecure = $this->mail::ENCRYPTION_SMTPS;
            $this->mail->AuthType   = Helper::env('MAIL_AUTH');
            $this->mail->SMTPAuth   = true;

            $provider = new static::$Google(
               [
                  'clientId' => Helper::env('MAIL_CLIENT_ID'),
                  'clientSecret' => Helper::env('MAIL_CLIENT_SECRET'),
               ]
            );

//            $grant = new static::$RefreshToken();
//
//            $token = $provider->getAccessToken(
//                $grant, [
//                    'refresh_token' => Helper::env("MAIL_REFRESH_TOKEN"),
//                ]
//            );

            $this->mail->setOAuth(
               new OAuth(
                  [
                     'provider' => $provider,
                     'clientId' => Helper::env('MAIL_CLIENT_ID'),
                     'clientSecret' => Helper::env('MAIL_CLIENT_SECRET'),
                     'refreshToken' => Helper::env('MAIL_REFRESH_TOKEN'),
//                        'accessToken' => $token->getToken(),
//                        'redirectUri' => 'http://localhost:3002/inc/get_oauth_token.php',
                     'userName' => Helper::env('MAIL_USERNAME'),
                  ]
               )
            );

         } catch(Exception $e){
            $this->error = "Working Here" . $e->getMessage() . " " . $e->getCode();
         }
      }

      public function mailer()
      {
         return $this->mail;
      }

      public function from($name=""){
         try {
            $this->mail->setFrom(Helper::env('MAIL_EMAIL'),$name);
         } catch(\Exception $e) {
            $this->error = $e->getMessage() . " " . $e->getCode();
         }
      }

      public function to($to,$name="") {
         try {
            $this->mail->addAddress($to,$name);
         } catch (\Exception $e) {
            $this->error = $e->getMessage() . " " . $e->getCode();
         }
      }

      public function replyTo($email,$name="") {
         try {
            $this->mail->addReplyTo($email,$name);
         } catch (\Exception $e) {
            $this->error = $e->getMessage() . " " . $e->getCode();
         }
      }

      public function Subject($subject) {
         try {
            $this->mail->Subject = $subject;
         } catch (\Exception $e) {
            $this->error = $e->getMessage() . " " . $e->getCode();
         }
      }

      public function Body($body) {
         try {
            $this->mail->Body = $body;
            $this->mail->AltBody = $body;
         } catch (\Exception $e) {
            $this->error = $e->getMessage() . " " . $e->getCode();
         }
      }

      public function Attach($file) {
         try {
            $this->mail->addAttachment($file);
         } catch (\Exception $e) {
            $this->error = $e->getMessage() . " " . $e->getCode();
         }
      }

      public function send(): bool
      {
         try {
            $this->mail->send();
            return true;
         } catch (\Exception $e) {
            $this->error = $e->getMessage() . " " . $e->getCode();
            return false;
         }
      }

      public function CharsetUTF8() {
         try {
            $this->mail->CharSet = $this->mail::CHARSET_UTF8;
         } catch (\Exception $e) {
            $this->error = $e->getMessage() . " " . $e->getCode();
         }
      }

      public function isHTML($isHtml=false) {
         try {
            $this->mail->isHTML($isHtml);
         } catch (\Exception $e) {
            $this->error = $e->getMessage() . " " . $e->getCode();
         }
      }

      public function Error(): string
      {
         return $this->error;
      }

      public function sendMail($to, $subject,$message,$from=""){
         $this->from($from);
         $this->to($to);
         $this->Subject($subject);
         $this->CharsetUTF8();
         $this->isHTML(true);
         $this->Body($message);
      }

      public function __destruct() {
         $this->mail->clearAddresses();
         $this->mail->clearAttachments();
         $this->mail->clearAllRecipients();
      }

      public function __toString() {
         return $this->error;
      }

   }