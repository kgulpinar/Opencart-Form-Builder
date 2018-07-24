<?php
class Pageform {
	public function __construct($registry) {
		$this->config = $registry->get('config');
		$this->db = $registry->get('db');
		$this->request = $registry->get('request');
		$this->session = $registry->get('session');
	}

	/*Input*/
	public function validateText($value = '') {
		if ((utf8_strlen(trim($value)) < 1) || (utf8_strlen(trim($value)) > 255)) {
			return true;
		} else{
			return false;
		}
	}

	public function validateTextarea($value = '') {
		if (utf8_strlen(trim($value)) < 1) {
			return true;
		} else{
			return false;
		}
	}

	public function validateNumber($value = '') {
		if (utf8_strlen(trim($value)) < 1) {
			return true;
		} else{
			return false;
		}
	}

	public function validateTelephone($value = '') {
		if ((utf8_strlen($value) < 3) || (utf8_strlen($value) > 32)) {

			return true;
		} else{
			return false;
		}
	}

	
	public function validateEmail($value = '') {
		if ((utf8_strlen($value) > 96) || !filter_var($value, FILTER_VALIDATE_EMAIL)) {
			return true;
		} else{
			return false;
		}
	}

	public function validateEmailExists($value = '') {
		if ((utf8_strlen($value) > 96) || !filter_var($value, FILTER_VALIDATE_EMAIL)) {
			return true;
		} else{
			return false;
		}
	}

	public function validatePassword($value = '') {
		if ((utf8_strlen($value) < 4) || (utf8_strlen($value) > 20)) {
			return true;
		} else{
			return false;
		}
	}

	public function validateConfirmPassword($confirm_password, $password = '') {
		if ($confirm_password != $password) {
			return true;
		} else{
			return false;
		}
	}

	/*File*/
	public function validateFile($value = '') {
		if (empty($value)) {
			return true;
		} else{
			return false;
		}
	}

	/*Date & Time*/
	public function validateDate($value = '') {
		if (empty($value)) {
			return true;
		} else{
			return false;
		}
	}

	public function validateTime($value = '') {
		if (empty($value)) {
			return true;
		} else{
			return false;
		}
	}

	public function validateDateTime($value = '') {
		if (empty($value)) {
			return true;
		} else{
			return false;
		}
	}

	/*Localisation*/
	public function validateCountry($value = '') {
		if ($value == '') {
			return true;
		} else{
			return false;
		}
	}
	public function validateZone($value = '') {
		if (!isset($value) || $value == '' || !is_numeric($value)) {
			return true;
		} else{
			return false;
		}
	}

	public function validatePostcode($value = '') {
		if ((utf8_strlen($value) < 3) || (utf8_strlen($value) > 32)) {
			return true;
		} else{
			return false;
		}
	}

	public function validateAddress($value = '') {
		if ((utf8_strlen(trim($value)) < 3) || (utf8_strlen(trim($value)) > 128)) {
			return true;
		} else{
			return false;
		}
	}
}
