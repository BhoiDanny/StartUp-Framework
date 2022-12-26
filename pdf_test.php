<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Testing</title>
   <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
   <iframe id="pdf-viewer" src="" width="100%" height="500px"></iframe>

   <input type="file" id="pdf-file" accept="application/pdf">
   <button id="pdf-view">View</button>

   <div id="drop-zone" style="height:160px;width:160px;border:1px solid black" ondrop="handleDrop(event)">
      Drop files here
   </div>
   <script>
       (function (){
           $("#pdf-view").click(function(){
               let file = $("#pdf-file").prop("files")[0];
               let fileReader = new FileReader();
               fileReader.onload = function(){
                   $("#pdf-viewer").attr("src", fileReader.result);
               }
               fileReader.readAsDataURL(file);
           })
           $("#drop-zone").on("dragover", function(e){
               e.preventDefault();
               $(this).css("background-color", "red");
               //change mouse cursor to hand
               $(this).css("cursor", "pointer");

           }).on("drop", function(e){
               e.preventDefault();
               $(this).css("background-color", "white");
               let files = e.originalEvent.dataTransfer.files;
               let file = files[0];
               let fileReader = new FileReader();
               fileReader.onload = function(){
                   $("#pdf-viewer").attr("src", fileReader.result);
               }
               fileReader.readAsDataURL(file);

           })

       })()
   </script>


</body>
</html>