<?php
         $modulName = $GLOBALS['modulName'] = 'practic';
         $modulCaption =$GLOBALS['modulCaption'] = 'Практические занятия';

         $back_html_path='back/'.$modulName.'/';
         $front_html_path='front/'.$modulName.'/';

         $fcount = $GLOBALS['fcount'] = $GLOBALS[$modulName.'_fcount'];

         $GLOBALS['sections_table'] = $GLOBALS[$modulName.'_sections'];
         $GLOBALS['items_table']  = $GLOBALS[$modulName.'_items'];

         $files_path = '/_files/Moduls/'.$modulName.'/files/';
         $extent = array('doc','zip','rar','xls','txt','jpg','png','gif','xlsx','docx');
         //$extent = array('jpg','png','gif');

         $arImgI = array(
                 'file1' => array($extent,$files_path,'file'),
//                 'file1' => array($extent,$files_path,'file')
         );




?>
