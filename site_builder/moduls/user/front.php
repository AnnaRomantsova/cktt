<?php

  include('config.php');
  include($inc_path.'/classes/class.BF.php');
  //include($inc_path.'/admin_functions.php');
  //include($inc_path.'/img.php');

  //echo $front_html_path;
  $lk_filename = $front_html_path.'panel.html';
  $lic_filename = $front_html_path.'lic.html';
  $lk_user_filename = $front_html_path.'user_panel.html';
  $profile_filename = $front_html_path.'master_profile.html';
  $user_filename = $front_html_path.'user_profile.html';

  //echo "!!!!!!!!";
 //die;
  //var_dump($_POST);

  $patch=$HTTP_SERVER_VARS[HTTP_REFERER];

  $message = '';
  unset($main);
  if (!(isset($_SESSION['user']))) {
      header ( "location: http://" . $_SERVER['HTTP_HOST']);
  };

  //��������������
  if ($_GET['edit']>0) {
         $r = new Select($db,'select * from users where id="'.$_SESSION['user'].'"');
         $r->next_row();
         if ($r->result('is_master')>0 or $r->result('pre_master')>0) { $is_master=$r->result('is_master'); $prof_master=1;};
         if ($_GET['master'] >0) $prof_master=1;
        //echo $prof_master;
          //���� ����-������
          if ($prof_master>0) {
                  $main = &addInCurrentSection($lk_filename);
          } else {
                  $main = &addInCurrentSection($lk_user_filename);
          };
                   //������� ������
                   if ($_POST['photo_del']>0){

                         if ($_POST['id']>0) {
                            $back = new BF($db,$modulName,$modulCaption,'galery',$arFiles);
                            $back->deleteRecord($_POST['id']);
                            $r1 = new Select($db,'delete from galery where id='.$_POST['id']);
                         };
                   }

                  // $main = new outTree($lk_filename);
                   //���� ���� ����� ��������� � ������ ��������
                   if (isset($_POST['save']))  {
                          //echo "kk";
                          foreach ( $_POST as $key => $value)
                                $$key= htmlspecialchars ( addslashes ($value), ENT_QUOTES, $encoding = "cp1251");

                          if ($prof_master == 1) $where = ",pre_master=1,is_master=0"; else $where='';
                          $r = new Select($db,"update users set fio='$fio',
                                                grafic='$grafic',skipe='$skipe',link='$link',adress='$adress',
                                                tel='$tel',id_city=$city,about='$about',
                                                price='$price',experience='$experience',time='$time',
                                                is_free = '$is_free' $where
                                                where id=$_SESSION[user]");


                          //��������� ������� �����
                          $r1 = new Select($db,"delete from user_types where id_user=$_SESSION[user]");
                          foreach ( $_POST as $key => $value) {
                         // echo $key;
                               if (strpos($key,'h_razdel')>0) {
                                  $id = substr($key,9);
                                  $r1 = new Select($db,"insert into user_types(id_user,id_type) values($_SESSION[user],$id)");
                               };
                          };

                          foreach ( $_POST as $key => $value) {
                          //echo substr($key,0,15);
                                if (substr($key,0,15) == 'portfolio_about') {
                                    $id=substr($key,15);
                                    if ($id>0) $r = new Select($db,"update galery set about='$value' where id=$id");
                                    $r = new Select($db,"update galery set pabl='".$_POST['portfolio_pabl'.$id]."' where id=$id");
                                };
                          };
                          $message = '������ ���������.';
                          if ($prof_master == 1) $message.="<br>������� ������� ������������ ����� ��������� �����������.";

                          if ($message!=='') $main->addField('message',$message);

                   };

                    $r = new Select($db,'select * from users where id="'.$_SESSION['user'].'"');
                    $r->next_row();
                    $r->addFields($main,$ar=array('id','name','email','fio','skipe','link','adress','tel','price','experience','grafic'));
                    add_user_avatar($main,$r);
                    if ($r->result('is_free') >0) $main->addfield('bisy',''); else  $main->addfield('free','');
                    $main->addfield('about',htmlspecialchars_decode($r->result('about')));
                    addSprav($main,'time',$r->result('time'),'time');
                    addSprav($main,'city',$r->result('id_city'),'city');

                    //�����������
                    $ri = new Select($db,"select * from galery where  id_user=$_SESSION[user] order by id");
                    if ($ri->num_rows() ==0) $main->addField('no_photo','');
                    if ($ri->num_rows()<30) $main->addField('photo_plus','');
                    while ($ri->next_row()) {
                        unset($sub);
                        $sub = new outTree();
                        $ri->addFields($sub,$ar=array('id','about'));
                        $ri->addFieldsIMG($sub,$ar=array('image1'));
                        if ($ri->result('pabl') >0 ) $sub->addfield('pabl','checked');
                        $main->addField('photo',$sub);
                    };

                   addUserSprav($main,$_SESSION['user'],'user_types');
  //�������� ���� � ���������� ������� �������
  } else if ($_GET['lic']>0) {
      $main = &addInCurrentSection($lic_filename);
      $r = new Select ( $db, 'select * from site_pages where id =6' );
      if ($r->next_row() > 0) {
             $main->addField('license',strip_tags($r->result('content')));
      };
      $r = new Select ( $db, 'select * from site_pages where id =140' );
      if ($r->next_row() > 0) {
             $main->addField('text',strip_tags($r->result('content')));
      };
///�������� ������ �������
  } else {
          $r = new Select($db,'select * from users where id="'.$_SESSION['user'].'"');
          if ($r->next_row()) $is_master=$r->result('is_master');


                  $main = &addInCurrentSection($user_filename);
                  $r->addFields($main,$ar=array('id','email','fio','skipe','link','adress','tel','about','price','experience','grafic','watch','pre_master'));
                  if ($r->result('id_city')>0) {
                        $r1 = new Select($db,"select * from city where id=".$r->result('id_city'));
                        $r1->next_row();
                        $main->addfield('city',$r1->result('name'));
                     };
                   //�������
                    if ($_SESSION['user']>0) $main->addfield('mess','');

  };
  //$site->addField($GLOBALS['currentSection'],&$main);
  unset($main);

 ?>