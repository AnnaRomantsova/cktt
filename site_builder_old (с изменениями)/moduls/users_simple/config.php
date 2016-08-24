<?php
 $GLOBALS['modulName'] = $modulName = 'users_simple';
 $modulCaption = 'Пользователи';

 $back_html_path='back/'.$modulName.'/';



 $acount = $GLOBALS['acount'] = $GLOBALS[$modulName.'_acount']=$GLOBALS['user_acount'];


 $table_name = $GLOBALS['table_name'] = $GLOBALS['user_table'];

 $files_path = '/_files/Moduls/'.$modulName.'/images/';
 $extent = array('jpg','png','gif');

 $arFiles = array(
                'image1' => array($extent,$files_path,'image'),
                'image2' => array($extent,$files_path,'image')
         );
?>
