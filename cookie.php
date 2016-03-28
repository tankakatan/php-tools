<?php

/**
 *
 *	Description: 	Cookies manager class
 *	Author: 		Denis Vororpaev
 * 	Author Email:	d.n.voropaev@gmail.com
 *	Version: 		0.1.0
 *	Copyright:		Denis Voropaev © 2016
 *
**/


class Cookie {

	public $name;
	public $expire;

	public function __construct ($name, $expire) {
		$this->name 	= $name;
		$this->expire 	= $expire;
	}

	public function get () {

		if (empty ($_COOKIE[$this->name])) { return array (); }
	
		$cart_cookie = json_decode (
			base64_decode (
				urldecode (
					$_COOKIE[$this->name] )), true);
		
		return is_array ($cart_cookie) ? $cart_cookie : array ();
	}

	public function set ($items) {
		
		$data 	= base64_encode (json_encode ($items));
		$result = setcookie ($this->name, $data, $this->expire, '/');
	
		$_COOKIE[$this->name] = $data;
		return $_COOKIE[$this->name];
	}

	public function add ($items) {
		return (is_array ($items) ?
			$this->set (array_merge ($this->get (), $items)) : $this->get ());
	}

	public function getProperty ($property) {
		$cookie = $this->get ();
		return isset ($cookie[$property]) ? $cookie[$property] : null;
	}

	public function setProperty ($property, $value) {
		
		if (!is_int ($property) && !is_string ($property)) {
			return $this->get ();
		}

		$cookie = $this->get ();

		if (isset ($cookie[$property]) && ($value === null)) {
			unset ($cookie[$property]);
		
		} else  {
			$cookie[$property] = $value;
		}

		return $this->set ($cookie);
	}

	public function unsetProperty ($property) {
		$this->setProperty ($property, null);
	}

}

?>
