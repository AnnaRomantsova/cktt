<?php

 include('config.php');

 unset($main);
 $s_FILENAME = $front_html_path.'front.html';
 $i_FILENAME = $front_html_path.'item.html';

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
                    $r->addFieldFile($sub,'file1');
                    $main->addField('sub',$sub);
            };

    }

// все записи
    else {
                include($inc_path.'/service/class.pager.php');
                $main = &addInCurrentSection($s_FILENAME);
                $where = 'parent=1';

                $ri = new Select($db,'SELECT t.* FROM '.$GLOBALS['sections_table'].' t, plan_sections ps, plan_items pi
                   WHERE ps.id_user='.$_SESSION['user'].'  and ps.id=pi.parent and pi.material_type=3 and pi.id_material = t.id
                     and '.time().' between ps.date1 and ps.date2 ORDER BY  t.sort');

                $pg = &PagerQuery::new_($ri,$GLOBALS[$modulName.'_fcount'],$_GET['cp'],$ar=array('href'=>'/'.$GLOBALS['page'],'jumpValue'=>$_GET['ib']));

                if ($pg) {
                        $pg->addPAGER($main);

                        while ($ri->next_row()) {
                                unset($sub);
                                $sub = new outTree();
                                $ri->addFields($sub,$ar=array('id','name'));
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

