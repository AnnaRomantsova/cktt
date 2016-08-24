<?php
 //список всех мастеров
 include('config.php');

 unset($main);
 $FILENAME = $front_html_path.'one.html';

 $main = &addInCurrentSection($FILENAME);

  //добавили отзыв
 if ($_POST['review'] >0 ) {
      $about= htmlspecialchars ( addslashes ($_POST['about']), ENT_QUOTES, $encoding = "cp1251");
      $r1 = new Select($db,"insert into review (id_user,id_what,type,date,about,pabl) values ($_SESSION[user],$_GET[id],3,".time().",'$about',1 )");
 };

 if ($_GET['id']>0) {

      $r = new Select($db,"select *  from $table_name where id = $_GET[id]");
      if ($r->num_rows() == 0) header('Location: /error404');


      $r->next_row();
      $site->title = $r->result('name')." - edu.cktt.ru";
      $r->addFields($main,$ar=array('id','name','watch'));
      if ($r->result('time1') !==''  )  $r->addFields($main,$ar=array('time1'));
      if ($r->result('time2') !==''  )  $r->addFields($main,$ar=array('time2'));
      if ($r->result('week')>0) {
          $r1 = new Select($db,"select * from week where id=".$r->result('week'));
          if ($r1->next_row()) $main->addfield('week',$r1->result('name'));
      };
    //  $main->addfield('cnt_review',cnt_review($_GET['id'],3));
      $r->addFieldHTML($main,'about');
      $r->addFieldsIMG($main,$ar=array('image1'));
      if ($r->result('price') !== '' )  $r->addFields($main,$ar=array('price'));
      //отзывы
     // add_review($main,$_GET['id'],3);
     // add_star($main,$_GET['id'],3);

       $main->addfield('date',make_date_in_days($r->result('date')));
      //check_review_rights($main,$_SESSION['user'],3,$id_what=0);
     //разделы
     //add_lenta_types($main,$_GET['id']);
     $r = new Select($db,'update '.$table_name.' set watch=watch+1 where id="'.$_GET['id'].'"');
     $main->addField('site',$_SERVER['HTTP_HOST']);
 }  else header('Location: /error404');


 ?>

