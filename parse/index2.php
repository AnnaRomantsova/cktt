<?php

   ini_set("max_execution_time", "1000000");
   ini_set('display_errors', 1); error_reporting(E_ALL);

    function put_to_sprav($table_name,$name){

   	   if ($table_name=='zakupki_okato') {
   	     //echo $name;
        // echo "select * from $table_name where substring(name,1,7) = '".substr($name,0,7)."'<br>";
   	     $q1 = mysql_query("select * from $table_name where substring(name,1,7) = '".substr($name,0,7)."'");
   	   } else
   	     $q1 = mysql_query("select * from $table_name where name = '$name'");
       $row = mysql_fetch_array($q1);
       //var_dump($row);
     //  echo count( $row );
       if ( $row !==false)
             return $row['id'];
       else {

          $name=trim($name);
          $name = str_replace('&nbsp;',' ',$name);
          $q2 = mysql_query("insert into $table_name (name) values ('$name')");


       //   echo "insert into $table_name (name) values ('$name')";

          if ($table_name=='zakupki_okato')
   	         $q1 = mysql_query("select * from $table_name where substring(name,1,7) = '".substr($name,0,7)."'");
   	      else
   	         $q1 = mysql_query("select * from $table_name where name = '$name'");
         // echo "select * from $table_name where name = '$name'";
	      $row = mysql_fetch_array($q1);
	      if ($row !==false)
	           return $row['id'];
	   };

   };

   function open_page($link){
   	           $fp1 = fsockopen ("zakupki.gov.ru", 80, $errno, $errstr);
               $posts = substr($link,21);
               //echo $posts."<br>";
              // die;
             // echo $url;
               $url="zakupki.gov.ru";
 			   $query="GET ".$posts." HTTP/1.0\r\n".
                   "Host: $url\r\n".
                   "Connection: keep-alive\r\n".
                   "Cache-Control: max-age=0\r\n".
                   "Accept: text/html, application/xml;q=0.9, application/xhtml+xml, image/png, image/jpeg, image/gif, image/x-xbitmap, */*;q=0.1\r\n".
                   "User-Agent: Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1)\r\n".
                   "Accept-Language: ru,en;q=0.9\r\n".
                   "Accept-Charset: utf-8, windows-1251, utf-16, iso-8859-1;q=0.6, *;q=0.1\r\n".
                   "Cookie:routeepz0=2; userQueryId=c68bf7db-0a0f-401b-9e3d-07e70eed729e; __utma=123872897.582294190.1376941321.1376998158.1377085641.3; __utmz=123872897.1376998158.2.2.utmcsr=online.cktt.ru|utmccn=(referral)|utmcmd=referral|utmcct=/tenders.html; contentFilter=null; FRONT-Insert=R2037273463; _ym_visorc=b\r\n\r\n";

               fputs ($fp1, $query);
               stream_set_timeout($fp1, 300);

               $data =  fgets ($fp1);
               $info = stream_get_meta_data($fp1);
               if ($info['timed_out']) {
                  echo 'Сайт госзакупок опять временно недоступен!';
                  mail('anutabur@mail.ru','Парсинг закупок cktt.ru',"Проблема загрузки страницы $link");
                  die;
               };

               $data='';

               //читаем файл построчно
               $txt='';
               while (!feof($fp1)) {
                     $data =  fgets ($fp1);
                     if ("\r\n" !== $data) {
                         $txt.=$data;
                     };

               };
               fclose($fp1);
               return $txt;
   };

   function to_real($str) {

      $str = str_replace(chr(194).chr(160),'',$str);
      $str =  str_replace(',','.',$str);
      return $str;

   };

   function to_date($date_in,$time=''){
   	  $year=substr($date_in,6);
   	 // echo $year;
   	  $mon=substr($date_in,3,2);
   	  $day=substr($date_in,0,2);
   	  if ($time !==''){
   	     $hour = substr($time,0,2);
   	     $min = substr($time,3,2);
   	     return $year."-".$mon."-".$day." ".$hour.":".$min.":00";
   	  };
   	  return $year."-".$mon."-".$day;

   };


