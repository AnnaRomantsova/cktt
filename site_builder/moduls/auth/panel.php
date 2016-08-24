<?php

  include('config.php');

  $FILENAME = $front_html_path.'panel.html';
  $FILENAME_tel = $front_html_path.'tel.html';



  /*if ( in_array($site->pageid,Array('plan','practic','tests','materials'))) {
          unset($main);
          $main = new outTree($FILENAME);

		  if (isset($_SESSION['user'])) {

		      $main->addField('log','');
		      $r = new Select($db,'select * from '.$GLOBALS['table_name'].' where id="'.$_SESSION['user'].'"');
		      if ($r->next_row())
		            $r->addFields($main,$ar=array('id','fio'));


		  //   var_dump($_COOKIE);
		  }else {
		          $main->addField('not_log','');
		  };
  } else  {
  */
       unset($main);
       $main = new outTree($FILENAME_tel);
 //};


//echoTree($main);
  if (isset($main)) {
                $site->addField($GLOBALS['currentSection'],$main);
//echoTree($site);
                unset($main);
  };
 ?>