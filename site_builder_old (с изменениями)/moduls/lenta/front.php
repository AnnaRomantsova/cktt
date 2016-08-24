<?php
 //список всех мастеров
 include('config.php');
 include($inc_path.'/service/class.pager.php');
 unset($main);
 $FILENAME = $front_html_path.'front.html';

 $main = &addInCurrentSection($FILENAME);
 //$where = 'z.id_city='.$_COOKIE['id_city'].' and zt.id_zakaz = z.id '.$razdel;
 $where = 'pabl = 1';


 if (isset($_GET['word'])) {

     $word= htmlspecialchars ( addslashes (urldecode($_GET['word'])));
     $word=trim(preg_replace('/\s+/', ' ', $word));
     $words= explode(' ', $word);
     $fields = array('name','about');

     foreach ( $fields as $f) {
         foreach ( $words as $sw ) {
            $wh .= "or $f LIKE '%$sw%'";
         };
     };

     $where .= ' and ('.substr($wh, 3).')';
     $main->addField('word',$word);
     $link="/word/".urlencode($word);
 } else $main->addField('no_word','');

 //$id_city = get_city();

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

 $where  .= $razdel;
 //echo $where;
 //$query = "select distinct(id), z.* from zakaz z ,zakaz_types zt where z.id_city=$id_city and z.id = zt.id_zakaz and $where order by date desc";


 if ($pg = Pagers::DA($db,'lenta',$where, $GLOBALS[$modulName.'_fcount'],$_GET['cp'],'/'.$site->pageid.$link)) {
   $pg->addPAGER($main);
   $r = &$pg->r;

   if ($_GET['word']!=='' && isset($_GET['word'])) {
         $message = "По вашему запросу найдено записей: ".$r->num_rows();
         if ($message!=='') $main->addField('message',$message);
   };
   $i=1;
   while ($r->next_row()) {

        unset($sub);
        $sub = new outTree();
        $r->addFields($sub,$ar=array('id','name','preview','watch'));
        $sub->addfield('cnt_review',cnt_review($r->result('id'),3));
        //$r->addFieldHTML($sub,'about');
        add_star($sub,$r->result('id'),3);
        add_lenta_types($sub,$r->result('id'));
        $r->addFieldsIMG($sub,$ar=array('image1'));
        $sub->addfield('date',make_date_in_days($r->result('date')));
        // echo make_date($r->result('date'));
         //$sub->addfield('date',make_date_in_days($r->result('date')));
        $main->addField('sub',&$sub);
        //реклама
        if ($i==2) addreklama ($sub,$razdel,'master',$_COOKIE['id_city']);
        $i++;
   };
 } else  $main->addField('no_sub','');

 $main->addField('site',$_SERVER['HTTP_HOST']);
   //$main->addField('site',&$sub);
 ?>

