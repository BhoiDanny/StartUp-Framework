<?php
   $appName = \SannyTech\Helper::env('APP_NAME');
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?php echo $appName?></title>
   <style>
      * {
          font-family: Montserrat, sans-serif;
          text-align: center;
          box-sizing: border-box;
      }
   </style>
</head>
<body>
      <h1><?php echo $appName?></h1>
      <p>
         <?php echo \SannyTech\Helper::env('APP_DESCRIPTION')?>
      </p>
      <p>
         Error Occurred Contact Developer
      </p>
</body>
</html>