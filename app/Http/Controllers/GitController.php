<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

class GitController extends Controller {
	public function push( Request $request ) {
		$DIR_BASE_LOG    = '/home/git.tutran.net/log-github/';
		$DIR_BASE_PULLER = '/home/git.tutran.net/puller/';

		error_reporting( E_ALL );
		ini_set( 'display_errors', '1' );
		set_time_limit( 0 );

		$server = $_SERVER;
		if ( isset( $server['HTTP_X_GITHUB_EVENT'] )
		     && $server['HTTP_X_GITHUB_EVENT'] == 'push'
		) {
			$content = file_get_contents( 'php://input' );
			$obj     = json_decode( $content );

			$repo_name = $obj->repository->name;

			$ref               = $obj->ref;
			$request_timestamp = intval( $server['REQUEST_TIME'] );
			$time              = date_create()
				->setTimestamp( $request_timestamp )->format( 'H:i:s d/m/Y' );

			$response = $time . ' ' . $ref . PHP_EOL;

			$commits = $obj->commits;
			foreach ( $commits as $index => $commit ) {
				$text = $commit->author->name . '(' . $commit->author->username
				        . ') - ';
				$text .= '"' . $commit->message . '" - ' . $commit->id;

				$response .= $text . PHP_EOL;
			}

			$response .= $repo_name . 'ok men';

			$response .= '---------------------------------------------------'
			            . PHP_EOL;

			$response .= $repo_name . '_sdfdsfds__dsfdsfn';

			printf( $response );

			/**
			 * Execute git pull
			 */
			$str_shell_puller = $DIR_BASE_PULLER . $repo_name . '.sh';
			shell_exec( $str_shell_puller );

			/**
			 * Log
			 */
			$path_log_txt = $DIR_BASE_LOG . $repo_name . '.txt';

			if ( $this->countLineTextFile( $path_log_txt ) > 1000 ) {
				file_put_contents( $path_log_txt, PHP_EOL . sprintf( $response ),
					FILE_TEXT );
			} else {
				file_put_contents( $path_log_txt, PHP_EOL . sprintf( $response ),
					FILE_APPEND );
			}
		}
	}

	function countLineTextFile( $path ) {
		$count  = 0;
		$handle = fopen( $path, 'r' );
		while ( ! feof( $handle ) ) {
			$line = fgets( $handle );
			$count ++;
		}

		fclose( $handle );

		return $count;
	}
}
