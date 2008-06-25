<?php
require_once('Zend/Log.php');
require('Zend/Log/Exception.php');

/**
 * Thomahawk Logger
 * 
 * 
 */
class Thomahawk_Log extends Zend_Log {
	
		//Extra niveaus
		const EXCEP = 8;
		const EDIT = 9;
		const LOGIN = 10;
		const VIEW = 11;
		
		protected $_logView;
		protected $_logEdit;
		protected $_logLogin;
		
	/**
     * Class constructor.  nieuwe logger
     *
     * @param string $path_to_root pad naar root
     * @param string|null $user De user die triggerde
     * @throws Th_Log_Exception
     */
	function Thomahawk_Log($path_to_root, $user = null){
		// Eerst originele constructor uitvoeren:
		parent::__construct();
		
		try{
		require_once('Zend/Config/Ini.php');
		$ini_log = new Zend_Config_Ini($path_to_root . 'conf/logging.ini');
		$this->_prioriteit= $ini_log->log->prioriteit;
		$this->_logView = $ini_log->log->view;
		$this->_logEdit = $ini_log->log->edit;
		}catch (Zend_Config_Exception $e){
			throw new Thomahawk_LogException('Zend_Config_Ini maakte een fout/kon de INI niet vinden');
		}catch (Exception $e){
			throw new Thomahawk_LogException('Zend_Config kon niet worden gevonden/andere error');
		}
		try {
			
		require_once('Zend/Log/Filter/Priority.php');
		require_once('Zend/Log/Writer/Stream.php');
		require_once('Zend/Log/Formatter/Xml.php');
		
		//extra loggers
		$this->setEventItem('user', $user);
		
		//filter
		$filter = new Zend_Log_Filter_Priority((int) $this->_prioriteit);
		
		//normale writer maken
		$wr1 = new Zend_Log_Writer_Stream($path_to_root . 'logs/Log.log');
		$wr1->addFilter($filter);
		$wr1->setFormatter(new Zend_Log_Formatter_Xml());
		$this->addWriter($wr1);

		//error writer
		$wr2 = new Zend_Log_Writer_Stream($path_to_root . 'logs/Errorlog.log');
		$filter2 = new Zend_Log_Filter_Priority(8, '=');
		$formatter2 = new Zend_Log_Formatter_Simple('%timestamp% %priorityName%(%priority%): %message%' . PHP_EOL. "%trace%" . PHP_EOL. PHP_EOL);
		$wr2->addFilter($filter2);
		$wr2->setFormatter($formatter2);
		$this->addWriter($wr2);
		
		//opruimen
		unset($ini_log);
		}catch (Exception $e){
			throw new Thomahawk_LogException($e);
		}
	}
	function logException($exception){
        // sanity checks
        $priority = 8;  //EXCEP
        if (empty($this->_writers)) {
            throw new Thomahawk_LogException('Geen Writers!');
        }

        if (! isset($this->_priorities[$priority])) {
            throw new Thomahawk_LogException('Geen Prioriteit');
        }
        // pack into event required by filters and writers
        $event = array_merge(array('timestamp'    => date('c'),
                                    'message'      => $exception->getMessage(),
                                    'priority'     => 8,
                                    'priorityName' => $this->_priorities[8],
                                    'trace' => $exception->getTraceAsString()),
                                    $this->_extras);
        // abort if rejected by the global filters
        foreach ($this->_filters as $filter) {
            if (! $filter->accept($event)) {
                return;
            }
        }

        // send to each writer
        foreach ($this->_writers as $writer) {
            $writer->write($event);
        }
	}
}

class Thomahawk_LogException extends Exception 
{}