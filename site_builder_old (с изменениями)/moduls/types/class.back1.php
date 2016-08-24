<?php
/**
 *  Классы и функции по обработке записей и выводу сущностей в шаблоны.
 */

include ($inc_path.'/func.back.php');
include ($inc_path.'/classes/class.BO.php');
include ($inc_path.'/delete.php');

class B_news_ {


 function deleteRecord(&$_this,$id) {
        $rs = new Select($_this->db,"select * from types where parent = $id");
        while ($rs->next_row()) {
              $r1 = new Select($_this->db,"delete from zakaz_types where id_type=".$rs->result('id'));
              $r1 = new Select($_this->db,"delete from user_types where id_type=".$rs->result('id'));

        };
        $rs = new Select($_this->db,"delete from zakaz_types where id_type=".$id);
        $rs = new Select($_this->db,"delete from user_types where id_type=".$id);


       B_::deleteRecord($_this,$id);
 }

 function redactValues(&$_this,&$values) {
       //var_dump ($_SERVER);
      if ($_GET['id']>0) $values['parent'] = $_GET['id'];  else $values['parent'] = 0;

      B_::redactValues($_this,$values);
 }

 function addIfcAddRecord(&$_this,&$main) {
         $_FILENAME = B_::addIfcAddRecord($_this,$main);
         //var_dump ($_SERVER);
         //echo $_GET['id'];
         if ($_GET['id']>0) $main->addField('link1',$_GET['id']);
    return $_FILENAME;
 }

 function addIfcEditRecord(&$_this,&$main,$id) {
    $_FILENAME = BF_::addIfcEditRecord($_this,$main,$id);
    return $_FILENAME;
 }

 function addSub(&$_this,&$sub,&$r,$param) {
         B_::addSub($_this,$sub,$r,$param);
         if ($r->result('parent')==0) $sub->addField('link','44');

         // $sub->addField('link','');
 }

 function &getParamMngr(&$_this) {
         $param = &B_::getParamMngr($_this);
         //по id
         if ($_GET['id']>0) $param['where'] = ' parent='.$_GET['id']; else $param['where'] = ' parent=0';
         $param['order'] = 'sort';

         return $param;
 }
 // формирует списки для фильтра
 function addManager(&$_this,&$main) {

        if ($_GET['id']>0) {$main->addField('link1',$_GET['id']);}

        return B_::addManager($_this,$main);
 }

}

class B_news extends BO {

 function redactValues(&$values) {
         B_news_::redactValues($this,$values);
 }

 function addIfcAddRecord(&$main) {
         return B_news_::addIfcAddRecord($this,$main);
 }
 /*
 function addIfcEditRecord(&$main,$id) {
         return B_news_::addIfcEditRecord($this,$main,$id);
 }
 */

 function addSub(&$sub,&$r,$param) {
           B_news_::addSub($this,$sub,$r,$param);
 }

 function &getParamMngr() {
           return B_news_::getParamMngr($this);
 }

  function addManager(&$main) {
           return B_news_::addManager($this,$main);
 }

  function deleteRecord($id) {
          return B_news_::deleteRecord($this,$id);
 }

}

?>
