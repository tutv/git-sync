<?php
/**
 * Created by PhpStorm.
 * User: Tu TV
 * Date: 19/11/2015
 * Time: 4:53 PM
 */

require_once __DIR__ . '/unirest/Unirest.php';

/**
 * Get content from url via unirest.io
 *
 * @param $url
 *
 * @return mixed
 */
function f_file_get_contents( $url ) {
	try {
		$obj_unirest = Unirest\Request::get( $url, null, null );

		return [
			'content' => $obj_unirest->raw_body,
			'code'    => $obj_unirest->code
		];
	} catch ( Exception $exception ) {
		die( $exception->getMessage() );
	}
}

/**
 * Get response 404
 *
 * @return \Illuminate\Http\JsonResponse
 */
function get404Error() {
	return response()->json( [
		'status' => 'error',
		'msg'    => '404',
	] );
}

/**
 * Only allowed POST Request | Abort 404 when request different POST request
 *
 * @param $request
 */
function onlyAllowPostRequest( $request ) {
	if ( method_exists( $request, 'getMethod' )
	     && $request->getMethod() !== 'POST'
	) {
		abort( 404 );
	}
}