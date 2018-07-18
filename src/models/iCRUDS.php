<?php
namespace Stripe;

if (!AUTHORIZED) {
  die("Hacking Attempt: ". $_SERVER['REMOTE_ADDR']);
}

/**
 * Common interface for all extended Models.
 */
interface iCRUDS
{
  public static function create( $_data );
  public static function read( $_data );
  public static function delete( $_data );
  public static function search( $_data );
}
