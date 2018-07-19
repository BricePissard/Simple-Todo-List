<?php
namespace Stripe;

if (!AUTHORIZED) {
	die("Hacking Attempt: ". $_SERVER['REMOTE_ADDR']);
}

final class Logger
{
	public function __construct(){}

	public static function get_signin_block($page = 'ACCOUNT_SIGNIN', $RESULT_=[], $redirect = '')
	{
		switch ( $page )
		{
			default:
			case 'ACCOUNT_SIGNIN':
				$position = '0';
				break;
			case 'ACCOUNT_LOGIN':
				$position = '-232px';
				break;
			case 'ACCOUNT_FORGOT':
				$position = '-464px';
				break;
		}
		?><div id="content"><?php
			self::error_handler($RESULT_);
			?><div class="half-right"><?php
				?><div class="r half-row form-section"><?php
					?><div class="forms-container" style="top:<?=$position;?>;"><?php
						self::signin_form($redirect);
						self::login_form($redirect);
						self::forgot_form($redirect);
					?></div><?php
				?></div><?php
			?></div><?php
		?></div><?php
	}

	public static function error_handler($RESULT_=[])
	{
		$display = (((isset($RESULT_['error']) && !empty($RESULT_['error'])) || (isset($RESULT_['result']) && !empty($RESULT_['result'])))? "block" : 'none' );
		?><div class="error-alert-container" style="display:<?=$display;?>;"><?php
			if (isset($RESULT_['error']) && !empty($RESULT_['error'])) {
				?><div class="error-alert margin-bottom-lg"><?php
					?><div class="error-exclaim"><i class="fa hc-attention"></i></div><?php
					?><div class="alert-content"><?php
						?><h4>Whoops!</h4><?php
						?><div class="error-message"><?=((isset($RESULT_['error']['message']))?$RESULT_['error']['message'] : '' );?></div><?php
					?></div><?php
				?></div><?php
			} else if (isset($RESULT_['result']) && !empty($RESULT_['result'])) {
				?><div class="ok-alert margin-bottom-lg"><?php
					?><div class="ok-exclaim"><i class="fa hc-validated"></i></div><?php
					?><div class="ok-content"><?php
						?><h4>That's it!</h4><?php
						?><div class="ok-message"><?=((isset( $RESULT_['result'])) ? $RESULT_['result'] : '' );?></div><?php
					?></div><?php
				?></div><?php
			} else {
				?><div class="error-alert margin-bottom-lg"><?php
					?><div class="error-exclaim"><i class="fa hc-attention"></i></div><?php
					?><div class="alert-content"><?php
						?><h4>Whoops!</h4><?php
						?><div class="error-message"></div><?php
					?></div><?php
				?></div><?php
			}
		?></div><?php
	}

	private static function signin_form($redirect='')
	{
		?><div id="signin-container"><?php
			?><form class="form-signin" autocomplete="on" accept-charset="<?=\Stripe\View::CHARSET;?>" method="<?=\Stripe\Model::POST;?>"><?php
				?><input type="hidden" name="redirect" id="redirect-signin" value="<?=( ( strlen( $redirect ) <= 0 ) ? ((isset($_POST['redirect']))?$_POST['redirect'] : '' ) : $redirect );?>"/><?php
				?><div class="r section">Already have an account? <a class="bt-login nolink" href="#">Login</a></div><?php
				?><div class="input-container full-row"><?php
					?><input type="text" autocomplete="on" name="email-signin" id="email-signin" placeholder="Email" value="<?=((isset($_POST['email-signin'] ) && !empty($_POST['email-signin']))?$_POST['email-signin'] : '' );?>"/><?php
				?></div><?php
				?><div class="input-container full-row"><?php
					?><input type="password" autocomplete="on" name="password-signin" id="password-signin" placeholder="Password" value="<?=((isset($_POST['password-signin'] ) && !empty($_POST['password-signin']))?$_POST['password-signin'] : '' );?>" /><?php
				?></div><?php
				?><div class="r section btns" style="margin-top:15px;"><?php
					?><button type="submit" class="button primary form-bt" alt="Create a new account" id="signin-btn"><?php
						?>Go!<?php
						?><i class="fa hc-rocket"></i><?php
					?></button><?php
				?></div><?php
			?></form><?php
		?></div><?php
	}

