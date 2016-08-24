<?php

    $local_path  = '';

    echo "Урааа!";
    echo "Work time for region: $min min $seconds sec <br>";

     include $local_path."db_connect.php";
     $q = mysql_query("update firma set pabl=0 where date_end <= ".time());
      echo "update firma set pabl=0 where date_end <= ".time();
?>
