<?php
$depth = 2;
try {
	if (! strstr(get_include_path(), 'Zend')) {
		//bepalen hoe diep we moeten graven tot root
		//$depth is diepte
		$downpath = '/';
		while ($depth) {
			$downpath .= '../';
			-- $depth;
			echo $downpath . "\n";
		}
		//libs bij include path
		$dir = realpath(dirname(__FILE__) . $downpath); //Klopt waarschijnlijk niet
		set_include_path($dir . DIRECTORY_SEPARATOR . 'libs' . DIRECTORY_SEPARATOR . PATH_SEPARATOR . get_include_path());
	}
} catch (Exception $e) {
	die('Zend_Framework kan niet worden toegevoegd aan include_path ' . $e->__toString());
}
require ("Acl.php");
require 'Errorhandler.php';
require_once 'Zend/Acl.php';
require_once 'Zend/Acl/Role.php';
require_once 'Zend/Acl/Resource.php';
require_once 'Zend/Acl/Role/Interface.php';
require_once 'Zend/Acl/Resource/Interface.php';
require_once 'PHPUnit/Framework.php';
require_once 'PHPUnit/Extensions/OutputTestCase.php';
$acl = new Zend_Acl();
$role = new Zend_Acl_Role("henk");
$acl->addRole($role);
$acl->addRole(new Zend_Acl_Role("hans"));
$acl->add(new Zend_Acl_Resource("freek"));
$acl->add(new Zend_Acl_Resource("sjon"));
$acl->allow("henk", 'freek');
$acl->allow("henk", 'sjon');
$acl->allow("hans", null);
class Test_Acl extends PHPunit_Framework_Testcase
{
	/**
	 * @var Thomahawk_Acl
	 */
	public $acl;
	function setUp ()
	{
		global $acl;
		$this->acl = new Thomahawk_Acl($acl);
	}
	function test_fail_load_acl ()
	{
		try {
			$this->setExpectedException('Thomahawk_Acl_Exception', 'Failed to load ACL');
			new Thomahawk_Acl();
		} catch (Thomahawk_Acl_Exception $e) {
			return;
		}
		return $this->fail();
	}
	/**
	 * Test $acl->makeHtmlTableRow
	 *
	 * @dataProvider testMakeHtmlTableRowProvider
	 */
	function testMakeHtmlTableRow ($a, $b, $c, $d)
	{
		$this->assertRegExp('/(^<tr><td>' . $d . '</td>)(for="' . $a . '_' . $b . '">)(name="' . $a . '_' . $b . '" value="allow")(name="' . $a . '_' . $b . '" value="deny")(id="ajaxStatus_' . $a . '_' . $b . '" >/td></tr>$)/', $this->acl->makeHtmlTableRow($a, $b, $c));
	}
	function testMakeHtmlTableRowProvider ()
	{
		return array(array('foo' , 'bar' , Thomahawk_Acl::RESOURCE , 'foo') , array('foo' , 'bar' , Thomahawk_Acl::ROLE , 'bar') , array('skup' , 'mock' , Thomahawk_Acl::RESOURCE , 'skup') , array('skup' , 'mock' , Thomahawk_Acl::ROLE , 'mock'));
	}
	/**
	 * Zal testen of editAcl failt bij $a > 1
	 *
	 * @dataProvider test_fail_editAcl_Provider
	 */
	function test_fail_editAcl ($a)
	{
		$this->setExpectedException('Thomahawk_Acl_Exception');
		$this->acl->editAcl('foo', 'bar', $a);
	}
	function test_fail_editAcl_Provider ()
	{
		return array(array(1) , array(2) , array(3));
	}
	function testEcho_jquery_code ()
	{
		$this->acl->Echo_jquery_code();
		PHPUnit_Extensions_OutputTestCase::expectOutputRegex('/^(<script)/');
	}
}
?>