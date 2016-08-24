<?php

  include('config.php');
  $lic_filename = $front_html_path.'lic.html';


  //echo "!!!!!!!!";
 //die;
  //var_dump($_POST);

  $patch=$HTTP_SERVER_VARS[HTTP_REFERER];

  $message = '';
  unset($main);
  if (!(isset($_SESSION['user']))) {
      header ( "location: http://" . $_SERVER['HTTP_HOST']);
  };


 //просмотр своего профиля

  $main = &addInCurrentSection($lic_filename);

  $r = new Select ( $db, 'select * from site_pages where id =142' );
  if ($r->next_row() > 0) {
             $main->addField('text',strip_tags($r->result('content')));
  };

  $transid = 15;//$db->query("INSERT INTO `payments` (`comment_id`, `uid`, `time`, `status`) VALUES ($id, {$user['id']}, ".time().",0)");

  $price = '100';
  $mrh_login = 'ckttcktt';      // your login here
  $mrh_login = 'demo';
  $mrh_pass1 = 'qazxsw123';//'qazxsw321';         // merchant pass1 here
  $mrh_pass1 = 'password_1
  ';

  // order properties
  $inv_id = $transid;       // shop's invoice number
                                       // (unique for shop's lifetime)
  $inv_desc  = "Оплата услуг сайта BAZAMASTEROV.RU";   // invoice desc
  $out_summ = $price;


  $culture = "Ru";
  $encoding = "windows-1251";

// build CRC value
//
  $crc  = md5("$mrh_login:$out_summ:$inv_id:$mrh_pass1");
  $test = 'https://merchant.roboxchange.com/Index.aspx';
$test = 'http://test.robokassa.ru/Index.aspx';

  $url = "$test?"."MrchLogin=$mrh_login&OutSum=$out_summ&InvId=$inv_id".
                                       "&Desc=$inv_desc&SignatureValue=$crc".
                                       "&Culture=$culture&Encoding=$encoding";

   //echo $url;
// ROBOKASSA HTML-page
   $main->addField('url',$url);

  unset($main);

 ?>