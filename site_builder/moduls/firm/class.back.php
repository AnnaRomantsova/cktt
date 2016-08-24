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
    $date = &$values['date1'];
    $values['date_begin'] = @mktime(0,0,0,substr($date,3,2),substr($date,0,2),substr($date,6));
    $date = &$values['date2'];
    $values['date_end'] = @mktime(23,59,59,substr($date,3,2),substr($date,0,2),substr($date,6));
     B_::redactValues($_this,$values);
 }


 function addIfcAddRecord(&$_this,&$main) {
     $_FILENAME = B_::addIfcAddRecord($_this,$main);
     $main->addField('datetime1',date('d.m.Y'));
     $main->addField('datetime2',date('d.m.Y'));
     addCalend($main,1);
	 addCalend($main,2);
	 return $_FILENAME;
 }

 function addIfcEditRecord(&$_this,&$main,$id) {
    $_FILENAME = BF_::addIfcEditRecord($_this,$main,$id);
    $main->addField('datetime1',date('d.m.Y', $main->date_begin));
    $main->addField('datetime2',date('d.m.Y', $main->date_end));
    addCalend($main,1);
	addCalend($main,2);
    return $_FILENAME;
 }


 function addSub(&$_this,&$sub,&$r,$param) {
         B_::addSub($_this,$sub,$r,$param);

        // var_dump($_SERVER) ;
         if ((time() > $r->result('date_end') ) || (time() < $r->result('date_begin') )) $sub->addField('ch','disabled' );
         $sub->addField('date_begin',date('d.m.Y', $r->result('date_begin')));
         $sub->addField('date_end',date('d.m.Y', $r->result('date_end')));
         $r->addFields($sub,$ar=array('inn','email'));

 }



  function &getParamMngr(&$_this) {
         $param = BP_::getParamMngr($_this);

         $where ='';

         if (isset($_GET['order']))
            $order = " order by $_GET[order]";

         //по id
         if ($_GET['id']>0) $where = ' and u.id='.$_GET['id'];
         //$param['order'] = 'id_street,number,fract';


         $param['query'] = "select u.* from firma u where 1=1 $where $order";

         //echo $param['query'];
         //var_dump($_SERVER);
         //$param['order'] = 'fio desc';

         return $param;
 }

 // формирует списки для фильтра
 function addManager(&$_this,&$main) {
         //echo $_GET['id_city'];

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


       //  $r1->unset_();
           $fields=array('name','inn','date_begin','date_end');
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
 /*
 function saveNewRecord(&$values) {
         return B_articles_::saveNewRecord($this,$values);
 }
 */

 function addIfcAddRecord(&$main) {
        return B_articles_::addIfcAddRecord($this,$main);
 }
 function redactValues(&$values) {
         return B_articles_::redactValues($this,$values);
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



}

?>
