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
  public static function create( Array $_data = [] );
  public static function read( Array $_data = [] );
  public static function delete( Array $_data = [] );
  public static function search( Array $_data = [] );
}
