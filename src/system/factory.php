<?php
/**
 * Factory for creating routes.
 *
 * @package   shelob9\api_factory
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 Josh Pollock
 */

namespace shelob9\api_factory\system;



class factory {

	/**
	 * Hold instance of this class
	 *
	 * @since 1.0.0
	 *
	 * @var  \shelob9\api_factory\system\factory
	 */
	private static $instance;

	/**
	 * Get isntance of this class
	 *
	 * @since 1.0.0
	 *
	 * @return  \shelob9\api_factory\system\factory
	 */
	public static function get_instance() {
		if (null === static::$instance) {
			static::$instance = new self();
		}

		return static::$instance;

	}


	protected function __construct() {}

	/**
	 * Names of callback class
	 *
	 * @since 1.0.0
	 *
	 * @access private
	 *
	 * @var array
	 */
	private $classes;
	
	/**
	 * Create an API route for this class
	 *
	 * @since 1.0.0
	 *
	 * @param string $callback_class Name of callback class
	 * @param string $route Name of route
	 * @param string $cap Optional. Capability to access this route. Default is "read".
	 * @param string|array $methods Optional. Transport methods GET|POST or array( 'GET', 'POST' ); Default is POST.
	 * @param string $namespace API namespace
	 */
	public function create( $callback_class, $route, $cap = 'read', $methods = 'GET', $namespace ) {
		if ( $this->impliments( $callback_class ) ) {
			$this->classes[ sanitize_key( $callback_class ) ] = new endpoint( $callback_class, $route, $cap, $methods, $namespace );
		}
	}
	/**
	 * Check if class cgcApiInterface
	 *
	 *
	 * @since 1.0.0
	 *
	 * @param string $class Name of class
	 *
	 * @return bool
	 */
	protected function impliments( $class ) {
		if( ! class_exists( $class ) ){
			return;
		}
		$impliments = class_implements( $class );
		if( ! empty( $impliments ) && in_array( 'shelob9\api_factory\system\api_interface', $impliments ) ) {
			return true;
		}
		
	}

	/**
	 * Get all registered callback classes
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function get_classes(){
		return $this->classes;
	}


}
