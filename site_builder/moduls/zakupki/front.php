<?php
 //список всех мастеров
 include('config.php');
 include($inc_path.'/service/class.pager.php');
 include('functions.php');
 unset($main);
 $FILENAME = $front_html_path.'front.html';

//var_dump($_GET);

 include($inc_path.'/myfunc.php');
 $main = &addInCurrentSection($FILENAME);

  $where = '1 = 1';
/*
 ///Отправка заявки

 if ($_POST['send_zayav']>0){
    require_once($inc_path."/phpmailer/func.mailViaSMTP.php");
    foreach ( $_POST as $key => $value)
               $$key=$value;

    $flag = true; $str_fieldsWF = '';
    foreach ( $fieldsWithoutFail as $value) {
         //echo "dd";
       $str_fieldsWF.= ('\''.$value.'\',');
       $flag = $flag && !empty($$value);
    }
   	if ($flag)  {

      if ($kod=='23') {
         //echo "s";
         $text = date('d.m.Y').' в '.date('H:i:s').' на сайте '.$_SERVER['SERVER_NAME'].' в разделе Закупки была отправлена заявка на тендер.
№ тендера: '.stripslashes($number).'
Email: '.stripslashes($email).'
Контактное лицо: '.stripslashes($name).'
Телефон: '.stripslashes($phone);

         $mail = &newViaSMTP('mail_feed');
         $mail->Subject = $tema;
         $subm = sendViaSMTP($mail,$text,false);
       //  header('Location: '.$GLOBALS['strPATH'].'?subm='.$subm.'#f');

         $mess = ($subm>0 ? 'ok' : 'er');
      } else $mess =  'er_kod';
    } else $mess =  'er_pole';
    $main->addField('message',$mess);

 };
 ///Конец Отправка заявки

 addSprav($main,'zakupki_zak',$_GET['zaktype_id'],'zak_list');
 addSprav($main,'zakupki_types',$_GET['type_id'],'type_list');
 addSprav($main,'region',$_GET['region_id'],'region_list');
 //$where = 'z.id_city='.$_COOKIE['id_city'].' and zt.id_zakaz = z.id '.$razdel;


 if ($_GET['zakaz_num'] >0 )  $where .= " and number like '%$_GET[zakaz_num]%'";
 if ($_GET['type_id'] >0 )  $where .= " and type = '$_GET[type_id]'";
 if ($_GET['region_id'] >0 )  $where .= " and region = '$_GET[region_id]'";
 if ($_GET['zaktype_id'] >0 )  $where .= " and zakon_type = '$_GET[zaktype_id]'";
  */
  addSprav_sql($main,'positions','placingWay_name',$_GET['placingWay_name'],'placingWay_name');
  addSprav($main,'grbs_sprav',$_GET['id_grbs'],'grbs');
//   function addSprav_sql(&$main,$table,$field,$selected,$sub_name) {

 $r1 = new Select($db,'SHOW COLUMNS FROM plan');
 while( $r1->next_row())   $fields[] = $r1->result('Field');

 $r1 = new Select($db,'SHOW COLUMNS FROM positions');
 while( $r1->next_row())   $fields[] = $r1->result('Field');

 $r1 = new Select($db,'SHOW COLUMNS FROM products');
 while( $r1->next_row())   $fields[] = $r1->result('Field');

 $r1 = new Select($db,'SHOW COLUMNS FROM org');
 while( $r1->next_row())   $fields[] = $r1->result('Field');

//var_dump($fields);
  foreach ($_GET as $key =>$val) {
		 if ( in_array($key,$fields) && strlen($val) >0 ) {
              $word=$val;
		    // $word= htmlspecialchars ( addslashes (urldecode($val)));
		    // $word=trim(preg_replace('/\s+/', ' ', $word));
		     $words= explode(' ', $word);
		     $wh ='';
	         foreach ( $words as $sw ) {
		            $wh .= "and $key LIKE '%$sw%'";
	         };

		     $where .= ' and ('.substr($wh, 3).')';
		 };
         if (strlen($key) >0 ) $main->addField($key,$val);
         $link="/word/".urlencode($word);
 };


