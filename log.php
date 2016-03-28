<?php

/**
 *
 *	Description: 	Log variable class
 *	Author: 		Denis Vororpaev
 * 	Author Email:	d.n.voropaev@gmail.com
 *	Version: 		0.1.0
 *	Copyright:		Denis Voropaev © 2016
 *
**/

class Log {

	public static function obj ($obj, $msg = false, $level = 1) {
		
		extract (debug_backtrace ()[$level]);
		error_log (
			sprintf ("\n\n\tDebug Variable Dump\n%s%s%s%s%s\n\n",
				(isset ($file) 		? "\n\t- File\t\t: $file" 			   :''),
				(isset ($function) 	? "\n\t- Caller\t: $function ()" 	   :''),
				(isset ($line) 		? "\n\t- Line\t\t: $line" 			   :''),
				(is_string ($msg) 	? "\n\t- Info\t\t: ". strtoupper ($msg):''),
				("\n\t- Object\t: " .
					str_replace ("\n", "\n\t",
						print_r ($obj, true) ))));
	}

	public static function type ($obj, $msg = false, $level = 1) {
		Log::obj ((is_object ($obj) ? get_class ($obj) : gettype ($obj)),
			$msg, $level);
	}
}

?>
