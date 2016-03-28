<?php
/**
 *
 *	Description: 	Email sender class
 *	Author: 		Denis Vororpaev
 * 	Author Email:	d.n.voropaev@gmail.com
 *	Version: 		0.1.0
 *	Copyright:		Denis Voropaev © 2016
 *
**/


class Email {

	private static function headers ($type, $from, $to, $charset='utf-8') {
		return 	"Content-type: {$type}; charset={$charset}\r\n" .
				"From: {$from}\r\n" .
				"Reply-To: {$from}\r\n";
				// "To: {$to}";
	}

	public static function sendText ($from, $to, $subject, $content) {		
		return Email::send (
			Email::headers (
				'text/plain', $from, $to), $to, $subject, $content);
	}

	public static function sendHTML ($from, $to, $subject, $content) {
		return Email::send (
			Email::headers (
				'text/html', $from, $to), $to, $subject, $content);
	}

	private static function send ($headers, $to, $subject, $content) {
		if (!$to) {
			return false;
		}
		
		return mail ($to, $subject, $content, $headers);
	}
}

?>
