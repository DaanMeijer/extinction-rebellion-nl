<?php
namespace EM_Event_Locations;
/**
 * Adds a URL event location type by extending EM_Event_Location and registering itself with EM_Event_Locations
 *
 * @property string url     The url of this event location.
 * @property string text    The text used in a link for the url.
 */
class URL extends Event_Location {
	
	public static $type = 'url';
	public static $admin_template = '/forms/event/event-locations/url.php';
	
	public $properties = array('url', 'text');
	
	public function get_post(){
		$return = parent::get_post();
		if( !empty($_POST['event_location_url']) ){
			$this->event->event_location_data['url'] = esc_url_raw($_POST['event_location_url']);
		}
		if( !empty($_POST['event_location_url_text']) ){
			$this->event->event_location_data['text'] = sanitize_text_field($_POST['event_location_url_text']);
		}
		return $return;
	}
	
	public function validate(){
		$result = false;
		if( empty($this->event->event_location_data['url']) ){
			$this->event->add_error( __('Please enter a valid URL for this event location.', 'events-manager') );
			$result = false;
		}
		if( empty($this->event->event_location_data['text']) ){
			$this->event->add_error( __('Please provide some link text for this event location URL.', 'events-manager') );
			$result = false;
		}
		return $result;
	}
	
	public function get_link( $new_target = true ){
		return '<a href="'.esc_url($this->url).'">'. esc_html($this->text).'</a>';
	}
	
	public function get_admin_column() {
		return '<strong>'. static::get_label() . ' - ' . $this->get_link().'</strong>';
	}
	
	public static function get_label( $label_type = 'singular' ){
		switch( $label_type ){
			case 'plural':
				return esc_html__('URLs', 'events-manager');
				break;
			case 'singular':
				return esc_html__('URL', 'events-manager');
				break;
		}
		return parent::get_label($label_type);
	}
	
	public function output( $what = null ){
		if( $what === null ){
			return '<a href="'. esc_url($this->url) .'" target="_blank">'. esc_html($this->text) .'</a>';
		}elseif( $what === '_self' ){
			return '<a href="'. esc_url($this->url) .'">'. esc_html($this->text) .'</a>';
		}elseif( $what === '_parent' || $what === '_top' ){
			return '<a href="'. esc_url($this->url) .'" target="'.$what.'">'. esc_html($this->text) .'</a>';
		}else{
			return parent::output($what);
		}
	}
}
URL::init();