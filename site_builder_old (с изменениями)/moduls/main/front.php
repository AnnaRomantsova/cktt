<?php
 //������ ���� ��������
 include('config.php');
 include($inc_path.'/service/class.pager.php');
 unset($main);
 $FILENAME = $front_html_path.'front.html';

 $main = &addInCurrentSection($FILENAME);
 $where = ' ';
 if (isset($_GET['word'])) {

     $word= htmlspecialchars ( addslashes (urldecode($_GET['word'])));
     $word=trim(preg_replace('/\s+/', ' ', $word));
     $words= explode(' ', $word);
     $fields = array('fio','about');

     foreach ( $fields as $f) {
         foreach ( $words as $sw ) {
            $where .= "or $f LIKE '%$sw%'";
         };
     };

     $where = ' and ('.substr($where, 3).')';
     $main->addField('word',$word);
     $link="/word/".urlencode($word);
 };

//var_dump($_POST);

 $limit=16;
 if ($_GET['city']>0) {
 	$where.=' and id_city ='.$_GET['city'];
 	//$limit = 8;
 };

 $id=''; $razdel='';
 foreach ($_POST as $key =>$value) {
    if (strpos($key,'razdel_sub') >0 ){
       //echo $key;
       $id .= ','.substr($key,10);
    };
 };
 if ($_GET['razdel'] > 0 )
       $razdel = " and id_type = $_GET[razdel]";

 //echo $id;
 if ($id !=='') {
         $id=substr($id,1);
         $razdel=" and id_type in ($id)";
 };

 $where  .= $razdel;
 //echo $where;

 $query = "select distinct(id), z.* from users z left join user_types zt on  z.id = zt.id_user where z.is_master=1 $where order by date desc limit $limit";
 $r = new Select($db,$query);
   $i=1;
  while ($r->next_row()) {

        unset($sub);
        $sub = new outTree();
        $r->addFields($sub,$ar=array('id','fio','is_free','watch'));
        $text = htmlspecialchars_decode($r->result('about'));
        $new_text = mb_substr( $text,0,80);
        $about = $new_text;
        if (strlen($text)!==strlen($new_text) ) $about.="...";

        $sub->addfield('about',$about);
        add_user_avatar($sub,$r);
        if ($r->result('is_free') >0) { $sub->addfield('status','�����');  $sub->addfield('bisy','_bisy');}
             else $sub->addfield('status','��������');
        if ($r->result('price') !=='') {
                $r1 = new Select($db,"select * from time where id=".$r->result('time'));
                $r1->next_row();
                if ($r->result('price') > 0 )$sub->addfield('price',$r->result('price').' ���.');
                    else $sub->addfield('price',$r->result('price'));
         }    else  $sub->addfield('price','���� ����������');
         if ($r->result('id_city')>0) {
            $r1 = new Select($db,"select * from city where id=".$r->result('id_city'));
            $r1->next_row();
            $sub->addfield('city',$r1->result('name'));
         };
         $sub->addfield('cnt_review',cnt_review($r->result('id'),2));

         //�������
         if ($i==2) addreklama ($sub,$razdel,'master',$_COOKIE['id_city']);

         add_user_top_types($sub,$r->result('id'),true);
         add_star($sub,$r->result('id'),2);
         $main->addField('sub',&$sub);
         $i++;

  };
  if ($r->num_rows >0)  $main->addField('masters',''); else  $main->addField('no_masters','');

  //�����


 $where ='1';
 if ($pg = Pagers::DA($db,'lenta',$where, 2,$_GET['cp'],'/'.$site->pageid.$link)) {
   $pg->addPAGER($main);
   $r = &$pg->r;

   if ($_GET['word']!=='' && isset($_GET['word'])) {
         $message = "�� ������ ������� ������� �������: ".$r->num_rows();
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
        $main->addField('sub_lenta',&$sub);
        //�������
        if ($i==2) addreklama ($sub,$razdel,'master',$_COOKIE['id_city']);
        $i++;
   };
 }
 if ($r->num_rows >0)  $main->addField('lentas','');

 ?>


