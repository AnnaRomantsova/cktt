<?

/**
 * ��������� �����
 * ����������� � ����, ������ � ������� �����������������
 * @package ALL
 */


//�����������������
 $adminpass='128500128500';      //������ ��������������

 $document_root=$_SERVER['DOCUMENT_ROOT'];
 $moduls_root=$document_root.'/site_builder/moduls';
 $inc_path=$document_root.'/site_builder/includes';

 $auth_path='/admin/exit.php';

//���� ������
 $db_host='localhost';
 $db_user='root';
 $db_password='';
 $db_name='estate';


//���� ������
 $db_host='localhost';
 $db_user='dba0006';
 $db_password='d4cwHZff';
 $db_name='demo';

error_reporting(E_ALL & E_NOTICE);
error_reporting(0);
// ini_set('session.use_trans_sid','0');

?>