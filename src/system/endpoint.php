<?php
/**
 * Add endpoint to API
 *
 * @package   shelob9\api_factory;
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 Josh Pollock
 */

namespace shelob9\api_factory\system;


use shelob9\api_factory\util;

class endpoint {

	/**
	 * Marks what this route is.
	 *
	 * @since 1.0.0
	 *
	 * @access protected
	 *
	 * @var string
	 */
	protected $route;

	/**
	 * Capability to use this route
	 *
	 * @since 1.0.0
	 *
	 * @access protected
	 *
	 * @var string
	 */
	protected $cap;

	/**
	 * Fields for endpoint
	 *
	 * @var array
	 */
	protected $fields;

	/**
	 * Name of callback class
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $callback_class;

	/**
	 * API namespace
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $namespace;

	/**
	 * Create  API endpoint
	 *
	 * @since 1.0.0
	 *
	 * @param string $callback_class Name of callback class
	 * @param string $route Name of route
	 * @param string $cap Optional. Capability to access this route. Default is "read".
	 * @param string|array $methods Optional. Transport methods GET|POST or array( 'GET', 'POST' ); Default is POST.
	 * @param string $namespace API namespace
	 */
	public function __construct( $callback_class, $route, $cap, $methods, $namespace ) {
		$this->route = $route;
		$this->cap = $cap;
		$this->namespace = $namespace;

		$this->fields = $callback_class::fields();
		$this->callback_class = $callback_class;
		$this->methods = $methods;

		$this->register_routes();
	}

	/**
	 * Register route for endpoint
	 *
	 * @since 1.0.0
	 *
	 */
	protected function register_routes() {
		register_rest_route( $this->namespace, '/' . $this->route, $this->args() );
	}

	/**
	 * Prepare args for endpoing
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	protected function args(){
		$args = [
			'methods'         => $this->methods,
			'callback'        => array( $this, 'do_callback' ),
			'args'            => $this->fields
		];

		$args[ 'args' ][ '_nonce' ] = [
			'type' => 'text',
			'required' => true,
		];

		if ( ! isset( $args[ 'args' ][ 'context' ] ) ) {
			$args[ 'args' ][ 'context' ] = [
				'type'     => 'text',
				'required' => false,
				'default'  => 'view'
			];
		}

		if( 'nonce' == $this->cap ){

			$args[ 'permission_callback' ] = [ $this, 'nonce_callback' ];
		}else{
			$args[ 'permission_callback' ] = [ $this, 'permission_callback' ];
		}

		return $args;
	}
	/**
	 * Handle callback
	 *
	 * @since 1.0.0
	 *
	 * @param \WP_REST_Request $request Full data about the request.
	 *
	 * @return \WP_Error|\WP_REST_Response
	 */
	public function do_callback( $request ) {
		$response_data = call_user_func( [$this->callback_class, 'process' ], $request );
		return util::response( $response_data );
	}
	/**
	 * Check permissions
	 *
	 * @since 1.0.0
	 *
	 * @param \WP_REST_Request $request Full data about the request.
	 *
	 * @return bool
	 */
	public function permission_callback( $request ) {
		if( ! get_current_user() ) {
			return false;
		}

		$params = $request->get_params();
		if( $this->can() && $this->check_nonce( $params[ '_nonce' ] ) ) {
			return true;
		}

	}

	/**
	 * Check if current user has acceptable permissions
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	private function can() {
		$can = current_user_can( $this->cap );
		return $can;
	}

	/**
	 * Handler for when nonce check is permissions callback
	 *
	 * @since 1.0.0
	 *
	 * @param \WP_REST_Request $request Request
	 *
	 * @return bool
	 */
	public function nonce_callback( $request ){
		return $this->check_nonce( $request->get_param( '_nonce' ) );
	}

	/**
	 * Check a nonce
	 *
	 * @since 1.0.0
	 *
	 * @param string $nonce Nonce to check
	 *
	 * @return bool
	 */
	protected function check_nonce( $nonce ){
		return util::verify_nonce( $nonce, $this->namespace );
	}



}
