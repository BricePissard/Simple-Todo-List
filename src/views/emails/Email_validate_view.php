<?php
namespace Stripe;

if(!AUTHORIZED) {
	die("Hacking Attempt: ". $_SERVER['REMOTE_ADDR']);
}

final class Email_validate_view extends \Stripe\Email implements \Stripe\iView
{
	public static function output()	{
		ob_start();

		self::in( self::$email );
		self::title( "Welcome to the Todo List App" );
		self::main('in');
			self::comment( "Your account have been successfuly created." );
		self::main('out');
		self::out();

		$html = ob_get_contents();
		$r = ob_end_clean();

		return $html;
	}
}
