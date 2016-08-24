<?php

// Запрет на кэширование
header("Expires: Mon, 23 May 1995 02:00:00 GTM");
header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GTM");
header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache");
//
//В этом файле хранятся логин и пароль к БД
require_once("../setup.php");
include_once($inc_path.'/db_conect.php');
include_once($inc_path.'/func.front.php');
$shablon = 'front/zakupki/show_one.html';

$main = new outTree();

 $id=$_POST['id'];
if ( $id >0 )
   {

       $r = new Select($db,'select * from zakupki where id= '.$id);
       if ( $r->next_row() ) {
                $r->addFields($main,$ar=array('id','number','customer','link','docs_link'));
                $main->addfield('zakaz_name',htmlspecialchars_decode($r->result('zakaz_name')));
		        $main->addfield('date_publ',to_date($r->result('date_publ')));
		        $main->addfield('srok_podachi',to_date($r->result('srok_podachi'),1));
		        $main->addfield('date_provedenia',to_date($r->result('date_provedenia'),1));
                $main->addfield('first_price',number_format($r->result('first_price'), 2, ',', ' '));
		        if (strlen($r->result('summ_zayavka'))>0) $main->addfield('summ_zayavka',$r->result('summ_zayavka'));
		           else  $main->addfield('summ_zayavka','Нет данных');
		        if (strlen($r->result('summ_contract'))>0) $main->addfield('summ_contract',$r->result('summ_contract'));
		           else  $main->addfield('summ_contract','Нет данных');

		        $r1 = new Select($db,'select * from zakupki_zak where id="'.$r->result('zakon_type').'"');
		        $r1->next_row();
		        $main->addfield('zakon_type',$r1->result('name'));

				$r1 = new Select($db,'select * from zakupki_types where id="'.$r->result('type').'"');
		        $r1->next_row();
		        $main->addfield('type',$r1->result('name'));

		        $r1 = new Select($db,'select * from region where id="'.$r->result('region').'"');
		        $r1->next_row();
		        $main->addfield('region',$r1->result('name'));

		        $r1 = new Select($db,'select * from zakupki_okato where id="'.$r->result('okato').'"');
		        $r1->next_row();
		        $main->addfield('okato',$r1->result('name'));

      };
     //echo "65645";
      out::_echo($main,$shablon);
  }





?>