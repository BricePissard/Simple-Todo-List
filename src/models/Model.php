<?php
namespace Stripe;

if (!AUTHORIZED) {
  die("Hacking Attempt [Model] : ". $_SERVER['REMOTE_ADDR']);
}

/**
 * Abstract Model class extended by all Models
 **/
abstract class Model
{
  public static $DB	= NULL;
	public static $sql = NULL;

	const API_OUTPUT_JSON = 'json';
	const API_OUTPUT_XML = 'xml';

	const POST = 'POST';
	const GET = 'GET';
  const PUT = 'PUT';

	/**
   * @see http://php.net/manual/fr/pdo.getattribute.php
   */
	public static $PDO_ATTR_ = [
		"AUTOCOMMIT",
		"ERRMODE",
		"CASE",
		"CLIENT_VERSION",
		"CONNECTION_STATUS",
		"ORACLE_NULLS",
		"PERSISTENT",
		"SERVER_INFO",
		"SERVER_VERSION",
	];

  public function __construct() {}

  public static function init( $isForce = FALSE )
  {
    if (!isset($_SESSION)) {
      self::set_session();
    }
    if (!isset( self::$DB ) || $isForce === TRUE) {
      self::$DB = self::get_db();
    }
  }

  /**
   * Initialize DB connection
   */
	public static function get_db()
	{
	  try {
			$DB = new \PDO(PDO_DSN,DB_USER, DB_PASS, [\PDO::ATTR_PERSISTENT => TRUE]);
			$DB->setAttribute(\PDO::ATTR_EMULATE_PREPARES,TRUE);
			$DB->setAttribute(\PDO::ATTR_ERRMODE,\PDO::ERRMODE_EXCEPTION);
			$DB->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE,\PDO::FETCH_ASSOC);
			self::$DB = $DB;
      return $DB;
		} catch (\PDOException $err) {
    	\Stripe\Controller::error( __CLASS__, __METHOD__, __LINE__,
			  "ERROR Model::DB: from[". ((isset($_SERVER['HTTP_HOST']))?$_SERVER['HTTP_HOST'] : ((isset($_SERVER['SERVER_ADDR']))?$_SERVER['SERVER_ADDR'] : '' ) ). "] > to[". PDO_DSN . "]",
			  ((is_callable([ $err, 'getMessage']) ) ? $err->getMessage() . ' - ' . __FILE__ . ':' . __LINE__ : '' ),
        FALSE,
        $err
      );
			return NULL;
		} catch ( \Exception $err ) {
    	\Stripe\Controller::error( __CLASS__, __METHOD__, __LINE__,
			  "ERROR Model::DB: from[". ((isset($_SERVER['HTTP_HOST']))?$_SERVER['HTTP_HOST'] : ((isset($_SERVER['SERVER_ADDR']))?$_SERVER['SERVER_ADDR'] : '' ) ). "] > to[". PDO_DSN . "]",
			  ( ( is_callable([ $err, 'getMessage']) ) ? $err->getMessage() . ' - ' . __FILE__ . ':' . __LINE__ : '' ),
        FALSE,
        $err
      );
			return NULL;
		}
		return NULL;
	}

	public function __destruct()
  {
  	self::$DB = NULL;
	}

  /**
   * Initialize Session
   */
	private static function set_session()
  {
	  $result = FALSE;
    $sessid = NULL;
    try {
      session_set_cookie_params(ONE_MONTH_SECONDS);
      if (session_status() == PHP_SESSION_NONE) {
        $result = session_start();
      }
    } catch (\Exception $err) {
      $message = ( ( is_callable([ $err, 'getMessage']) ) ? $err->getMessage() : '' );
      \Stripe\Controller::error( __CLASS__, __METHOD__, __LINE__, $message, $err, TRUE, $err );
      return $result;
    }
    return $result;
  }

  /**
   * Reset Session
   */
	private static function reset_session()
	{
		$_SESSION = NULL;
		session_regenerate_id();
	}

  public static function get_request_id($request='')
  {
    return md5(
      $request .
      ((isset($_SERVER['SERVER_ADDR']))? $_SERVER['SERVER_ADDR']:'') .
      ( ( isset(self::$DB) && is_callable(self::$DB, 'getAttribute')) ?
        self::$DB->getAttribute( constant( "PDO::ATTR_CONNECTION_STATUS"))
        : ''
      ),
      FALSE
    );
  }

	public static function error( $class, $method, $sql = '', $message = '' )
	{
		$DB_ATTR = "";
		foreach ( self::$PDO_ATTR_ as $val ) {
			$DB_ATTR .= "<b>PDO::ATTR_" . $val . ": </b>" . self::$DB->getAttribute( constant( "PDO::ATTR_" . $val ) ) . "<br/>";
    }
    $error_ = ((isset(self::$DB ) && is_callable( self::$DB, 'errorInfo'))?: [] );
		$message =  ((is_object($message) || is_array($message))? htmlvardump($message):$message);
    \Stripe\Controller::error( $class, $method, null, $message, [ $error_, $sql, $DB_ATTR ], TRUE );
	}

	public static function get_sql_error( $sql = NULL )
	{
		return (($sql!=NULL && DEBUG && DEBUG_IP==$_SERVER['REMOTE_ADDR'] ) ?
		  "<br/><pre>" . ( ( PROD === FALSE ) ? $sql : '' ) . "</pre>"
      : ''
    );
	}

	public static function get_http_root()
	{
		return PROTOCOL . $_SERVER['HTTP_HOST'];
	}

	public static function get_asset_path( $file, $type = 'JS', $force_secure	= FALSE, $force_update = FALSE )
	{
		$path = self::get_http_root();
		switch ( $type ) {
			case 'JS': $path .= JS.$file; break;
			case 'CSS': $path .= CSS.$file; break;
			case 'IMG': $path .= IMG.$file; break;
		}
		return $path . ( ( $force_update === TRUE ) ? "?v=" . date( 'U' ) : '' );
	}
}
