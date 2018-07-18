<?php
namespace Stripe;

if(!AUTHORIZED) {
	die("Hacking Attempt: ". $_SERVER['REMOTE_ADDR']);
}

final class Popup_login extends \Stripe\Elements implements \Stripe\iPopup
{
	public static function get_JS(){}
	public static function get_CSS(){}

	public static function output()
	{
		Logger::get_loggin_popup_control();
	}
}
