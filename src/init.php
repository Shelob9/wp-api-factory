<?php
/**
 * Start up the system
 *
 * IMPORTANT: This class doest not use a singleton so more than one instance can be used, but you should keep track of this instance in a relatively global scope for yoru plugin/application.
 *
 * @package
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 Josh Pollock
 */

namespace josh\api_factory;


use josh\api_factory\system\factory;
use josh\api_factory\util;


class init {

	/**
	 * API namespace
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $namespace;

	/**
	 * API route factory
	 *
	 * @since 1.0.0
	 *
	 * @var \josh\api_factory\system\factory
	 */
	protected $factory;

	/**
	 * Holds a nonce
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $api_nonce;

	/**
	 * @param string $namespace namespace for routes created with this system
	 * @param array $endpoints Optional. Array of endpoints to add. Default is false.
	 */
	public function __construct( $namespace, array $endpoints = array() ){
		$this->namespace = $namespace;
		$this->set_api_nonce();
		$this->factory = factory::get_instance();
		if( ! empty( $endpoints ) ){
			$this->run_endpoints( $endpoints );
		}
	}

	/**
	 * Get our namespace
	 *
	 * @return string
	 */
	public function get_namespace(){
		return $this->namespace;
	}

	/**
	 * Add endpoints
	 *
	 * @since 1.0.0
	 *
	 * @param array $endpoints
	 */
	public function run_endpoints( array $endpoints ){
		foreach ( $endpoints as $endpoint  ) {
			if( ! isset( $endpoint[ 'cap' ] ) ){
				$endpoint[ 'cap' ] = 'nonce';
			}

			$this->factory->create( $endpoint[ 'callback_class' ], $endpoint[ 'route' ], $endpoint[ 'cap' ], $this->namespace );
		}

	}

	/**
	 * Set the api_nonce property with a nonce
	 *
	 * @since 1.0.0
	 */
	private function set_api_nonce(){
		$this->api_nonce = wp_create_nonce( $this->namespace );
	}

	/**
	 * Get the api_nonce property of this class
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_api_nonce(){
		return $this->api_nonce;
	}

	/**
	 * Verify nonce
	 *
	 * @since 1.0.0
	 *
	 * @param string $nonce
	 *
	 * @return bool
	 */
	public function verify_nonce( $nonce ){
		return util::verify_nonce( $nonce, $this->namespace );
	}



}
