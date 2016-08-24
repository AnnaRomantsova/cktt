<?php
 //один заказ
 include('config.php');
 require_once($inc_path."/phpmailer/send.php");
 unset($main);
 $FILENAME = $front_html_path.'one.html';

 $main = &addInCurrentSection($FILENAME);

 if ($_GET['id']>0) {

      $r = new Select($db,"select *  from zakaz where id = $_GET[id]");
       if ($r->num_rows() == 0) header('Location: /error404');
      $r->next_row();
      $site->title=$r->result('name')." - BAZA-MASTEROV.RU";

     //добавили отзыв
     if ($_POST['review'] >0 ) {

         // $about= htmlspecialchars ( addslashes ($_POST['about']));
          $r1 = new Select($db,"insert into review (id_user,id_what,type,date,about,pabl) values ($_SESSION[user],$_GET[id],1,".time().",'',1 )");
          $r1 = new Select($db,"insert into likes (id_user,id_like,id_type) values ($_SESSION[user],$_GET[id],1)");

         //письмо заказчику
          $r1 = new Select($db,"select *  from users where id = ".$r->result('id_user'));
          $r1->next_row();
          //echo $r1->result('email');
          if ($r1->result('new_comments') >0) {

                $r2 = new Select($db,"select *  from users where id = $_SESSION[user]");
                $r2->next_row();
                $master="<a href='http://$_SERVER[SERVER_NAME]/master_profile/id/$_SESSION[user]'>".$r2->result('fio')."</a>";
                $zakaz="<a href='http://$_SERVER[SERVER_NAME]/zakaz_one/id/$_GET[id]'>".$r->result('name')."</a>";
                $text = date('d.m.Y').' в '.date('H:i:s').' на сайте '.$_SERVER['SERVER_NAME'].' мастер '.$master.' откликнулся на Ваш заказ '.$zakaz;


                //$mail = &newViaSMTP('mail_feed');
                $subject = 'Отклик на Ваш заказ на сайте '.$_SERVER['SERVER_NAME'];
                //$subm = sendViaSMTP($mail,$text,true);

                $subm=mailViaSMTP($text,'mail_send',$r1->result('email'),$subject,true);
                        //reload('', $f = array('subm' => intval($subm)) );

          };
     };

       //удалить отзыв
       if ($_POST['review_del']>0){
             if ($_POST['id']>0) {
                 $r1 = new Select($db,'delete from review where id='.$_POST['id']);
             };
       }

      $r->addFields($main,$ar=array('id','name','watch'));
      $r->addFieldHTML($main,'about');
      if ($r->result('price')!=='') {
                $r1 = new Select($db,"select * from time where id=".$r->result('time'));
                $r1->next_row();
                if ($r->result('price') > 0 )$main->addfield('price',$r->result('price').' руб.');
                    else $main->addfield('price',$r->result('price'));
         }    else  $main->addfield('price','Цена договорная');
      if ($r->result('id_city')>0) {
            $r1 = new Select($db,"select * from city where id=".$r->result('id_city'));
            $r1->next_row();
            $main->addfield('city',$r1->result('name'));
      };

      $main->addfield('cnt_review',cnt_review($_GET['id'],1));

      add_zakaz_types($main,$r->result('id'));
      //отзывы
      //echo $_SESSION['user'];
      add_review($main,$_GET['id'],1,$_SESSION['user']);
      $main->addfield('date',make_date_in_days($r->result('date')));
      $main->addfield('date_before',make_date_in_days($r->result('date_before'),false));
      $r = new Select($db,"update zakaz set watch = watch+1 where id = $_GET[id]");
      add_star($main,$_GET['id'],1);
      //право на коментарий
      check_review_rights($main,$_SESSION['user'],1,$_GET['id']);

 }  else header('Location: /error404');

 ?>

