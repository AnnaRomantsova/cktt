<?php

 if (!isset($_GET['city'])) {

		 unset($main);
		 $FILENAME = 'front/menu/city.html';

		 //$main = &addInCurrentSection($main_FILENAME);
		 $main = new outTree($FILENAME);



		  $r = new Select($db,'select * from city where sort >0  and sort <127 order by sort ');
		  //$i=1;
		  while ( $r->next_row() ) {
		      unset($sub);

		      $sub = new outTree();
		      $r->addFields($sub, $ar=array('id') );
		      $sub->addField('name',str_pad($r->result('name'),10));


		      $href='/index/city/'.$r->result('id');

		      $sub->addField('href',$href);

		      $main->addField('city',$sub );
		      $i++;
		  };



		 $site->addField($GLOBALS['currentSection'],&$main);
  };
?>