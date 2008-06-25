<?php
set_error_handler('Thomahawk_Errorhandler');
try {
//libs bij include path
$dir = realpath(dirname(__FILE__)."/../../..");
set_include_path($dir . '/libs/' . PATH_SEPARATOR . get_include_path());
}catch (Exception $e){
	die ('Zend_Framework kan niet worden toegevoegd aan include_path ' . $e->__toString());
}
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
require_once('Zend/Loader.php');
Zend_Loader::registerAutoload();
$auth = Zend_Auth::getInstance();
$identity = $auth->getIdentity();

//log starten
require('Thomahawk/Log.php');
$log = new Thomahawk_Log('./', $identity);

//zend_translate
$taal = new Zend_Translate('tmx', '/lang');

$smarty->assign('titel', $taal->_('Startpagina'));

Zend_Loader::loadClass('Zend_Config_Ini');
$cat = new Zend_Config_Ini('conf/categorie.ini');

//controleer of is authed
if($identity) {
	$acl_role = $auth->getStorage()->read('ACL_role');
} else {
	/**
	 * Hier moet code zodat de  
	 * inlogpagina wordt weergeven
	 * met Smarty
	 */
}
$categories = $cat->getSectionName();
try {	
	unserialize(file("inc/acl.php"));
	$i = 0;
	foreach($categories as $cat_id){
		if($acl->isAllowed($acl_role, $cat_id)){
			$smarty->assign('cat['. ++$i. '][i]', $cat_id);
			$smarty->assign('cat['.   $i .'][n]', $cat->$cat_id->name);
		}
	}
} catch(Zend_Exception $e){
	/**
	 * Iets met exception doen
	 */
} catch (Zend_Acl_Exception $e){
	/**
	 * Iets doen met Exception
	 */
}