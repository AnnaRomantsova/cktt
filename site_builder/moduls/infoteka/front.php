<?php

 include('config.php');

 unset($main);
 $s_FILENAME = $front_html_path.'front.html';
 $i_FILENAME = $front_html_path.'item.html';

// запись
    if ( $_GET['i']>0) {
              $main = &addInCurrentSection($i_FILENAME);
            unset($main->content);


           // echotree($site);
            $id=$_GET['i'];

            $r = new Select($db,'select * from '.$GLOBALS['sections_table'].' where id='.$id);
            $r->next_row();

			//var_dump($_SERVER['DOCUMENT_ROOT']);

            //echo $r->result('file1');
            //вывод большого текста из файла:
            if (file_exists ($_SERVER['DOCUMENT_ROOT'].$r->result('file1')) && strlen($r->result('file1')) >0) {
               //echo "1";
               $text =  file_get_contents($_SERVER['DOCUMENT_ROOT'].$r->result('file1'));
               $main->addField('about',$text);
            } else  $r->addFieldHTML($main,'about');



            $r->addFields($main,$ar=array('id','name'));


    	   //
    	   // $GLOBALS['site']->path->br[$modulName] = $br;
                 //echotree($GLOBALS['site']->br[$modulName] );
    		// addLast($GLOBALS['site']->path,$main->name,$modulName);
    		$br =  new Brunch($r->result('id'),$GLOBALS['sections_table'],'');
    		$GLOBALS['site']->br[$modulName] = $br;
    		$site->path->sub->name = $modulCaption;
            $site->path->sub->href = $modulName;
            unset($site->path->last);

    		$br->addFieldPATH($site->path,$GLOBALS['page'].'/s/',$ar=array(0),false);
            unset($site->path->sub[1]->separator);
            unset($site->path->sub[2]->href);

            //echotree($site->path);
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
    else  if ( $_GET['s']>0) {
            $main = &addInCurrentSection($s_FILENAME);
            unset($main->content);


		            $br =  new Brunch($_GET['s'],$GLOBALS['sections_table'],'');
		    		$GLOBALS['site']->br[$modulName] = $br;

		    		$site->path->sub->name = $modulCaption;
		            $site->path->sub->href = $modulName;
		            unset($site->path->last);
                    //echotree($site->path);
		            $br->addFieldPATH($site->path,$GLOBALS['page'].'/s/',$ar=array(),true);
		            unset($site->path->sub[1]->separator);
		            unset($site->path->sub[2]->href);
		    //};
            //unset($site->path->sub[1]->separator);
            //unset($site->path->sub[2]->href);


            $id=$_GET['s'];

            $r = new Select($db,'select * from '.$GLOBALS['sections_table'].' where parent='.$id." order by sort");
            while ($r->next_row()) {
                    unset($sub);
                    $sub = new outTree();
                    $r->addFields($sub,$ar=array('id','name'));
                    $sub->addField('type','i');
                    $main->addField('sub',$sub);
            };

    }
// все записи
    else {
                include($inc_path.'/service/class.pager.php');
                $main = &addInCurrentSection($s_FILENAME);
                $where = 'parent=1';

                $site->path->sub->name = $modulCaption;
		        $site->path->sub->href = $modulName;
		        unset($site->path->last);
		       // unset($site->path->sub[0]->separator);

                $ri = new Select($db,'SELECT * FROM '.$GLOBALS['sections_table'].' WHERE parent=1 ORDER BY sort');

                $pg = &PagerQuery::new_($ri,$GLOBALS[$modulName.'_fcount'],$_GET['cp'],$ar=array('href'=>'/'.$GLOBALS['page'],'jumpValue'=>$_GET['ib']));

                if ($pg) {
                        $pg->addPAGER($main);

                        while ($ri->next_row()) {
                                unset($sub);
                                $sub = new outTree();
                                $ri->addFields($sub,$ar=array('id','name'));
                                $sub->addField('type','s');
                                $main->addField('sub',$sub);
                        }
                        $ri->unset_();
                }
              //  echotree($main);
    }

       //  unset($main);

 ?>

