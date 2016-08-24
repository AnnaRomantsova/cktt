<?php

 include('config.php');
 unset($main);
 $main_FILENAME = $front_html_path.'panel.html';

 //$main = &addInCurrentSection($main_FILENAME);
 $main = new outTree($main_FILENAME);


  $sel_city_id =0;
  $sel_city_name = 'не выбран';

  $cookie=$_GET['city'];

  //echo $_SERVER['REQUEST_URI'];
  //echo $cookie;
  if ($cookie>0) {
	  $r = new Select($db,"select * from city where id=$cookie");
	  if ( $r->next_row() ) {

	         $sel_city_id = $r->result('id');
	         $sel_city_name = $r->result('name');

	      };

  };

 $main->addField('sel_city_id',$sel_city_id);
 $main->addField('sel_city_name',$sel_city_name);
 //$main->addField('sel_city_tel',$sel_city_tel);
 //$main->addField('sel_city_work',$sel_city_work);
// echotree($main);
 $site->addField($GLOBALS['currentSection'],&$main);
//echotree($site);
?>