// echo $where;
 $link='';
 //$query = "select distinct(id), z.* from zakaz z ,zakaz_types zt where z.id_city=$id_city and z.id = zt.id_zakaz and $where order by date desc";
 if (strlen($_GET['orderBy'])>0) {
    if ($_GET['orderBy'] == 'purchasePlacingTerm') { $order = " order by purchasePlacingTerm_year $_GET[orderType],purchasePlacingTerm_month $_GET[orderType]";
      } else if ($_GET['orderBy'] == 'contractExecutionTerm') { $order = " order by contractExecutionTerm_year $_GET[orderType],contractExecutionTerm_month $_GET[orderType]"; }
        else $order = " order by $_GET[orderBy] $_GET[orderType]";

   // echo $order;
    $link = "/orderBy/$_GET[orderBy]/orderType/$_GET[orderType]";
    if ($_GET['orderType'] == 'asc') {
        $main->addfield($_GET['orderBy'].'_arr','<img src="/i/arr_down.gif">');
       // $main->addfield($_GET['orderBy'].'_ord','desc');
    }  else {
        $main->addfield($_GET['orderBy'].'_arr','<img src="/i/arr_up.gif">');
       // $main->addfield($_GET['orderBy'].'_ord','asc');
    };
 }  else  $order = " ";

 //var_dump($_SERVER["REQUEST_URI"]);
 //$link=
//echo $order;

 //$query = "select *  from positions pos,plan pl,products pr, org o where o.spz=pl.regNum and pos.plan_id=pl.plan_id and pr.position_id=pos.id and $where $order";
 $query = "select *  from positions pos,plan pl, org o where o.spz=pl.regNum and pos.plan_id=pl.plan_id and  $where $order";
// echo $query;
//echo $_GET['cp'];
 if ((int)$_GET['cp']>0) $page_num=$_GET['cp']; //else $page_num=1;
 if ($pg = Pagers::DA($db,'','', $GLOBALS[$modulName.'_fcount'],$_GET['cp'],'/'.$site->pageid.$link,null,null,$query)) {


   $pg->addPAGER($main);
   $r = $pg->r;

   $sql_itog = "select sum(pos.contractMaxPrice) as sum  from positions pos,plan pl, org o where pos.plan_id=pl.plan_id and $where $order";
   $r_itog= new Select($db,$sql_itog);
   if ( $r_itog->next_row())   $main->addField('itogo_contractMaxPrice',round($r_itog->result('sum'),2));


   $i=($page_num - 1)*$GLOBALS[$modulName.'_fcount']+1;

   $main->addField('cnt',$r->num_rows());
   if ($r->num_rows() >0) {
           $main->addField('tbl','');
		   $fields = array('contractSubjectName','contractMaxPrice','name','minRequirement','summax','placingWay_name','positionNumber','publishDate','versionNumber','customer_fullName','contractExecutionTerm','purchasePlacingTerm');
		   foreach ($fields as $key) {

				if ($_GET['orderBy']==$key && $_GET['orderType']=='asc')
		          $main->addfield($key.'_ord','desc');
		        else  $main->addfield($key.'_ord','asc');

		   };
   };

   ///дерево ОКПД

   $tree_FILENAME = $front_html_path.'form_tree.html';


  //вывод дерево КБК
  //$tree = new outTree($tree_FILENAME);
  //$sub = new outTree();
 // ShowTree($sub, 1);
  //$tree->addField('sub_tree',&$sub);
  //unset($sub);
   //echotree($sub);

  $main->addField('tree1',$sub);

   while ($r->next_row()) {

        unset($sub);
        $sub = new outTree();
        $r->addFields($sub,$ar=array('id','contractMaxPrice','minRequirement','summax','contractExecutionTerm_month','contractExecutionTerm_year','purchasePlacingTerm_year','purchasePlacingTerm_month','placingWay_name','positionNumber','publishDate','versionNumber','customer_fullName','position_id'));
        $sub->addfield('number',str_replace('№','',trim(($r->result('number')))));
        $sub->addfield('contractSubjectName',htmlspecialchars($r->result('contractSubjectName'),null, "windows-1251"));
        $sub->addfield('name',str_replace('amp;','',str_replace('#','',str_replace('&','',htmlspecialchars($r->result('name'),null, "windows-1251")))));
        $name= str_replace('#','',htmlspecialchars($r->result('name')));
       // echo str_replace('&','',htmlspecialchars($name));
        $sub->addfield('ppnum',$i);
        $sub->addfield('first_price',number_format((int)$r->result('first_price'), 2, ',', ' '));
        $sub->addfield('date_publ',to_date($r->result('date_publ')));
        $sub->addfield('srok_podachi',to_date($r->result('srok_podachi'),1));
        $sub->addfield('date_provedenia',to_date($r->result('date_provedenia'),1));

        $query = "select *  from products where position_id=".$r->result('id');
        $r_pr = new Select($db,$query);
        while ($r_pr->next_row()) {
        //echo "1";
             unset($sub_pr);
             $sub_pr = new outTree();
             $r_pr->addFields($sub_pr,$ar=array('id','minRequirement','name','summax'));
             $sub->addField('sub_products',$sub_pr);
        };
        $main->addField('sub',$sub);

        $i++;
   };
 } else  {

      $main->addField('no_sub','');
      $main->addField('cnt','0');
  };

 $main->addField('site',$_SERVER['HTTP_HOST']);
   //$main->addField('site',&$sub);
 ?>

