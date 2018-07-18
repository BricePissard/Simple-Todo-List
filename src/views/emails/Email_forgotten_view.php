<?php
namespace Stripe;

if(!AUTHORIZED) {
	die("Hacking Attempt: ". $_SERVER['REMOTE_ADDR']);
}

final class Email_forgotten_view extends \Stripe\Email implements \Stripe\iView
{
	public static $password = NULL;

	public static function output()
	{
		ob_start();

		self::in( self::$email );
		self::title( "You have forgotten your TodoList App password" );
		self::main('in');
			self::comment( "Your passord is: <b style='background:#90EE90;border-radius:5px;color:#008000;padding:6px 11px;'>". \Stripe\Account_model::get_password( self::$password, 'decrypt' ) ."</b>" );
		self::main('out');
		self::out();

		$html = ob_get_contents();
		$r = ob_end_clean();

		return $html;
	}
}
