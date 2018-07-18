<?php
namespace Stripe;

if ( !AUTHORIZED ) {
  die( "Hacking Attempt: ". $_SERVER[ 'REMOTE_ADDR' ] );
}

/**
 * Common interface for all extended View.
 */
interface iView
{
  public static function output();
}
