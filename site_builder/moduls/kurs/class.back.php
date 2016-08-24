<?php

include ($inc_path.'/func.back.php');
include ($inc_path.'/classes/class.BCt.php');
include ($inc_path.'/myfunc.php');

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
                  //var_dump($_GET);
                  if ($_GET['addtype'] == 'test') {
                     addSprav($main,'test_sections','','tests',' and parent=1 and id not
                                                    in (select id_material from kurs_items where material_type=1 and parent='.$parent.' )');
                     $main->addField('is_test', "");
                  };
                  if ($_GET['addtype'] == 'pract') {
                     addSprav($main,'practic_sections','','practics',' and parent=1 and id not
                                                    in (select id_material from kurs_items where material_type=2 and parent='.$parent.' )');
                     $main->addField('is_pract', "");
                  };
                  if ($_GET['addtype'] == 'material') {
                     addSprav($main,'materials_sections','','material',' and parent=1 and id not
                                                    in (select id_material from kurs_items where material_type=3 and parent='.$parent.' )');
                     $main->addField('is_material', "");
                  };
          //        echotree($main);

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



   function addSub(&$_this,&$sub,&$r,$param) {
         B_::addSub($_this,$sub,$r,$param);

         if ($r->result('material_type')==1) {
             $sub->addField('material_type','Тест');
             $ri = new Select($_this->db,"select * from test_sections where id=".$r->result('id_material'));
             $ri->next_row();
             $sub->addField('name_m',$ri->result('name'));
            // echo $ri->result('name');
         };
         if ($r->result('material_type')==2) {
             $sub->addField('material_type','Практическое занятие');
             $ri = new Select($_this->db,"select * from practic_sections where id=".$r->result('id_material'));
             $ri->next_row();
             $sub->addField('name_m',$ri->result('name'));
         };
         if ($r->result('material_type')==3) {
             $sub->addField('material_type','Учебный материал');
             $ri = new Select($_this->db,"select * from materials_sections where id=".$r->result('id_material'));
             $ri->next_row();
             $sub->addField('name_m',$ri->result('name'));
         };

 }

  function saveNewRecord(&$_this,&$values,$parent){
    $values['parent']=$_GET['sct_back'];
    B_::saveNewRecord($_this,$values);

    $ri = new Select($_this->db,"select * from plan_sections where id_kurs=".$_GET['sct_back']);
	while ($ri->next_row()) {
	     $r = new Select($_this->db,"insert into plan_items(parent,id_material,material_type,sort)
	              values(".$ri->result('id').",".$values['id_material'].",".$values['material_type'].",'".$values['sort']."')" );
	};
 }

 function deleteRecord(&$_this,$id){

    $ri = new Select($_this->db,"select * from kurs_items where id=$id");
	while ($ri->next_row()) {
	     $r = new Select($_this->db,"delete from plan_items where
	         id_material=".$ri->result('id_material')." and material_type=".$ri->result('material_type')." and parent in
	         (select id from plan_sections where id_kurs=".$ri->result('parent').")" );
	};
	B_::deleteRecord($_this,$id);
 }
}

class BI_catalog extends BIt {

 function addIfcAddRecord(&$main,$parent,$table) {
         return BI_catalog_::addIfcAddRecord($this,$main,$parent,$table);
 }

 function addIfcEditRecord(&$main,$id,$table) {
         return BI_catalog_::addIfcEditRecord($this,$main,$id,$table);
 }

 function deleteRecord($id) {
         return BI_catalog_::deleteRecord($this,$id);
 }

  function saveNewRecord(&$values) {
         return BI_catalog_::saveNewRecord($this,$values,$parent);
 }

  function addSub(&$sub,&$r,$param) {
           return BI_catalog_::addSub($this,$sub,$r,$param);
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
                // echo "ffff";
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
