<?php
     $GLOBALS['modulName'] = $modulName = 'zakaz';
     $modulCaption = 'Заказы';

         $back_html_path='back/'.$modulName.'/';
         $front_html_path='front/'.$modulName.'/';
         $fcount = $GLOBALS['fcount'] = $GLOBALS[$modulName.'_fcount'];
         $acount = $GLOBALS['acount'] = $GLOBALS[$modulName.'_acount'];
         $table_name = 'zakaz';


         $arFiles = array(
                 'image1' => array($extent,$files_path,'image'),
                 'image2' => array($extent,$files_path,'image')
         );

           // обязательные поля
 $fieldsWithoutFail = array(
  'name','zakaz_types'
 );

?>
