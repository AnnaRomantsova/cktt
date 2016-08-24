<?php
 //список всех мастеров
 include('config.php');

 unset($main);
 $FILENAME = $front_html_path.'my.html';

 if (!($_SESSION['user'])>0) {
      header ( "location: http://" . $_SERVER['HTTP_HOST']);
 };

  if ($_POST['del']>0 )
    if ($_POST['id_del']>0)
        del_zakaz($_POST['id_del']);

 $main = &addInCurrentSection($FILENAME);

 $r = new Select($db,"select * from $table_name where id_user=$_SESSION[user] order by date desc");
 if ($r->num_rows()==0) $main->addField('no_sub','');
 while ($r->next_row()) {

        unset($sub);
        $sub = new outTree();
        $r->addFields($sub,$ar=array('id','name','watch'));
        $r->addFieldHTML($sub,'about');
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

         $sub->addfield('cnt_review',cnt_review($r->result('id'),1));
         $sub->addfield('date',make_date_in_days($r->result('date')));
         $sub->addfield('date_before',make_date_in_days($r->result('date_before'),false));
         add_zakaz_types($sub,$r->result('id'));
         $main->addField('sub',&$sub);

 };


 ?>

