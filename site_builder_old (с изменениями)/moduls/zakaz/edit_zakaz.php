<?php
 //список всех мастеров
 include('config.php');

 unset($main);
 $FILENAME = $front_html_path.'edit.html';

 if (!($_SESSION['user'])>0) {
      header ( "location: http://" . $_SERVER['HTTP_HOST']);
 };

 $main = &addInCurrentSection($FILENAME);
//var_dump($_POST);
 if ($_GET['id']>0) {

       if ($_POST['save']>0) {

           foreach ( $_POST as $key => $value)
               $$key= strip_tags( htmlspecialchars ( addslashes ($value)));

           $day = (int)substr($date,0,2);
           $mon =  (int)substr($date,3,2);
           $year = (int)substr($date,6);
           if ($hour =='') $hour =0;
           if ($minute =='') $minute =0;

              $date_before = mktime($hour,$minute,0,$mon,$day,$year);
           $r = new Select($db,"update zakaz set name='$name',id_city=$city,price='$price',date_before=$date_before,about='$about',pabl=0
                                    where id = $_GET[id]");

           $r = new Select($db,"delete from zakaz_types where id_zakaz=$_GET[id] ");
           $r = new Select($db,"insert into zakaz_types(id_zakaz,id_type) values ($_GET[id],$zakaz_types)");

           $message = 'Данные сохранены. ';
      };

      $r = new Select($db,"select *  from zakaz where id = $_GET[id]");
      $r->next_row();
      $r->addFields($main,$ar=array('id','name','price','about'));
      $main->addField('date',date('d.m.Y',$r->result('date_before')));
      addSprav($main,'time',$r->result('time'),'time');
      addSprav($main,'city',$r->result('id_city'),'city');
      addZakazSprav($main,$_GET['id'],'zakaz_types');

      if ($_GET['new']>0) $message = 'Данные сохранены. ';

      if ($message!=='') $main->addField('message',$message);

 };


 ?>

