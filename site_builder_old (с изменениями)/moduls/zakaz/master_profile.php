<?php
 //список всех мастеров
 include('config.php');

 unset($main);
 $FILENAME = $front_html_path.'master_profile.html';

 $main = &addInCurrentSection($FILENAME);

 if (!$_GET['id'] >0) header('Location:/');

 $r = new Select($db,'select * from users where id="'.$_GET['id'].'"');
 if ($r->next_row()) {
         $r->addFields($main,$ar=array('id','email','fio','skipe','link','adress','tel','about','price','experience','grafic','watch'));
         $r->addFieldsIMG($main,$ar=array('image1'));
         if ($r->result('is_free') >0) $main->addfield('bisy',''); else  $main->addfield('free','');

         if ($r->result('is_free') >0) $main->addfield('status','занят');
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

         //фотогалерея
         $ri = new Select($db,"select * from galery where id_user=$_GET[id] order by id");
         if ($ri->num_rows() ==0) $main->addField('no_photo','');
         while ($ri->next_row()) {
             unset($sub);
             $sub = new outTree();
             $ri->addFields($sub,$ar=array('id','about'));
             $ri->addFieldsIMG($sub,$ar=array('image1'));
             if ($ri->result('pabl') >0 ) $sub->addfield('pabl','checked');
             $main->addField('photo',&$sub);
         };
         $r = new Select($db,'update users set watch=watch+1 where id="'.$_GET['id'].'"');
  };
 ?>

