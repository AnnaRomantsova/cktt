<?php
/**
 *  Классы и функции по обработке записей и выводу сущностей в шаблоны.
 */

include ($inc_path.'/func.back.php');
include ($inc_path.'/myfunc.php');
include ($inc_path.'/classes/class.BFO_OP.php');
include ($inc_path.'/img.php');

class B_articles_ {

 function redactValues(&$_this,&$values) {
      if(!empty($values['pass_text']))
              $values['pass'] = md5( $values['pass_text'] );
      else unset($values['pass']);
      if ($values['is_master'] >0) $values['pre_master']=0;
     // var_dump($values);
     // die;

      B_::redactValues($_this,$values);
 }

 function saveRecord(&$_this,&$values,$id) {

        BF_::saveRecord($_this,$values,$id);

        foreach ( $values as $key => $value) {
         //echo substr($key,0,15);
               if (substr($key,0,15) == 'portfolio_about') {
                   $idf=substr($key,15);
                   if ($idf>0) $r = new Select($_this->db,"update galery set about='$value' where id=$idf");
                   $r = new Select($_this->db,"update galery set pabl='".$_POST['portfolio_pabl'.$id]."' where id=$id");
               };

               if (substr($key,0,13) == 'portfolio_del') {
                   $idf=substr($key,13);
                   //$_this->deleteRecord($idf);
                   $_this->db->query('select image1,image2 from galery where id_user="'.$id.'"');
                   @unlink($GLOBALS['document_root'].rawurldecode($_this->db->result(0,0)));
                   @unlink($GLOBALS['document_root'].rawurldecode($_this->db->result(1,0)));
                   $r1 = new Select($_this->db,'delete from galery where id='.$idf);
               };
         };
        // echo ($values['is_master']);

        //сохраняем разделы юзера
         $r1 = new Select($_this->db,"delete from user_types where id_user=$id");
         foreach ( $_POST as $key => $value) {
        // echo $key;
              if (strpos($key,'h_razdel')>0) {
                 $idr = substr($key,9);
                 $r1 = new Select($_this->db,"insert into user_types(id_user,id_type) values($id,$idr)");
              };
         };

         //автарка
         $r1 = new Select($_this->db,'select * from '.$this->table.' where id='.$id);

         if ($r1->next_row()) {
            image_resize_admin($r1->result('image1'),52,52);
         };
  }

  function deleteRecord(&$_this,$id) {


       $rs = new Select($_this->db,"delete from zakaz_types where id_zakaz in (select id from zakaz where id_user=$id)");
      $rs = new Select($_this->db,"delete from zakaz where id_user=$id");
      $rs = new Select($_this->db,"delete from galery where id_user=$id");
       $rs = new Select($_this->db,"delete from likes where id_user=$id");
       $rs = new Select($_this->db,"delete from messages where user_from=$id or user_to=$id");
       $rs = new Select($_this->db,"delete from user_types where id_user=$id");
       BF_::deleteRecord($_this,$id);
 }


 function addIfcEditRecord(&$_this,&$main,$id) {
    $_FILENAME = BF_::addIfcEditRecord($_this,$main,$id);

    addSprav($main,'city',$main->id_city,'city');
    addSprav($main,'time',$main->time,'time');

    addUserSprav($main,$id,'user_types');


    //фотогалерея
   $ri = new Select($_this->db,"select * from galery where  id_user=$id order by id");
   if ($ri->num_rows() ==0) $main->addField('no_photo','');
    $i=1;
   while ($ri->next_row()) {
       unset($sub);
       $sub = new outTree();
       $ri->addFields($sub,$ar=array('id','about'));
       $ri->addFieldsIMG($sub,$ar=array('image1'));
       if ($ri->result('pabl') >0 ) $sub->addfield('pabl','checked');
       $main->addField('photo',&$sub);
       if ($i==3)  {$sub->addfield('tr','</tr><tr>'); $i=0; };
                  // echo $i;
                   $i++;
   };
   // echo $main->act_category;

    //$rs->unset_();
    //$main->addField('is_parent','1');
         //$main->addField('date',date('d.m.Y', $main->datetime));
        //addCalend($main,1);
     //   echotree($main);
    return $_FILENAME;
 }


