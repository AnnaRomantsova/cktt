<?php
 //список всех мастеров
 include('config.php');

 unset($main);
 $FILENAME = $front_html_path.'add.html';

 if (!($_SESSION['user'])>0) {
      header ( "location: http://" . $_SERVER['HTTP_HOST']);
 };

 $main = &addInCurrentSection($FILENAME);

  // строка для JavaScript и проверка наличия обязательных полей
 $flag = true; $str_fieldsWF = '';
 foreach ( $fieldsWithoutFail as $value) {
         //echo "dd";
       $str_fieldsWF.= ('\''.$value.'\',');
       $flag = $flag && !empty($$value);
 }

  $main->addField('fieldsWithoutFail',substr($str_fieldsWF,0,-1));

 if ($_POST['add']>0) {
     foreach ( $_POST as $key => $value)
         $$key=  addslashes ($value);

     $date = explode('.',$date);
     $day = $date[0];
     $mon =  $date[1];
     $year = $date[2];
     if ($hour =='') $hour =0;
     if ($minute =='') $minute =0;
    // echo "$day<br>$mon<br>$year";
    // $date_before = mktime($hour,$minute,0,$mon,$day,$year);
    // echo $date_before;

     $r = new Select($db,"insert into zakaz(name,id_city,price,time,date,date_before,about,id_user,watch,pabl)
                               values ('$name',$city,'$price',0,".time().",0,'$about',$_SESSION[user],0,1)");

     $newid =  $db->insert_id();
     $r1 = new Select($db,"insert into zakaz_types(id_zakaz,id_type) values ($newid,$zakaz_types)");

      header ( "location: http://" . $_SERVER['HTTP_HOST'].'/edit_zakaz/id/'.$newid.'/new/1');
 };

 addSprav($main,'time','','time');
 $r1 = new Select($db,"select id_city from users where id=$_SESSION[user]");
 $r1->next_row();
 addSprav($main,'city',$r1->result('id_city'),'city');
 addZakazSprav($main,0,'zakaz_types');

 ?>

