<?php
namespace Stripe;

if ( !AUTHORIZED ) {
  die( "Hacking Attempt: ". $_SERVER['REMOTE_ADDR'] );
}

abstract class Controller
{
  public function __construct() {}

  /**
   * @param {string}  Class name,    ex: __CLASS__
   * @param {string}  Method name,   ex: __METHOD__
   * @param {int}     Line number,   ex: __LINE__
   * @param {string}  Error message.
   * @param {object}  Error object.
   */
  public static function error($class, $method, $line, $error_message = '', $error_object = NULL, $is_email_admin = TRUE, \Exception $exception = NULL )
  {
    if ( preg_match('/::/',$method)>0) {
      $sp_ = preg_split('/::/',$method);
      if (isset($sp_) && !empty( $sp_)) {
        $method = $sp_[count($sp_)-1];
      }
    }
    $error_ = ['message'=>$error_message];
    // -- Log the errors on server-side
    $enc = @json_encode($error_object);
    self::log(
      self::ssh_style('red',
        ((isset($class))?$class:'').
        ((isset($method))?"::".$method."()":'').
        ((isset($line))?':'.$line:'')
      )." ".
      self::ssh_style('bold', $error_message ) .
      self::ssh_style('italic', ( ( isset( $enc ) && $enc != FALSE && !empty( $enc ) && $enc != 'false' ) ? " > " . $enc : "" ) )
    );

    return $error_;
  }

  /**
   * Function used to handle Fatal Error in
   * @see ./src/config/config.php
   * @return {void}
   */
  public static function set_fatal_handler()
  {
    $errfile = "unknown file";
    $errstr  = "shutdown";
    $errno   = E_CORE_ERROR;
    $errline = 0;
    $error = error_get_last();
    if ( $error !== NULL ) {
      $errno   = $error[ "type"    ];
      $errfile = $error[ "file"    ];
      $errline = $error[ "line"    ];
      $errstr  = $error[ "message" ];
      \Stripe\Controller::error( $errno, $errfile, $errline, $errstr, $error, TRUE );
    }
  }

  /**
   * Redirect the page to a specific URL, some data can be POSTed to that URL.
   *
   * @param {string} $url URL where to be redirected.
   * @param {string} $method can be 'GET' ot 'POST', Default 'GET'
   * @param {array} $data Data to POST to the redirected URL, Default NULL
   * @return {void}
   */
  public static function redirect( $url, $method = 'GET', Array $data = [] )
  {
    if ( $method == \Stripe\Model::GET ) {
      @ob_end_clean();
      header( 'Location: ' . $url );
      die;
    } else {
      @ob_end_clean();
      header( 'Location: ' . $url );
      header( "HTTP/1.1 302" );
      if ( isset( $data ) && !empty( $data ) ) {
        foreach ( $data as $k => $v ) {
          $_POST[ $k ] = $v;
        }
      }
    }
  }


  // == Private methods

  /**
   * Log the errors on the server-side with colors on the text.
   * To display the live debugging, start a server ssh session:
   * ```ssh
   *   $> cd /home/apache2_logs/
   *   $> tail -f php.stripe.errors.log
   * ```
   *
   * @access private
   * @param {string} $message
   * @return {void}
   */
  private static function log($message=NULL)
  {
    if (PROD && isset($message) && function_exists('error_log')) {
      error_log(
        "[".self::ssh_style('green', date('d-M-Y H:i:s e'))."]".
        "[".self::ssh_style('yellow',((isset($_SESSION['ACCOUNT']['id']))?'accountID:'.$_SESSION['ACCOUNT']['id']:''))."]".
        ((isset($message))?" > ".$message:'').PHP_EOL,
        3,
        ERRORS_LOG
      );
    }
  }

  /**
   * Set SSH log file style for a specific text.
   *
   * @access private
   * @param {string} $style Name of the style to apply.
   * @param {string} $text text to embed with this style.
   * @return {string} returs the text embeded into the style.
   */
  private static function ssh_style($style,$text)
  {
  	$colorFormats = [
      // -- `italic` and `blink` may not work depending of your terminal
      'bold' 			=> "\033[1m%s\033[0m",
      'dark' 			=> "\033[2m%s\033[0m",
      'italic' 		=> "\033[3m%s\033[0m",
      'underline' => "\033[4m%s\033[0m",
      'blink' 		=> "\033[5m%s\033[0m",
      'reverse' 	=> "\033[7m%s\033[0m",
      'concealed' => "\033[8m%s\033[0m",
      // -- foreground colors
      'black' 		=> "\033[30m%s\033[0m",
      'red' 			=> "\033[31m%s\033[0m",
      'green' 		=> "\033[32m%s\033[0m",
      'yellow' 		=> "\033[33m%s\033[0m",
      'blue' 			=> "\033[34m%s\033[0m",
      'magenta' 	=> "\033[35m%s\033[0m",
      'cyan' 			=> "\033[36m%s\033[0m",
      'white' 		=> "\033[37m%s\033[0m",
      // -- background colors
      'bg_black' 	=> "\033[40m%s\033[0m",
      'bg_red' 		=> "\033[41m%s\033[0m",
      'bg_green' 	=> "\033[42m%s\033[0m",
      'bg_yellow' => "\033[43m%s\033[0m",
      'bg_blue' 	=> "\033[44m%s\033[0m",
      'bg_magenta'=> "\033[45m%s\033[0m",
      'bg_cyan' 	=> "\033[46m%s\033[0m",
      'bg_white' 	=> "\033[47m%s\033[0m",
    ];
  	return sprintf($colorFormats[$style],$text);
  }
}
