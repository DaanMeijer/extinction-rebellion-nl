<?php
namespace EM_Event_Locations;
/**
 * Handles registration and retrival of EM_Event_Location child classes for use with events.
 * Class EM_Event_Locations
 */
class Event_Locations {
	
	/**
	 * Associative array with type key => class name.
	 * @var array
	 */
	private static $types = array();
	
	/**
	 * @return array[EM_Event_Location::]
	 */
	public static function get_types(){
		return static::$types;
	}
	
	/**
	 * @param string $type
	 * @param string $classname
	 * @return bool
	 */
	public static function register( $type, $classname ){
		self::$types[$type] = $classname;
		return true;
	}
	
	/**
	 * @param string $type
	 * @return bool
	 */
	public static function unregister( $type ){
		if( !empty(self::$types[$type]) ){
			unset(self::$types[$type]);
			return true;
		}
		return false;
	}
	
	/**
	 * @param string $type
	 * @param \EM_Event $EM_Event
	 * @return bool
	 */
	public static function get( $type = null, $EM_Event = null ){
		if( !empty(self::$types[$type]) && class_exists(self::$types[$type]) ){
			$location_type = self::$types[$type];
			return new $location_type( $EM_Event );
		}
		return false;
	}
	
	/**
	 * Returns whether or not the supplied event location $type is enabled for use.
	 * @param string $type
	 * @return bool
	 */
	public static function is_enabled( $type ){
		$location_types = get_option('dbem_location_types', array());
		return !empty($location_types[$type]);
	}
	
}
require('em-event-location.php');