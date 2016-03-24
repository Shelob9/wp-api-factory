<?php
/**
 * Interface for endpoints classes to implement
 *
 * @package   josh\api_factory
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 Josh Pollock
 */

namespace josh\api_factory;


interface api_interface {

	/**
	 * Processing callback
	 *
	 *@since 1.0.0
	 *
	 * @param \WP_REST_Request $request REST Request object
	 *
	 * @return array|string|\WP_Error
	 */
	public static function process( \WP_REST_Request $request );

	/**
	 * Define fields for endpoint(s) of this route
	 *
	 *@since 1.0.0
	 *
	 * @return array
	 */
	public static function fields();

}
