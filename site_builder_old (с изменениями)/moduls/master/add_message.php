<?php
 //список всех мастеров
 include('config.php');
 require_once($inc_path."/phpmailer/send.php");

 unset($main);
 $FILENAME = $front_html_path.'add_message.html';

 $main = &addInCurrentSection($FILENAME);

 if (!$_GET['id'] >0) header('Location:/');
 if (!$_SESSION['user'] >0) header('Location:/');

 //добавили отзыв
 if ($_POST['send'] >0 ) {
      $about= htmlspecialchars ( addslashes ($_POST['message']));
      $r1 = new Select($db,"insert into messages (user_from,user_to,date,about,is_read)
                            values ($_SESSION[user],$_GET[id],".time().",'$about',0 )");

          //письмо пользователю
          $r1 = new Select($db,"select *  from users where id = ".$_GET['id']);
          $r1->next_row();
          //echo $r1->result('email');
          if ($r1->result('new_messages') >0) {

                $r2 = new Select($db,"select *  from users where id = $_SESSION[user]");
                $r2->next_row();
                $master="<a href='http://$_SERVER[SERVER_NAME]/master_profile/id/$_SESSION[user]'>".$r2->result('fio')."</a>";
                $text = date('d.m.Y').' в '.date('H:i:s').' на сайте '.$_SERVER['SERVER_NAME'].' '.$master.' отправил Вам личное сообщение:
'.$about;


                //$mail = &newViaSMTP('mail_feed');
                $subject = 'Личное сообщение на сайте '.$_SERVER['SERVER_NAME'];
                //$subm = sendViaSMTP($mail,$text,true);

                $subm=mailViaSMTP($text,'mail_send',$r1->result('email'),$subject,true);
                        //reload('', $f = array('subm' => intval($subm)) );

          };
 };

 add_user_info($main,$_GET['id'] ,'user_to');


 $r = new Select($db,"select * from messages where (user_from = $_SESSION[user] and user_to=$_GET[id] ) or (user_from =$_GET[id] and user_to= $_SESSION[user] ) order by date ");

 while ($r->next_row()) {

        unset($sub);
        $sub = new outTree();
        $r->addFields($sub,$ar=array('about'));
        $sub->addField('date',make_date($r->result('date'),true));

       // add_user_info($sub,$_GET['id'] ,'user_to');
        add_user_info($sub,$r->result('user_from') ,'user_from');

        $main->addField('sub',$sub);
  };

  $r = new Select($db,"update messages set is_read=1 where user_from = $_GET[id] and user_to=$_SESSION[user] ");
 ?>

