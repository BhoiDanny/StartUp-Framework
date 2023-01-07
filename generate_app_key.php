<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Generate App Key</title>
   <style>
      * {
          font-family: Montserrat, sans-serif;
      }
   </style>
</head>
<body>
   <h1>Generate App Key</h1>
   <?php
      if(isset($_POST['generate'])) {
         if(empty($_POST['key'])) {
             echo('Please enter a key');
         };
         $keyLength = $_POST['key'];
         $key = bin2hex(openssl_random_pseudo_bytes($keyLength));
      }
   ?>
   <form action="" method="post">
      <input type="text" name="key" placeholder="Enter Length of Key" value="<?php echo $keyLength ?? '' ?>">
      <input type="submit" name="generate" value="Generate">
   </form>
   <?php if(isset($key)): ?>
      <p>Copy the following key and paste it into the .env file</p>
      <p><?php echo $key; ?></p>
   <?php endif; ?>
</body>
</html>