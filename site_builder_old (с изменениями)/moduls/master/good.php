<?php

 //список всех мастеров
 include('config.php');

 unset($main);
 $FILENAME = $front_html_path.'good.html';

 if (!($_SESSION['user'])>0) {
      header ( "location: http://" . $_SERVER['HTTP_HOST']);
 };

 $main = &addInCurrentSection($FILENAME);

 $r = new Select($db,"select * from $table_name z, likes l where l.id_user=$_SESSION[user] and l.id_type=2 and l.id_like=z.id");
 if ($r->num_rows()==0) $main->addField('no_sub','');
 while ($r->next_row()) {

        unset($sub);
        $sub = new outTree();
        $r->addFields($sub,$ar=array('id','fio','is_free','about','watch'));

        if ($r->result('is_free') >0) { $sub->addfield('status','занят');  $sub->addfield('bisy','_bisy');}
             else $sub->addfield('status','свободен');
        if ($r->result('price')!=='') {
                $r1 = new Select($db,"select * from time where id=".$r->result('time'));
                $r1->next_row();
               if ($r->result('price') > 0 )$sub->addfield('price',$r->result('price').' руб.');
                    else $sub->addfield('price',$r->result('price'));
         }    else  $sub->addfield('price','Цена договорная');
         if ($r->result('id_city')>0) {
            $r1 = new Select($db,"select * from city where id=".$r->result('id_city'));
            $r1->next_row();
            $sub->addfield('city',$r1->result('name'));
         };
         $sub->addfield('cnt_review',cnt_review($r->result('id'),2));
         add_user_avatar($sub,$r);
         add_user_types($sub,$r->result('id'));
         add_star($sub,$r->result('id'),2);
         $main->addField('sub',&$sub);

 };
 ?>


