<?php
 //список всех мастеров
 include('config.php');

 unset($main);
 $FILENAME = $front_html_path.'messages.html';

 $main = &addInCurrentSection($FILENAME);

 //if (!$_GET['id'] >0) header('Location:/');
 if (!$_SESSION['user'] >0) header('Location:/');

 //список юзеров с которыми общался
 $r = new Select($db,"SELECT max(id) as id,user_id from (
         (select max(id) as id,user_to as user_id  from messages where user_from = $_SESSION[user] group by user_to  )
          UNION all
         (select max(id) as id,user_from as user_id from messages where user_to = $_SESSION[user] group by user_from  )
          ) as tbl GROUP by user_id");
 $i=1;
 while ($r->next_row()) {

        $r1 = new Select($db,"SELECT * from messages where id = ".$r->result('id'));
        $r1->next_row();
        unset($sub);
        $sub = new outTree();
        $r1->addFields($sub,$ar=array('about','is_read'));
        $sub->addField('date',make_date($r1->result('date'),true));

        $r2 = new Select($db,"SELECT * from users where id = ".$r->result('user_id'));
        $r2->next_row();
        //add_user_avatar($sub,$r2);

        $sub->addField('user_id',$r->result('user_id'));

        if ($i==1) $sub->addField('no-border','no-border');

        add_user_info($sub,$r->result('user_id') ,'user');
        //echotree($sub);
        $i++;
        $main->addField('sub',$sub);
  };
 ?>

