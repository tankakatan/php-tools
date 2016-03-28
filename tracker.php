<?php

/**
 *
 *	Description: 	Process time tracker class
 *	Author: 		Denis Vororpaev
 * 	Author Email:	d.n.voropaev@gmail.com
 *	Version: 		0.1.0
 *	Copyright:		Denis Voropaev © 2016
 *
**/


class Tracker {

	private static $startTime 	= null;
	private static $events 		= null;
	private static $actions 	= null;
	private static $name 		= null;
	private static $bypass 		= null;
	private static $format 		= 'Y/m/d, H:i:s';

	public static function start ($name = false, $bypass = false) {

		if (Tracker::$bypass 	= $bypass) { return; }

		Tracker::$startTime 	= microtime (true);
		Tracker::$events 		= array ();
		Tracker::$actions 		= array ();
		Tracker::$name 			= is_string ($name) ? $name : 'Process';
	}

	public static function log ($event = null) {

		if (Tracker::$bypass) { return; }

		if (!Tracker::isStarted ()) {
			Tracker::start ();
		}

		if (!$event) {
			$event = 'Event #' . count (Tracker::$events) + 1;
		}

		Tracker::$events[$event] = microtime (true);
	}

	public static function run ($action = null, $subject = null) {

		if (Tracker::$bypass) { return; }

		if (!Tracker::isStarted ()) {
			Tracker::start ();
		}

		if (!$action) {
			$action 	= 'Action #' . count (Tracker::$actions) + 1;
		}

		if (!$subject) {
			$count 		= isset (Tracker::$actions[$action]) ?
						  count (Tracker::$actions[$action]) : 0;
			$subject 	= 'Subject #' . $count + 1;
		}
		
		Tracker::$actions[$action][$subject]['start'] 	= microtime (true);
		Tracker::$events["{$action}ing {$subject}"] 	= microtime (true);
	}

	public static function halt ($action, $subject) {

		if (Tracker::$bypass) { return; }

		if (!Tracker::isStarted () 								||
			!isset (Tracker::$actions[$action]) 				||
			!isset (Tracker::$actions[$action][$subject]) 		||
			!isset (Tracker::$actions[$action][$subject]['start'])){
			return;
		}

		Tracker::$actions[$action][$subject]['end'] 	= microtime (true);
		Tracker::$events["{$action}ing {$subject}"] 	= microtime (true);	
		Tracker::$actions[$action][$subject]['total'] 	= (
			Tracker::$actions[$action][$subject]['end'] -
			Tracker::$actions[$action][$subject]['start']);
	}

	public static function report ($onEvents = false, $onActions = true,
		$threshold = 1.0) {

		if (Tracker::$bypass || !Tracker::isStarted ()) {
			return;
		}

		$report  = sprintf ("%s timing list:\n", ucfirst (Tracker::$name));
		$report .= sprintf ("Started at %s\n",
			Tracker::microDate (
				Tracker::$startTime));

		$stop	 = microtime (true);
		$time 	 = $stop - Tracker::$startTime;
		
		if ($onEvents  && floatval ($time) > $threshold) {
			$report .= Tracker::reportOnEvents ();
		}

		$report .= sprintf ("Stopped at %s\n\n", Tracker::microDate ($stop));

		if ($onActions && floatval ($time) > $threshold) {
			$report .= Tracker::reportOnActions ();
		}
		
		$report .= sprintf ("Total time: %s.\n", $time);
		
		Log::obj ($report, 'Tracker report');

		Tracker::stop ();
	}

	public static function setFormat ($format) {

		if (Tracker::$bypass 		||
			!is_string ($format) 	||
			!date ($format, time ())){
			
			return;
		}

		Tracker::$format = $format;
	}

	public static function microDate ($micro) {

		if (Tracker::$bypass) { return; }

		list ($seconds, $microseconds) = explode (".", $micro);
		return date (Tracker::$format, $seconds) . ".$microseconds";
	}
	
	private static function reportOnEvents () {
		
		$report 	 = '';
		$trace 	 	 = Tracker::$startTime;

		foreach (Tracker::$events as $name => $time) {
			$report .= sprintf ("+%s secs : %s\n", $time - $trace, $name);
			$trace 	 = $time;
		}

		return $report;
	}

	private static function reportOnActions () {

		$report 		 = '';
		
		foreach (Tracker::$actions as $name => $subjects) {

			$total 		 = 0;
			foreach ($subjects as $subject 	=> $time) {
				$report .= sprintf ("Time spent on %sing %s: %s\n",
					$name, $subject, $time['total']);
				$total 	+= $time['total'];
			}
			$report 	.= sprintf ("Total time on %sing: %s\n", $name, $total);
		}

		return $report;
	}

	public static function stop () {

		Tracker::$bypass 	= null;
		Tracker::$startTime = null;
		Tracker::$events 	= null;
		Tracker::$actions 	= null;
		Tracker::$name 		= null;
	}

	private static function isStarted () {
		return (Tracker::$startTime) ? true : false;	
	}
	
}

?>
