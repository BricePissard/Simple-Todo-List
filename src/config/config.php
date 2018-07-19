<?php if(defined('AUTHORIZED')==FALSE){define('AUTHORIZED',TRUE);}
setlocale( LC_ALL, 'en_US.UTF8' );
$DIE_MESSAGE = "Whoops... it seems that the API is broken. We've been informed, please comme back later.";
( include_once dirname(__FILE__).'/constants.php') OR die($DIE_MESSAGE);
$autoloader = (include_once dirname(__FILE__).'/MVC_autoloader.php');
$autoloader->require_all();

// -- Override PHP.ini global vars that can be dynamically be changed by 3rd party libraries, cf Composers libs in: ./libs/vendor/...
ini_set( 'memory_limit',              	 '1024M'        );
ini_set( 'max_execution_time',         	 600            ); // 10 minutes: 60*10
ini_set( 'default_socket_timeout',     	 600            ); // 10 minutes: 60*10, Default: 60
ini_set( 'default_charset',            	 DEFAULT_CHARSET);
ini_set( 'always_populate_raw_post_data','-1'					  );
ini_set( 'zlib.output_compression',    	 'On'           ); // <--- /!\ if a simple char or even a space exists before this statement, the server will crash!
ini_set( 'session.cookie_lifetime', ONE_MONTH_SECONDS); // one month: 3600*24*30
ini_set( 'session.gc_maxlifetime',  ONE_MONTH_SECONDS); // one month: 3600*24*30
// ----- DEBUG - KILL ALL SESSIONS ---------------------------------------------
//ini_set('session.gc_max_lifetime', 	0	);
//ini_set('session.gc_probability', 	1	);
//ini_set('session.gc_divisor', 			1	);
// -----------------------------------------------------------------------------

// -- init the global services: Session, DB, Memcached, Socket Pull/Push
\Stripe\Model::init();

// -- Error handlers
error_reporting(E_ALL);
register_shutdown_function(['\Stripe\Controller','set_fatal_handler']);

if ( DEBUG ) {
	ini_set( 'display_startup_errors',TRUE);
	ini_set( 'display_errors', 				TRUE);
	ini_set( 'html_errors',    				TRUE);
} else {
	ini_set( 'display_startup_errors',FALSE);
	ini_set( 'display_errors', 				FALSE);
	ini_set( 'html_errors',    				FALSE);
}

function _is($page='')
{
	$search = strtolower(str_replace('_',' ',$page));
	$path	= str_replace('_',' ',str_replace( '-',' ',strtolower(((isset($_SERVER['REQUEST_URI']) && !empty($_SERVER['REQUEST_URI']))?$_SERVER['REQUEST_URI']:''))));
	return ((preg_match("/\/".$search."/i",$path)>0)?TRUE:FALSE);
}
