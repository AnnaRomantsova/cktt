<?php

 //список всех мастеров
 include('config.php');

 unset($main);
 $FILENAME = $front_html_path.'good.html';

 if (!($_SESSION['user'])>0) {
      header ( "location: http://" . $_SERVER['HTTP_HOST']);
 };

 $main = &addInCurrentSection($FILENAME);

 $r = new Select($db,"select * from $table_name z, likes l where l.id_user=$_SESSION[user] and l.id_type=3 and l.id_like=z.id order by date desc");
 if ($r->num_rows()==0) $main->addField('no_sub','');
 while ($r->next_row()) {

        unset($sub);
        $sub = new outTree();
        $r->addFields($sub,$ar=array('id','name','preview','watch','about'));
        $sub->addfield('cnt_review',cnt_review($r->result('id'),3));
        $r->addFieldsIMG($sub,$ar=array('image1'));
        add_star($sub,$r->result('id'),3);
        add_lenta_types($sub,$r->result('id'));
         $sub->addfield('date',make_date_in_days($r->result('date')));  
        // echo make_date($r->result('date'));
         //$sub->addfield('date',make_date_in_days($r->result('date')));
        $main->addField('sub',&$sub);


 };

 $main->addField('site',$_SERVER['HTTP_HOST']);


 ?>

