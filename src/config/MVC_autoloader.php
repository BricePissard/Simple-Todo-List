<?php if ( !AUTHORIZED ){ die( "Hacking Attempt: ". $_SERVER[ 'REMOTE_ADDR' ] ); }

// -- Load local libraries
require_once( INCLUDES_LIBS . 'PHPMailer.php' );
require_once( INCLUDES_LIBS . 'Strings.php'	);
require_once( INCLUDES_LIBS . 'Crypter.php'	);

final class MVC_autoloader
{
  private $classmap = NULL;

  private function get_classmap()
  {
    if ( !isset( $this->classmap ) ) {
      $classmap = ( include INCLUDES_CONFIG .'MVC_classmap.php' );

      if (
        isset(  $classmap ) &&
        !empty( $classmap )
      ) {
        $this->classmap = $classmap;
      }
    }
    return $this->classmap;
  }

  public function findFile( $className = '' )
  {
    $classmap = $this->get_classmap();

    return ( (
      isset(  $classmap ) &&
      !empty( $classmap )
    ) ?
      $classmap[ $className ]
      :
      ''
    );
  }

  public function require_all()
  {
    $classes_ = $this->get_classmap();

    if (
      isset(  $classes_ ) &&
      !empty( $classes_ )
    ) {
      foreach ( $classes_ as $classPath )
      {
        require_once( $classPath );
      }
    }
  }
}

$autoloader = new MVC_autoloader();
return $autoloader;
