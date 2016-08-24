<?php


   header('Content-Type: text/html; charset=utf-8');
   ini_set("max_execution_time", "1000000");
   ini_set('display_errors', 1); error_reporting(E_ALL);

   //echo dirname(__FILE__);
   //die;
   $local_path  = dirname(__FILE__).'/';
   //$local_path  = 'cktt.ru/parse/';

   include $local_path."db_connect.php";
   include $local_path."func.php";
   $start = microtime(true);

//die;

  $q = mysql_query("select * from region where id=242");
  while ($row = mysql_fetch_array($q)) {
      mysql_query("delete from zakupki where region=$row[id]");
      $cnt_page=2;
	  for ($i=1;$i<=$cnt_page; $i++) {
	     parse_region($row['number'],$row['district'],$i);
	    // if ($i==1) exit;
	  };
  };


  mysql_query("delete from zakupki_okato where id not in (select DISTINCT okato from zakupki) and id<>1");
  mysql_query("delete from zakupki_types where id not in (select DISTINCT type from zakupki) and id<>1");

  //parse_zakaz_info('http://zakupki.gov.ru/pgz/public/action/orders/info/common_info/show?source=EPZ&notificationId=6991086','0358300132813000200');
 /*
  $q = mysql_query("select * from zakupki where date_provedenia like '%с%'");
  while ($row = mysql_fetch_array($q)) {
       parse_zakaz_info($row['link'],$row['number']);
  };
 */
    $end = microtime(true);
    $time=$end-$start;
    $min=floor($time/60);
    $seconds= $time % 60;
    echo "Work time for region: $min min $seconds sec <br>";
   // echo "Всего пройдено закупок: ".($new_cnt+$updated_cnt+$old_cnt);




?>
