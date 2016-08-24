<?php
/**
 * @package FRONT
 */
  //
  $user_company = 0;
  if ( $_SESSION ['user']>0) {
              $menuName = 'menu_user';
              unset($menu);
              $menu = new Menu('front/menu/'.$menuName.'.html',161,0);
              $menu->addMenu(&$site,$menuName);
             // if ($user_company == 0) $site->menu_sub->menu->sub[4]->href='zakupki';

    //echotree ($site->menu_user->menu);
    $r = new Select($db,"select count(*) as cnt from messages where user_to=$_SESSION[user] and is_read=0 ");
    $r->next_row();
    if ($r->result('cnt')>0) $site->menu_user->menu->sub[1]->addfield('message_cnt','<sup>'.$r->result('cnt').'</sup>');

    //            $site->menu_sub->menu->sub[5]->addfield('sep','');

   };
  //echotree($site->menu_user);
?>
