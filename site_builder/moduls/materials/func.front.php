<?php

/**
 * ������������� ���� ����� �����������
 * (���� id ��������� - ������� �������� �������)
 *
 * @param outTree $ot
 * @param int $depth
 * @param bool $open_in_section
 * @param int $parent
 * @param int $level
 */
  function &initMenuTree($ot,$depth=0,$open_in_section=true,$parent=1,$level=1) {

    $r = new Select($GLOBALS['db'],'select * from '.$GLOBALS['sections_table'].' where parent="'.addslashes($parent).'" order by sort');
    if ($r->num_rows) {
            $main = new outTree();
            while ($r->next_row()) {
                    $sub = new outTree();
                    $r->addFields($sub,$ar=array('id'));
                    $sub->addField('name',strip_tags($r->result('name'),'<br>'));
                    $T = 'A';

                    $br = $GLOBALS['site']->br[$GLOBALS['modulName']];
                 //   echotree($br);
                    $flag = isset($br);
                    if ($flag && ($br->ids[$level] === $r->result('id'))) {
                            $T = 'S';
                            if ( isset($_GET['i']) || ($_GET['s'] != $r->result('id')))
                            $T = 'SA';
                    }
                    $sub->addField('T',$T);

                    if ( $depth &&
                             ( empty($open_in_section) ||
                               !empty($open_in_section) && ('A' != $sub->T) )
                    ) {
                           // echo "1";
                            $paramNext['parent'] = $r->result('id');
                            initMenuTree($sub,$depth-1,$open_in_section,$r->result('id'),$level+1);
                    }

                    $main->addField('sub',$sub);
                    unset($sub);
            }
            $ot->addField('menu',$main);
    }
    $r->unset_();
 }




 /**
  * ��������� ������ �� �������
  *
  * @param outTree $main ���� ���������
  * @param Select $ri ������
  * @param string $field ����� ����� �������
  * @param bool $with c ��������� � ������
  */
 function addRecords($main,$ri,$field,$with = true) {
        if ($ri->num_rows) {
                $ot_i = new outTree();
                while ($ri->next_row()) {
                        $sub = new outTree();
                        $ri->addFields($sub,$ar=array('id','name','alt1'));
                        if ($with) {
                                $ri->addFieldIMG($sub,'image1');
                                $ri->addFieldHTML($sub,'preview');
                        }
                        $ot_i->addField('sub',$sub);
                        unset($sub);
                }
                $main->addField($field,$ot_i);
        }
 }

 function addPrices($main,$ri,$nameParent,$field,$FILENAME = null) {
        if ($ri->num_rows) {
                $ot_i = new outTree($FILENAME);
                $ot_i->addField('nameParent',$nameParent);
                while ($ri->next_row()) {
                        $sub = new outTree();
                        $ri->addFieldFILE($sub,'download');
                        $ot_i->addField('sub',$sub);
                        unset($sub);
                }
                $main->addField($field,$ot_i);
        }
 }




?>