///парсит заказы
   function parse($txt,$region){
       global  $new_cnt;
       global  $updated_cnt;
       global  $old_cnt;

       $txt = strstr($txt,'<td class="tenderTd">');
       $txt = strstr($txt,'<dt');

       $type = substr($txt,4,strpos($txt,'</dt')-4);
       //echo $region;
       //die;
       $txt = strstr($txt,'<td class="descriptTenderTd');
       $txt = strstr($txt, '<a ');
       $txt = strstr( $txt,'>');
       $number =  substr($txt,5,strpos($txt,'<')-5);

       $txt=strstr($txt,'<a');
       //echo $number."<br>-----------";
       $customer = substr($txt,strpos($txt,'>')+1,strpos($txt,'</a')-strpos($txt,'>')-1);
      // echo $customer."<br>-----------";
       $txt=strstr($txt,'>');
       $txt=strstr($txt,'<a');
       $txt=strstr($txt,'href');
       $txt=strstr($txt,'"');
       //echo $number."<br>-----------";
       $link = substr($txt,1,strpos($txt,'"',2)-1);
       if (strpos($link,'zakupki.gov.ru') === false) $link = 'http://zakupki.gov.ru'.$link;
       if (strpos($link,'/pgz') >0) $zakon_type=1;
          elseif (strpos($link,'/223') >0) $zakon_type=2;
           elseif (strpos($link,'/ea44') >0) $zakon_type=3;


       $zakaz_name = substr($txt,strpos($txt,'>')+1,strpos($txt,'</a')-strpos($txt,'>')-1);
       //echo $desc."<br>-----------";
       $txt1=strstr($txt,'<dt>');
       $first_price = to_real(substr($txt1,strpos($txt1,'>')+1,strpos($txt1,'</dt')-strpos($txt1,'>')-1));


       $txt=strstr($txt,'<li class="publishingDate">');
       $date_publ =to_date(substr($txt,27,strpos($txt,'</li')-27));

       $txt=strstr($txt,'<li class="publishingDate">');
       $txt=strstr($txt,'</li>');
       $txt=strstr($txt,'<li class="publishingDate">');
       $date_change = to_date(substr($txt,27,strpos($txt,'</li')-27));
      // $date_change=to_date($date_change);

       $txt=strstr($txt,'<div class="reportBox">');
       $txt= strstr($txt,'<li>'); $txt= strstr($txt,'</li>');
       $txt= strstr($txt,'<li>');
       $docs_link = substr($txt,strpos($txt,'href=')+6,strpos($txt,'" target')-strpos($txt,'href=')-6);
       if (strpos($docs_link,'zakupki.gov.ru') === false) $docs_link = 'http://zakupki.gov.ru'.$docs_link;
      // echo $docs_link; die;

       $type_id = put_to_sprav('zakupki_types',$type);
       $q1 = mysql_query("select count(*) as cnt from zakupki where number = '$number'");
       $row = mysql_fetch_array($q1);
       if ( $row['cnt'] ==0) {
           mysql_query("insert into zakupki(zakaz_name,number,customer,first_price,date_publ,date_change,link,type,docs_link,not_del,region,zakon_type)
                                              values ('$zakaz_name', '$number', '$customer', '$first_price', '$date_publ','$date_change','$link','$type_id','$docs_link',1,$region,$zakon_type)");
          // echo "<br>".$link."<br>";
           parse_zakaz_info($link,$number);
       };
   };


    function parse_zakaz_info($link,$number) {
              // echo "$link<br>";
               $txt=open_page($link);

//////////// срок подачи заявок
               //echo $txt;
               //$txt1=strstr($txt,iconv('windows-1251','utf-8','Дата'));
             //  echo $txt1;
               $srok='';
               if (strpos($link,'/pgz') >0) {

	               $txt1=strstr($txt,'Дата и время окончания срока подачи заявок');
	               if ($txt1 !==false) {
		                   $txt1=strstr($txt1,'<td class="orderInfoCol2">');
			               $txt1= strstr($txt1,'<span class="iceOutTxt">');
			               $srok= strip_tags(trim(substr($txt1,24,strpos($txt1,'</span>')-24)));
		           };
		           if ($srok=='') {
			           	$txt1=strstr($txt,'Окончание подачи котировочных заявок');
		                if ($txt1 !==false) {
		                   //echo "ll";
		                   $txt1=strstr($txt1,'<span class="iceOutTxt">');
			               $txt1= strstr($txt1,'</span');
			               $txt1=strstr($txt1,'<span class="iceOutTxt">');
			               $srok= strip_tags(trim(substr($txt1,24,strpos($txt1,'</span>')-24)));
			            };
			       };
	               if ($srok=='') {
	               	  	$txt1=strstr($txt,'Вскрытие конвертов с заявками');
		                if ($txt1 !==false) {
		                   $txt1=strstr($txt1,'Дата и время');
			               $txt1=strstr($txt1,'<span class="iceOutTxt">');
			               $srok= strip_tags(trim(substr($txt1,24,strpos($txt1,'</span')-24)));
			            };
	               };
	               if ($srok=='') {
	               	  	$txt1=strstr($txt,'Срок предоставления предложений до');
		                if ($txt1 !==false) {
		                   $txt1=strstr($txt1,'<span class="iceOutTxt">');
			               $srok= strip_tags(trim(substr($txt1,24,strpos($txt1,'</span')-24)));
			            };
	               };
	               if ($srok=='') {
	               	  	$txt1=strstr($txt,'Дата окончания срока подачи заявок на участие');
		                if ($txt1 !==false) {
		                  $txt1=strstr($txt1,'<span class="iceOutTxt">');
			               $srok= strip_tags(trim(substr($txt1,24,strpos($txt1,'</span')-24)));
			            };
	               };
	               if ($srok=='') {
	               	  	$txt1=strstr($txt,'Срок предоставления');
		                if ($txt1 !==false) {
		                   $txt1=strstr($txt,'по');
		                   $txt1=strstr($txt1,'<span class="iceOutTxt">');
			               $srok= strip_tags(trim(substr($txt1,24,strpos($txt1,'</span')-24)));
			            };
	               };
               } else  {
                   $txt1=strstr($txt,'Дата и время окончания подачи заявок');
	               if ($txt1 !==false) {
	                   $txt1=strstr($txt1,'<td><span>');
		               //$txt1= strstr($txt1,'</td>');
		               $srok= strip_tags(trim(substr($txt1,10,strpos($txt1,'</td>')-10)));
		           }
               };
               if ($srok =='') {
               	   $txt1=strstr($txt,'Дата и время окончания подачи заявок');
	               if ($txt1 !==false) {
	                   $txt1=strstr($txt1,'<td>');
		               //$txt1= strstr($txt1,'</td>');
		               $srok= strip_tags(trim(substr($txt1,4,strpos($txt1,'</td>')-4)));
		           }
               };
              //echo $srok;
               if ($srok !=='') {
               	  //echo $srok;

                  if (strpos($srok,":")>0) $time=substr($srok,strpos($srok,":")-2,5);
                  $time='';
               	  $srok=to_date(substr($srok,0,10),$time);
                  mysql_query("update zakupki set srok_podachi='$srok' where number= '$number'");
               };

//////////Дата проведения
               $srok='';
               if (strpos($link,'/pgz') >0) {
	               $txt1=strstr($txt,'Дата и время проведения');
	               if ($txt1 !==false) {
			               $txt1= strstr($txt1,'<span class="iceOutTxt">');
			               $srok= strip_tags(trim(substr($txt1,24,strpos($txt1,'</span>')-24)));
		           };
		           if ($srok=='') {
			           	$txt1=strstr($txt,'Срок подачи котировочных заявок продлен до');
		                if ($txt1 !==false) {
		                   $txt1=strstr($txt1,'<span class="iceOutTxt">');
   			               $srok= strip_tags(trim(substr($txt1,24,strpos($txt1,'</span>')-24)));
			            };
			       };
			       if ($srok=='') {
			           	$txt1=strstr($txt,'Окончание подачи котировочных заявок');
		                if ($txt1 !==false) {
		                   $txt1=strstr($txt1,'Дата и время');
		                   $txt1=strstr($txt1,'<span class="iceOutTxt">');
   			               $srok= strip_tags(trim(substr($txt1,24,strpos($txt1,'</span>')-24)));
			            };
			       };
			       if ($srok=='') {
			           	$txt1=strstr($txt,'Подведение итогов');
		                if ($txt1 !==false) {
		                   $txt1=strstr($txt1,'Дата');
		                   $txt1=strstr($txt1,'<span class="iceOutTxt">');
   			               $srok= strip_tags(trim(substr($txt1,24,strpos($txt1,'</span>')-24)));
			            };
			       };
			       if ($srok=='') {
			           	$txt1=strstr($txt,'Дата и время проведения открытого аукциона');
		                if ($txt1 !==false) {

		                   $txt1=strstr($txt1,'<span class="iceOutTxt">');
   			               $srok= strip_tags(trim(substr($txt1,24,strpos($txt1,'</span>')-24)));
			            };
			       };

               } else {
                   $txt1=strstr($txt,'Дата и время подведения итогов');
	               if ($txt1 !==false) {
	                   $txt1=strstr($txt1,'<td><span>');
		               //$txt1= strstr($txt1,'</td>');
		               $srok= strip_tags(trim(substr($txt1,10,strpos($txt1,'</td>')-10)));
		           };
		           if ($srok=='') {
			           	$txt1=strstr($txt,'Дата и время рассмотрения и оценки котировочных заявок');
		                if ($txt1 !==false) {
		                  	$txt1=strstr($txt1,'<td><span>');
		                    $srok= strip_tags(trim(substr($txt1,10,strpos($txt1,'</td>')-10)));
			            };
			       };
			       if ($srok=='') {
			           	$txt1=strstr($txt,'Дата и время рассмотрения заявок');
		                if ($txt1 !==false) {
		                  	$txt1=strstr($txt1,'<td><span>');
		                    $srok= strip_tags(trim(substr($txt1,10,strpos($txt1,'</td>')-10)));
			            };
			       };
			       if ($srok=='') {
			           	$txt1=strstr($txt,'Дата и время проведения аукциона');
			           	//echo $txt1;
		                if ($txt1 !==false) {
		                  	$txt1=strstr($txt1,'<span>');
		                  	//echo $txt1;
		                    $srok= strip_tags(trim(substr($txt1,6,strpos($txt1,'</td>')-6)));
		                    //echo $srok;
			            };
			       };
               };
               if ($srok=='') {
			           	$txt1=strstr($txt,'Дата проведения');
			            if ($txt1 !==false) {
		                  	$txt1=strstr($txt1,'<td>');
		                    $srok = strip_tags(trim(substr($txt1,4,strpos($txt1,'</td>')-4)));
			            };
			            $txt1=strstr($txt,'Время проведения');
			            if ($txt1 !==false) {
		                  	$txt1=strstr($txt1,'<td>');
		                    $srok .= strip_tags(trim(substr($txt1,4,strpos($txt1,'</td>')-4)));
			            };
			   };
               if ($srok !=='') {

               	  if (strpos($srok,":")>0) $time=substr($srok,strpos($srok,":")-2,5);
               	  $srok=to_date(substr($srok,0,10),$time);
                  //echo $srok;  die;
                  mysql_query("update zakupki set date_provedenia='$srok' where number= '$number'");
               };
              // else {echo $link; echo $txt; }
              //echo $srok;
               //echo "update zakupki set date_provedenia='$srok' where number= '$number'";
              // die;

////////размер обеспечения заявки
             $srok='';
               if (strpos($link,'/pgz') >0) {
	               $txt1=strstr($txt,'Размер обеспечения заявки');
	               if ($txt1 !==false) {
			               $txt1= strstr($txt1,'<span class="iceOutTxt">');
			               $srok= strip_tags(trim(substr($txt1,24,strpos($txt1,'</span>')-24)));
		           };
               };
               if ($srok=='') {
			           	$txt1=strstr($txt,'Размер обеспечения заявок');
			            if ($txt1 !==false) {
		                  	$txt1=strstr($txt1,'<td>');
		                    $srok = strip_tags(trim(substr($txt1,4,strpos($txt1,'</td>')-4)));
			            };
			   };
               if ($srok !=='')
                  mysql_query("update zakupki set summ_zayavka='$srok' where number= '$number'");
////////Размер обеспечения исполнения контракта
             $srok='';
               if (strpos($link,'/pgz') >0) {
	               $txt1=strstr($txt,'Размер обеспечения исполнения контракта');
	               if ($txt1 !==false) {
			               $txt1= strstr($txt1,'<span class="iceOutTxt">');
			               $srok= strip_tags(trim(substr($txt1,24,strpos($txt1,'</span>')-24)));
		           };
               };
               if ($srok=='') {
			           	$txt1=strstr($txt,'Размер обеспечения исполнения контракта');
			            if ($txt1 !==false) {
		                  	$txt1=strstr($txt1,'<td>');
		                    $srok = strip_tags(trim(substr($txt1,4,strpos($txt1,'</td>')-4)));
			            };
			   };
               if ($srok !=='')
                  mysql_query("update zakupki set summ_contract='$srok' where number= '$number'");
/////ОКАТО

             $srok='';

             if (strpos($link,'/pgz') >0) {
	               $txt1=strstr($txt,'Классификация товаров, работ и услуг');
	               if ($txt1 !==false) {
		                   $txt1=strstr($txt1,'<td class="icePnlGrdCol1">');
			               $txt1= strstr($txt1,'</td>');
			               $txt1=strstr($txt1,'<span class="iceOutTxt">');
			               $srok= strip_tags(trim(substr($txt1,24,strpos($txt1,'</span>')-24)));
		           };
		           if ($srok =='') {

		              if (strpos($txt,'Предмет контракта') >0) {
		                 //
		              	   $txt1=strstr($txt,'Общая информация');
		              	   $txt1=strstr($txt1,'href="');
		              	   $link= strip_tags(substr($txt1,6,strpos($txt1,'">')-6));
		              	   $txt2=open_page($link);
		              	   //echo $link;
		              	   $txt1=strstr($txt2,'Классификация товаров, работ и услуг');
				           if ($txt1 !==false) {
			                   $txt1=strstr($txt1,'<span class="iceOutTxt"><span class="iceOutTxt">');
				               $srok= strip_tags(trim(substr($txt1,24,strpos($txt1,'</span>')-24)));
				              // echo $srok;
				               $txt1=strstr($txt1,'</span');
					       };

		              };
		           };

             };
             if ($srok=='') {
			    	$txt1=strstr($txt,'Код по ОКПД');
			        if ($txt1 !==false) {

		              	$txt1=strstr($txt1,'<td>');
		              	$txt1= strstr($txt1,'</td>');
		                $txt1=strstr($txt1,'<td>');
		                $srok = strip_tags(trim(substr($txt1,4,strpos($txt1,'</td>')-4)));
			        };

			 };

             $q1 = mysql_query("select id from zakupki where number= '$number'");
             $row = mysql_fetch_array($q1);
			 if ($row !==false) $id_zakupki=$row['id'];
             //echo "<br>".$number."  zak=$id_zakupki, ";
             //если найден ОКПД (1 лот)
             if ($srok !=='') {
                  $okato_id = put_to_sprav('zakupki_okato',$srok);

                //  echo "1-$okato_id-$srok";
                 // die;
                  mysql_query("update zakupki set okato='$okato_id' where number= '$number'");
			      mysql_query("insert into zakupki_lots(id_zakupki,id_okato) values ($id_zakupki, $okato_id)");
             //ищем в лотах
             } else if (strpos($txt,'<a href="#">Список лотов</a>') >0){
                 // if ($link =="http://zakupki.gov.ru/pgz/public/action/orders/info/common_info/show?source=epz&notificationId=8249511")
                 // echo "!!!";
                  $link1=str_replace('common-info','lot-list',$link);
                  $txt2 = open_page($link1);
                  $txt1=strstr($txt2,'<tbody>');
	              if ($txt1 !==false) {
	                   $i=1;
	                   while ($txt1 !== false && $i<30) {
	                           $txt1=strstr($txt1,'<tr>');
			                   $txt1=strstr($txt1,'<td');  $txt1= strstr($txt1,'</td>');
				               $txt1=strstr($txt1,'<td');  $txt1= strstr($txt1,'</td>');
				               $txt1=strstr($txt1,'<td');  $txt1= strstr($txt1,'</td>');
		   		               $txt1=strstr($txt1,'<td');  $txt1= strstr($txt1,'</td>');
		   		               $txt1=strstr($txt1,'<td');
				               $srok= strip_tags(trim(substr($txt1,4,strpos($txt1,'</td>')-4)));

				               if ($srok !=='') {
				                  $okato_id = put_to_sprav('zakupki_okato',$srok);
				                  $q1 = mysql_query("select count(*) as cnt from zakupki_lots where id_zakupki = $id_zakupki and id_okato=$okato_id");
				                  //echo "select count(*) as cnt from zakupki_okato where id_zakupki = $id_zakupki and id_okato=$okato_id";
								  $row = mysql_fetch_array($q1);
								  if ( $row['cnt'] ==0) {
				                          //echo "2-$okato_id";
						                  mysql_query("update zakupki set okato='$okato_id' where number= '$number'");
									      mysql_query("insert into zakupki_lots(id_zakupki,id_okato) values ($id_zakupki, $okato_id)");
						          };
						       };
				          	  $i++;
				       };
                  };
			 };
   };


   function parse_region($region,$district,$page) {
      // die;
	   global $cnt_page;

       $fp = fsockopen ("zakupki.gov.ru", 80, $errno, $errstr);
      // echo "f";
       if (!$fp) {
            //echo "dff";
               echo "$errstr ($errno)&lt;br&gt;\n";

       } else {
               //$posts = "/epz/order/extendedsearch/search.html?sortDirection=false&sortBy=UPDATE_DATE&recordsPerPage=_50&pageNo=$page&placeOfSearch=FZ_44%2CFZ_223%2CFZ_94&searchType=ORDERS&morphology=false&strictEqual=false&orderPriceCurrencyId=-1&okdpWithSubElements=false&regionIds=$region&orderStages=AF&headAgencyWithSubElements=false&smallBusinessSubject=I&rnpData=I&executionRequirement=I&penalSystemAdvantage=I&disabilityOrganizationsAdvantage=I&russianGoodsPreferences=I&orderPriceCurrencyId=-1&okvedWithSubElements=false&jointPurchase=false&byRepresentativeCreated=false&selectedMatchingWordPlace223=NOTICE_AND_DOCS&matchingWordPlace94=NOTIFICATIONS&changeParameters=true&law44.okpd.withSubElements=false";               echo $posts;
               //$posts = "/epz/main/public/extendedsearch/search.html?sortDirection=false&sortBy=UPDATE_DATE&recordsPerPage=_50&pageNo=$page&searchPlace=EVERYWHERE&morphology=false&strictEqual=false&orderPriceCurrencyId=-1&okdpWithSubElements=false&districtIds=$district&regionIds=$region&orderStages=AF&headAgencyWithSubElements=false&smallBusinessSubject=I&rnpData=I&executionRequirement=I&penalSystemAdvantage=I&disabilityOrganizationsAdvantage=I&russianGoodsPreferences=I&contractPriceCurrencyId=-1&okvedWithSubElements=false&changeParameters=true";
               $posts = "/epz/order/extendedsearch/search.html?sortDirection=false&sortBy=UPDATE_DATE&recordsPerPage=_50&pageNo=$page&placeOfSearch=FZ_44%2CFZ_223%2CFZ_94&searchType=ORDERS&morphology=false&strictEqual=false&orderPriceCurrencyId=-1&okdpWithSubElements=false&regionIds=$region&orderStages=AF&headAgencyWithSubElements=false&smallBusinessSubject=I&rnpData=I&executionRequirement=I&penalSystemAdvantage=I&disabilityOrganizationsAdvantage=I&russianGoodsPreferences=I&orderPriceCurrencyId=-1&okvedWithSubElements=false&jointPurchase=false&byRepresentativeCreated=false&selectedMatchingWordPlace223=NOTICE_AND_DOCS&matchingWordPlace94=NOTIFICATIONS&changeParameters=true&law44.okpd.withSubElements=false";
             //  $posts = "/epz/order/extendedsearch/search.html?sortDirection=false&sortBy=UPDATE_DATE&recordsPerPage=_50&pageNo=1&placeOfSearch=FZ_44%2CFZ_223%2CFZ_94&searchType=ORDERS&morphology=false&strictEqual=false&orderPriceCurrencyId=-1&okdpWithSubElements=false&regionIds=5277357&orderStages=AF&headAgencyWithSubElements=false&smallBusinessSubject=I&rnpData=I&executionRequirement=I&penalSystemAdvantage=I&disabilityOrganizationsAdvantage=I&russianGoodsPreferences=I&orderPriceCurrencyId=-1&okvedWithSubElements=false&jointPurchase=false&byRepresentativeCreated=false&selectedMatchingWordPlace223=NOTICE_AND_DOCS&matchingWordPlace94=NOTIFICATIONS&matchingWordPlace44=NOTIFICATIONS&changeParameters=true&showLotsInfo=false&extendedAttributeSearchCriteria.searchByAttributes=NOTIFICATION&law44.okpd.withSubElements=false";
               //echo $posts;
               $url="zakupki.gov.ru";
               $query="GET ".$posts." HTTP/1.0\r\n".
                   "Host: $url\r\n".
                   "Connection: keep-alive\r\n".
                   "Cache-Control: max-age=0\r\n".
                   "Accept: text/html, application/xml;q=0.9, application/xhtml+xml, image/png, image/jpeg, image/gif, image/x-xbitmap, */*;q=0.1\r\n".
                   "User-Agent: Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1)\r\n".
                   "Accept-Language: ru,en;q=0.9\r\n".
                   "Accept-Charset: utf-8, windows-1251, utf-16, iso-8859-1;q=0.6, *;q=0.1\r\n".
                   "Cookie:routeepz0=2; userQueryId=c68bf7db-0a0f-401b-9e3d-07e70eed729e; __utma=123872897.582294190.1376941321.1376998158.1377085641.3; __utmz=123872897.1376998158.2.2.utmcsr=online.cktt.ru|utmccn=(referral)|utmcmd=referral|utmcct=/tenders.html; contentFilter=null; FRONT-Insert=R2037273463; _ym_visorc=b\r\n\r\n";

               fputs ($fp, $query);
               stream_set_timeout($fp, 300);

               $data =  fgets ($fp);
               $info = stream_get_meta_data($fp);
               if ($info['timed_out']) {
                  echo 'Сайт госзакупок опять временно недоступен!';
                  mail('anutabur@mail.ru','Парсинг закупок cktt.ru',"Проблема загрузки страницы ".$url.$posts);
                  die;
               };

               $data='';

               //находим  id region
                $q1 = mysql_query("select * from region where number = '$region'");
			    $row = mysql_fetch_array($q1);
			    if ($row !==false) $region=$row['id'];

              // $h = fopen("my_file_$page.html","w");

               //читаем файл построчно
               $begin=false; $first=false;  $txt=''; $end=false;
             //  $i=1;
               while (!feof($fp)) {
                     $data =  fgets ($fp);

                  // echo $data;
                    // die;
                     if ("\r\n" !== $data) {
                        // fputs ($h, $data);
                         //если встретился новый блок с заявками то парсим предыдущий
                         if (strpos($data,'<div class="registerBox">') !==false and !$end) {
                                 $begin =true;
                                 if ($txt !=='') parse($txt,$region);
                                 $txt='';
                                // if ($i==4) die;  $i++;
                         };

                         //копим строки в txt пока не встретили конец заявок
                         if ($begin and !$end) {
                               if (strpos($data,'<script type="text/javascript">') !==false)
                                 {
                                    parse($txt,$region);
                                    $end = true;
                                 };
                               $txt.=$data;
                         };

                         //подсчитываем кол-во страниц
                         if (strpos($data,'<div class="allRecords">') !==false)
                         {
                            $page_cnt = substr($data,strpos($data,'Records">') + 36, strpos ($data,'</div>') - strpos($data,'Records">') - 36);
                            if ($page_cnt>0) $cnt_page = ceil($page_cnt/50);
                              else echo "Ошибка подсчета кол-ва страниц";
                              //if ($page==2) $cnt_page=1;
                              echo $cnt_page;

                         }
                     };
                    //
                    //
               };

               fclose($fp);
         };



   };

    $local_path  = '';
  // $local_path  = dirname(__FILE__).'/';

   include $local_path."db_connect.php";
   //include $local_path."func.php";
   $start = microtime(true);

    mysql_query("SET NAMES 'utf8'");
mysql_query("SET CHARACTER SET 'utf8'");
mysql_query("SET SESSION collation_connection = 'utf8_general_ci'");

  $q = mysql_query("select * from region where parse=1");
  while ($row = mysql_fetch_array($q)) {
      //
     //echo "f";
      mysql_query("delete from zakupki_lots where id_zakupki in (select id from zakupki where region = $row[id])");
      mysql_query("delete from zakupki where region=$row[id]");
      //die;
      $cnt_page=2;
      ///*
	  for ($i=1;$i<=$cnt_page; $i++) {
	     parse_region($row['number'],$row['district'],$i);
	     //if ($i==2) exit;
	  };
      //*/
	  $q = mysql_query("select * from zakupki where date_provedenia is null or okato is null or srok_podachi is null  and region = $row[id]");
	  while ($row = mysql_fetch_array($q)) {
		      parse_zakaz_info($row['link'],$row['number']);
	  };
  };





  mysql_query("delete from zakupki_okato where id not in (select DISTINCT okato from zakupki) and id<>1");
  mysql_query("delete from zakupki_types where id not in (select DISTINCT type from zakupki) and id<>1");


    $end = microtime(true);
    $time=$end-$start;
    $min=floor($time/60);
    $seconds= $time % 60;
    echo "Work time for region: $min min $seconds sec <br>";
   // echo "Всего пройдено закупок: ".($new_cnt+$updated_cnt+$old_cnt);




?>
