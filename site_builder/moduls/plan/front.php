<?php

 include('config.php');

 unset($main);
 $FILENAME = $front_html_path.'front.html';


// запись
 if ( $_SESSION['user']>0) {
            $main = &addInCurrentSection($FILENAME);
            unset($main->content);


              $r = new Select($db,'select * from '.$GLOBALS['sections_table'].' where id_user='.$_SESSION['user'].' and '.time().' between date1 and date2' );
			  if ( $r->next_row()){

				            $main->addField('num',$r->result('id'));
				            $main->addField('datetime1',date('d.m.Y', $r->result('date1')));
				            $main->addField('datetime2',date('d.m.Y', $r->result('date2')));
                            $plan_id=$r->result('id');

					        $r = new Select($db,'select * from users where id='.$_SESSION['user']);
				            $r->next_row();
				            $main->addField('fio',$r->result('fio'));


                            for($i=1; $i<=3; $i++) {
						          //список занятий
						            $r = new Select($db,'select pi.* from '.$GLOBALS['items_table'].' pi where pi.parent='.$plan_id.' and pi.material_type='.$i.' order by pi.sort');
						           // echo 'select pi.* from '.$GLOBALS['items_table'].' pi where pi.parent='.$plan_id.' and pi.material_type='.$i.' order by pi.sort';
						            while ($r->next_row()) {
						                    unset($sub);
						                    $sub = new outTree();

						                    if ($r->result('material_type') == 1) { $table= 'test_sections'; $type='Тестирование'; $link='/tests';
						                          if ($r->result('test_date')>0)
						                              $sub->addField('info','Пройден: '.date('d.m.Y H:i', $r->result('test_date')).'<br>Правильных ответов: '.round($r->result('test_wright')*100/($r->result('test_wright')+$r->result('test_wrong'))).'%' );
						                    };
						                    if ($r->result('material_type') == 2) {$table= 'practic_sections'; $type='Практическое занятие'; $link='/practic/i/'.$r->result('id_material');
						                     if ($r->result('file1_date')>0)  {
						                              $sub->addField('info','Выполнен: '.date('d.m.Y в H:i', $r->result('file1_date')));
						                              $r->addFieldFile($sub,'file1');
						                     };
						                    };
						                    if ($r->result('material_type') == 3) {$table= 'materials_sections'; $type='Учебный материал'; $link='/materials/i/'.$r->result('id_material');};

						                    $r1 = new Select($db,'select * from '.$table.' where id='.$r->result('id_material'));
						            		$r1->next_row();
						            		$r1->addFields($sub,$ar=array('id','name'));

						            		$r->addFields($sub,$ar=array('pabl'));
						                    $sub->addField('type',$type);
						                    $sub->addField('material_type',$r->result('material_type'));
						                    $sub->addField('link',$link);
						                    $main->addField('sub'.$i,$sub);
						            };
						    };
			  } else {
                    $main->addField('no_sub','');
            };
 } else {


    $FILENAME = 'front/auth/auth.html';
    unset($main);
    $main = &addInCurrentSection($FILENAME);
//    echotree($main);

 };

       //  unset($main);

 ?>

