<?php

if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
class MY_Form_validation extends CI_Form_validation {
	function __construct() {
		parent::__construct ();
	}
	function error_array() {
		return $this->_error_array;
	}
	public function checkDateFormat($date) {
		return true;
		
		/*
		 * if (preg_match("/[0-31]{2}\/[0-12]{2}\/[0-9]{4}/", $date)) {
		 * if(checkdate(substr($date, 3, 2), substr($date, 0, 2), substr($date, 6, 4)))
		 * return true;
		 * else
		 * return false;
		 * } else {
		 * return false;
		 * }
		 */
	}
	public function checkEmailFormat($emails = '{}') {
		$emails = json_decode ( $emails, false );
		$error = false;
		foreach ( $emails as $email ) {
			
			if (! preg_match ( "/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix", $email )) {
				$error = true;
				break;
			}
		}
		
		return ! $error;
	}
}

?>