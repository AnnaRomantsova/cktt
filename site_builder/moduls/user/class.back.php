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
      if  (!($values['date'] >0)) $values['date'] = time();
      B_::redactValues($_this,$values);
 }


  function deleteRecord(&$_this,$id) {

     /*
       $rs = new Select($_this->db,"delete from zakaz_types where id_zakaz in (select id from zakaz where id_user=$id)");
      $rs = new Select($_this->db,"delete from zakaz where id_user=$id");
      $rs = new Select($_this->db,"delete from galery where id_user=$id");
       $rs = new Select($_this->db,"delete from likes where id_user=$id");
       $rs = new Select($_this->db,"delete from messages where user_from=$id or user_to=$id");
       $rs = new Select($_this->db,"delete from user_types where id_user=$id");
       */
       BF_::deleteRecord($_this,$id);
 }

  function saveNewRecord(&$_this,&$values){

    //if(!empty($values['pass_text']))
     $values['pass'] = md5( $values['pass_text'] );
    //  else unset($values['pass']);
     $values['date'] = time();
  // var_dump($values);
   //die;
    return B_::saveNewRecord($_this,$values);
 }

 function addIfcEditRecord(&$_this,&$main,$id) {
    $_FILENAME = BF_::addIfcEditRecord($_this,$main,$id);
    return $_FILENAME;
 }


 function addSub(&$_this,&$sub,&$r,$param) {
         B_::addSub($_this,$sub,$r,$param);

        // var_dump($_SERVER) ;
         $sub->addField('date',date('d.m.y', $r->result('date')));
         $sub->addField('date_visit',date('d.m.y', $r->result('date_visit')));
         $r->addFields($sub,$ar=array('fio','email'));

 }



  function &getParamMngr(&$_this) {
         $param = BP_::getParamMngr($_this);

         $where ='';

         if (isset($_GET['order']))
            $order = " order by $_GET[order]";

         //по id
         if ($_GET['id']>0) $where = ' and u.id='.$_GET['id'];
         //$param['order'] = 'id_street,number,fract';


         $param['query'] = "select u.* from users u where 1=1 $where $order";

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
           $fields=array('fio','email','date','date_visit');
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

  function deleteRecord($id) {
          return B_articles_::deleteRecord($this,$id);
 }


}

?>
