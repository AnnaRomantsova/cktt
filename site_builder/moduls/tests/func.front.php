<?php

include ($inc_path.'/func.back.php');
include ($inc_path.'/classes/class.BCt.php');

// раздел
class BS_catalog_ {

 /**
  * @param BS_catalog $_this
  */
 function addIfcAddRecord(&$_this,&$main,$parent) {

          if ($_FILENAME = BSe_::addIfcAddRecord($_this,$main,$parent)) {
                 $_FILENAME = 'redact_s.html';
          }
     return $_FILENAME;
 }

 /**
  * @param BS_catalog $_this
  */
 function addIfcEditRecord(&$_this,&$main,$id) {

          if ($_FILENAME = BSe_::addIfcEditRecord($_this,$main,$id)) {
                $_FILENAME = 'redact_s.html';
          }
          //echotree($main);
     return $_FILENAME;
 }


}

class BS_catalog extends BSe {

 function addIfcAddRecord(&$main,$parent) {
         return BS_catalog_::addIfcAddRecord($this,$main,$parent);
 }

 function addIfcEditRecord(&$main,$id) {
         return BS_catalog_::addIfcEditRecord($this,$main,$id);
 }


}

// товар
class BI_catalog_ {

 /**
  * @param BI_catalog $_this
  */
 function addIfcAddRecord(&$_this,&$main,$parent,$table) {
          if ($_FILENAME = BIt_::addIfcAddRecord($_this,$main,$parent,$table)) {
                   $main->addField('about',  "loadFCKeditor('about','');");
                   addCalend($main,1);
          //         $main->addField('preview',  "loadFCKeditor('preview','');");
                 $_FILENAME = 'redact_i.html';
          }
     return $_FILENAME;
 }

 /**
  * @param BI_catalog $_this
  */
 function addIfcEditRecord(&$_this,&$main,$id,$table) {
          if ($_FILENAME = BIt_::addIfcEditRecord($_this,$main,$id,$table)) {
                  $r = &$GLOBALS['r'];
                  removeFields($main,$ar = array('about','preview'));
                  addCalend($main,1);
                  $main->addField('about','addFCKeditor($GLOBALS["r"],"about");');
                  $_FILENAME = 'redact_i.html';
          }
     return $_FILENAME;
 }

 function saveRecord(&$_this,&$values,$id) {
        $values['name'] = strip_tags(substr($values['about'],0,200));

        //var_dump($values);
        //die;
        B_::saveRecord($_this,$values,$id);
 }
  function saveNewRecord(&$_this,&$values) {
        $values['name'] = strip_tags(substr($values['about'],0,200));
        $values['parent'] = $_GET['sct_back'];
       // var_dump($values);
       // die;
        B_::saveNewRecord($_this,$values);
 }
}

class BI_catalog extends BIt {

 function addIfcAddRecord(&$main,$parent,$table) {
         return BI_catalog_::addIfcAddRecord($this,$main,$parent,$table);
 }

 function addIfcEditRecord(&$main,$id,$table) {
         return BI_catalog_::addIfcEditRecord($this,$main,$id,$table);
 }

 function saveRecord(&$values,$id) {
         return BI_catalog_::saveRecord($this,$values,$id);
 }

  function saveNewRecord(&$values) {
         return BI_catalog_::saveNewRecord($this,$values);
 }
}


class B_catalog_ {

 function addActions(&$_this,&$main,&$param) {
  //если утерян путь к корню - выходим на страницу по умолчанию.
        if ( 0 > $GLOBALS['br']->level)
       header('Location: ?sct=1');
        //echo $GLOBALS['br']->level;
        if ( 2 > $GLOBALS['br']->level ) {
           $main->addField('actAddSection'.$GLOBALS['br']->level,'');
         };
        if ( 2 == $GLOBALS['br']->level )
           $main->addField('actAddItem','');

        $main->addField('delItem'.$GLOBALS['br']->level,'');

        if (   isset($_SESSION['idCuts']) &&
              ( $co = count($_SESSION['idCuts'][$_this->Item->table])+count($_SESSION['idCuts'][$_this->Section->table]))
             )
                 $main->addField('actPast',$co);

  // если проинициализировано хотя бы одно из действий
        if (     isset($main->actAddSection)
              ||  isset($main->actAddItem)
              ||  isset($main->actClear2)
              ||  isset($main->actClear3)
              ||  isset($main->actPast)
            )
                 $main->addField('actions','');
 }


}

class B_catalog extends BCt  {

 /**
  * @var BS_catalog
  */
 var $Section;

 /**
  * @var BI_catalog
  */
 var $Item;

 function B_catalog(&$_db,$_name = null,$_caption = null,$table_sections,$table_items,&$arFilesS,&$arFilesI) {
         $this->initB_catalog($_db,$_name,$_caption,$table_sections,$table_items,$arFilesS,$arFilesI);
 }

 function initB_catalog(&$_db,$_name,$_caption,$table_sections,$table_items,&$arFilesS,&$arFilesI) {
        $this->Section = new BS_catalog($_db,$_name,$_caption,$table_sections,$arFilesS);
        $this->Item = new BI_catalog($_db,$_name,$_caption,$table_items,$arFilesI);
        $this->initModule($_db,$_name,$_caption);
 }

 function addActions(&$main,&$param) {
         B_catalog_::addActions($this,$main,$param);
 }


}

?>
