<?php
     $GLOBALS['modulName'] = $modulName = 'reklama';
     $modulCaption = 'Реклама';

         $back_html_path='back/'.$modulName.'/';
         $front_html_path='front/'.$modulName.'/';

       //  $table_name = 'company';

      $acount = $GLOBALS[$modulName.'_acount'];

      $table_name = $GLOBALS['table_name'] = $GLOBALS[$modulName.'_table'];

      $files_path = '/_files/Moduls/'.$modulName.'/images/';
      $extent = array('jpg','png','gif');

      $arFiles = array(
                 'image1' => array($extent,$files_path,'image'),
                 'image2' => array($extent,$files_path,'image'),
                 'image3' => array($extent,$files_path,'image'),
      );

?>
