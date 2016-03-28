<?php

/**
 *
 *	Description: 	Form field automation class
 *	Author: 		Denis Vororpaev
 * 	Author Email:	d.n.voropaev@gmail.com
 *	Version: 		0.1.0
 *	Copyright:		Denis Voropaev © 2016
 *
**/


class Field {

	public $name;
	public $type;
	public $value;
	public $title;
	public $isRequired;

	public function __construct ($name, $type, $title, $isRequired, $value = null) {

		$this->name 		= strval ($name);
		$this->type 		= strval ($type);
		$this->title 		= strval ($title);
		$this->isRequired 	= $isRequired ? true : false;
		
		if ($value) { $this->setValue ($value); }
	}

	public function setValue ($value) {

		$this->value 		= secure_input ($value);
	}

	public function render () {

		$value 		= !empty ($this->value)	? $this->value 	: '';
		$optional	= !$this->isRequired	? ' (Optional)' : '';
		$required 	= $this->isRequired 	? ' required' 	: '';

		$input 		= ($this->type === 'textarea') ?
			sprintf ('<textarea id="%s" name="%s"%s>%s</textarea>',
				$this->name, $this->name, $required, $value) :
			sprintf ('<input id="%s" name="%s" type="%s" value="%s"%s/>',
				$this->name, $this->name, $this->type, $value, $required);

		return sprintf ('<label><span>%s%s</span>%s</label>',
			$this->title, $optional, $input);
	}
}

?>
