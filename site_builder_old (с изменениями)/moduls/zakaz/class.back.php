<?php
/**
 *  Классы и функции по обработке записей и выводу сущностей в шаблоны.
 */

include ($inc_path.'/func.back.php');
include ($inc_path.'/classes/class.BF_P.php');
include ($inc_path.'/myfunc.php');


class B_news_ {


 function redactValues(&$_this,&$values) {
    $time = &$values['timed'];
    $date = &$values['date'];
    $values['date'] = @mktime(substr($time,0,2),substr($time,3,2),0,substr($date,3,2),substr($date,0,2),substr($date,6));
    $time_before = &$values['time_before'];
    $date_before = &$values['date_before'];
    $values['date_before'] = @mktime(substr($time_before,0,2),substr($time_before,3,2),0,substr($date_before,3,2),substr($date_before,0,2),substr(          $date_before,6));
    if (empty($values['title']))
            $values['title'] = $values['name'];


        B_::redactValues($_this,$values);
 }

   function deleteRecord(&$_this,$id) {


      // $rs = new Select($_this->db,"delete from zakaz_types where id_zakaz =$id");
       $rs = new Select($_this->db,"delete from likes where id_like=$id and id_type=1");
       $rs = new Select($_this->db,"delete from review where id_what=$id and type=1");
       BF_::deleteRecord($_this,$id);
 }

 function saveRecord(&$_this,&$values,$id) {

        BF_::saveRecord($_this,$values,$id);

        //сохраняем разделы юзера
         $r1 = new Select($_this->db,"delete from zakaz_types where id_zakaz=$id");
         if ($_POST['zakaz_types']>0) {
                 $r1 = new Select($_this->db,"insert into zakaz_types(id_zakaz,id_type) values($id,$_POST[zakaz_types])");

         };
  }



 function addIfcEditRecord(&$_this,&$main,$id) {
    $_FILENAME = BF_::addIfcEditRecord($_this,$main,$id);

    $date= $main->date;
    removeFields($main,$ar = array('about','date'));
    $main->addField('date',date('d.m.Y', $date));
    $main->addField('timed',date('H:i', $date));

    $date_before= $main->date_before;
    removeFields($main,$ar = array('date_before'));
    $main->addField('date_before',date('d.m.Y', $date_before));
    $main->addField('time_before',date('H:i', $date_before));

    addSprav($main,'time',$main->time,'time');
    addSprav($main,'city',$main->id_city,'city');
    addZakazSprav($main,$id,'zakaz_types');


    $main->addField('about','addFCKeditor($GLOBALS["r"],"about");');
    //$main->addField('preview','addFCKeditor($GLOBALS["r"],"preview");');
    addCalend($main,1);
    addCalend($main,2);
    return $_FILENAME;
 }

 function addSub(&$_this,&$sub,&$r,$param) {
         B_::addSub($_this,$sub,$r,$param);
         $sub->addField('date',date('d.m.y, H i', $r->result('date')));
         $r1 = new Select($_this->db,'select * from users where id='.$r->result('id_user'));
         if ($r1->next_row())          $sub->addField('fio', $r1->result('fio'));

         if ($r->result('id_city')>0) {
           $rs = new Select($_this->db,'select * from city where id='.$r->result('id_city'));
           if ($rs->next_row())
             $sub->addField('city',$rs->result('name'));
         };

 }

 function &getParamMngr(&$_this) {
         $param = &BP_::getParamMngr($_this);

         $where='';

         if (isset($_GET['order']))
            $order = " $_GET[order]";

         else  $order ='z.date desc';
         //var_dump($_SERVER);

         if (isset($_GET['pabl1']))
           if ($_GET['pabl1']>=0) $where = " and pabl = ".$_GET['pabl1'];

         if ($_GET['id']>0) $where .= ' and z.id='.$_GET['id'];

         $param['query'] = "select * from zakaz z, city c,users u where c.id=z.id_city and u.id=z.id_user $where order by $order  ";
         //echo $param['query'];
//         $param['order'] = 'date desc';

         return $param;
 }


 // формирует списки для фильтра
 function addManager(&$_this,&$main) {
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

            $fields=array('z.date','c.name','u.fio','z.name','z.pabl');
           foreach ($fields as $val)  {
                $main->addField($val.'_sort',"<a href='$q"."order=$val'><image src='/_images/back/button_down.gif'></a>
                                              <a href='$q"."order=$val desc'><image src='/_images/back/button_up.gif'></a>");
           };

          if ($_GET['pabl1']>=0) $main->addField('pabl',$_GET['pabl1']);
         if ($_GET['id']>0) $main->addField('id',$_GET['id']);
        return B_::addManager($_this,$main);
 }

}

class B_news extends BF_P {

 function redactValues(&$values) {
         B_news_::redactValues($this,$values);
 }

 function addIfcAddRecord(&$main) {
         return B_news_::addIfcAddRecord($this,$main);
 }

 function addIfcEditRecord(&$main,$id) {
         return B_news_::addIfcEditRecord($this,$main,$id);
 }

 function addSub(&$sub,&$r,$param) {
           B_news_::addSub($this,$sub,$r,$param);
 }

   function &getParamMngr() {
          return B_news_::getParamMngr($this);
 }
  function deleteRecord($id) {
         return B_news_::deleteRecord($this,$id);
 }

 function saveRecord(&$values,$id) {
         B_news_::saveRecord($this,$values,$id);
 }
  function addManager(&$main) {
           return B_news_::addManager($this,$main);
 }


}

?>
