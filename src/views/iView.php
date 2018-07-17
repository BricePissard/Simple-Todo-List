<?php namespace Stripe; if ( !AUTHORIZED ){ die( "Hacking Attempt: ". $_SERVER[ 'REMOTE_ADDR' ] ); }
interface iView
{
  public static function output();
}
