<?php
 //список всех мастеров
 include('config.php');
 include($inc_path.'/service/class.pager.php');
 unset($main);
 $FILENAME = $front_html_path.'front.html';

 $main = &addInCurrentSection($FILENAME);

 if ($_POST['del']>0 )
    if ($_POST['id_del']>0)
        del_zakaz($_POST['id_del']);



 //var_dump($_POST);
 $where = ' ';
 if (isset($_GET['word'])) {

     $word= htmlspecialchars ( addslashes (urldecode($_GET['word'])));
     $word=trim(preg_replace('/\s+/', ' ', $word));
     $words= explode(' ', $word);
     $fields = array('name','about');
     foreach ( $fields as $f) {
         foreach ( $words as $sw ) {
            $where .= "or $f LIKE '%$sw%'";
         };
     };

     $where = ' and ('.substr($where, 3).')';
     //echo $where;
     $main->addField('word',$word);
     $link="/word/".urlencode($word);
 } else $main->addField('no_word','');

 if ($_COOKIE['id_city']>0) $where.=' and id_city ='.$_COOKIE['id_city'];


 $id=''; $razdel='';
 foreach ($_POST as $key =>$value) {
    if (substr($key,0,10)=='razdel_sub') {
       $id .= ','.substr($key,10);
    };
 };
 if ($_GET['razdel'] > 0 )
       $razdel = " and id_type = $_GET[razdel]";

 if ($id !=='') {
         $id=substr($id,1);
         $razdel=" and id_type in ($id)";
 };

 $where  .= $razdel;
 //echo $where;
 $query = "select distinct(id), z.* from zakaz z left join zakaz_types zt on z.id = zt.id_zakaz where z.pabl=1  $where order by date desc";


 if ($pg = Pagers::DA($db,'','', $GLOBALS[$modulName.'_fcount'],$_GET['cp'],'/'.$site->pageid.$link,null,null,$query)) {
   $pg->addPAGER($main);
   $r = &$pg->r;

   if ($_GET['word']!=='' && isset($_GET['word'])) {
         $message = "ѕо вашему запросу найдено записей: ".$r->num_rows();
         if ($message!=='') $main->addField('message',$message);
   };
   $i=1;
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
         }    else  $sub->addfield('price','÷ена договорна€');
         if ($r->result('id_city')>0) {
            $r1 = new Select($db,"select * from city where id=".$r->result('id_city'));
            $r1->next_row();
            $sub->addfield('city',$r1->result('name'));
         };
         $sub->addfield('cnt_review',cnt_review($r->result('id'),1));
         add_zakaz_types($sub,$r->result('id'));

         //реклама
         if ($i==2) addreklama ($sub,$razdel,'zakaz',$_COOKIE['id_city']);

         add_star($sub,$r->result('id'),1);

        // echo make_date($r->result('date'));
         $sub->addfield('date',make_date_in_days($r->result('date')));
         $sub->addfield('date_before',make_date_in_days($r->result('date_before'),false));
         $main->addField('sub',&$sub);
         $i++;
   };
 } else  $main->addField('no_sub','');


 ?>

