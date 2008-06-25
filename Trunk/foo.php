<?php ob_start();?><html>
<head>
<title>hi</title>
</head>
<body>
<b>
<?php
$cid = "c1";
$i = 1;
//libs bij include path
$dir = realpath(dirname(__FILE__)."/../../..");
set_include_path($dir . '/libs/' . PATH_SEPARATOR . get_include_path());
require('Zend/Config/Ini.php');
$ini = new Zend_Config_Ini('./conf/categorie.ini', $cid);
$db_ini = new Zend_Config_Ini('./conf/config.ini', 'database');

$count = $ini->c1->db->field->count;
$cols = array();
for($i = 1; $i <= $count; ++$i){
	$fieldno = (string) "field" . (string) $i;
	if ($ini->c1->db->$fieldno->display) {
//		if($ini->c1->db->$fieldno->special->aes){
//			$cols[] = new Zend_Db_Expr('AES_DECRYPT('.$ini->c1->db->$fieldno->name.', '. $ini->db->$fieldno->special->aes->key . ")");
//			continue;
//		}
		$cols[] = $ini->c1->db->$fieldno->name;
	}
}
print_r($cols);
?>
</b>
</body>
</html>
