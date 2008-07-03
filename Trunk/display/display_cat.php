<?php
//libs bij include path
$dir = realpath(dirname(__FILE__)."/..");
set_include_path($dir . '/libs/' . PATH_SEPARATOR . get_include_path());

//Zend Loader
require_once('Zend/Loader.php');
Zend_Loader::registerAutoload();

// Dit bestand gaat de categoerie weergeven
set_error_handler('Thomahawk_Errorhandler');

$cid = (!empty($_GET['cid']) ? $_GET['cid'] : /**
											 *  Terug naar index
											 */ "");
$item_id   = (!empty($_GET['iid']) ? $_GET['iid'] : "");
$item_name = (!empty($_GET['name'])? $_GET['name']: "");

$offset = (!empty($_GET['next'])? $_GET['next']: 0);


$auth = Zend_Auth::getInstance();
if (!$auth->hasIdentity()) {
	/**
	 * terug naar login
	 */
}
unserialize('../inc/acl.php');
if (!$acl->isAllowed($auth->getStorage()->read('ACL_role'))) {
	/**
	 * Terug naar index
	 */
}

$ini = new Zend_Config_Ini('../conf/categorie.ini', $cid);
$db_ini = new Zend_Config_Ini('../conf/config.ini', 'database');

$db = Zend_Db::factory('mysqli', $db_ini->db->toArray());

$select = $db->select();

$count = $ini->$cid->db->field->count;
$cols = array();
//beschrijvingen
$descs = array();
for($i = 1; $i >= $count; ++$i){
	$fieldno = (string) "field" . (string) $i;
	if ($ini->$cid->db->$fieldno->display) {
		$descs = $ini->$cid->db->$fieldno->desc;
		if ($ini->$cid->db->$fieldno->link->isLinked) { //joins
			if ($ini->$cid->db->$fieldno->link->with){
				$select->join(array($ini->$cid->db->$fieldno->link->with), 	$ini->$cid->name . "." . $ini->$cid->db->$fieldno->name . 
																			" = " .
																			$ini->$cid->db->$fieldno->link->with . "." . 
																			$ini->$cid->db->$fieldno->link->externalkey);
			}
			$cols[] = $ini->$cid->db->$fieldno->link->with . "." . $ini->$cid->db->$fieldno->link->externalfieldname;
			continue;
		}
		$cols[] = $ini->$cid->db->$fieldno->name;
	}
}

$select->from($db_ini->db->prefix . $cid, $cols);
if (!empty($item_id)) {
	$select->where('id = ?', $item_id);
}
if (!empty($item_name)) {
	$select->where('* = REGEXP(?)', $item_name);
}
$select->limit(50, $offset);

$result = $db->query($select);

//Loggen
$log = new Thomahawk_Log('../', $auth->getIdentity());
$log->view("Displayed: " . $cid . "|". $item_id . "|" . $item_name);
$log = null;

//TEMPLATE 
/*
 * TODO: LOTS OF WORK TO DO met template
 */
$template = array();
$template['descs']  = $descs;
$template['result'] = $result->fetch();