	private static function login_form($redirect='')
	{
		?><div id="login-container"><?php
			?><form class="form-login" autocomplete="on" accept-charset="<?=\Stripe\View::CHARSET;?>" method="<?=\Stripe\Model::POST;?>"><?php
				?><input type="hidden" name="redirect" id="redirect-login" value="<?=( ( strlen( $redirect ) <= 0 ) ? ((isset($_POST['redirect'] ) && !empty($_POST['redirect']))?$_POST['redirect'] : '' ) : $redirect );?>"/><?php
				?><div class="r section">Don't have an account? <a class="bt-signin nolink" href="#">Sign Up</a></div><?php
				?><div class="input-container full-row"><?php
					?><input type="text" autocomplete="on" name="email-login" id="email-login" placeholder="Email" value="<?=((isset($_REQUEST['email-login'] ) && !empty($_REQUEST['email-login']))?$_REQUEST['email-login'] : '' );?>" /><?php
				?></div><?php
				?><div class="input-container full-row"><?php
					?><input type="password" autocomplete="on" name="password-login" id="password-login" placeholder="Password" value="<?=((isset($_REQUEST['password-login']))?urldecode( $_REQUEST['password-login'] ) : '' );?>" /><?php
				?></div><?php
				?><div class="r section btns" style="margin-top:10px"><?php
					?><a class="bt-forgot nolink" alt="Forgot your password?" href="#">Forgot ?</a><?php
					?><input type="submit" class="button primary form-bt" title="Login to your existing account" id="login-btn" value="Login" /><?php
				?></div><?php
			?></form><?php
		?></div><?php
	}

	private static function forgot_form($redirect='')
	{
		?><div id="forgot-container"><?php
			?><form class="form-forgot" autocomplete="on" accept-charset="<?=\Stripe\View::CHARSET;?>" method="<?=\Stripe\Model::POST;?>"><?php
				?><input type="hidden" name="redirect" id="redirect-forgot" value="<?=( ( strlen( $redirect ) <= 0 ) ? ((isset($_POST['redirect']))?$_POST['redirect'] : '' ) : $redirect );?>"/><?php
				?><div class="r tagline">Enter your email address and we'll send instructions for setting a new password.</div><?php
				?><div class="r"><?php
					?><div class="input-container full-row"><?php
						?><input type="text" autocomplete="on" name="email-forgot" id="email-forgot" placeholder="email@example.com" value="<?=((isset($_REQUEST['email-forgot'] ) && !empty($_REQUEST['email-forgot']))?$_REQUEST['email-forgot'] : '' );?>"/><?php
					?></div><?php
				?></div><?php
				?><div class="r btns"><?php
					?><a class="bt-login nolink" alt="Login to your existing account" href="#">Login ?</a><?php
					?><input type="submit" class="button primary form-bt" alt="Reset your password" id="forgot-btn" value="Send"/><?php
				?></div><?php
			?></form><?php
		?></div><?php
	}

	public static function get_loggin_popup_control()
	{
		if ( isset($_SESSION['ACCOUNT']['id'])==FALSE) {
			?><section id="signup-popdown" class="internal"><?php
				\Stripe\Logger::get_signin_block( 'ACCOUNT_SIGNIN', NULL, ((isset($_SERVER['HTTP_REFERER']))?$_SERVER['HTTP_REFERER'] : NULL ) );
			?></section><?php
			?><script>if (jQuery) {jQuery( document ).ready( stripe.set_popup_signup_actions );}</script><?php
			die;
		}
	}

	public static function get_account_icon_menu()
	{
		?><div class="toggle-menu" id="admin-menu-selector"><?php
			?><a href="" id="admin-menu-toggle" class="bttn"><?php
				?><img src="<?=\Stripe\Account_model::get_image_url(((isset($_SESSION['ACCOUNT']['email'] ) && !empty($_SESSION['ACCOUNT']['email']))?$_SESSION['ACCOUNT']['email'] : '' ) );?>" class="avatar" alt="Me"/><?php
			?></a><?php
	  	?><nav class="menu-options" id="admin-menu-options"><?php
				?><ul><?php
					?><li><a class="nolink" href="#" onClick="stripe.ajax_logout(true);"><b>Logout</b></a></li><?php
				?></ul><?php
	    ?></nav><?php
	  ?></div><?php
	}

}
