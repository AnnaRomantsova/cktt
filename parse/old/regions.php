<?php
   ///закачивает с сайта закупок списки регионов и субъектов РФ

   include "db_connect.php";

   function parse($txt){
    //   echo $txt;
         while ( $txt = strstr($txt,'<input id="districtIds') ) {
            //echo $txt;
            //$id = substr($txt,22,strpos($txt,'" name')-22);
            //echo $id;
            $txt = strstr($txt,'value="');
            $number = substr($txt,7,strpos($txt,'"/>')-7);
            $txt=strstr($txt,'<label for="district');
            $txt=strstr($txt,'">');
            $name=substr($txt,2,strpos($txt,'</label>')-2);

            mysql_query("insert into district(id,number,name) values ('$id', '$number', '$name')");
      };
                                        // echo "insert into zakupki(zakaz_name,number,customer,first_price,date_publ,date,author,link)
                                        // values ('$zakaz_name', '$number', '$customer', '$first_price', '$date_publ','','','')<br>";
   };

  //очищаем старые данные
  mysql_query("delete from district");


  $fp = fsockopen ("zakupki.gov.ru", 80, $errno, $errstr);
//парсер регионов и субъектов РФ
  $posts = "/epz/main/public/extendedsearch/extended_search.html";
  //echo $posts."<br>";
  $url="zakupki.gov.ru";
  $query="GET ".$posts." HTTP/1.0\r\n".
      "Host: $url\r\n".
      "Connection: keep-alive\r\n".
      "Cache-Control: max-age=0\r\n".
      "Accept: text/html, application/xml;q=0.9, application/xhtml+xml, image/png, image/jpeg, image/gif, image/x-xbitmap, */*;q=0.1\r\n".
      "User-Agent: ".$_SERVER['HTTP_USER_AGENT']."\r\n".
      "Accept-Language: ru,en;q=0.9\r\n".
      "Accept-Charset: windows-1251, utf-8, utf-16, iso-8859-1;q=0.6, *;q=0.1\r\n\r\n";

  fputs ($fp, $query);
  $data='';

  //читаем файл построчно
  $begin=false; $first=false;  $txt=''; $end=false;
  while (!feof($fp)) {
        $data =  fgets ($fp);
        if ("\r\n" !== $data) {

            //если встретился новый блок с ФО
            if (strpos($data,'<td class="manySelect" id="manySelect_districts">') !==false and !$end) {
                    $begin =true;
                    $txt='';
            };

            //копим строки в txt пока не встретили конец ФО
            if ($begin and !$end) {
                  if (strpos($data,'<td class="manySelect" id="manySelect_regions">') !==false)
                    {
                       $begin =true;
                       parse($txt);
                       $end = true;
                    };
                  $txt.=$data;
            };



        };

  };
  fclose($fp);


  include "districts.php";
?>


