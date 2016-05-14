<?php
/**
 * Utilities for this package
 *
 * @package   shelob9\api_factory
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 Josh Pollock
 */

namespace shelob9\api_factory;


class util {


	/**
	 * Create a REST response
	 *
	 * @since 1.0.0
	 *
	 * @param array|object|\WP_Error $data Response data
	 * @param int $status Status Code, Optional. Default is 200. Ignored if $data is an WP_Error, code will be 500, or if $data is empty, code will be 404.
	 * @param array $headers. Optional. Array of headers to send. Ignored if $data is an WP_Error, code will be 500.
	 *
	 * @return \WP_REST_Response|\WP_Error
	 */
	public static  function response( $data, $status = 200, array $headers = [] ){
		if ( ! is_wp_error( $data )  ) {
			if ( 404 == $status || empty( $data ) ) {
				$response = new \WP_REST_Response( null, 404, $headers );
			} else {
				$response = new \WP_REST_Response( $data, $status, $headers );
			}


			return $response;
		} else {
			rest_ensure_response( $data );


		}

	}

	/**
	 * Check a nonce and return a boolean
	 *
	 * @since 1.0.0
	 *
	 * @param string $nonce Nonce
	 * @param string $namespace Namespace for nonce
	 *
	 * @return bool
	 */
	public static function verify_nonce( $nonce, $namespace ){
		$verified = wp_verify_nonce( $nonce, $namespace );
		return (bool) $verified;
	}

}
