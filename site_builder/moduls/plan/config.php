<?php
         $modulName = $GLOBALS['modulName'] = 'plan';
         $modulCaption =$GLOBALS['modulCaption'] = 'План обучения';

         $back_html_path='back/'.$modulName.'/';
         $front_html_path='front/'.$modulName.'/';

         $acount = $GLOBALS['acount'] = $GLOBALS[$modulName.'_acount'];

         $table_name = $GLOBALS['table_name'] = $GLOBALS[$modulName.'_table'];
         $GLOBALS['sections_table'] = $GLOBALS[$modulName.'_sections'];
         $GLOBALS['items_table']  = $GLOBALS[$modulName.'_items'];


         $files_path = '/_files/Moduls/'.$modulName.'/images/';
         $extent = array('jpg','png','gif');

         $files_path2 = '/_files/Moduls/'.$modulName.'/files/';
         $extent2 = array('doc','zip','rar','xls');


?>
