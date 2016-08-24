<?php
/**
 * @package FRONT
 */

 include_once($inc_path."/service/class.menu.php");

// главное меню
 $menuName = 'menu_main';
 unset($menu);
 $menu = new Menu('front/menu/'.$menuName.'.html');



 $menu->addMenu(&$site,$menuName);

 if ($_GET['city']>0) {
 //echo  $menu->menu->sub[0]->href;
   foreach ($site->menu_main->menu->sub as $key => $value)  {
       $site->menu_main->menu->sub[$key]->addField('href_a',$site->menu_main->menu->sub[$key]->href);
       $site->menu_main->menu->sub[$key]->href .= '/city/'.$_GET['city'];
   };

 } else {
 	foreach ($site->menu_main->menu->sub as $key => $value)  {
       $site->menu_main->menu->sub[$key]->addField('href_a',$site->menu_main->menu->sub[$key]->href);
      // $site->menu_main->menu->sub[$key]->href .= '/city/'.$_GET['city'];
   };
 };

 //echotree($site->menu_main);



// if (1 < $count_) {
//         // подменю
//          $menuName = 'menu_sub';
//          unset($menu);
//         $menu = new Menu('front/menu/'.$menuName.'.html',$parent_,0);
//         $menu->addMenu(&$site,'menu_sub');
// }
 $site->menu_main->menu->sub[0]->addfield('css',$site->menu_main->menu->sub[0]->href);
 $site->menu_main->menu->sub[1]->addfield('css',$site->menu_main->menu->sub[1]->href);
 $site->menu_main->menu->sub[2]->addfield('css',$site->menu_main->menu->sub[2]->href);

 //$site->menu_main->menu->sub[3]->addfield('css','bricks');


?>
