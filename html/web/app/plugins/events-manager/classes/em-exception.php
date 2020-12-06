<?php
//in case we include it in EM core code
if( !class_exists('EM_Exception') ){
	/**
	 * Extended Exception class that allows for creating multiple error messages in an exception as an array and outputting them together at once.
	 * Class Exception
	 */
	class EM_Exception extends Exception {
		
		/**
		 * @var WP_Error
		 */
		public $wp_error;
		/**
		 * @var array
		 */
		public $error_messages = array();
		/**
		 * @var int|string Allows for a custom code to be used rather than an integer.
		 */
		public $error_code;
		
		/**
		 * Exception constructor.
		 * @param string $error
		 * @param int $code
		 * @param null $previous
		 */
		public function __construct($error = '', $code = 0, $previous = null ){
			if( is_array($error) ) {
				$this->error_messages = $error;
				$message = $this->get_message();
			}elseif( is_wp_error($error) ){ /* @var WP_Error $error */
				$this->wp_error = $error;
				$code = $error->get_error_code();
				$message = $error->get_error_message();
			}else{
				$message = $error;
			}
			if( !is_numeric($code) ){
				$this->error_code = $code;
				$code = 0;
			}
			parent::__construct($message, $code, $previous);
		}
		
		/**
		 * Returns either a string code reference, or a regular Exception code number.
		 * @return int|string
		 */
		public function get_error_code(){
			if( $this->error_code ){
				return $this->error_code;
			}
			return $this->getCode();
		}
		
		/**
		 * Provides a paragraph-formatted message which may contain multiple paragraphs for multiple errors.
		 * @return string
		 */
		public function get_message(){
			if( $this->is_wp_error() ){
				$message = '<p>' . implode('</p><p>', $this->wp_error->get_error_messages()) . '</p>';
			}elseif( !empty($this->error_messages) ){
				$message = '<p>' . implode('</p><p>', $this->error_messages) . '</p>';
			}else{
				$message = '<p>' . $this->getMessage() . '</p>';
			}
			return $message;
		}
		
		/**
		 * @return array|string
		 */
		public function get_messages(){
			if( $this->is_wp_error() ){
				return $this->wp_error->get_error_messages();
			}elseif( !empty($this->error_messages) ){
				return $this->error_messages;
			}else{
				return array($this->getMessage());
			}
		}
		
		/**
		 * Whether or not this exception was triggered by a WP_Error
		 * @return bool
		 */
		public function is_wp_error(){
			return is_wp_error( $this->wp_error );
		}
		
		/**
		 * Returns exception in WP_Error format, whether or not it was originally a WP_Error in the first place.
		 * @return WP_Error
		 */
		public function get_wp_error(){
			if( $this->is_wp_error() ){
				return $this->wp_error;
			}
			$WP_Error = new WP_Error();
			$WP_Error->add_data( $this->get_messages(), $this->getCode() );
			return $WP_Error;
		}
	}
}