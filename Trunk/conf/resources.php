<?php
$resources = array(
'edit_cat',
'display_cat',
'manage_acl'
);
$ini = new Zend_Config_Ini('../conf/categorie.ini');
$secties = $ini->getSectionName();
foreach ($secties as $s){
	$resources[] = $ini->$s->name;
}