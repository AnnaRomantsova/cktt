<?php
 //список всех мастеров
 include('config.php');

 unset($main);
 $FILENAME = $front_html_path.'master_profile.html';

 $main = &addInCurrentSection($FILENAME);
 //echotree($site);

 if (!$_GET['id'] >0 && !$_SESSION['user'] >0) header('Location:/');

 //добавили отзыв
 if ($_POST['review'] >0 ) {
      $about= htmlspecialchars ( addslashes ($_POST['about']));
      $r1 = new Select($db,"insert into review (id_user,id_what,type,date,about,pabl) values ($_SESSION[user],$_GET[id],2,".time().",'$about',0 )");
      $main->addfield('message','Ваш отзыв будет опубликован на сайте после просмотра модератором.');
      header('Location: /master_profile/rev/1/id/'.$_GET['id']);
 };

 if ($_GET['rev'] >0 ){
 	$main->addfield('message','Ваш отзыв будет опубликован на сайте после просмотра модератором.');
 };

 $r = new Select($db,'select * from users where id="'.$_GET['id'].'" ');
 if ($r->next_row()) {
         $r->addFields($main,$ar=array('id','email','fio','watch'));

         $fields  = array('experience','grafic','tel','skipe','link','dostavka','oplata_type');
         if ($r->result('adress') !=='')  $main->addfield('adress',$r->result('adress'));
         $cont=0;
         foreach ($fields as $val) {
             if ($val=='link') {
                $link=$r->result('link');
	  				         if (strlen($link) >0) {
         	   	 if (strpos($link,'http') === false) $link='http://'.$link;
         	   	 $cont=1;
         	   	 $main->addfield('link',$link);
         	   };
         	} else
                if (strlen($r->result($val))>0) { $main->addfield($val,$r->result($val)); $cont=1;};
         };

         if ($cont>0) $main->addfield('cont','');

         add_user_avatar($main,$r);

         $site->title=$r->result('fio')." - BIGUDI-ONLINE.RU";


         $main->addfield('about',htmlspecialchars_decode($r->result('about')));
         if ($r->result('is_free') >0) { $main->addfield('status','занят');  $main->addfield('bisy','_bisy');}
             else $main->addfield('status','свободен');
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


         $main->addfield('cnt_review',cnt_review($_GET['id'],2));

         //отзывы
         add_review($main,$_GET['id'],2);

         //ОТЗЫВ ПРАВА
         check_review_rights($main,$_SESSION['user'],2,$_GET['id']);
         //разделы
         add_user_types($main,$_GET['id']);

		 add_star($main,$_GET['id'],2);

          add_date_visit(&$main,$r->result('id'));
          //ПРАВА на личное сообдение
         check_message_rights($main,$_SESSION['user'],$_GET['id']);
         //фотогалерея
         $ri = new Select($db,"select * from galery where id_user=$_GET[id] and pabl=1 order by id");
         if ($ri->num_rows() ==0) $main->addField('no_photo','');
         while ($ri->next_row()) {
             unset($sub);
             $sub = new outTree();
             $ri->addFields($sub,$ar=array('id'));
             if ($ri->result('about') !=='')  $sub->addField('about',$ri->result('about'));
             $ri->addFieldsIMG($sub,$ar=array('image1'));
             $ri->addFieldsIMG($sub,$ar=array('image2'));
             if ($ri->result('pabl') >0 ) $sub->addfield('pabl','checked');
             $main->addField('photo',&$sub);
         };
         $r = new Select($db,'update users set watch=watch+1 where id="'.$_GET['id'].'"');
  } else header('Location: /error404');
 ?>

