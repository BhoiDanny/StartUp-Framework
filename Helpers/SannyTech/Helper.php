<?php

   namespace SannyTech;

   use Exception;
   use JetBrains\PhpStorm\NoReturn;

   class Helper
   {
      /**
       * Check if App is in Production
       * @return bool
       */
      public static function isProduction(): bool
      {
         if(self::env('APP_ENV') == 'production' || self::env('APP_ENV') == 'prod') {
            return true;
         } else {
            return false;
         }
      }

      public static function productionErrorPage($page)
      {
         return require_once $page;
      }

      public static function productionErrorLog(
         mixed $error,
         string $destination='logs/error.log',
         int $type= 3,
         string $message = "",
      ):void
      {
         $log = 'Date: [' . date('Y-m-d H:i:s') . "]" . PHP_EOL . $message . PHP_EOL . $error->getLine() . PHP_EOL . $error->getMessage() . PHP_EOL . $error->getFile() . PHP_EOL . '-------------------------' . PHP_EOL;
         error_log($log, $type, $destination);
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

      /*EncryptValue*/
      public static function encryptValue($data, $encrypt_key): string
      {
         $output = false;
         $encrypt_method = "AES-256-CBC";
         $secret_key = $encrypt_key;
         $secret_iv = '1234567890123456';
         //hash
         $key = hash('sha256', $secret_key);
         // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
         $iv = substr(hash('sha256', $secret_iv), 0, 16);
         $output = openssl_encrypt($data, $encrypt_method, $key, 0, $iv);
         return base64_encode($output);
      }

      /*Decrypt*/
      public static function decrypt($string): string
      {
         $encrypt_method = "AES-256-CBC";
         $secret_key = static::env('APP_SECRET_KEY');
         $secret_iv = '1234567890123456';
         // hash
         $key = hash('sha256', $secret_key);
         // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
         $iv = substr(hash('sha256', $secret_iv), 0, 16);
         return openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
      }

      /*Decrypt Url*/
      public static function decryptValue($string, $decrypt_key): string
      {
         $encrypt_method = "AES-256-CBC";
         $secret_key = $decrypt_key;
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


      /**
       * Make data safe to use in a query
       * @param $data
       * @return string
       */
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

      /**
       * Generate Token
       * @param int $int
       * @return string
       * @throws Exception
       */
      public static function generateToken(int $int=32): string
      {
         return bin2hex(random_bytes($int));
      }

      /**
       * Create a folder
       * @param string $path
       * @return bool
       */
      public static function createDir(string $path=''): bool
      {
         if (!is_dir($path)) {
            mkdir($path, 0777, true);
            return true;
         }
         return false;
      }

      /**
       * Delete a folder and all its content
       * @param string $path
       * @return bool
       */
      public static function deleteDir(string $path=''): bool
      {
         if(!file_exists($path) || !is_dir($path)) {
            return false;
         } else {
            $files = glob($path . '/*');
            foreach($files as $file) {
               if(is_dir($file)) {
                  static::deleteDir($file);
               }
               unlink($file);
            }
            rmdir($path);
            return true;
         }
      }

      /**
       * Create a file
       * @param string $path
       * @param string $content
       * @return bool
       * @throws Exception
       */
      public static function createFile(string $path='', string $content=''): bool
      {
         if (!file_exists($path)) {
            $file = fopen($path, "w");
            fwrite($file, $content);
            fclose($file);
            return true;
         }
         return false;
      }

      /**
       * Delete a file
       * @param string $path
       * @return bool
       */
      public static function deleteFile(string $path=''): bool
      {
         if (file_exists($path)) {
            unlink($path);
            return true;
         }
         return false;
      }

      /**
       * Get file content
       * @param string $path
       * @return string
       */
      public static function getFileContent(string $path=''): string
      {
         if (file_exists($path)) {
            return file_get_contents($path);
         }
         return '';
      }

      /**
       * Write content to a file
       * @param string $path
       * @param string $content
       * @return bool
       */
      public static function writeFileContent(string $path='', string $content=''): bool
      {
         if (file_exists($path)) {
            $file = fopen($path, "w");
            fwrite($file, $content);
            fclose($file);
            return true;
         }
         return false;
      }

      /**
       * Append content to a file
       * @param string $path
       * @param string $content
       * @return bool
       */
      public static function appendFileContent(string $path='', string $content=''): bool
      {
         if (file_exists($path)) {
            $file = fopen($path, "a");
            fwrite($file, $content);
            fclose($file);
            return true;
         }
         return false;
      }

      /**
       * Get file size
       * @param string $path
       * @return int
       */
      public static function getFileSize(string $path=''): int
      {
         if (file_exists($path)) {
            return filesize($path);
         }
         return 0;
      }

      /**
       * Get file extension
       * @param string $path
       * @return string
       */
      public static function getFileExtension(string $path=''): string
      {
         if (file_exists($path)) {
            return pathinfo($path, PATHINFO_EXTENSION);
         }
         return '';
      }

      /**
       * Hash a string
       * @param string $string
       * @return string
       */
      public static function hashString(string $string='') : string
      {
         return password_hash($string, PASSWORD_BCRYPT, array('cost' => 10));
      }

      /**
       * Verify a hashed string
       * @param string $string
       * @param string $hash
       * @return bool
       */
      public static function verifyHash(string $string='', string $hash='') : bool
      {
         return password_verify($string, $hash);
      }

      /**
       * Get the current URL
       * @return string
       */
      public static function getCurrentUrl() : string
      {
         $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http';
         $host = $_SERVER['HTTP_HOST'];
         $uri = $_SERVER['REQUEST_URI'];
         return $protocol . '://' . $host . $uri;
      }

      /**
       * Get the current URL without the query string
       * @return string
       */
      public static function getCurrentUrlWithoutQueryString() : string
      {
         $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http';
         $host = $_SERVER['HTTP_HOST'];
         $uri = $_SERVER['REQUEST_URI'];
         $uri = explode('?', $uri);
         return $protocol . '://' . $host . $uri[0];
      }

      /**
       * Break name into pieces and store them in an array
       * @param string $name
       * @return array
       */
      public static function breakName(string $name='') : array
      {
         $name = strtolower($name);
         $name = explode(' ', $name);
         $name = array_filter($name);
         $name = array_values($name);
         foreach($name as $key => $value) {
            $name[$key] = ucfirst($value);
         }
         for($i = 0; $i < count($name); $i++) {
            if($i == 0) {
               $name['first_name'] = $name[$i];
               unset($name[$i]);
            } else if($i == 1) {
               $name['last_name'] = $name[$i];
               unset($name[$i]);
            } else if($i == 2) {
               $name['middle_name'] = $name[$i];
               unset($name[$i]);
            }
         }
         return $name;
      }

      /**
       * Check if is Email
       * @param string $email
       * @return bool
       */
      public static function isEmail(string $email='') : bool
      {
         if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return true;
         }
         return false;
      }

      /**
       * Check Email Provider
       * @param string $email
       * @param string $provider
       * @return bool
       */
      public static function checkEmailProvider(string $email='', string $provider='') : bool
      {
         $email = explode('@', $email);
         $email = end($email);
         if($email == $provider) {
            return true;
         }
         return false;
      }

      /**
       * Check if Token Match
       * @param string $token
       * @return bool
       */
      public static function isBinMatch(string $token='') : bool
      {
         if(ctype_xdigit($token)){
            return true;
         }
         return false;
      }

      /**
       * Expose Php in Ini
       * @return void
       */
      public static function exposePhp(string $value = 'Off'): void
      {
         ini_set('expose_php', $value);
      }

      /**
       * Set Time Zone
       * @param string $timezone
       * @return void
       */
      public static function setTimeZone(string $timezone='UTC'): void
      {
         date_default_timezone_set($timezone);
      }

      /**
       * Set Php Max Execution Time
       * @param int $time
       * @return void
       */
      public static function setMaxExecTime(int $time=30): void
      {
         ini_set('max_execution_time', $time);
      }

      /**
       * Set Php Max Input Time
       * @param int $time
       * @return void
       */
      public static function setMaxInputTime(int $time=60):void
      {
         ini_set('max_input_time', $time);
      }

      /**
       * Set Php Memory Limit
       * @param int $memory
       * @return void
       */
      public static function setMemoryLimit(int $memory=128): void
      {
         ini_set('memory_limit', $memory . 'M');
      }

      /**
       * Set Php Post Max Size
       * @param int $size
       * @return void
       */
      public static function setPostMaxSize(int $size=8): void
      {
         ini_set('post_max_size', $size . 'M');
      }

      /**
       * Set Php Upload Max File Size
       * @param int $size
       * @return void
       */
      public static function setUploadMaxFileSize(int $size=2): void
      {
         ini_set('upload_max_filesize', $size . 'M');
      }

      /**
       * Set Php Max File Uploads
       * @param int $files
       * @return void
       */
      public static function setMaxFileUploads(int $files=20): void
      {
         ini_set('max_file_uploads', $files);
      }

      /**
       * Set Php Max Input Vars
       * @param int $vars
       * @return void
       */
      public static function setMaxInputVars(int $vars=1000): void
      {
         ini_set('max_input_vars', $vars);
      }

      /**
       * Set Php Display Errors
       * @param bool $display
       * @return void
       */
      public static function setDisplayErrors(bool $display=false): void
      {
         ini_set('display_errors', $display);
      }

      /**
       * Set Php Display Startup Errors
       * @param bool $display
       * @return void
       */
      public static function setDisplayStartupErrors(bool $display=false): void
      {
         ini_set('display_startup_errors', $display);
      }

      /**
       * Set Php Log Errors
       * @param bool $log
       * @return void
       */
      public static function setLogErrors(bool $log=false): void
      {
         ini_set('log_errors', $log);
      }

      /**
       * Log Error
       * @param string $message
       * @param Exception $error
       * @param int $type
       * @param string $destination
       * @return void
       */
      public static function logError(
         Exception $error,
         string $destination='logs/exceptions.log',
         int $type= 3,
         string $message = '',
      ):void
      {
         $log = 'Date: ' . date('Y-m-d H:i:s') . PHP_EOL . $message . PHP_EOL . $error->getLine() . PHP_EOL . $error->getMessage() . PHP_EOL . $error->getFile() . PHP_EOL . '-------------------------' . PHP_EOL;
         error_log($log, $type, $destination);
      }

      /**
       * Changes the X-Powered-By
       * @param string $powered
       * @return void
       */
      public static function setPoweredBy(string $powered=''): void
      {
         header('X-Powered-By: ' . $powered);
      }

      /**
       * Set Server Signature
       * @param string $signature
       * @return void
       */
      public static function setServerSignature(string $signature=''): void
      {
         header('Server: ' . $signature);
      }

   }