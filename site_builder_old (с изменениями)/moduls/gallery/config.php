<?php
/**
 * @package ALL
 */
        $GLOBALS['modulName'] = $modulName = 'galery';
         $modulCaption = 'Фотогалерея';

         $back_html_path='back/'.$modulName.'/';
         $front_html_path='front/'.$modulName.'/';
         $fcount = $GLOBALS['fcount'] = $GLOBALS[$modulName.'_fcount'];
         $acount = $GLOBALS[$modulName.'_acount'];
         $table_name = 'galery';

         $files_path = '/_files/Moduls/'.$modulName.'/images/';
         $extent = array('jpg','png','gif');

         $arFiles = array(
                'image1' => array($extent,$files_path,'image'),
                'image2' => array($extent,$files_path,'image')
         );


?>
