<?php if(!defined('AUTHORIZED')){die("Hacking Attempt: ".((isset($_SERVER['REMOTE_ADDR']))?$_SERVER['REMOTE_ADDR']:''));}
define('DEFAULT_LANG','en');
// _______ [ START EDITABLE ] __________________________________________________
define('DEFAULT_CHARSET','UTF-8');
define('DB_BASE', 			 'stripe');
define('DB_USER', 			 'stripe_user');
define('DB_PASS', 			 'helloStripe77&%');// Replace this password by the one you've set.
define('SUB_DOMAIN_NAME','stripe'); 		    // Your server sub-domaine.
define('DOMAIN_NAME', 	 'robby.ai'); 			// Your server domain name.
define('DEBUG_IP', 			 '89.2.69.205'); 	  // Your local debug IP address.
define('DB_HOST_WWW', 	 '91.121.80.48');   // Your PROD server IP,(www) 	> https://stripe.robby.ai
define('DB_HOST_LOCAL',  '127.0.0.1'); 		  // Your DEV server IP, (local) > http://localhost:8080
define( 'ERRORS_LOG','/home/bibi/apache2_logs/php.stripe.errors.log'); // error log file
//_________ [ END EDIDATE ] ____________________________________________________
define('PROD',(isset($_SERVER['HTTP_HOST'])&&preg_match('/localhost/i',$_SERVER['HTTP_HOST'])>0)?FALSE:TRUE);
define('DB_HOST_PROD',((!PROD)?DB_HOST_LOCAL:DB_HOST_WWW));
define('PDO_DSN','mysql:host='.DB_HOST_PROD.';dbname='.DB_BASE.';charset=utf8');

define('PROTOCOL',  		 'http'.((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']=='on')?'s':'').'://');
define('PROTOCOL_HTTP',  'http://');
define('DOMAIN_DEBUG',   'localhost:8080');
define('ROOT_DOMAIN',(isset($_SERVER['DEFAULT_VERSION_HOSTNAME'])?$_SERVER['DEFAULT_VERSION_HOSTNAME']:SUB_DOMAIN_NAME.'.'.DOMAIN_NAME));
define('DEBUG',(isset($_SERVER['REMOTE_ADDR'])&&$_SERVER['REMOTE_ADDR']==DEBUG_IP)?TRUE:FALSE);

define('LOCAL_ENCRIPTION_KEY','TTYU77ER985XQA' 		 ); // used as the private key to crypt the password (@see ./src/libs/Crypter.php)
define('TIMESTAMP_STRUCTURE', 'Y-m-d H:i:s' 	     ); // template of timestamp to ALWAYS use in the code
define('THIRTY_SECONDS',   		30                   ); // 30s
define('ONE_MINUTE_SECONDS',  THIRTY_SECONDS*2     ); // 60s
define('ONE_HOUR_SECONDS',    ONE_MINUTE_SECONDS*60); // 3600s
define('ONE_DAY_SECONDS',     ONE_HOUR_SECONDS*24  ); // 86400s
define('ONE_WEEK_SECONDS',    ONE_DAY_SECONDS*7    ); // 604800s
define('ONE_MONTH_SECONDS',   ONE_DAY_SECONDS*30   ); // 2592000s
// -- Server local path -------------------------------
define('WEB_PATH', 		str_replace('/src/config','',__DIR__));// >> /home/www/stripe.robby.ai
define('CURRENT_SITE_FOLDER','/'                          ); // >> /
define('SITE_PATH', 	 WEB_PATH.CURRENT_SITE_FOLDER 			); // >> /home/www/stripe.robby.ai/
define('INCLUDES_PATH',WEB_PATH.CURRENT_SITE_FOLDER."src/"); // >> /home/www/stripe.robby.ai/src/
// --- MVC constants ----------------------------------
define('INCLUDES_ROOT',     INCLUDES_PATH 		            ); // >> /home/www/stripe.robby.ai/src/
define('INCLUDES_CONFIG',   INCLUDES_ROOT.'config/'       ); // >> /home/www/stripe.robby.ai/src/config/
// -- 							Relatives paths
define('LIBS_PATH',					'libs/'	            								); // >> libs/
define('MODELS_PATH',				'models/'	            							); // >> models/
define('VIEWS_PATH',				'views/'	            							); // >> views/
define('CONTROLLERS_PATH',	'controllers/'         							); // >> controllers/
define('API_ROOT_PATH',			'api/'	            								); // >> api/
define('API',	CURRENT_SITE_FOLDER.API_ROOT_PATH	); // >> /api/
define('ASSETS',						CURRENT_SITE_FOLDER.'assets/'     ); // >> /assets/
define('IMG',								ASSETS . 'img/' 	            			); // >> /assets/img/
define('CSS',								ASSETS . 'css/' 	            			); // >> /assets/css/
define('JS',								ASSETS . 'js/' 	            				); // >> /assets/js/
// -- 						Absolute paths
define('INCLUDES_LIBS',     INCLUDES_ROOT . LIBS_PATH   	      ); // >> /home/www/stripe.robby.ai/src/libs/
define('INCLUDES_MODELS',   INCLUDES_ROOT . MODELS_PATH   	    ); // >> /home/www/stripe.robby.ai/src/models/
define('INCLUDES_CONTROLLERS',INCLUDES_ROOT.CONTROLLERS_PATH    ); // >> /home/www/stripe.robby.ai/src/controllers/
define('INCLUDES_VIEWS',    INCLUDES_ROOT . VIEWS_PATH    	    ); // >> /home/www/stripe.robby.ai/src/views/
define('INCLUDES_API',  	  INCLUDES_ROOT . API_ROOT_PATH	      ); // >> /home/www/stripe.robby.ai/src/api/
