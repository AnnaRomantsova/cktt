<?php
     $db_server = 'localhost';
     $db_user   = 'dba0006';
     $db_pass   = 'd4cwHZff';
     $db_base   = 'dba0006_1';

    $db_user   = 'root';
     $db_pass   = '';
     $db_base   = 'cktt';


     //соединение с бд
     if (!mysql_connect ($db_server, $db_user, $db_pass)) die('Нет соединения с сервером баз данных');
     if (!mysql_select_db($db_base)) die('Нет соединения с базой данных');

?>
