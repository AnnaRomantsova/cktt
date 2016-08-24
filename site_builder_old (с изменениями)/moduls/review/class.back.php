<?php
/**
 *  Классы и функции по обработке записей и выводу сущностей в шаблоны.
 */

include ($inc_path.'/func.back.php');
include ($inc_path.'/myfunc.php');
include ($inc_path.'/classes/class.BF_P.php');
include ($inc_path.'/img.php');

class B_articles_ {



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
         if ($r->result('type') == 3) {
                  $sub->addField('type','Лента');
                  $ri = new Select($_this->db,"select * from lenta where id=".$r->result('id_what'));
           if ($ri->next_row()) $sub->addField('info',"<a target='blank' href='/lenta_one/id/".$r->result('id_what')."'>".$ri->result('name')."</a>");
                 // echo $ri->result('name');
         }
         else  {
                  $sub->addField('type','Мастер');
                  $ri = new Select($_this->db,"select * from users where id=".$r->result('id_what'));
         if ($ri->next_row()) $sub->addField('info',"<a target='blank' href='/master_profile/id/".$r->result('id_what')."'>".$ri->result('fio')."</a>");
         };
         $sub->addField('date',date('d.m.y H:i', $r->result('date')));

         $ri = new Select($_this->db,"select * from users where id=".$r->result('id_user'));
         //if ($ri->next_row()) $sub->addField('user',$ri->result('fio'));
         if ($ri->next_row()) $sub->addField('user',"<a target='blank' href='/master_profile/id/".$ri->result('id')."'>".$ri->result('fio')."</a>");
         $r->addFields($sub,$ar=array('about'));
 }



  function &getParamMngr(&$_this) {
         $param = BP_::getParamMngr($_this);
  //var_dump($_GET);
         $where ='';

         if (isset($_GET['order'])) $order = " $_GET[order]";     else  $order ='r.date desc';

         //по статусу
         if (isset($_GET['type']))
            if ($_GET['type']>=2) $where .= ' and r.type='.$_GET['type'];


         //по id
         if ($_GET['id']>0) $where = ' and u.id='.$_GET['id'];
         //$param['order'] = 'id_street,number,fract';

         $param['query'] = "select  u.id as id_user, r.* from review r,users u where r.id_user = u.id and type in (2,3)
                         $where order by $order";
         //echo $param['query'];

         $param['order'] = 'id_city';

         return $param;
 }

 // формирует списки для фильтра
 function addManager(&$_this,&$main) {
        if ($_GET['type']>=2) $main->addField('type',$_GET['type']);

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

            $fields=array('r.date','type','u.fio','r.about','r.pabl');
           foreach ($fields as $val)  {
                $main->addField($val.'_sort',"<a href='$q"."order=$val'><image src='/_images/back/button_down.gif'></a>
                                              <a href='$q"."order=$val desc'><image src='/_images/back/button_up.gif'></a>");
           };

        return B_::addManager($_this,$main);
 }

}

class B_articles extends BF_P {


 function addIfcEditRecord(&$main,$id) {
         return B_articles_::addIfcEditRecord($this,$main,$id);
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


 function saveRecord(&$values,$id) {
         B_articles_::saveRecord($this,$values,$id);
 }
}

?>
