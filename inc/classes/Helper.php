<?php

   namespace SannyTech;

   use JetBrains\PhpStorm\NoReturn;

   class Helper
   {
      /**
       * Check if App is in Production
       * @return bool
       */
      public static function isProduction(): bool
      {
         if(self::env('APP_ENV') == 'production') {
            return true;
         } else {
            return false;
         }
      }

      public static function productionErrorPage()
      {
         return require_once 'inc/errors/production.php';
      }

      /**
       * @param $data
       * @return string
       */
      public function sanitize($data): string
      {
         return filter_var($data, FILTER_SANITIZE_STRING);
      }

      /**
       * @param $data
       * @return string
       */
      public function sanitizeEmail($data): string
      {
         return filter_var($data, FILTER_SANITIZE_EMAIL);
      }

      /**
       * @param $data
       * @return string
       */
      public function sanitizeUrl($data): string
      {
         return filter_var($data, FILTER_SANITIZE_URL);
      }

      public static function get($key, $default = null)
      {
         return $_GET[$key] ?? $default;
      }

      public static function post($key, $default = null)
      {
         return $_POST[$key] ?? $default;
      }

      public static function request($key, $default = null)
      {
         return $_REQUEST[$key] ?? $default;
      }

      public static function session($key, $default = null)
      {
         return $_SESSION[$key] ?? $default;
      }

      public static function cookie($key, $default = null)
      {
         return $_COOKIE[$key] ?? $default;
      }

      public static function server($key, $default = null)
      {
         return $_SERVER[$key] ?? $default;
      }

      public static function env($key, $default = null)
      {
         return $_ENV[$key] ?? $default;
      }

      public static function file($key, $default = null)
      {
         return $_FILES[$key] ?? $default;
      }

      #[NoReturn] public static function redirect($url, $statusCode = 303): void
      {
         header('Location: ' . $url, true, $statusCode);
         die();
      }

      public static function isPost(): bool
      {
         return $_SERVER['REQUEST_METHOD'] === 'POST';
      }

      public static function isGet(): bool
      {
         return $_SERVER['REQUEST_METHOD'] === 'GET';
      }

      public static function isPut(): bool
      {
         return $_SERVER['REQUEST_METHOD'] === 'PUT';
      }

      public static function isDelete(): bool
      {
         return $_SERVER['REQUEST_METHOD'] === 'DELETE';
      }

      /**
       * @param $url
       * @param $data
       * @return string|bool
       */
      public static function curl($url, $data): string|bool
      {
         $ch = curl_init();
         curl_setopt($ch, CURLOPT_URL, $url);
         curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
         curl_setopt($ch, CURLOPT_POST, 1);
         curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
         $output = curl_exec($ch);
         curl_close($ch);
         return $output;
      }

      /**
       * @param $url
       * @return string|bool
       */
      public static function curlGet($url): string|bool
      {
         $ch = curl_init();
         curl_setopt($ch, CURLOPT_URL, $url);
         curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
         $output = curl_exec($ch);
         curl_close($ch);
         return $output;
      }

      /**
       * @param $url
       * @param $data
       * @return string|bool
       */
      public static function curlPut($url, $data): string|bool
      {
         $ch = curl_init();
         curl_setopt($ch, CURLOPT_URL, $url);
         curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
         curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
         curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
         $output = curl_exec($ch);
         curl_close($ch);
         return $output;
      }

      /**
       * @param $url
       * @return string|bool
       */
      public static function curlDelete($url): string|bool
      {
         $ch = curl_init();
         curl_setopt($ch, CURLOPT_URL, $url);
         curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
         curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
         $output = curl_exec($ch);
         curl_close($ch);
         return $output;
      }

      /*Encrypt*/
      public static function encrypt($string): string
      {
         $output = false;
         $encrypt_method = "AES-256-CBC";
         $secret_key = static::env('APP_SECRET_KEY');
         $secret_iv = '1234567890123456';
         // hash
         $key = hash('sha256', $secret_key);
         // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
         $iv = substr(hash('sha256', $secret_iv), 0, 16);
         $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
         return base64_encode($output);
      }

      /*Decrypt*/
      public static function decrypt($string): string
      {
         $output = false;
         $encrypt_method = "AES-256-CBC";
         $secret_key = static::env('APP_SECRET_KEY');
         $secret_iv = '1234567890123456';
         // hash
         $key = hash('sha256', $secret_key);
         // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
         $iv = substr(hash('sha256', $secret_iv), 0, 16);
         return openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
      }

      public static function getIp(): string
      {
         if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
         } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
         } else {
            $ip = $_SERVER['REMOTE_ADDR'];
         }
         return $ip;
      }

      public static function getBrowser(): string
      {
         $user_agent = $_SERVER['HTTP_USER_AGENT'];
         $browser = 'Unknown Browser';
         $browser_array = array(
            '/msie/i' => 'Internet Explorer',
            '/firefox/i' => 'Firefox',
            '/safari/i' => 'Safari',
            '/chrome/i' => 'Chrome',
            '/edge/i' => 'Edge',
            '/opera/i' => 'Opera',
            '/netscape/i' => 'Netscape',
            '/maxthon/i' => 'Maxthon',
            '/konqueror/i' => 'Konqueror',
            '/mobile/i' => 'Handheld Browser'
         );
         foreach ($browser_array as $regex => $value) {
            if (preg_match($regex, $user_agent)) {
               $browser = $value;
            }
         }
         return $browser;
      }

      public static function getOS(): string
      {
         $user_agent = $_SERVER['HTTP_USER_AGENT'];
         $os_platform = "Unknown OS Platform";
         $os_array = array(
            '/windows nt 10/i' => 'Windows 10',
            '/windows nt 6.3/i' => 'Windows 8.1',
            '/windows nt 6.2/i' => 'Windows 8',
            '/windows nt 6.1/i' => 'Windows 7',
            '/windows nt 6.0/i' => 'Windows Vista',
            '/windows nt 5.2/i' => 'Windows Server 2003/XP x64',
            '/windows nt 5.1/i' => 'Windows XP',
            '/windows xp/i' => 'Windows XP',
            '/windows nt 5.0/i' => 'Windows 2000',
            '/windows me/i' => 'Windows ME',
            '/win98/i' => 'Windows 98',
            '/win95/i' => 'Windows 95',
            '/win16/i' => 'Windows 3.11',
            '/macintosh|mac os x/i' => 'Mac OS X',
            '/mac_powerpc/i' => 'Mac OS 9',
            '/linux/i' => 'Linux',
            '/ubuntu/i' => 'Ubuntu',
            '/iphone/i' => 'iPhone',
            '/ipod/i' => 'iPod',
            '/ipad/i' => 'iPad',
            '/android/i' => 'Android',
            '/blackberry/i' => 'BlackBerry',
            '/webos/i' => 'Mobile'
         );
         foreach ($os_array as $regex => $value) {
            if (preg_match($regex, $user_agent)) {
               $os_platform = $value;
            }
         }
         return $os_platform;
      }

      public static function getDevice(): string
      {
         $user_agent = $_SERVER['HTTP_USER_AGENT'];
         $device = "Unknown Device";
         $device_array = array(
            '/windows nt 10/i' => 'Windows 10',
            '/windows nt 6.3/i' => 'Windows 8.1',
            '/windows nt 6.2/i' => 'Windows 8',
            '/windows nt 6.1/i' => 'Windows 7',
            '/windows nt 6.0/i' => 'Windows Vista',
            '/windows nt 5.2/i' => 'Windows Server 2003/XP x64',
            '/windows nt 5.1/i' => 'Windows XP',
            '/windows xp/i' => 'Windows XP',
            '/windows nt 5.0/i' => 'Windows 2000',
            '/windows me/i' => 'Windows ME',
            '/win98/i' => 'Windows 98',
            '/win95/i' => 'Windows 95',
            '/win16/i' => 'Windows 3.11',
            '/macintosh|mac os x/i' => 'Mac OS X',
            '/mac_powerpc/i' => 'Mac OS 9',
            '/linux/i' => 'Linux',
            '/ubuntu/i' => 'Ubuntu',
            '/iphone/i' => 'iPhone',
            '/ipod/i' => 'iPod',
            '/ipad/i' => 'iPad',
            '/android/i' => 'Android',
            '/blackberry/i' => 'BlackBerry',
            '/webos/i' => 'Mobile'
         );
         foreach ($device_array as $regex => $value) {
            if (preg_match($regex, $user_agent)) {
               $device = $value;
            }
         }
         return $device;
      }

      public static function getCountry(): string|object
      {
         $ip = static::getIp();
         $details = json_decode(file_get_contents("https://ipinfo.io/{$ip}/json"));
         return $details->country ?? "Unknown Country";
      }

      public static function getCity(): string
      {
         $ip = static::getIp();
         $details = json_decode(file_get_contents("https://ipinfo.io/{$ip}/json"));
         return $details->city ?? "Unknown City";
      }

      public static function getRegion(): string
      {
         $ip = static::getIp();
         $details = json_decode(file_get_contents("https://ipinfo.io/{$ip}/json"));
         return $details->region ?? "Unknown Region";
      }

      public static function getLat(): string
      {
         $ip = static::getIp();
         $details = json_decode(file_get_contents("https://ipinfo.io/{$ip}/json"));
         return $details->loc ?? "Unknown Lat";
      }

      public static function getLong(): string
      {
         $ip = static::getIp();
         $details = json_decode(file_get_contents("https://ipinfo.io/{$ip}/json"));
         return $details->loc ?? "Unknown Long";
      }

      public static function getPostal(): string
      {
         $ip = static::getIp();
         $details = json_decode(file_get_contents("https://ipinfo.io/{$ip}/json"));
         return $details->postal ?? "Unknown Postal";
      }

      public static function getOrg(): string
      {
         $ip = static::getIp();
         $details = json_decode(file_get_contents("https://ipinfo.io/{$ip}/json"));
         return $details->org ?? "Unknown Org";
      }

      public static function getHostname(): string
      {
         $ip = static::getIp();
         $details = json_decode(file_get_contents("https://ipinfo.io/{$ip}/json"));
         return $details->hostname ?? "Unknown Hostname";
      }


      /*Make myy app more secured*/
      /*Secure */
      public static function secure($data): string
      {
         $data = trim($data);
         $data = stripslashes($data);
         return htmlspecialchars($data);
      }

      /*Encrypt out going post request*/
      public static function encryptData($data): string
      {
         return base64_encode($data);
      }

      /*Decrypt incoming post request*/
      public static function decryptData($data): string
      {
         return base64_decode($data);
      }

      /**Reset Password Helpers */
      public static function generateRandomString($length = 10): string
      {
         $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
         $charactersLength = strlen($characters);
         $randomString = '';
         for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
         }
         return $randomString;
      }

      public static function generateRandomNumber($length = 10): string
      {
         $characters = '0123456789';
         $charactersLength = strlen($characters);
         $randomString = '';
         for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
         }
         return $randomString;
      }






   }