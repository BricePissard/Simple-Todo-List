<?php
namespace Stripe;

if (!AUTHORIZED) {
	die("Hacking Attempt: ". $_SERVER['REMOTE_ADDR']);
}

final class Popup extends \Stripe\Elements
{
	public static function get_container()
	{
		?><div class="pp_int" id="popup_container" style="display:none;"><?php
			?><div class="modal-display-area"><div class="edit_close"><i class="hc-cross">x</i></div><?php
				?><div class="int_cont"><?php
					self::get_error_block('');
					self::get_valid_block('');
					?><div class="popup-container-element"></div><?php
				?></div><?php
			?></div><?php
		?></div><?php
		?><div class="pp_int" id="popup_container2" style="display:none;"><?php
			?><div class="modal-display-area"><div class="edit_close"><i class="hc-cross">x</i></div><?php
				?><div class="int_cont"><?php
					self::get_error_block('');
					self::get_valid_block('');
					?><div class="popup-container-element2"></div><?php
				?></div><?php
			?></div><?php
		?></div><?php
		?><div class="pp_int" id="popup_container3" style="display:none;"><?php
			?><div class="modal-display-area"><div class="edit_close"><i class="hc-cross">x</i></div><?php
				?><div class="int_cont"><?php
					self::get_error_block('');
					self::get_valid_block('');
					?><div class="popup-container-element3"></div><?php
				?></div><?php
			?></div><?php
		?></div><?php
	}
}
