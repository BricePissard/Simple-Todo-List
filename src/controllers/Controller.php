<?php namespace Stripe; if ( !AUTHORIZED ) { die( "Hacking Attempt: ". $_SERVER[ 'REMOTE_ADDR' ] ); }
abstract class Controller
{
  function __construct() {}

  /**
   * @param {string}  Class name,    ex: __CLASS__
   * @param {string}  Method name,   ex: __METHOD__
   * @param {int}     Line number,   ex: __LINE__
   * @param {string}  Error message.
   * @param {object}  Error object.
   */
  public static function error( $class, $method, $line, $error_message = '', $error_object = NULL, $is_email_admin = TRUE, \Exception $exception = NULL )
  {
    if ( preg_match( '/::/', $method ) > 0 )
    {
      $sp_ = preg_split( '/::/',  $method );
      if (
        isset(  $sp_ ) &&
        !empty( $sp_ )
      )
      {
        $method = $sp_[ count( $sp_ ) -1 ];
      }
    }

    $error_ = [ 'message' => $error_message ];

    return $error_;
  }

  /**
   * Function used to handle Fatal Error in
   * @see ./src/config/config.php
   */
  public static function set_fatal_handler()
  {
    $errfile = "unknown file";
    $errstr  = "shutdown";
    $errno   = E_CORE_ERROR;
    $errline = 0;

    $error = error_get_last();

    if ( $error !== NULL )
    {
      $errno   = $error[ "type"    ];
      $errfile = $error[ "file"    ];
      $errline = $error[ "line"    ];
      $errstr  = $error[ "message" ];

      \Stripe\Controller::error( $errno, $errfile, $errline, $errstr, $error, TRUE );
    }
  }

  /**
   *
   */
  public static function redirect( $url, $method = 'GET', $data = NULL )
  {
    if ( $method == \Stripe\Model::GET )
    {
      @ob_end_clean();
      header( 'Location: ' . $url );
      die;
    }
    else
    {
      @ob_end_clean();

      header( 'Location: ' . $url );
      header( "HTTP/1.1 302" );

      if ( isset( $data ) && !empty( $data ) )
      {
         foreach ( $data as $k => $v )
         {
           $_POST[ $k ] = $v;
         }
       }
    }
  }

}
