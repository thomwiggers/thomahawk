<?php
function Thomahawk_Errorhandler($msg, $code, $errfile, $errline, $errcontext, $log){
	if( isset($log)){
		$e = new Exception($msg, $code);
		$log->logException($e);
	}else{
		die("Error! \n Bericht: $msg \n Code: $code \n In File: $errfile, Line: $errline \n\n Context: \n $errcontext");
	}
	return false;
}