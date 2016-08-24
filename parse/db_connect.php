<?php
     $db_server = 'localhost';
     $db_user   = 'dba0006';
     $db_pass   = 'd4cwHZff';
     $db_base   = 'dba0001_1';
     //echo "dd";
   /*
   $db_user   = 'root';
     $db_pass   = '';
     $db_base   = 'estate';

   */
     //соединение с бд
     if (!mysql_connect ($db_server, $db_user, $db_pass)) die('Нет соединения с сервером баз данных');
     if (!mysql_select_db($db_base)) die('Нет соединения с базой данных');

?>
