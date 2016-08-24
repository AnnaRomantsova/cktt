<?php

 include('config.php');

 unset($main);
 $s_FILENAME = $front_html_path.'front.html';
 $i_FILENAME = $front_html_path.'item.html';

 include($inc_path.'/classes/class.BF.php');
 include($inc_path.'/admin_functions.php');

 if ( $_SESSION['user']>0) {
// запись
    if ( $_GET['i']>0) {
            $main = &addInCurrentSection($i_FILENAME);
            unset($main->content);

            $id=$_GET['i'];

            $r = new Select($db,'select * from '.$GLOBALS['sections_table'].' where id='.$id);
            $r->next_row();
            $r->addFields($main,$ar=array('id','name'));
            $r->addFieldHTML($main,'about');
            $r = new Select($db,'select * from '.$GLOBALS['items_table'].' where parent='.$id." order by sort");
            if ($r->num_rows()>0) $main->addField('files_is','');
            while ($r->next_row()) {
                    unset($sub);
                    $sub = new outTree();
                    $r->addFields($sub,$ar=array('id','name'));
                    $r1 = new Select($db,"select pi.* from plan_sections ps,plan_items pi where id_user=$_SESSION[user] and ".time()." between date1 and date2");
		            $r1->addFieldsFILE($sub,$ar=array('practic_file'));
                    $r->addFieldFile($sub,'file1');
                    $main->addField('sub',$sub);
            };

 		   //юзер сохраняет свой файл
           if ($_POST['rep_save']>0){
                         //var_dump($_FILES);
                         $values['file'] = $_POST['file1'];
                         $values['kn_file'] = $_POST['kn_file'];
                         $values['d_file'] = $_POST['d_file'];
                        //  var_dump($values);
                         $back = new BF($db,$modulName,$modulCaption,'plan_items',$arImgI);
                         $r = new Select($db,'select pi.id from plan_items pi,plan_sections ps where pi.parent=ps.id and ps.id_user='.$_SESSION['user'].'
                            and pi.material_type=2 and pi.id_material = '.$_GET['i'].' and '.time().' between ps.date1 and ps.date2');
                         $r->next_row();
                         $id= $r->result('id');
                         $r = new Select($db,'update plan_items set file1_date='.time().' where id='.$id);
                         $back->saveRecord($values,$id);
           };

           //файл юзера если есть
           $r = new Select($db,'select pi.* from plan_items pi,plan_sections ps where pi.parent=ps.id and ps.id_user='.$_SESSION['user'].'
                            and pi.material_type=2 and pi.id_material = '.$_GET['i'].' and '.time().' between ps.date1 and ps.date2');
           $r->next_row();
           if ($r->result('file1_date')>0) {
		           $r->addFieldsFILE($main,$ar=array('file1'));
		           $main->addField('user_file_date',date('d.m.Y в H:i', $r->result('file1_date')));
           };


    }

// все записи
    else {
                include($inc_path.'/service/class.pager.php');
                $main = &addInCurrentSection($s_FILENAME);
                $where = 'parent=1';

                $ri = new Select($db,'SELECT t.* FROM '.$GLOBALS['sections_table'].' t, plan_sections ps, plan_items pi
                   WHERE ps.id_user='.$_SESSION['user'].'  and ps.id=pi.parent and pi.material_type=2 and pi.id_material = t.id
                       and '.time().' between ps.date1 and ps.date2
                   ORDER BY  t.sort');

                $pg = &PagerQuery::new_($ri,$GLOBALS[$modulName.'_fcount'],$_GET['cp'],$ar=array('href'=>'/'.$GLOBALS['page'],'jumpValue'=>$_GET['ib']));

                if ($pg) {
                        $pg->addPAGER($main);

                        while ($ri->next_row()) {
                                unset($sub);
                                $sub = new outTree();
                                $ri->addFields($sub,$ar=array('id','name'));
                               // $r = new Select($GLOBALS['db'],'SELECT * FROM '.$GLOBALS['sections_table'].' WHERE parent='.$ri->result('id').' ORDER BY  sort');
                              //  if ($r->next_row())  $sub->addField('id',$r->result('id'));
                                $sub->addField('type',$type);
                                $main->addField('sub',$sub);
                        }
                        $ri->unset_();
                } else {
                      $main->addField('no_sub','');
                };
              //  echotree($main);
    }
  } else {
    $FILENAME = 'front/auth/auth.html';
    unset($main);
    $main = &addInCurrentSection($FILENAME);


 };
       //  unset($main);

 ?>

