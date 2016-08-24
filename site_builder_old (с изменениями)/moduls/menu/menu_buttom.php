<?php
 $FILENAME = 'front/menu/menu_buttom.html';

 $main = &addInCurrentSection($FILENAME);
 unset($main->content);



  $i=1;
 $r = new Select($db,'select * from types_sections where parent =1 order by sort');
 while ($r->next_row()) {
     unset($sub);
     $sub = new outTree();
     $sub->addfield('href','lk');
     $r->addFields($sub,$ar=array('id','name'));

     $r1 = new Select($db,'select * from types where parent ='.$r->result('id').' order by sort' );
     if ($r1->num_rows() >0 ) $sub->addField('subs','');
     while ($r1->next_row()) {
          unset($sub1);
          $sub1 = new outTree();
          $r1->addFields($sub1,$ar=array('id','name'));

          $sub->addField('sub',$sub1);
     };

     $sub->addField('num',$i);
     $main->addField('sub',$sub);
     $i++;
  };


?>
