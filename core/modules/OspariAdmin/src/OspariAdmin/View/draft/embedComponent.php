<?php
     if(!isset($cmp)){
         $cmp = $this->component;
     }
       $componentType = $cmp->getType();
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <script src="https://code.jquery.com/jquery-1.10.2.min.js"></script>
  </head>
  <body>
      <?php echo $cmp->code.' '.$componentType->javascript; ?>
      
      <script>
        setTimeout(function(){
          var height = $('span').height()+30; ;
          var width = $('span').width()+30; ;
          var p = parent.document.getElementById('iframe-<?php echo $cmp->id; ?>');
         
          $(p).css('width', width).css('height', height);
      }, 2000);
      </script>
      
  </body>
</html>