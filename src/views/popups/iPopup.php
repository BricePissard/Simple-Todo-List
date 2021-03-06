<?php
namespace Stripe;

if ( !AUTHORIZED ) {
  die( "Hacking Attempt: ". $_SERVER['REMOTE_ADDR'] );
}

/**
 * Common interface for all extended Popup.
 */
interface iPopup
{
  public static function output();
	public static function get_CSS();
	public static function get_JS();
}
