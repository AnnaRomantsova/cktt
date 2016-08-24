<?php
         $modulName = $GLOBALS['modulName'] = 'types';
         $modulCaption =$GLOBALS['modulCaption'] = 'Разделы';

         $back_html_path='back/'.$modulName.'/';
         $front_html_path='front/'.$modulName.'/';

         $table_name = $GLOBALS['table_name'] =  $GLOBALS[$modulName.'_table'];
         $GLOBALS['sections_table'] = $GLOBALS[$modulName.'_sections'];
         $GLOBALS['items_table']  = $GLOBALS[$modulName.'_items'];
        $arImgS = array(
                 'image1' => array($extent,$files_path,'image')
         );
         $arImgI = array(
                 'image1' => array($extent,$files_path,'image'),
                 'image2' => array($extent,$files_path,'image')
         );


?>
