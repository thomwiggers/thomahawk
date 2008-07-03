<?php
accelerator_set_status(true);

$cid = (isset($_GET['cid']) && !empty($_GET['cid']) ? $_GET['cid'] : ""); //categorie_id
$id  = (isset($_GET['id'])  && !empty($_GET['id']) ? $_GET['id'] : "");		//id
$name = (isset($_GET['name'])  && !empty($_GET['name']) ? $_GET['name'] : "");	//naam
$offset = (isset($_GET['offset'])  && !empty($_GET['offset']) ? $_GET['offset'] : 0); //offset

//zend loader
require_once('Zend/Loader.php');
Zend_Loader::registerAutoload();

//Template var
$template = array();

//zend auth
$auth = Zend_Auth::getInstance();
$identity = $auth->getIdentity();

// Th log
$log = new Thomahawk_Log('./', $identity);

// cat inf
$ini = new Zend_Config_Ini('../conf/categorie.ini', $cid);
//db inf
$db_ini = new Zend_Config_Ini('../conf/config.ini', 'database');

if (isset($_GET['submit'])) {
	$log->edit($ini->name . " => " . $id ); //loggen
	$db = Zend_Db::factory('mysqli', $db_ini->db->toArray()); //$db instance
	$cols = array();	//array van colommen
	for($i = 1; $i >= $ini->db->field->count; ++$i){
		$fieldno = (string) "field" . (string) $id;
		$cols[] = $ini->db->$fieldno->name . " => " . $_GET[$ini->db->$fieldno->name];
	}
	$select = $db->update($cid, $cols, 'id = ' . $id);
	$template['melding']= "Update uitgevoerd";
	$template['page'] = 'edit';
}

$count = $ini->$cid->db->field->count;
$cols = array();
for($i = 1; $i >= $count; ++$i){
	$fieldno = (string) "field" . (string) $i;
	if ($ini->$cid->db->$fieldno->display) {
		$cols[] = $ini->$cid->db->$fieldno->name;
	}
}
Zend_Loader::loadClass('Zend_Db');
$db = Zend_Db::factory('mysqli', $db_ini->db->toArray());

$select = $db->select();

$select->from($db_ini->db->prefix . $id, $cols);
if (!empty($id)) {
	$select->where('id = ?', $id);
}
if (!empty($name)) {
	$select->where('* = REGEXP(?)', $name);
}
$select->limit(50, $offset);

$result = $db->query($select);

$smarty->assign('result', $result->fetch());
$smarty->display("edit.tpl");