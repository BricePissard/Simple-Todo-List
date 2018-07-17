<?php if ( !defined( 'AUTHORIZED' ) ){ die( "Hacking Attempt: ". ( ( isset( $_SERVER[ 'REMOTE_ADDR' ] ) ) ? $_SERVER[ 'REMOTE_ADDR' ] : '' ) ); }

define( 'DEFAULT_LANG',    'en'    );
define( 'DEFAULT_CHARSET', 'UTF-8' );

define( 'PROD', ( isset( $_SERVER[ 'HTTP_HOST' ] ) && preg_match( '/localhost/i', $_SERVER[ 'HTTP_HOST' ] ) > 0 ) ? FALSE : TRUE );

// _______ [ START - Database ] _____________________
// == Start mysql on localhost:
// shell> sudo /usr/local/mysql/support-files/mysql.server restart

define( 'DB_BASE', 'stripe' );
define( 'DB_USER', 'stripe_user' );
define( 'DB_PASS', 'helloStripe77&%' );

define( 'DB_HOST_WWW', 	 '91.121.80.48' ); // (www) 	> https://stripe.robby.ai:10000/mysql/
define( 'DB_HOST_LOCAL', '127.0.0.1' 		); // (local) > http://localhost:8080
define( 'DB_HOST_PROD',  ( ( !PROD ) ? DB_HOST_LOCAL : DB_HOST_WWW ) );
define( "PDO_DSN", 'mysql:host=' . DB_HOST_PROD . ';dbname=' . DB_BASE . ';charset=utf8' );
//_________ [ END - Database ] _________________________________________________

// -----------------------------------------------------------------------------
define( 'DEBUG_IP', 	'89.2.69.205' 	 ); // IP: Jean Jacques Rousseau
//define( 'DEBUG_IP', '82.251.157.85'  ); // IP: Rambuteau
//define( 'DEBUG_IP', '::1'            ); // IP: GAE in local
//die( $_SERVER[ 'REMOTE_ADDR' ] );
// -----------------------------------------------------------------------------

define( 'PROTOCOL',  					'http'. ( ( isset( $_SERVER[ 'HTTPS' ] ) && $_SERVER[ 'HTTPS' ] == 'on' ) ? 's' : ''  ) . '://' ); // $protocol = stripos($_SERVER['SERVER_PROTOCOL'],'https') === true ? 'https://' : 'http://';
define( 'PROTOCOL_SECURE', 		'https://' 		 		); // /!\ remove  's' in api.robby.ai because SSL certificate is not valid for sub-domains.
define( 'PROTOCOL_HTTP', 			'http://' 		 		);

define( 'SUB_DOMAIN_NAME', 		'stripe' 					); // /!\ THIS VALUE MUST BE CHANGED MANUALLY accordingly to te server
define( 'DOMAIN_NAME', 				'robby.ai' 	 	 		); // /!\ no www. it's important (it's used to compose url with subdomains)
define( 'DOMAIN_DEBUG',    	  'localhost:8080' 	);
define( 'ROOT_DOMAIN', 				( isset( $_SERVER[ 'DEFAULT_VERSION_HOSTNAME' ] ) ? $_SERVER[ 'DEFAULT_VERSION_HOSTNAME' ] : SUB_DOMAIN_NAME . '.'. DOMAIN_NAME ) ); // /!\ replace by api. for API server, Default: 'stripe.robby.ai' (www. is important for OAuth redirection match)
define( 'DEBUG',            	( isset( $_SERVER[ 'REMOTE_ADDR' ] ) && $_SERVER[ 'REMOTE_ADDR' ] == DEBUG_IP ) ? TRUE : FALSE );

define( 'LOCAL_ENCRIPTION_KEY', 'TTYU77ER985XQA'); // used as the private key to crypt the account password in the database (see /src/libs/Crypter.php)

