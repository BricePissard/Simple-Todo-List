<?php namespace Stripe; if(!AUTHORIZED){die("Hacking Attempt: ". $_SERVER['REMOTE_ADDR']);}
final class Popup_todo_add extends \Stripe\Elements implements \Stripe\iPopup
{
	const NAME = 'popup_todo_add';

	public static function get_JS()  {}
	public static function get_CSS()
	{
		?><style type="text/css"><?php
			?>#popup-todo-add-name {width:100%;border:1px solid #eaeaea;border-radius:30px;background:#F8FAFC;color:#444;padding:20px;font-size:15px;font-weight:600;}<?php
		?></style><?php
	}

	public static function output()
	{
		self::get_CSS();
		?><section id="<?=self::NAME;?>"><?php

			?><header><?php
				?><h2>Add a todo</h2><?php
			?></header><?php

			?><section class="int_cont"><?php
				?><input id="popup-todo-add-name" placeholder="Todo Name..."/><?php
			?></section><?php

			?><footer><?php
				?><input type="submit" class="btn confirm todo-add-save" value="Save"/><?php
				?><input type="button" class="btn cancel edit_close" value="Cancel"/><?php
			?></footer><?php

		?></section><?php
		self::get_JS();
	}
}
