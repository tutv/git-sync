<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

class GitController extends Controller {
	public function push( Request $request ) {
		error_reporting( E_ALL );
		ini_set( 'display_errors', '1' );
		set_time_limit( 0 );
		$path_log_txt     = '/home/f.tutran.me/github.txt';
		$path_git_pull_sh = '/home/f.tutran.me/git-puller.sh';

		$output = shell_exec( $path_git_pull_sh );

		$server = $_SERVER;
		if ( isset( $server['HTTP_X_GITHUB_EVENT'] )
		     && $server['HTTP_X_GITHUB_EVENT'] == 'push'
		) {
			$content = file_get_contents( 'php://input' );
			$obj     = json_decode( $content );

			$ref               = $obj->ref;
			$request_timestamp = intval( $server['REQUEST_TIME'] );
			$time              = date_create()
				->setTimestamp( $request_timestamp )->format( 'H:i:s d/m/Y' );

			$reponse = $time . ' ' . $ref . PHP_EOL;

			$commits = $obj->commits;
			foreach ( $commits as $index => $commit ) {
				$text = $commit->author->name . '(' . $commit->author->username
				        . ') - ';
				$text .= '"' . $commit->message . '" - ' . $commit->id;

				$reponse .= $text . PHP_EOL;
			}

			$reponse .= '---------------------------------------------------'
			            . PHP_EOL;

			printf( $reponse );

			if ( $this->countLineTextFile( $path_log_txt ) > 1000 ) {
				file_put_contents( $path_log_txt, PHP_EOL . sprintf( $reponse ),
					FILE_TEXT );
			} else {
				file_put_contents( $path_log_txt, PHP_EOL . sprintf( $reponse ),
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

	public function testShell() {
		$string
			= shell_exec( 'cd /home/git.tutran.net/repos/fdownload && git pull' );
		$output = shell_exec('ls -lart');
		echo "<pre>$output</pre>";
	}
}
