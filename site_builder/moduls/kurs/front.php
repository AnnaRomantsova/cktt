<?php

  include('config.php');
  include_once('func.front.php');
  include($inc_path.'/service/class.pager.php');
//   var_dump($_COOKIE);
  $s_FILENAME = $front_html_path.'front.html';
  $i_FILENAME = $front_html_path.'i.html';
  $p_FILENAME = $front_html_path.'panel.html';

  if (!isset($_GET['s'])) $sect = 1; else $sect = $_GET['s'];
  $r = new Select($GLOBALS['db'],'select * from '.$GLOBALS['sections_table'].' where parent="'.$sect.'" order by sort limit 1');
  while ($r->next_row()) {
    $sect = $r->result('id');
    $r = new Select($GLOBALS['db'],'select * from '.$GLOBALS['sections_table'].' where parent="'.$sect.'" order by sort limit 1');
  };
  // var_dump($_COOKIE['cash_item']);
//  echo $HTTP_SERVER_VARS[HTTP_REFERER];
 // echo $sect;
  if($sect !== $_GET['s']) Header('Location: /catalog/s/'.$sect);
 // разделы
    if (isset($_GET['s'])) {
      $r = new Select($db,'select * from '.$GLOBALS['sections_table'].' where id="'.addslashes($_GET['s']).'" and pabl="1"');
      if ($r->next_row()) {

        $site->addField('catalog_name',$r->result('name'));
        $main = &incPage($s_FILENAME);
        $main->addField('s',$_GET['s']);

        //сортировка
        $field = 'sort';

        if(!isset($_GET['artikul']) && !isset($_GET['name']) && !isset($_GET['price_rozn'])) $_GET['name'] ='asc';
        if (isset($_GET['artikul']))     {
                     $field='artikul';
                     $field_value=$_GET[$field];
                     $main->addField('art',$_GET['artikul']);
                     if($_GET['artikul']=='asc') { $main->addField('art_href','art_asc'); $main->addField('art_Ahref','desc'); }
                        else $main->addField('art_href','art_notasc');
                     if($_GET['artikul']=='desc') { $main->addField('art_dhref','art_desc'); $main->addField('art_Ahref','asc');}
                        else $main->addField('art_dhref','art_notdesc');
        } else {
                 $main->addField('art_Ahref','asc');
                 $main->addField('art_href','art_notasc');
                 $main->addField('art_dhref','art_notdesc');
         };

        if (isset($_GET['name']))        {
                     $field='name';
                     $field_value=$_GET[$field];
                     $main->addField('name',$_GET['name']);
                     if($_GET['name']=='asc') {$main->addField('name_href','name_asc'); $main->addField('name_Ahref','desc');   }
                        else $main->addField('name_href','name_notasc');
                     if($_GET['name']=='desc'){ $main->addField('name_dhref','name_desc');  $main->addField('name_Ahref','asc');}
                        else $main->addField('name_dhref','name_notdesc');
        }else {
                 $main->addField('name_Ahref','asc');
                 $main->addField('name_href','name_notasc');
                 $main->addField('name_dhref','name_notdesc');
         };
        if (isset($_GET['price_rozn']))  {
                     $field='price_rozn';
                     $field_value=$_GET[$field];
                     $main->addField('price',$_GET['price_rozn']);
                     if($_GET['price_rozn']=='asc') { $main->addField('price_href','price_asc'); $main->addField('price_Ahref','desc'); }
                        else $main->addField('price_href','price_notasc');
                     if($_GET['price_rozn']=='desc') { $main->addField('price_dhref','price_desc'); $main->addField('price_Ahref','asc'); }
                        else $main->addField('price_dhref','price_notdesc');

        } else {
                 $main->addField('price_Ahref','asc');
                 $main->addField('price_href','price_notasc');
                 $main->addField('price_dhref','price_notdesc');
         };
        if (!isset($_GET[$field])) $sord=''; else {$sord= $_GET[$field]; $main->addField('arr','1'); };
       // echo "h";
       //  echo $GLOBALS[$modulName.'_fcount'];
        if ($pg = Pagers::PrSoCt($db,$GLOBALS['items_table'],$_GET['s'],$GLOBALS[$modulName.'_fcount'],$_GET['cp'],'/'.$GLOBALS['modulName'].'/s/'.$_GET['s']."/".$field."/".$field_value,$field,$sord)) {
              $pg->addPAGER($main);
              $ri = $pg->r;
              $i=1;
             // echo "jj";
             // echo $ri->num_rows();
              if ($ri->num_rows() >0 ) $main->addField('goods',''); //else $main->addField('no_goods','');
              while ($ri->next_row()) {
                    $sub = new outTree();
                    $ri->addFields($sub,$ar=array('id','artikul'));
                    $ri->addFieldsIMG($sub,$ar=array('image1','image2'));
                    $sub->addField('name',stripslashes($ri->result('name')));
                    $sub->addField('price_rozn',round($ri->result('price_rozn'),2));

                    if($ri->result('image1') == '')
                    { $sub->addField('no_img1','');
//                     echo "h";
                    }
                    if($ri->result('measure')>0){
                       $rm = new Select($db,'select * from units where id='.$ri->result('measure'));
                       if ($rm->next_row()) $sub->addField('measure',$rm->result('name'));
                    };

                    $price_rub = get_sum_rubl($ri->result('price_rozn'),$ri->result('valuta'));
                    $sub->addField('price_rozn_rub',round($price_rub,2));
                   // echo $price_rub;
                    if($ri->result('valuta')>0){
                       $rm = new Select($db,'select * from valuta where id='.$ri->result('valuta'));
                       if ($rm->next_row()) $sub->addField('valuta',$rm->result('name'));
                    };

                   if( $_SESSION['user'] ) {
                       $rm = new Select($db,'select * from users where id='.$_SESSION['user']);
                       if ($rm->next_row()) $is_partner=$rm->result('is_partner');
                   } else $is_partner=0;
                   $id=$ri->result('id');
                   if ( $_COOKIE['cash_item'][$id] >0 )
                       $sub->addField('cnt_order', $_COOKIE['cash_item'][$id]);
                   //else $sub->addField('cnt_order', 1);

                    //товар есть
                    if ($ri->result('volume') > 0) {
                         $sub->addField('yest','');
                         //чел авторизован
                         if(isset($_SESSION['user'])) {
                             //партнер
                             if ($is_partner == 1) {
                               $sub->addField('max',$ri->result('volume')+1000);
                               $sub->addField('cnt',$ri->result('volume'));
                             } else {
                                $sub->addField('cnt','есть');
                                $sub->addField('max',1000);
                            };
                         } else {
                                $sub->addField('cnt','есть');
                                $sub->addField('max',1000);
                            };

                    } else
                    //товару ни разу нет
                    {
                         $sub->addField('td_gray','class="grey"');
                         $sub->addField('cnt','нет');
                         $sub->addField('max',1000);
                    }
                    if ($i>3) $sub->addField('gray','class="grey"');
                    if($i==6) $i=1;
                    $i++;
                    $main->addField('sub',$sub);
                    unset($sub);
              }
              $ri->unset_();
        } else   $main->addField('no_goods','');

     //    echotree($main);

         unset($main);



      }
      else
                    header('Location: /error404');
      $r->unset_();

  }
  /*else {
         $main = new outTree($p_FILENAME);
         $r = new Select($GLOBALS['db'],'select * from '.$GLOBALS['sections_table'].' where parent="1" order by sort');
         if ($r->num_rows)   addRecords($main,$r,'img');
        // $site->addField($GLOBALS['currentSection'],$main);
            //        unset($main);
  }
  */
                    //header('Location: /error404');
  //$r->unset_();

    if (isset($main)) {
            $site->addField($GLOBALS['currentSection'],$main);
                 unset($main);
    }
 ?>
