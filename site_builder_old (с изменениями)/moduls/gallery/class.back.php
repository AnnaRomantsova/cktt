<?php
/**
 *  Классы и функции по обработке записей и выводу сущностей в шаблоны.
 */

include ($inc_path.'/func.back.php');
include ($inc_path.'/classes/class.BF_P.php');

//сжтие картинки
 function image_resize($filename,$new_width,$new_height){

  //$filename=substr($filename,1);
  list($width, $height) = getimagesize($filename);

  $ratio_orig = $width/$height;

  if ($new_width/$new_height > $ratio_orig) {
     $new_width = $new_height*$ratio_orig;
  } else {
     $new_height = $new_width/$ratio_orig;
  }

  $image_p = imagecreatetruecolor($new_width, $new_height);
  $image = imagecreatefromjpeg($filename);
  imagecopyresampled($image_p, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
  imagejpeg($image_p, $filename, 100); //50% это качество 0-100%
 };


class B_news_ {

 function saveRecord(&$_this,&$values,$id) {
        $values['kn_image1'] = $values['kn_image2'];
        $values['alt1'] = $values['alt2'];
        $values['d_image1'] = $values['d_image2'] ;

        $_FILES['image1'] =  $_FILES['image2'];
        // BF_::saveRecord($_this,$values);
        BF_::saveRecord($_this,$values,$id);

        $r1 = new Select($_this->db,'select * from '.$this->table.' where id='.$id);
        if ($r1->next_row()) {
            image_resize( $_SERVER['DOCUMENT_ROOT'].$r1->result('image1'),80,80);
            image_resize( $_SERVER['DOCUMENT_ROOT'].$r1->result('image2'),600,600);
        };
 }

 function addIfcAddRecord(&$_this,&$main) {
        $_FILENAME = B_::addIfcAddRecord($_this,$main);

    return $_FILENAME;
 }

 function addIfcEditRecord(&$_this,&$main,$id) {
    $_FILENAME = BF_::addIfcEditRecord($_this,$main,$id);
    return $_FILENAME;
 }

 function addSub(&$_this,&$sub,&$r,$param) {
         B_::addSub($_this,$sub,$r,$param);
         //$sub->addField('date',date('d.m.y, H i', $r->result('datetime')));
         //$r->addFieldIMG($sub,$ar=array('image1'));
         if ($r->result('image1')!=='') $r->addField($sub,'image1');
         $rs = new Select($_this->db,'select c.name as company,h.*,s.name as street from house h,company c, street s where c.id=h.id_company and s.id=h.id_street and h.id='.$r->result('id_house'));
         if ($rs->next_row())
           $rs->addFields($sub,$ar=array('company','street','number','fract'));
  }

 function &getParamMngr(&$_this) {
         $param = &BP_::getParamMngr($_this);
         $param['order'] = 'id_house desc';
        // $param['where'] = ' ntype=1';
         return $param;
 }


}

class B_news extends BF_P {

 //function redactValues(&$values) {
 //        B_news_::redactValues($this,$values);
// }

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

}

?>
