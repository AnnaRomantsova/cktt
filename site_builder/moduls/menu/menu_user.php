<?php
/**
 * @package FRONT
 */

 // $user_company = 0;
//

              $menuName = 'menu_user';
              unset($menu);
              $menu = new Menu('front/menu/'.$menuName.'.html',161,0);
              $menu->addMenu($site,$menuName); //echotree($menu);
             // if ($user_company == 0) $site->menu_sub->menu->sub[4]->href='zakupki';
              if ( $_SESSION ['user']>0){
                unset($sub);
                /*$sub = new outTree();
                $sub->addfield('name','Выход');
                $sub->addfield('href','/auth/exit/1');
                $sub->addfield('T','A');
                $sub->addfield('separator','');
                 $sub->addfield('page','auth');
                  $sub->addfield('num','6');
                   $sub->addfield('id','131');
                 */
                  $site->addfield('exit','');
              };
            //  $site->menu_sub->menu->addField('sub',$sub);
$site->menu_user->menu->sub[5]=$sub;
//unset($site->menu_user->menu->sub[0]);
//echotree($site->menu_user->menu);

    //            $site->menu_sub->menu->sub[5]->addfield('sep','');

  // };

?>
