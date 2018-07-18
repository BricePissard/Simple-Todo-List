<?php
namespace Stripe;

if (!AUTHORIZED) {
	die("Hacking Attempt: ". $_SERVER['REMOTE_ADDR']);
}

final class Popup_confirm extends \Stripe\Elements implements \Stripe\iPopup
{
	const NAME = 'popup_confirm';

	public static function get_JS()  {}
	public static function get_CSS() {}

	public static function output()
	{
		self::get_CSS();
		?><section id="<?=self::NAME;?>"><?php
			?><header><?php
				?><h2>Confirmation</h2><?php
			?></header><?php
			?><section class="int_cont"><?php
				?><div id="popup-confirm-message"></div><?php
			?></section><?php
			?><footer><?php
				?><input type="submit" class="btn confirm" value="Yes"/><?php
				?><input type="button" class="btn cancel edit_close" value="No"/><?php
			?></footer><?php
		?></section><?php
		self::get_JS();
	}
}
