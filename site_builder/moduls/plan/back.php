<?
/** ����������������� �������� (������� ���������)
 *  @author Milena Eremeeva <fenyx@yandex.ru>
 *
 *  ����� � ������������ ����:
 *  ��� ��������/������� ���������� ������ �� ������� $sections_table
 *  ��� ������� ���������� ������ �� ������� $items_table
 */

 session_start();

 if ($_SESSION['valid_user']=='admin')  {

     include($_SERVER['DOCUMENT_ROOT'].'/setup.php');
     include($inc_path.'/db_conect.php');
     include('config.php');
     include('class.back.php');

     $back = new B_catalog($db,$modulName,$modulCaption,$sections_table,$items_table,$arImgS,$arImgI,$choice_table);
     $back->getEvent();

 }
 else header('Location: '.$auth_path);
?>