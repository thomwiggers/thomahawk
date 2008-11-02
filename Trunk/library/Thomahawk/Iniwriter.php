<?php
/**
 * Dit is de class om ini's te schrijven
 *
 * @package Thomahawk
 * @author Thom
 */
class Thomahawk_Iniwriter
{
	/**
	 * array van ingelezen ini
	 *
	 * @var array
	 */
	private $inicontent;
	private $inilocation;
	private $editedIni;
	private $backuplocation;
	/**
	 * Constructor
	 *
	 * @param string $inilocation
	 * @return Thomahawk_Iniwriter
	 */
	function Thomahawk_Iniwriter (string $inilocation)
	{
		$this->setInilocation($inilocation);
		$this->setInicontent();
	}
	/**
	 * Past de ini aan
	 *
	 * @param string $section
	 * @param string $key
	 * @param string $value
	 */
	function editIni (string $section, string $key, string $value)
	{
		$lines = $this->getInicontent();
		$sections_matched = preg_grep('/^\[' . $section . '\]$/', $lines); //secties die matchten.
		$keys_matched = preg_grep('/^' . $key . '\s*=', $lines);
		$i = 0;
		foreach ($keys_matched as $lno => $value_m) {
			unset($value_m);
			++ $i;
			$lines[$lno] = $key . ' = ' . $value;
			if ($i > 1) {
				trigger_error('More than one line replaced!!', E_USER_WARNING);
			}
		}
		$i = 0;
		foreach ($sections_matched as $lno => $line) {
			unset($line);
			$lines = array_insert($lines, $value, ++ $lno);
			++ $i;
			if ($i > 1) {
				trigger_error('More than one line matched and inserted!!', E_USER_WARNING);
			}
		}
		$this->setEditedIni($lines);
		unset($lines, $i, $keys_matched, $sections_matched, $lno);
	}
	function writeIni ()
	{
		if ($this->getEditedIni() == null || $this->getEditedIni() === $this->getInicontent()) {
			return false;
		} else {
			$ini = $this->getEditedIni();
			$this->setBackuplocation();
			if (!copy($this->getInilocation(), $this->getBackuplocation))
				throw new Thomahawk_Iniwriter_Exception('Kon niet backuppen!');
			$f = fopen($this->getInilocation(), 'w');
			try {
				while (fwrite($f, array_shift($ini))) {
					continue;
				}
			} catch (Exception $e) {
				if(!copy($this->getBackuplocation(), $this->getInilocation()))
					trigger_error('Kon de backup niet terugzetten!', E_USER_ERROR);
				return false;
			}
			return true;
		}
	}
	function array_insert ($array, $insert, $position)
	{
		if ($position != (count($array))) {
			$ta = $array;
			for ($i = $position; $i < (count($array)); $i ++) {
				if (! isset($array[$i])) {
					die(print_r($array, 1) . "<br />Invalid array: All keys must be numerical and in sequence.");
				}
				$tmp[$i + 1] = $array[$i];
				unset($ta[$i]);
			}
			$ta[$position] = $insert;
			$array = $ta + $tmp;
		} else {
			$array[$position] = $insert;
		}
		ksort($array);
		return $array;
	}
	function setBackuplocation()
	{
		$this->backuplocation = 'inibackup/' . microtime() . '.bak';
	}
	function getBackuplocation(){
		return $this->backuplocation;
	}
	function getEditedIni ()
	{
		return $this->editedIni;
	}
	function setEditedIni ($editedIni)
	{
		$this->editedIni = $editedIni;
	}
	function getInicontent ()
	{
		return $this->inicontent;
	}
	function setInicontent ()
	{
		$this->inicontent = file($this->getInilocation());
	}
	function getInilocation ()
	{
		return $this->inilocation;
	}
	function setInilocation ($inilocation)
	{
		if (is_string($inilocation)) {
			$this->inilocation = $inilocation;
		} else {
			throw new Thomahawk_Iniwriter_Exception('False parameter passed, must be string. Parameter: ' .
													 $inilocation);
		}
	}
}
class Thomahawk_Iniwriter_Exception extends Exception
{}