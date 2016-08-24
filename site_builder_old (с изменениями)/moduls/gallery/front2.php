<?php

 include('config.php');

 unset($main);
 $FILENAME = $front_html_path.'front.html';

 if ($_COOKIE['id_house']>0) $cookie=$_COOKIE['id_house'];

 include($inc_path.'/classes/class.BF.php');
 include($inc_path.'/admin_functions.php');

 //сжтие картинки
 function image_resize($filename,$new_width,$new_height){

  $filename=substr($filename,1);
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
  //echo $width;
  //exif_thumbnail($filename, $new_width, $new_height, 'jpeg');
  //die;
 };

// var_dump($_POST);

// выбран дом
 if ($cookie>0) {
     //если авторизован и председатель этого тсж
     if ( $_SESSION ['user']>0) {
         $r1 = new Select($db,'select * from users where id="'.$user.'"');
         if ($r1->next_row()) $user_company = $r1->result('id_company');
     };

     $r = new Select($db,'select * from house where id="'.$cookie.'"');
     if ($r->next_row())  $id_company=$r->result('id_company');

     $main = &addInCurrentSection($FILENAME);
     unset($main->content);

     if ( $_SESSION ['user']>0) {

           //если юзер - председатель этого ТСЖ
           if ($user_company == $id_company)
           {

                  //нажали кнопку удалить
                  if ($_POST['rep_del']>0){

                        if ($_POST['id_del_group']>0) {
                           $back = new BF($db,$modulName,$modulCaption,$table_name,$arFiles);
                           $back->deleteRecord($_POST['id_del_group']);

                           $r1 = new Select($db,'delete from galery where id='.$_POST['id_del_group']);
                        };
                        $main->addField('mode','mode_redact');
                  }
                  //нажали кнопку редактировать
                  if ($_POST['edit_submit']>0){
                        $main->addField('mode','mode_redact');
                  }
                  //нажали кнопку сохранить
                  else if ($_POST['rep_submit']>0){

                        // image_resize($_FILES['image1']['tmp_name'],80,80);
                         $_FILES['image2'] =  $_FILES['image1'];

                         $values['kn_image1'] = $_POST['kn_image1'];
                         $values['d_image1'] = $_POST['d_image1'];
                         $values['alt1'] = $_POST['alt1'];

                         $back = new BF($db,$modulName,$modulCaption,$table_name,$arFiles);
                         $back->saveRecord($values,$_POST['id_save']);

                         $r1 = new Select($db,'select * from galery where id='.$_POST['id_save']);
                         if ($r1->next_row()) {
                            image_resize($r1->result('image1'),80,80);
                            image_resize($r1->result('image2'),600,600);
                         };
                         $main->addField('mode','mode_edit');
                  }
                  //н сохранить новый отчет
                  else if ($_POST['new_rep_submit']>0){
                         $values['kn_image1'] = $_POST['kn_image1'];
                         $values['id_company'] = $id_company;
                         $values['alt1'] = $_POST['alt1'];

                          $_FILES['image2'] =  $_FILES['image1'];

                         $back = new BF($db,$modulName,$modulCaption,$table_name,$arFiles);
                         $back->saveNewRecord($values);


                         $r1 = new Select($db,'select * from galery where id='.$values['id']);
                         if ($r1->next_row()) {
                            image_resize($r1->result('image1'),80,80);
                            image_resize($r1->result('image2'),600,600);
                         };

                         /*
                         $r1 = new Select($db,'insert into reports(name,id_company) values("'.$_POST['name'].'",'.$id_company.')');

                         if ($db->insert_id()>0) {
                                 echo 'update reports set file="'.$values['file'].'" where id='.$db->insert_id();
                             if (strlen($values['file'])>0)
                                $r1 = new Select($db,'update reports set file="'.$values['file'].'" where id='.$db->insert_id());

                         };
                         */
                        $main->addField('mode','mode_edit');
                  }
                  //ничего не нажимали
                  else {
                       $main->addField('mode','mode_edit');
                  };
           } //юзер - не председатель этого ТСЖ
           else
           {
               $main->addField('mode','mode_show');
           }
     } else
     {
             $main->addField('mode','mode_show');
     }

     $r1 = new Select($db,'select * from galery where id_company="'.$r->result('id_company').'"');
     $main->addField('id_company',$r->result('id_company'));
     while ($r1->next_row()) {
             unset($sub);
             $sub = new outTree();
             $r1->addFields($sub,$ar=array('id','alt1'));
             $r1->addFieldsIMG($sub,$ar=array('image1','image2'));
             $main->addField('sub',&$sub);
     };

  }

  else header('Location: /');
// echotree($main);

 ?>

