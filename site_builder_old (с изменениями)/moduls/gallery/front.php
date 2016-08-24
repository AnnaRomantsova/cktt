<?php

 include('config.php');
 include($inc_path.'/service/class.pager.php');

 unset($main);
 $FILENAME = $front_html_path.'front.html';

 $main = &addInCurrentSection($FILENAME);


 if ($_COOKIE['id_city']>0) $city=' and id_city ='.$_COOKIE['id_city'];

 $id=''; $razdel='';
 foreach ($_POST as $key =>$value) {
    if (strpos($key,'razdel_sub') >= 0 ){
       $id .= ','.substr($key,10);
    };
 };
 if ($_GET['razdel'] > 0 )
       $razdel = " and id_type = $_GET[razdel]";

 if ($id !=='') {
         $id=substr($id,1);
         $razdel=" and id_type in ($id)";
 };

 //$where  = $razdel;
 //echo $where;
 $query = "select distinct g.id as id,g.* from galery g left join users u on (g.id_user=u.id ) left join user_types ut on (ut.id_user=u.id)
             where g.pabl=1   $city and ut.id_user=u.id $razdel order by g.id_user";

 // echo $query;
 if ($pg = Pagers::DA($db,'','', $GLOBALS[$modulName.'_fcount'],$_GET['cp'],'/'.$site->pageid.$link,null,null,$query)) {
   $pg->addPAGER($main);
   $r = &$pg->r;

 while ($r->next_row()) {

        unset($sub);
        $sub = new outTree();
        $r->addFields($sub,$ar=array('id'));
        if ($r->result('about') !=='')  $sub->addField('about',$r->result('about'));

        $r1 = new Select($db,"select * from users where id=".$r->result('id_user'));
        $r1->next_row();
        $sub->addField('user_id',$r1->result('id'));
        $sub->addField('fio',$r1->result('fio'));

        //if ($r->result('about') !=='' )
        //   $r->addField($sub,$r->result('about'));
        $r->addFieldIMG($sub,'image1');
        $r->addFieldIMG($sub,'image2');

        $main->addField('sub',&$sub);

   };
 } else  $main->addField('no_sub','');



 ?>
