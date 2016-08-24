<?php
/**
 *  Классы и функции по обработке записей и выводу сущностей в шаблоны.
 */

include ($inc_path.'/func.back.php');
include ($inc_path.'/classes/class.BF_P.php');
include ($inc_path.'/img.php');
include ($inc_path.'/myfunc.php');

class B_news_ {

  function saveRecord(&$_this,&$values,$id) {

        BF_::saveRecord($_this,$values,$id);

        $r1 = new Select($_this->db,'select * from '.$this->table.' where id='.$id);
        //echo 'select * from '.$this->table.' where id='.$id;
        if ($r1->next_row()) {
            image_resize_admin( $r1->result('image1'),182,182);
        };

 }

 function redactValues(&$_this,&$values) {
    $time = &$values['time'];
    $date = &$values['date'];
    $values['date'] = @mktime(substr($time,0,2),substr($time,3,2),0,substr($date,3,2),substr($date,0,2),substr($date,6));
    if (empty($values['title']))
    $values['title'] = $values['name'];
    B_::redactValues($_this,$values);
 }

 function addIfcAddRecord(&$_this,&$main) {
    $_FILENAME = B_::addIfcAddRecord($_this,$main);
    $main->addField('date',date('d.m.Y'));
   // $main->addField('time1',date('H:i'));
    addSprav($main,'week',0,'week','','id');
    // addLentaSprav($main,0,'razdel');
   // $main->addField('about',"loadFCKeditor('about','');");
       // $main->addField('preview',"loadFCKeditor('preview','');");
    addCalend($main,1);
    return $_FILENAME;
 }

 function addIfcEditRecord(&$_this,&$main,$id) {
    $_FILENAME = BF_::addIfcEditRecord($_this,$main,$id);
    $date = $main->date;
    removeFields($main,$ar = array('date'));
    $main->addField('date',date('d.m.Y', $date));
    $main->addField('time',date('H:i', $date));
    addSprav($main,'week',$main->week,'week','','id');
   // addLentaSprav($main,$id,'razdel');
   // $main->addField('about','addFCKeditor($GLOBALS["r"],"about");');
    //$main->addField('preview','addFCKeditor($GLOBALS["r"],"preview");');
    addCalend($main,1);
    return $_FILENAME;
 }

 function addSub(&$_this,&$sub,&$r,$param) {
         B_::addSub($_this,$sub,$r,$param);
           $sub->addField('date',date('d.m.y, H i', $r->result('date')));
 }

 function &getParamMngr(&$_this) {
          $param = &BP_::getParamMngr($_this);
         if (isset($_GET['order'])) $param['order'] = " $_GET[order]";  else  $param['order'] ='date desc';


        // $param['order'] = 'date desc desc';
          //$param['where'] = ' ntype=1';
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

            $fields=array('date','name','pabl');
           foreach ($fields as $val)  {
                $main->addField($val.'_sort',"<a href='$q"."order=$val'><image src='/_images/back/button_down.gif'></a>
                                              <a href='$q"."order=$val desc'><image src='/_images/back/button_up.gif'></a>");
           };

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

 function saveRecord(&$values,$id) {
         B_news_::saveRecord($this,$values,$id);
 }

/*

  function addManager(&$main) {
           return B_news_::addManager($this,$main);
 }
*/
}

?>
