<?php

 include('config.php');

 unset($main);
 $s_FILENAME = $front_html_path.'front.html';
 $i_FILENAME = $front_html_path.'item.html';
 $end_FILENAME = $front_html_path.'end.html';
 // $_COOKIE=null;
  function del_cookie(){
		  $past = time() - 3600;
		  foreach ( $_COOKIE as $key => $value )
		  {
		      if (strpos($key,'nswer_') >0 ) {setcookie( $key, $value, $past, '/' ); };
		  }
  };

  //
   //del_cookie();
   //

  if ( $_SESSION['user']>0) {
// запись
    if ($_POST['i']>0 or $_GET['i']>0) {
            $main = &addInCurrentSection($i_FILENAME);
            unset($main->content);
            if ($_GET['i']>0) {$id=$_GET['i']; del_cookie();}
              else if ($_POST['i']>0) $id=$_POST['i'];
            $main->addField('quest_id',$id);

            $r = new Select($db,'select * from '.$GLOBALS['items_table'].' where parent='.addslashes($id).' order by sort');
            while ($r->next_row()) {
                    unset($sub);
                    $sub = new outTree();
                    $r->addFields($sub,$ar=array('id','name'));
                    $main->addField('sub',$sub);
                    //addLast($GLOBALS['site']->path,$main->name);
            }
            $r = new Select($db,'select * from '.$GLOBALS['sections_table'].' where id='.addslashes($id));
            $r->next_row();
            $main->addField('question',$r->result('name'));
            $main->addField('test_id',$r->result('parent'));

            if ($_POST['ans']>0 and $_POST['quest_id']>0) setcookie('answer_'.$_POST['quest_id'],$_POST['ans']);
             //var_dump($_COOKIE);
            $r1 = new Select($db,'select * from '.$GLOBALS['sections_table'].' where parent='.$r->result('parent').' and sort>'.$r->result('sort'));
            if ($r1->next_row())
                 $main->addField('next',$r1->result('id'));
            else
               $main->addField('next','end');

    }
    //конец теста
    else if (($_POST['i']=='end') and ($_POST['test_id']>0) and ($_POST['quest_id']>0)) {
        $main = &addInCurrentSection($end_FILENAME);
        unset($main->content);
        if ($_POST['ans']>0) setcookie('answer_'.$_POST['quest_id'],$_POST['ans']);
        $r = new Select($db,'select * from '.$GLOBALS['sections_table'].' where parent='.$_POST['test_id']);
        $wright=0; $wrong=0;
        $cnt_quest=$r->num_rows();
        while ($r->next_row()) {
		     $r1 = new Select($db,'select * from '.$GLOBALS['items_table'].' where pabl=1 and parent='.$r->result('id'));
             $flag=0;
	    	 if ($r1->next_row()) {
	    	          if ($_COOKIE['answer_'.$r->result('id')] > 0) {
   		                 if ($_COOKIE['answer_'.$r->result('id')] == $r1->result('id')) { $wright++; $flag=1;};
   		              } else if ( $_POST['quest_id'] == $r->result('id') ) {
   		                  if ($_POST['ans'] == $r1->result('id')) {$wright++;  $flag=1;};
		              };
		              if ($flag==0) $wrong++;
		     };
		};
		$r = new Select($db,'select * from '.$GLOBALS['sections_table'].' where id='.$_POST['test_id']);
        if ($r->next_row()) $perc_need = $r->result('persent');
        $main->addField('name',$r->result('name'));

        $perc=$wright/$cnt_quest*100;
        //echo $perc_need;
        if ($perc>=$perc_need) {
                $main->addField('result','Вы успешно прошли тест!');
                $r = new Select($db,'update plan_items set test_date='.time().' ,test_wright='.$wright.', test_wrong='.$wrong.' where id_material= '.$_POST[test_id].' and material_type=1 and
                  parent= (select id from plan_sections where id_user='.$_SESSION['user'].' and '.time().' between date1 and date2)');
        }  else $main->addField('result','Вы не прошли тест.');
        $main->addField('err','Количество ошибок:'.$wrong);

        //var_dump($_COOKIE);
        //del_cookie();
    }
// все записи
    else {
                include($inc_path.'/service/class.pager.php');
                $main = &addInCurrentSection($s_FILENAME);


               $ri = new Select($db,'SELECT t.* FROM '.$GLOBALS['sections_table'].' t, plan_sections ps, plan_items pi
                   WHERE ps.id_user='.$_SESSION['user'].'  and ps.id=pi.parent and pi.material_type=1 and pi.id_material = t.id
                     and '.time().' between ps.date1 and ps.date2   ORDER BY  t.sort');

                $pg = &PagerQuery::new_($ri,$GLOBALS[$modulName.'_fcount'],$_GET['cp'],$ar=array('href'=>'/'.$GLOBALS['page'],'jumpValue'=>$_GET['ib']));

                if ($pg) {
                        $pg->addPAGER($main);

                        while ($ri->next_row()) {
                                unset($sub);
                                $sub = new outTree();
                                $ri->addFields($sub,$ar=array('id','name','time'));
                                $r = new Select($GLOBALS['db'],'SELECT * FROM '.$GLOBALS['sections_table'].' WHERE parent='.$ri->result('id').' ORDER BY  sort');
                                if ($r->next_row())  $sub->addField('id_q',$r->result('id'));
                                $sub->addField('type',$type);
                                $main->addField('sub',$sub);
                        }
                        $ri->unset_();
                } else {
                      $main->addField('no_sub','');
                };
    }

         unset($main);
 } else {
    $FILENAME = 'front/auth/auth.html';
    unset($main);
    $main = &addInCurrentSection($FILENAME);


 };
 ?>

