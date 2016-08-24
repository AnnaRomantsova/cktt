<?php
/**
 * @package FRONT
 */
 $FILENAME = 'front/menu/menu_bottom.html';

 $main = &addInCurrentSection($FILENAME);
 unset($main->content);


  //echotree($site);
 $keys=array();
 $keys_top=array();
 foreach ($_POST as $key =>$value) {
    //echo "<br>".strpos($key,'razdel_sub');
    if (substr($key,0,10) =='razdel_top') {
       $id = substr($key,10);
       $keys_top[] = $id;
    };
    if (substr($key,0,10) =='razdel_sub') {
       $id = substr($key,10);
       $keys[] = $id;
   };

 };


 $r = new Select($db,'select * from types_sections where parent =1 order by sort');
 while ($r->next_row()) {
     unset($sub);
     $sub = new outTree();
     $sub->addfield('href','lk');
     $r->addFields($sub,$ar=array('id','name'));
     $open=0;
     if (in_array($r->result('id'),$keys_top))  {
        $sub->addField('checked','checked');
        $open=1;
     };

     if ($site->pageid =='lk') $sub->addfield('T','S');  else  $sub->addfield('T','A');

     $r1 = new Select($db,'select * from types where parent ='.$r->result('id').' order by sort' );
     if ($r1->num_rows() >0 ) $sub->addField('subs','');
      while ($r1->next_row()) {
          unset($sub1);
          $sub1 = new outTree();
          $sub1->addfield('href','lk');
          $r1->addFields($sub1,$ar=array('id','name'));

          if (in_array($r1->result('id'),$keys) || $r1->result('id')==$_GET['razdel']){
              $sub1->addField('checked','checked');
              $open=1;
          };
          if ($site->pageid =='lk') $sub1->addfield('T','S');  else  $sub1->addfield('T','A');
          $sub->addField('sub',$sub1);
     };

     if ($open==1) $sub->addField('open','class="open"');

     $main->addField('sub',$sub);
  };

  $page=$site->pageid;
  switch ($site->pageid ){
   case 'lenta_one': $page='lenta'; break;
   case 'master_profile': $page='master'; break;
   case 'zakaz_one': $page='zakaz'; break;
  };
  $main->addField('page',$page);
    //echotree($main);
?>
