<?php
   //���������� � ����� ���������� �������� ��, ���������� �������� regions.php
   ///��������������� ������

   include "db_connect.php";

   function parse_txt($txt,$region){

         while ( $txt = strstr($txt,'<input id="regionIds') ) {
            //echo $txt;
            //$id = substr($txt,22,strpos($txt,'" name')-22);
            //echo $id;
            $txt = strstr($txt,'value="');
            $number = substr($txt,7,strpos($txt,'"/>')-7);
            $txt=strstr($txt,'<label for="regionId');
            $txt=strstr($txt,'">');
            $name=substr($txt,2,strpos($txt,'</label>')-2);

            mysql_query("insert into region(number,name,district) values ('$number', '$name',$region)");

      };
   };


  function parse_district($region) {
        $fp = fsockopen ("zakupki.gov.ru", 80, $errno, $errstr);
      //������ �������� � ��������� ��
        $posts = "/epz/main/public/extendedsearch/extended_search.html?districtIds=$region";
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

        //������ ���� ���������
        $begin=false; $first=false;  $txt=''; $end=false;
        while (!feof($fp)) {
              $data =  fgets ($fp);
              if ("\r\n" !== $data) {

                  //���� ���������� ����� ���� � ��
                  if (strpos($data,'<td class="manySelect" id="manySelect_regions">') !==false and !$end) {
                          $begin =true;
                          $txt='';
                  };

                  //����� ������ � txt ���� �� ��������� ����� ��
                  if ($begin and !$end) {
                        if (strpos($data,'<td class="manySelect">') !==false)
                          {
                             $begin =true;
                             parse_txt($txt,$region);
                             $end = true;
                          };
                        $txt.=$data;
                  };



              };

        };
        fclose($fp);
  };


  //������� ������ ������
  mysql_query("delete from region");
  $q = mysql_query("select * from district ");

  while ($row = mysql_fetch_array($q)) {
         parse_district($row['number']);
         //die;
  };
?>