define( 'DATE_STRUCTURE',				'Y-m-d' 																		); // for mobile app.
define( 'TIMESTAMP_STRUCTURE',  'Y-m-d H:i:s' 	                            ); // template of timestamp to ALWAYS use in the code
define( 'THIRTY_SECONDS',   		30                                          ); // 30s
define( 'ONE_MINUTE_SECONDS',   THIRTY_SECONDS*2                            ); // 60s
define( 'ONE_HOUR_SECONDS',     ONE_MINUTE_SECONDS*60                       ); // 3600s
define( 'ONE_DAY_SECONDS',      ONE_HOUR_SECONDS*24                         ); // 86400s
define( 'ONE_WEEK_SECONDS',     ONE_DAY_SECONDS*7                           ); // 604800s
define( 'ONE_MONTH_SECONDS',    ONE_DAY_SECONDS*30                          ); // 2592000s
define( 'ONE_YEAR_SECONDS',     ONE_MONTH_SECONDS*12                        ); // 31104000s
define( 'ONE_YEAR',		 time() + ONE_YEAR_SECONDS                   					);

// -- stripe.robby.ai Settings ------------------------
define( 'WEB_PATH', 						str_replace( '/src/config', '', __DIR__ ) 	); // >> /home/bibi/www/stripe.robby.ai
define( 'CURRENT_SITE_FOLDER',  '/'                                         ); // >> /
define( 'SITE_PATH', 						WEB_PATH. CURRENT_SITE_FOLDER 							); // >> /home/bibi/www/stripe.robby.ai/
define( 'INCLUDES_PATH',        WEB_PATH. CURRENT_SITE_FOLDER . "src/"			); // >> /home/bibi/www/stripe.robby.ai/src/

// --- MVC constants ----------------------------------
define( 'INCLUDES_ROOT',        INCLUDES_PATH 		                        	); // >> /home/bibi/www/stripe.robby.ai/src/
define( 'INCLUDES_CONFIG',      INCLUDES_ROOT . 'config/'                   ); // >> /home/bibi/www/stripe.robby.ai/src/config/

// -- 							Relatives paths
define( 'LIBS_PATH',						'libs/'	            												); // >> libs/
define( 'MODELS_PATH',					'models/'	            											); // >> models/
define( 'VIEWS_PATH',					  'views/'	            											); // >> views/
define( 'CONTROLLERS_PATH',		  'controllers/'         											); // >> controllers/
define( 'API_ROOT_PATH',				'api/'	            												); // >> api/

define( 'API',									CURRENT_SITE_FOLDER . API_ROOT_PATH				 	); // >> /api/
define( 'ASSETS',								CURRENT_SITE_FOLDER . 'assets/'             ); // >> /assets/
define( 'IMG',									ASSETS . 'img/' 	            							); // >> /assets/img/
define( 'CSS',									ASSETS . 'css/' 	            							); // >> /assets/css/
define( 'JS',										ASSETS . 'js/' 	            								); // >> /assets/js/

// -- 						Absolute paths
define( 'INCLUDES_LIBS',      	INCLUDES_ROOT . LIBS_PATH   	         	    ); // >> /home/bibi/www/stripe.robby.ai/src/libs/
define( 'INCLUDES_MODELS',      INCLUDES_ROOT . MODELS_PATH   	            ); // >> /home/bibi/www/stripe.robby.ai/src/models/
define( 'INCLUDES_CONTROLLERS', INCLUDES_ROOT . CONTROLLERS_PATH            ); // >> /home/bibi/www/stripe.robby.ai/src/controllers/
define( 'INCLUDES_VIEWS',       INCLUDES_ROOT . VIEWS_PATH    	            ); // >> /home/bibi/www/stripe.robby.ai/src/views/
define( 'INCLUDES_API',  	    	INCLUDES_ROOT . API_ROOT_PATH	            	); // >> /home/bibi/www/stripe.robby.ai/src/api/
