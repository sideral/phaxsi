<?php

/**
 * Helps with time calculations and formatting.
 * 

 * Copyright 2008-2014, Alejandro Zuleta (http://phplab.co)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2008-2014, Alejandro Zuleta (http://phplab.co)
 * @link          http://phaxsi.net Phaxsi PHP Framework
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @package       Phaxsi.Helpers
 * @since         Phaxsi v 0.1
 */

class DateTimeHelper{

	static function getTimeInterval($later, $earlier){

		$diff = $later - $earlier;

		$one_second = 1;
		$one_minute = $one_second*60;
		$one_hour = $one_minute*60;
		$one_day = $one_hour*24;
		$one_week = $one_day*7;
		$one_month = $one_day*30;
		$one_year = $one_day*365;

		$ago = array();

		$ago['year'] = floor($diff/$one_year);
		$diff -= $ago['year']*$one_year;
		$ago['month'] = floor($diff/$one_month);
		$diff -= $ago['month']*$one_month;
		$ago['week'] = floor($diff/$one_week);
		$diff -= $ago['week']*$one_week;
		$ago['day'] = floor($diff/$one_day);
		$diff -= $ago['day']*$one_day;
		$ago['hour'] = floor($diff/$one_hour);
		$diff -= $ago['hour']*$one_hour;
		$ago['minute'] = floor($diff/$one_minute);
		$diff -= $ago['minute']*$one_minute;
		$ago['second'] = $diff;

		return 	$ago;

	}

	static function timeAgoString($timestamp, $units = 1, $translation = array()){
		$ago = self::getTimeInterval(time(), $timestamp);
		return self::formatTimeString($ago, $units, $translation);
	}

	static function timeToString($timestamp, $units = 1, $translation = array()){
		$time = self::getTimeInterval($timestamp, time());
		return self::formatTimeString($time, $units, $translation);
	}

	private static function formatTimeString($diff, $units, $translation = array()){

		if(!$translation){
			$translation = array(
				'year'	=> array('year', 'years'),
				'month'	=> array('month', 'months'),
				'week'	=> array('week', 'weeks'),
				'day'	=> array('day', 'days'),
				'hour'	=> array('hour', 'hours'),
				'minute' => array('minute', 'minutes'),
				'second' => array('second', 'seconds'),
				'now'	=> 'instants'
			);
		}

		$intervals = array('year', 'month', 'week', 'day', 'hour', 'minute', 'second');

		$i=0;
		while(isset($intervals[$i]) && $diff[$intervals[$i]] == 0){
			$i++;
		}
		if(isset($intervals[$i])){
			$parts = array();
			for($j = 0; $j < $units && isset($diff[$intervals[$i]]); $j++, $i++){
				if($diff[$intervals[$i]] == 0){
					$units++;
					continue;
				}
				$parts[] = $diff[$intervals[$i]] . " " . ($diff[$intervals[$i]]!=1?$translation[$intervals[$i]][1]:$translation[$intervals[$i]][0]);
			}
			return implode(' ', $parts);
		}
		else{
			return $translation['now'];
		}

		return '';
	}

	/**This shoulnd't be here**/
  /*  static function date($timestamp){
		return date(AppConfig::$vars['date_format'], $timestamp);
	}*/

	static function secondsToNext($unit){

		$current_time = time();
		$date = getdate();
		switch($unit){
			case 's':
				$next = mktime($date['hours'], $date['minutes'], $date['seconds']+1);
				break;
			case 'm':
				$next = mktime($date['hours'], $date['minutes']+1, 0);
				break;
			case 'h':
				$next = mktime($date['hours']+1, 0, 0);
				break;
			case 'd':
				$next = mktime(0, 0, 0, $date['mon'], $date['mday'] + 1);
				break;
			case 'w':
				$next = mktime(0, 0, 0, $date['mon'], $date['mday'] + 7 - $date['wday']);
				break;
			default:
				$next = $current_time;
		}

		return $next - $current_time;
	}

	static function dateFromMySQLTimestamp($format, $my_stamp){
		list($date, $time) = explode(' ', $my_stamp);
		list($year, $month, $day) = explode('-', $date);
		list($hour, $minute, $second) = explode(':', $time);
		$timestamp = mktime($hour, $minute, $second, $month, $day, $year);
		return date($format, $timestamp);
	}

}
