<?php

 include('config.php');

 unset($main);
 $FILENAME = $front_html_path.'front.html';

 $main = new outTree($FILENAME);
 //echotree($site);
 $r1 = new Select($db,'select * from site_tree where page="'.$site->pageid.'"');
 if ($r1->next_row()) {
         $parent = $r1->result('parent');
         $page_id = $r1->result('id');
 } else exit;

  function get_city_rek($id_house){
         global $db;
         $r1 = new Select($db,'select s.id_city from house h,street s where s.id=h.id_street and h.id='.$id_house);
         $r1->next_row();
         return $r1->result('id_city');
 };
//echo $parent;
 if ($parent !== '135') {
      $r1 = new Select($db,'select * from site_pages where id="7"');
      if ($r1->next_row())  $r1->addFieldHTML($main,'content');
 } else {
      //город
      if ($_COOKIE['id_house'] > 0 ) $id_city=get_city_rek($_COOKIE['id_house']);
        else if ($_SESSION['user'] >0 ) {
             $r1 = new Select($db,'select s.id_city from house h, users u, street s  where s.id = h.id_street and u.id_company = h.id_company and u.id='.$_SESSION['user'].' limit 1 ');
             echo 'select s.id_city from house h, users u, street s  where s.id = h.id_street and u.id_company = h.id_company and u.id='.$SESSION['user'].' limit 1 ';
             if ($r1->next_row()) $id_city = $r1->result('id_city');
          } else if ($_SESSION['vendor'] >0) {
             $r1 = new Select($db,'select * from users where id='.$_SESSION['vendor']);
             if ($r1->next_row()) $id_city = $r1->result('id_city');
            }
            else {
               $id_city=0;
            };
  //   echo $id_city;
     if ($id_city>0) {
              $r1 = new Select($db,"select *  from rek r, reklama_city c ,reklama_page p where c.id_city = $id_city and
                                      p.id_page= $page_id and r.id =p.id_reklama and r.id = c.id_reklama and r.pabl=1 ORDER BY RAND() LIMIT 1");

              if ($r1->next_row())
                 $main->addField('link',$r1->result('link'));
                 $r1->addFieldIMG($main,'image1');
    };
 };
   $site->addField($GLOBALS['currentSection'],&$main);
 ?>
