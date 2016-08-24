<?php
         $modulName = $GLOBALS['modulName'] = 'tests';
         $modulCaption =$GLOBALS['modulCaption'] = 'Тестирование';

         $back_html_path='back/'.$modulName.'/';
         $front_html_path='front/'.$modulName.'/';

         $fcount = $GLOBALS['fcount'] = $GLOBALS[$modulName.'_fcount'];

         $GLOBALS['sections_table'] = $GLOBALS[$modulName.'_sections'];
         $GLOBALS['items_table']  = $GLOBALS[$modulName.'_items'];

         $files_path = '/_files/Moduls/'.$modulName.'/images/';
         $extent = array('jpg','png','gif');

         $files_path2 = '/_files/Moduls/'.$modulName.'/files/';
         $extent2 = array('doc','zip','rar','xls');

         $arImgS = array(
                 'image1' => array($extent,$files_path,'image')
         );
         $arImgI = array(
                 'image1' => array($extent,$files_path,'image'),
                 'image2' => array($extent,$files_path,'image')
         );

         $arPrices = array(
                 'download' => array($extent2,$files_path2,'file')
         );
?>
