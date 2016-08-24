<?php
     $GLOBALS['modulName'] = $modulName = 'rek';
     $modulCaption = 'Реклама';

         $back_html_path='back/reklama/';
         $front_html_path='front/reklama/';

       //  $table_name = 'company';

      $acount = $GLOBALS[$modulName.'_acount'];

      $table_name = $GLOBALS['table_name'] = $GLOBALS['reklama_table'];

      $files_path = '/_files/Moduls/rek/images/';
      $extent = array('jpg','png','gif');

      $GLOBALS['modulName'] = $modulName = 'rek';

      $arFiles = array(
                 'image1' => array($extent,$files_path,'image'),
                // 'image2' => array($extent,$files_path,'image'),
                // 'image3' => array($extent,$files_path,'image'),
      );

?>
