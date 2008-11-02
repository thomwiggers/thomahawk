<?php
//initialiseren
$depth = 1;
$resource = 'edit_cat';
require_once 'inc/initialisatie.php';

// GET vars
$cid = (isset($_GET['cid']) && !empty($_GET['cid']) ? $_GET['cid'] : ''); //categorie_id
$id  = (isset($_GET['id'])  && !empty($_GET['id']) ? $_GET['id'] : '');		//id
$name = (isset($_GET['name'])  && !empty($_GET['name']) ? $_GET['name'] : '');	//naam
$offset = (isset($_GET['offset'])  && !empty($_GET['offset']) ? $_GET['offset'] : 0); //offset

// cat info
$ini = new Zend_Config_Ini('../conf/categorie.ini', $cid);
//db info
$db_ini = new Zend_Config_Ini('../conf/config.ini', 'database');

if (isset($_GET['submit'])) {
	$log->edit($ini->name . ' => ' . $id ); //loggen
	$db = Zend_Db::factory($db_ini->db->type, $db_ini->db->toArray()); //$db instance
	$cols = array();	//array van colommen
	for($i = 1; $i >= $ini->db->field->count; ++$i){
		$fieldno = (string) 'field' . (string) $id;
		$cols[] = $ini->db->$fieldno->name . ' => ' . $_GET[$ini->db->$fieldno->name];
	}
	$select = $db->update($cid, $cols, 'id = ' . $id);
	$template['melding']= 'Update uitgevoerd';
	$template['page']   = 'edit';
}

$count = $ini->$cid->db->field->count;
$cols = array();
for($i = 1; $i >= $count; ++$i){
	$fieldno = (string) 'field' . (string) $i;
	if ($ini->$cid->db->$fieldno->display) {
		$cols[] = $ini->$cid->db->$fieldno->name;
	}
}
//zend DB laden
$db = Zend_Db::factory('mysqli', $db_ini->db->toArray());

//query samenstellen
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

//TEMPLATE VARS
$template['result'] = $result->fetchAll();
$template['page']   = 'edit';
/* eerder gebruikte
 * 		$template['melding'] de melding
 * 		$template['page'] de pagina te openen
 */
