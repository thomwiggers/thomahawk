<?php
accelerator_set_status(true);

$cid = (isset($_GET['cid']) && !empty($_GET['cid']) ? $_GET['cid'] : "");
$id  = (isset($_GET['id'])  && !empty($_GET['id']) ? $_GET['id'] : "");

require_once('Zend/Loader.php');
Zend_Loader::registerAutoload();

try{
//Smarty
require('libs/Smarty/Smarty.class.php');
$smarty = new Smarty();
$smarty->template_dir = 'smarty_templates/';
$smarty->config_dir = 'smarty_configs/';
$smarty->compile_dir = 'smarty_templates_comp/';
}catch (Exception $e){
	die ("Fatal Error: Smarty failed to start" . $e->__toString());
}

$auth = Zend_Auth::getInstance();
$auth->getIdentity();

$log = new Thomahawk_Log('./', $identity);

$ini = new Zend_Config_Ini('../conf/categorie.ini', $cid);
$db_ini = new Zend_Config_Ini('../conf/config.ini', 'database');

if (isset($_GET['submit'])) {
	$log->edit($ini->name . " => " . $id );
	
	$db = Zend_Db::factory('mysqli', $db_ini->db->toArray());
	$cols = array();
	for($i = 1; $i >= $ini->db->field->count; ++$i){
		$fieldno = (string) "field" . (string) $id;
		$cols[] = $ini->db->$fieldno->name . " => " . $_GET[$ini->db->$fieldno->name];
	}
	$select = $db->update($cat, $cols, 'id = ' . $id);
	$smarty->assign('melding', "Update uitgevoerd");
	$smarty->display('edit.tpl');
}

$count = $ini->$cid->db->field->count;
$cols = array();
for($i = 1; $i >= $count; ++$i){
	$fieldno = (string) "field" . (string) $i;
	if ($ini->$cid->db->$fieldno->display) {
		if($ini->$cid->db->$fieldno->special->aes){
			$cols[] = new Zend_Db_Expr('AES_DECRYPT('.$ini->$cid->db->$fieldno->name.', '. $ini->db->field.$i->special->aes->key . ")");
			continue;
		}
		$cols[] = $ini->$cid->db->$fieldno->name;
	}
}
Zend_Loader::loadClass('Zend_Db');
$db = Zend_Db::factory('mysqli', $db_ini->db->toArray());

$select = $db->select();

$select->from($db_ini->db->prefix . $id, $cols);
if (!empty($item_id)) {
	$select->where('id = ?', $item_id);
}
if (!empty($item_name)) {
	$select->where('* = REGEXP(?)', $item_name);
}
$select->limit(50, $offset);

$result = $db->query($select);

$smarty->assign('result', $result->fetch());
$smarty->display("edit.tpl");