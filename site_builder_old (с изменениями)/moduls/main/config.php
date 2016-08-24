<?php
     $GLOBALS['modulName'] = $modulName = 'main';
     $modulCaption = 'Мастер';

         $back_html_path='back/'.$modulName.'/';
         $front_html_path='front/'.$modulName.'/';
         $fcount = $GLOBALS['fcount'] = $GLOBALS[$modulName.'_fcount'];
         $acount = $GLOBALS[$modulName.'_acount'];
         $table_name = 'users';


         $arFiles = array(
                 'image1' => array($extent,$files_path,'image'),
                 'image2' => array($extent,$files_path,'image')
         );

?>
