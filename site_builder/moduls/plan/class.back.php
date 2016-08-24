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
                     $main->addField('datetime1',date('d.m.Y'));
                     $main->addField('datetime2',date('d.m.Y'));

				     $ri = new Select($_this->db,"select * from users  order by fio");
				     while ($ri->next_row()) {
				       unset($sub);
				       $sub = new outTree();
				       $ri->addFields($sub,$ar=array('id','fio'));
				       $main->addField('users',$sub);
				     };

				     $ri = new Select($_this->db,"select * from kurs_sections where parent=1 order by sort");
				     while ($ri->next_row()) {
				       unset($sub);
				       $sub = new outTree();
				       $ri->addFields($sub,$ar=array('id','name'));
				       $main->addField('kurs',$sub);
				     };
				      addCalend($main,1);
				       addCalend($main,2);
                      $_FILENAME = 'redact_s.html';
          }
     return $_FILENAME;
 }

 /**
  * @param BS_catalog $_this
  */
 function addIfcEditRecord(&$_this,&$main,$id) {

          if ($_FILENAME = BSe_::addIfcEditRecord($_this,$main,$id)) {
				    $main->addField('datetime1',date('d.m.Y', $main->date1));
                    $main->addField('datetime2',date('d.m.Y', $main->date2));

				     $ri = new Select($_this->db,"select * from users order by fio");
				     while ($ri->next_row()) {
				       unset($sub);
				     //  echo "1";

				       $sub = new outTree();
				       if ($main->id_user ==$ri->result('id')) $sub->addField('selected','selected');
				       $ri->addFields($sub,$ar=array('id','fio'));
				      // echotree($sub);
				       $main->addField('users',$sub);
				     };

				     $ri = new Select($_this->db,"select * from kurs_sections where parent=1 order by sort");
				     while ($ri->next_row()) {
				       unset($sub);
				       $sub = new outTree();
				       if ($main->id_kurs==$ri->result('id')) $sub->addField('selected','selected');
				       $ri->addFields($sub,$ar=array('id','name'));
				       $main->addField('kurs',$sub);
				     };
				      addCalend($main,1);
				      addCalend($main,2);
                $_FILENAME = 'redact_s.html';
          }
          //echotree($main);
     return $_FILENAME;
 }

 function saveNewRecord(&$_this,&$values){
    $values['parent']=1;
    B_::saveNewRecord($_this,$values);
   // var_dump($values);
    $ri = new Select($_this->db,"delete from plan_items where parent=".$values['id']);
    $ri = new Select($_this->db,"select * from kurs_items where parent=".$values['id_kurs']);
	while ($ri->next_row()) {
	     $r = new Select($_this->db,"insert into plan_items(parent,id_material,material_type,sort)
	              values(".$values['id'].",".$ri->result('id_material').",".$ri->result('material_type').",'".$ri->result('sort')."')" );
	};
 }

 function saveRecord(&$_this,&$values,$id) {

        B_::saveRecord($_this,$values,$id);

        $ri = new Select($_this->db,"delete from plan_items where parent=$id");
		$ri = new Select($_this->db,"select * from kurs_items where parent=".$values['id_kurs']);
		while ($ri->next_row()) {
		     $r = new Select($_this->db,"insert into plan_items(parent,id_material,material_type,sort)
		            values($id,".$ri->result('id_material').",".$ri->result('material_type').",'".$ri->result('sort')."')" );
		};

 }

 function redactValues(&$_this,&$values) {

    $date = &$values['date1'];
    $values['date1'] = @mktime(0,0,0,substr($date,3,2),substr($date,0,2),substr($date,6));
    $date = &$values['date2'];
    $values['date2'] = @mktime(23,59,59,substr($date,3,2),substr($date,0,2),substr($date,6));
    if (empty($values['title']))
            $values['title'] = $values['name'];
        B_::redactValues($_this,$values);
 }

function addSub(&$_this,&$sub,&$r,$param) {

         B_::addSub($_this,$sub,$r,$param);

         $ri = new Select($_this->db,"select * from users where id=".$r->result('id_user'));
         if ($ri->next_row())
             $ri->addFields($sub,$ar=array('fio'));
         $ri = new Select($_this->db,"select * from kurs_sections where id=".$r->result('id_kurs'));
         if ($ri->next_row())
             $sub->addField('kurs',$ri->result('name'));

 }

}

class BS_catalog extends BSe {

 function addIfcAddRecord(&$main,$parent) {
         return BS_catalog_::addIfcAddRecord($this,$main,$parent);
 }

 function addIfcEditRecord(&$main,$id) {
         return BS_catalog_::addIfcEditRecord($this,$main,$id);
 }

  function addSub(&$sub,&$r,$param) {
           return BS_catalog_::addSub($this,$sub,$r,$param);
 }

  function saveNewRecord(&$values) {
         return BS_catalog_::saveNewRecord($this,$values);
 }

 function saveRecord(&$values,$id) {
         return BS_catalog_::saveRecord($this,$values,$id);
 }

 function redactValues(&$values) {
         return BS_catalog_::redactValues($this,$values);
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
                  $main->addField('about','addFCKeditor($GLOBALS["r"],"about");');
                  $_FILENAME = 'redact_i.html';
          }
     return $_FILENAME;
 }

 function addSub(&$_this,&$sub,&$r,$param) {
         B_::addSub($_this,$sub,$r,$param);
         $sub->addField('material_type_n',$r->result('material_type'));
         if ($r->result('material_type')==1) {
             $sub->addField('material_type','Тест');
             $ri = new Select($_this->db,"select * from test_sections where id=".$r->result('id_material'));
             $ri->next_row();
             $sub->addField('name_m',$ri->result('name'));
             if ($r->result('test_date')>0){
                 $info = 'Пройден: '.date('d.m.Y H:i', $r->result('test_date')).'<br>Правильных ответов: '.round($r->result('test_wright')*100/($r->result('test_wright')+$r->result('test_wrong'))).'%';
                 $sub->addField('info',$info);
             };
            // echo $ri->result('name');
         };
         if ($r->result('material_type')==2) {
             $sub->addField('material_type','Практическое занятие');
             $ri = new Select($_this->db,"select * from practic_sections where id=".$r->result('id_material'));
             $ri->next_row();
             $sub->addField('name_m',$ri->result('name'));
             if ($r->result('file1_date')>0)  {
	             $info = 'Выполнен: '.date('d.m.Y в H:i', $r->result('file1_date'));
	             $r->addFieldFile($sub,'file1');
    	         $sub->addField('info',$info);
    	     };
         };
         if ($r->result('material_type')==3) {
             $sub->addField('material_type','Учебный материал');
             $ri = new Select($_this->db,"select * from materials_sections where id=".$r->result('id_material'));
             $ri->next_row();
             $sub->addField('name_m',$ri->result('name'));
         };

 }

}

class BI_catalog extends BIt {

 function addIfcAddRecord(&$main,$parent,$table) {
         return BI_catalog_::addIfcAddRecord($this,$main,$parent,$table);
 }

 function addIfcEditRecord(&$main,$id,$table) {
         return BI_catalog_::addIfcEditRecord($this,$main,$id,$table);
 }
 /*
 function saveRecord(&$values,$id) {
         return BI_catalog_::saveRecord($this,$values,$id);
 }

 function saveNewRecord(&$values) {
        return BI_catalog_::saveNewRecord($this,$values);
 }
 */

  function addSub(&$sub,&$r,$param) {
           return BI_catalog_::addSub($this,$sub,$r,$param);
 }
}


class B_catalog_ {

 function addActions(&$_this,&$main,&$param) {
  //если утерян путь к корню - выходим на страницу по умолчанию.
        if ( 0 > $GLOBALS['br']->level)
       header('Location: ?sct=1');

        if ( 1 > $GLOBALS['br']->level )
           $main->addField('actAddSection','');
        if ( 1 == $GLOBALS['br']->level )
           $main->addField('actAddItem','');

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