 function addSub(&$_this,&$sub,&$r,$param) {
         B_::addSub($_this,$sub,$r,$param);

         if ($r->result('id_city')>0) {
           $rs = new Select($_this->db,'select * from city where id='.$r->result('id_city'));
           if ($rs->next_row())
             $sub->addField('city',$rs->result('name'));
         };
         if ($r->result('is_master') == 1) $sub->addField('user_type','Мастер');
            else   $sub->addField('user_type','Заказчик');
        if ($r->result('pre_master') == 1) $sub->addField('pre_master','Да');
        // var_dump($_SERVER) ;
         $sub->addField('date',date('d.m.y', $r->result('date')));
         $sub->addField('date_visit',date('d.m.y', $r->result('date_visit')));
         $r->addFields($sub,$ar=array('fio','email'));

 }



  function &getParamMngr(&$_this) {
         $param = BP_::getParamMngr($_this);

         $where ='1=1';

         if (isset($_GET['order']))
            $order = " order by $_GET[order]";
            else $order = " order by date desc";


         //по статусу
         if (isset($_GET['pay_active']))
           if ($_GET['pay_active']>=0) $where .= ' and u.pay_active='.$_GET['pay_active'];

         //город
         if ($_GET['id_city'] > 0) $where .= ' and c.id= '.$_GET['id_city'];

         //по статусу
         if (isset($_GET['is_master']))
            if ($_GET['is_master']>=0) $where .= ' and u.is_master='.$_GET['is_master'];

         //по id
         if ($_GET['id']>0) $where = ' and u.id='.$_GET['id'];
         //$param['order'] = 'id_street,number,fract';

         //if ($_GET['id_city'] > 0)
            $param['query'] = "select u.id as id,  u.* from users u left join  city c on (c.id=u.id_city) where $where $order";
            // echo $param['query'];
         //else
            //$param['query'] = "select u.* from users u where   $where $order";

      //
         //var_dump($_SERVER);
         //$param['order'] = 'fio desc';

         return $param;
 }

 // формирует списки для фильтра
 function addManager(&$_this,&$main) {
         //echo $_GET['id_city'];
         //города
         $r1 = new Select($_this->db,'select * from city order by name');
         while ($r1->next_row()) {
           unset($str_sub);
           $str_sub = new outTree();
           $r1->addFields($str_sub,$ar=array('id','name'));
           if ($_GET['id_city'] == $r1->result('id'))
              $str_sub->addField('selected','selected');
           $main->addField('city_sub',&$str_sub);
         };

          if ($_GET['pre_master']>=0) $main->addField('pre_master',$_GET['pre_master']);
          if ($_GET['pay_active']>=0) $main->addField('pay_active',$_GET['pay_active']);
          if ($_GET['is_master']>=0) $main->addField('is_master',$_GET['is_master']);
          if ($_GET['id']>0) $main->addField('id',$_GET['id']);

          //сортировки
          $q='?';
          $arr = explode('&',$_SERVER['argv'][0]);
          //var_dump($arr);
          foreach ($arr as $arg) {
           if ($arg!=='') {
            $str = explode ('=',$arg);
              if ($str[0] !=='order')
               $q.="&$arg";
           };
          };
          if ($q!=='?')  $q.='&';


         $r1->unset_();
           $fields=array('fio','email','c.name','is_master','date','date_visit','pre_master');
           foreach ($fields as $val)  {
                $main->addField($val.'_sort',"<a href='$q"."order=$val'><image src='/_images/back/button_down.gif'></a>
                                              <a href='$q"."order=$val desc'><image src='/_images/back/button_up.gif'></a>");
           };
          // echotree($main);

        return B_::addManager($_this,$main);
 }

}

class B_articles extends BFO_OP {


 function addIfcEditRecord(&$main,$id) {
         return B_articles_::addIfcEditRecord($this,$main,$id);
 }

 function redactValues(&$values) {
         B_articles_::redactValues($this,$values);
 }

 function addSub(&$sub,&$r,$param) {
           B_articles_::addSub($this,$sub,$r,$param);
 }

  function &getParamMngr() {
          return B_articles_::getParamMngr($this);
 }

 function addManager(&$main) {
           return B_articles_::addManager($this,$main);
 }

  function deleteRecord($id) {
          return B_articles_::deleteRecord($this,$id);
 }

 function saveRecord(&$values,$id) {
         B_articles_::saveRecord($this,$values,$id);
 }
}

?>
