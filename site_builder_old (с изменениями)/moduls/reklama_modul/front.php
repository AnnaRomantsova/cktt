<?php

 include('config.php');

 unset($main);
 $FILENAME = $front_html_path.'front.html';

 $main = new outTree($FILENAME);
 $r1 = new Select($db,'select * from site_pages where id="7"');
 if ($r1->next_row())
     $r1->addFieldHTML($main,'content');
     
   $site->addField($GLOBALS['currentSection'],&$main);
 ?>
