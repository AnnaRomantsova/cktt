<?php

 /**
  * служебные функции - могут зависеть от сайта
  * @package FRONT
  * @version 3.03.argentum - 23.10.2007 10:30
  */

//include($inc_path.'/service/class.output.php');
//include($inc_path.'/service/func.service.php');



  function addSprav(&$main,$table_name,$selected_id,$sub_name,$where ='',$order='name') {
         global $db;
         $r1 = new Select ( $db, "select * from $table_name where 1=1 $where order by $order " );
         while ($r1->next_row() > 0) {
                   unset($sub);
                   $sub = new outTree();
                   $r1->addFields($sub,$ar=array('id','name'));
                   if ($r1->result('id')==$selected_id) $sub->addfield('selected','selected');
                   $main->addField($sub_name,$sub);
         };
  };

  //список разделов заказов
  function addZakazSprav(&$main,$zakaz_id,$sub_name) {
         global $db;

         $r = new Select ( $db, "select * from types_sections t where parent=1 order by sort" );
         while ($r->next_row()) {
           $i=1; $j=1;$first=true;
           $r1 = new Select ( $db, "select * from types t where parent=".$r->result('id')." order by sort" );
           while ($r1->next_row() > 0) {
                   unset($sub);
                   $sub = new outTree();
                   $r2 = new Select ( $db, "select * from zakaz_types zt where zt.id_zakaz=$zakaz_id and id_type=".$r1->result('id'));
                   $r1->addFields($sub,$ar=array('id','name'));
                   if ($r2->next_row()) { $sub->addfield('selected','selected'); $sub->addfield('checked','checked'); };
                   if ($j==3)  {$sub->addfield('tr','</tr><tr>'); $j=0; };
                   if ($first) $sub->addfield('razdel',$r->result('name'));
                    $j++; $first=false;
                   $main->addField($sub_name,$sub);
           };
         };
  };

 //список разделов мастера
  function addUserSprav(&$main,$user_id,$sub_name) {
         global $db;

         $r = new Select ( $db, "select * from types_sections t where parent=1 " );
         $cnt = $r->num_rows();
         $r = new Select ( $db, "select * from types t " );
         $cnt += $r->num_rows();
         $num = ceil($cnt/3);

        // echo $num;
         $r = new Select ( $db, "select * from types_sections t where parent=1 order by sort" );
         $i=1;
         while ($r->next_row()) {
              $j=0;$first=true;
              $r1 = new Select ( $db, "select * from types t where parent=".$r->result('id')." order by sort" );


              while ($r1->next_row() ) {
                   unset($sub);
                   $sub = new outTree();
                   $r2 = new Select ( $db, "select * from user_types zt where zt.id_user=$user_id and id_type=".$r1->result('id'));

                   $r1->addFields($sub,$ar=array('id','name'));

                   if ($r2->next_row()) $sub->addfield('checked','checked');

                   if ($first) $sub->addfield('razdel',$r->result('name'));

                   if ($i==$num)  {$sub->addfield('ul','</ul><ul>'); $i=0; };
                   //if ($i==0)  {$sub->addfield('ul','</ul><ul>');  };
                   if ($j==3)  {$sub->addfield('tr','</tr><tr>'); $j=0; };
                  // echo $i;
                   $i++; $j++; $first=false;
                   $main->addField($sub_name,$sub);
              };
          };
          //echotree($main);
  };

   //список разделов рекламы
  function addRekSprav(&$main,$rek_id,$sub_name) {
         global $db;
         $r = new Select ( $db, "select * from types_sections t where parent=1 order by sort" );
         $i=0;
         while ($r->next_row()) {
              $j=0;$first=true;
              $r1 = new Select ( $db, "select * from types t where parent=".$r->result('id')." order by sort" );
              while ($r1->next_row() ) {
                   unset($sub);
                   $sub = new outTree();
                   $r2 = new Select ( $db, "select * from reklama_types where id_rek=$rek_id and id_type=".$r1->result('id'));
                   if ($first) $sub->addfield('razdel',$r->result('name'));
                   $r1->addFields($sub,$ar=array('id','name'));

                   if ($r2->next_row()) $sub->addfield('checked','checked');

                   if ($i==8)  {$sub->addfield('ul','</ul><ul>'); $i=0; };
                   if ($j==3)  {$sub->addfield('tr','</tr><tr>'); $j=0; };
                  // echo $i;
                   $i++; $j++;$first=false;
                   $main->addField($sub_name,$sub);
              };
         };
  };


   //список разделов мастера
  function addLentaSprav(&$main,$rek_id,$sub_name) {
         global $db;
         $r = new Select ( $db, "select * from types_sections t where parent=1 order by sort" );
         $i=0;
         while ($r->next_row()) {

              $j=0;$first=true;
              $r1 = new Select ( $db, "select * from types t where parent=".$r->result('id')." order by sort" );
              while ($r1->next_row() ) {
                   unset($sub);
                   $sub = new outTree();
                   $r2 = new Select ( $db, "select * from lenta where id=$rek_id and id_type=".$r1->result('id'));

                   $r1->addFields($sub,$ar=array('id','name'));

                   if ($r2->next_row()) {$sub->addfield('checked','checked');  $sub->addfield('selected','selected');};
                   if ($first) $sub->addfield('razdel',$r->result('name'));
                   if ($i==8)  {$sub->addfield('ul','</ul><ul>'); $i=0; };
                   if ($j==3)  {$sub->addfield('tr','</tr><tr>'); $j=0; };
                  // echo $i;
                   $i++; $j++;$first=false;
                   $main->addField($sub_name,$sub);
               };
         };
  };

  //дата по русски
  function make_date($date,$time=false){

        $month=array("января","февраля","марта","апреля","мая","июня","июля","августа","сентября","октября","ноября","декабря");
        $strdate = date("j",$date)." ".$month[((int) date("m",$date)-1)]." ".date('Y',$date);
        if ($time) $strdate .= date(" H:i",$date);
        return $strdate;
  };


  function make_date_in_days($date,$word=true){
        $days = floor( (time()- $date) /86400);
       // echo $days;
        if ($days <5 && $word) {
           if ($days==0) return 'сегодня';
            else if ($days==1) return 'вчера';
              else if ($days==2) return 'позавчера';
               else return "$days дня назад";
        }
        else return make_date($date);
  };

  //разделы заказа
  function add_zakaz_types(&$main,$zakaz_id) {
         global $db;
         $r1 = new Select($db,"select t.id,t.name from zakaz_types zt,types t where zt.id_type = t.id and id_zakaz=$zakaz_id");
         $i=1;
         while ($r1->next_row()) {
            unset($sub);
            $sub = new outTree();
            $r1->addFields($sub,$ar=array('id','name'));
            if ($i < $r1->num_rows()) $sub->addField('zpt',',');
            $main->addField('zakaz_types',$sub);
            $i++;
         };
  };

  //разделы юзера
  function add_user_types(&$main,$user_id) {
         global $db;
         $r1 = new Select($db,"select t.id,t.name from user_types zt,types t where zt.id_type = t.id and id_user=$user_id");
         $i=1;
         while ($r1->next_row()) {
            unset($sub);
            $sub = new outTree();
            $r1->addFields($sub,$ar=array('id','name'));
            if ($i < $r1->num_rows()) $sub->addField('zpt',',');
            $main->addField('user_types',$sub);
            $i++;
         };
  };

  //разделы ленты
  function add_lenta_types(&$main,$lenta_id) {
         global $db;
         $r1 = new Select($db,"select t.id,t.name from lenta l,types t where l.id_type = t.id and l.id=$lenta_id");
         $i=1;
         while ($r1->next_row()) {
            unset($sub);
            $sub = new outTree();
            $r1->addFields($sub,$ar=array('id','name'));
            if ($i < $r1->num_rows()) $sub->addField('zpt',',');
            $main->addField('lenta_types',$sub);
            $i++;
         };
  };

  //мне нравится
  function add_star(&$main,$id_like,$id_type) {
       global $db;
       if (!($_SESSION['user'])>0) {
          return null;
       };
       $flag=false;
       //заказы
       if ($id_type==1) {
           $r = new Select($db,"select * from zakaz where id=$id_like");
           $r->next_row();
           if ($r->result('id_user')==$_SESSION['user']) { $main->addField('edit',''); $flag=true; };
       }
       //мастера
       if ($id_type==2) {
           if ($id_like==$_SESSION['user']) { $main->addField('edit',''); $flag=true; };
       }
       if ($flag==false ) {
               $main->addField('star','');
               //echo "select * from likes where id_user=$_SESSION[user] and id_like = $id_like";
               $r = new Select($db,"select * from likes where id_user=$_SESSION[user] and id_like = $id_like and id_type = $id_type");
               if ($r->next_row()) { $main->addField('active','active'); $main->addField('like','like'); }
      };
  };

  function get_city() {
       global $db;
       if (!($_COOKIE['id_city'] >0)) {
           $r = new Select($db,'select *  from city where first = 1');
           $r->next_row();
           return $r->result('id');
       } else return $_COOKIE['id_city'];
  };

  //список отзывов
  function add_review(&$main,$id_what,$type,$id_user=0) {
  global $db;
         $r1 = new Select($db,"select * from review where id_what = $id_what and type=$type and pabl=1");
         while ($r1->next_row()) {
            unset($sub);
            $sub = new outTree();

            $sub->addField('about',htmlspecialchars_decode($r1->result('about')));
            $sub->addField('date',make_date($r1->result('date'),true));
            $r2 = new Select($db,"select * from users where id = ".$r1->result('id_user'));

            if ($r1->result('id_user') == $id_user) $sub->addField('my_review','');
            $r2->next_row();
            $r2->addFields($sub,$ar=array('id','fio'));
            if ($r2->result('is_master') == 1) $sub->addField('is_master','');
            $sub->addField('idrev',$r1->result('id'));
            $r2->addFieldIMG($sub,'image1');
            $main->addField('review',$sub);
            $i++;
         };
         if ($r1->num_rows == 0) {
             if ($type==1) $main->addField('no_review','Откликов пока нет.');
             if ($type==2) $main->addField('no_review','Отзывов пока нет.');
             if ($type==3) $main->addField('no_review','Отзывов пока нет.');
         };
  };

  //кол-во отзывов
  function cnt_review($id_what,$type) {
         global $db;
         $r1 = new Select($db,"select count(*) as cnt from review where id_what = $id_what and type=$type and pabl=1");
         $r1->next_row();
         return $r1->result('cnt');
  };

  //список отзывов
  function add_user_info(&$main,$id_user,$sub_name) {
        global $db;

        unset($user_sub);
        $user_sub = new outTree();
        $r1 = new Select($db,"select * from users where id=$id_user");
        $r1->next_row();
        $r1->addFields($user_sub,$ar=array('id','fio'));

        if ($r1->result('is_master') == 1) $user_sub->addField('is_master','3');
        //$r1->addFieldsIMG($user_sub,$ar=array('image1'));
        add_user_avatar($user_sub,$r1);

        $main->addField($sub_name,$user_sub); //echotree($main);
  };

    //автар мастера
  function add_user_avatar(&$main,$r) {
        global $db;
        $r->addFieldsIMG($main,$ar=array('image1'));

        //echo $main->image1;
        if (isset($main->not_image1)){
                $tmp = new outTree();
                $tmp->addField('src', '/i/photo-none.jpg' );
                $main->addField( 'image1',$tmp);

        } else $main->addField( 'is_avatar','');
  };

     //автар мастера
  function is_master($user) {
        global $db;
        $r1 = new Select($db,"select * from users where id=$user");
        $r1->next_row();
        return $r1->result('is_master');
  };

  //права оставлять коментарии на конкретную запись
  function check_review_rights(&$main,$user_id,$type,$id_what=0){
       global $db;
       $rev=true;
       //echo $user_id;
       if (!$user_id>0) {
           $rev = false;
           if ($type==2 || $type==3)
              $err_message='Комментарии могут оставлять только зарегистрированные пользователи.';
           else
              $err_message='Предлагать свои услуги могут только зарегистрированные пользователи.';
       //заказы
       } else if ($type==1) {
            //проверка на свой заказ
            $r=new Select($db,"select * from zakaz where id=$id_what");
            $r->next_row();
            //свой заказ
            if ($r->result('id_user') == $user_id)
                $rev = false;
            //чужой
            else {
            	if (is_master($user_id) ==false) {
            	  $rev = false;
            	  $err_message='Если Вы хотите предложить свои услуги, то необходимо сначала в личном кабинете добавить и заполнить профиль мастера. После того как профиль мастера будет одобрен модератором Вы сможете откликаться на заказы.';
                } else {

                  $err_message='Если Вы хотите предложить свои услуги, то вам необходимо откликнуться на этот заказ. Заказчик выберет исполнителя самостоятельно и свяжется с ним по указанным в профиле контактам.';
                };
            };
            if ($id_what >0){
              $r=new Select($db,"select count(*) as cnt from review where id_user=$user_id and type=$type and id_what=$id_what");
              $r->next_row();
              if ($r->result('cnt') >0 ) {$rev = false; $err_message='Вы уже откликнулись на этот заказ.';};
            };

            //старый заказ
            $r1 = new Select($db,"select * from zakaz where id=$id_what");
            $r1->next_row();
            if ($r1->result('date_before') < time()) {$rev = false; $err_message='На этот заказ нельзя откликнуться т.к. он не актуален.';};


       //миастера
       } else if ($type==2)
            if ($user_id == $id_what) {$rev = false; };

       $main->addField( 'err_mess',$err_message);
       if ($rev==true) $main->addField( 'rev_true','');
          else {$main->addField( 'rev_false',''); };

  };

  function addreklama (&$main,$id_type,$modul,$id_city) {
       global $db;
       if ($id_city>0) {
               $tables = ',reklama_city rc';
               $where = ' and rc.id_reklama = r.id and rc.id_city='.$id_city;
       };
       if ($id_type!=='') {
               $tables .= ',reklama_types rt';
               $where .= $id_type;
       };
       //echo "select *  from rek r $tables where pabl=1 and $modul=1 $where ORDER BY RAND() LIMIT 1";
       $r = new Select($db,"select *  from rek r $tables where pabl=1 and $modul=1 $where ORDER BY RAND() LIMIT 1");
       if ($r->next_row())  {
               unset($sub);
               $sub = new outTree();
               $r->addFields($sub,$ar=array('name1','name2','name3','text1','text2','text3','tel1','tel2','tel3','link1','link2','link3'));
               $main->addField( 'reklama',$sub);
       };
  };

  function del_zakaz ($id) {
        global $db;
        $r1 = new Select($db,"delete from likes where id_like = $id and id_type=1 ");
        $r1 = new Select($db,"delete from zakaz_types where id_zakaz = $id");
        $r1 = new Select($db,"delete from zakaz where id = $id");

 };
?>
