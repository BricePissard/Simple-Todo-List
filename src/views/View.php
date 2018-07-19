<?php
namespace Stripe;

if (!AUTHORIZED) {
	die("Hacking Attempt: ". $_SERVER['REMOTE_ADDR']);
}

/**
 * @link http://stripe.robby.ai/
 * @link http://localhost:8080/
 */
abstract class View
{
	const PAGE = "";
	const CHARSET = "UTF-8";

	public function __construct() {}
  public function __destruct()  {}

	/**
	 * Display on screen the content of a specific page.
	 */
	public static function get( $page = NULL )
	{
		$page = ucfirst( strtolower( $page ) );
		$class_name = "\\Stripe\\" . $page . "_view";

    if ( class_exists( $class_name ) === TRUE ) {
			$view = new $class_name;
	   	$view::output();
    }
	}

	protected static function page($page_type='TODOLIST', $title, $description='', $keywords_=[])
	{
		ob_start(); // used before redirection Headers to remove pre-printed content

		echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "https://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">';

	  ?><!--[if lt IE 7] <html class="no-js lt-ie9 lt-ie8 lt-ie7">--><?php
	  ?><!--[if IE 7] <html class="no-js lt-ie9 lt-ie8">--><?php
	  ?><!--[if IE 8] <html class="no-js lt-ie9">--><?php
	  ?><!--[if gt IE 8]><html.no-js><![endif]--><?php
	  ?><html xmlns="https://www.w3.org/1999/xhtml" dir="ltr" lang="<?=DEFAULT_LANG;?>"><?php
		?><head><?php
			?><meta charset="<?=self::CHARSET;?>"><?php
			?><title><?=$title;?></title><?php
			?><meta name="description" content="<?=((isset($description))?addslashes( $description ) : '' );?>"/><?php
			?><meta name="keywords" content="<?=((isset($keywords_ ) && !empty( $keywords_))?implode( ',', $keywords_ ) : '' );?>"/><?php
			?><meta name="language" content="<?=DEFAULT_LANG;?>"/><?php
			?><meta name="country" content="US"/><?php
			?><meta name="date" content="<?=date( \DateTime::ATOM );?>"/><?php

			?><meta name="apple-mobile-web-app-capable"<?php	?> content="yes"/><?php
			?><meta name="apple-mobile-web-app-status-bar-style" content="black-translucent"/><?php
			?><meta name="viewport"<?php											?> content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no"><?php
			?><meta name="format-detection"<?php							?> content="telephone=no"><?php
			?><meta name="apple-touch-fullscreen"<?php				?> content="yes"/><?php

			?><meta http-equiv="content-type" content="text/html;charset=<?=self::CHARSET;?>"/><?php
			?><meta http-equiv="content-language" content="EN-us"/><?php
			?><meta http-equiv="expires" content="never"/><?php
			?><meta http-equiv="X-UA-Compatible" content="IE=9,IE=edge,chrome=1"/><?php

			?><meta name="rating"<?php					?> content="general"/><?php
			?><meta name="distribution"<?php		?> content="global"/><?php
			?><meta name="resource-type"<?php		?> content="document"/><?php
			?><meta name="revisit-after"<?php		?> content="1 DAY"/><?php
			?><meta name="generator"<?php				?> content="<?=ROOT_DOMAIN;?>"/><?php
			?><meta name="author"<?php					?> content="copyright <?=ROOT_DOMAIN;?>" lang="<?=DEFAULT_LANG;?>"/><?php
			?><meta name="copyright"<?php				?> content="copyright <?=ROOT_DOMAIN;?> ©<?=date( "Y" );?>"/><?php
			?><meta name="googlebot"<?php				?> content="index,follow,all"/><?php
			?><meta name="robots"<?php					?> content="index,follow,all"/><?php
			?><meta name="identifier-url"<?php	?> content="<?=PROTOCOL.SUB_DOMAIN_NAME.'.'.ROOT_DOMAIN;?>"/><?php

			?><link title="Home" href="<?= CURRENT_SITE_FOLDER;	?>" rel="start"/><?php
			?><link rel="icon" type="image/png" href="<?=\Stripe\Model::get_asset_path( "favicon.png", 'IMG' );?>" /><?php
			?><link href="https://fonts.googleapis.com/css?family=Oleo+Script" rel="stylesheet"/><?php

			echo self::get_css_dependencies( $page_type );
			?><script type='text/javascript'><?=self::get_js_global(); ?></script><?php
			echo self::get_google_analytics();

			?><base target="_parent" /><?php
		?></head><?php
	}

	/**
	 * Get the <boby> or </body> of the page with its subsidiary elements.
	 *
	 * @param {string} $type cans be 'in' or 'out'.
	 * @param {string} $page_type name of the current page displayed.
	 * @return {string}
	 */
	protected static function body($type='in', $page_type='TODOLIST')
	{
		switch ( $type )
		{
			default :
			case 'in'  :
				ob_start();
				Popup::get_container();
				$popup_container = ob_get_clean();
				return "<body data-page='" . $page_type . "'>".
				  $popup_container;
			case 'out' :
				return
					"<a class='scrollup' style='display:none;'><i class='hc-angle-up'></i></a>" .
					self::get_page_loader() .
					self::get_js_dependencies( $page_type ) .
				"</body>".
			"</html>";
		}
	}

	/**
	 * Get the page main <header>
	 *
	 * @param {string} $page_type name of the current page displayed.
	 * @return {view}
	 */
	protected static function header($page_type='TODOLIST')
	{
		?><header class="strip_bg"><?php
			?><div class="contnr"><?php
      	?><h1>Todo List</h1><?php
				if ( isset( $_SESSION['ACCOUNT']['id'] ) == TRUE )
				{
				  \Stripe\Logger::get_account_icon_menu();
        }
			?></div><?php
		?></header><?php
	}

	/**
	 *
	 */
	protected static function footer()
	{
		?><footer class="hc_footer"><?php
			?><div class="contC"><?php
				?><div class="col col5 floatleft"><?php
					?><h5>© <?=date( 'Y' );?> TodoList.</h5><?php
				?></div><?php
			?></div><?php
		?></footer><?php
	}



	// == Private Methods

	/**
	 * Set javascript "global_var" variable
	 * @return {string}
	 */
	private static function get_js_global()
	{
		return "var global_vars={".
			"CORS:'".			"false"	."'," .
			"LANG:'".			DEFAULT_LANG ."'," .
			"API:'".			API	. "'," .
			"KEY:'" .			((isset($_SERVER['REMOTE_ADDR']))?$_SERVER['REMOTE_ADDR'] : 'abcd' ). "'," .
			"IS_LOGGED:".	((isset($_SESSION['ACCOUNT']['id'] ) && intval( $_SESSION['ACCOUNT']['id'] ) > 0 ) ? 'true,' : 'false,' ) .
		"};";
	}

	/**
	 *
	 */
	private static function get_js_dependencies( $page_type = 'TODOLIST' )
	{
	  $JS = "";
		$JS .= "<script charset='".self::CHARSET."' language='javascript' type='text/javascript' src='" . \Stripe\Model::get_asset_path( "dependencies/jquery-3.3.1.min.js", 'JS' ) . "'></script>";
		$JS .= "<script charset='".self::CHARSET."' language='javascript' type='text/javascript' src='" . \Stripe\Model::get_asset_path( "dependencies/jquery-ui.min.js", 'JS' ) . "'></script>";
		$JS .= "<script charset='".self::CHARSET."' language='javascript' type='text/javascript' src='" . \Stripe\Model::get_asset_path( "dependencies/jquery.bpopup.min.js", 'JS' ) . "'></script>";
		$JS .= "<script charset='".self::CHARSET."' language='javascript' type='text/javascript' src='" . \Stripe\Model::get_asset_path( "dependencies/jquery.toggles.min.js", 'JS' ) . "'></script>";
		$JS .= "<script charset='".self::CHARSET."' language='javascript' type='text/javascript' src='" . \Stripe\Model::get_asset_path( "app.js", 'JS', FALSE, TRUE ) . "'></script>";
		$JS .= "<script charset='".self::CHARSET."' language='javascript' type='text/javascript' src='" . \Stripe\Model::get_asset_path( "web/" . strtolower( $page_type ) . ".js", 'JS', FALSE, TRUE ) . "'></script>";

		return $JS;
	}

	private static function get_css_dependencies( $page_type = 'TODOLIST' )
	{
		?><link rel="stylesheet" type='text/css' href="<?=\Stripe\Model::get_asset_path( 'dependencies/bootstrap.min.css', 'CSS', FALSE, FALSE );?>"/><?php
		?><link rel="stylesheet" type='text/css' href="<?=\Stripe\Model::get_asset_path( 'dependencies/jquery-ui.min.css', 'CSS', FALSE, FALSE );?>"/><?php
		?><link rel="stylesheet" type='text/css' href="<?=\Stripe\Model::get_asset_path( 'app.css', 'CSS', FALSE, TRUE );?>"/><?php
		?><link rel="stylesheet" type='text/css' href="<?=\Stripe\Model::get_asset_path( 'web/'.strtolower( $page_type ).'.css', 'CSS', FALSE, TRUE );?>"/><?php
	}

	private static function get_page_loader()
	{
		return
		"<div id='pageLoader' style='display:none;'>".
			\Stripe\Elements::get_loader_SVG() .
		"</div>".
		"<div id='hidder_overlay' style='display:none;'></div>";
	}

	/**
	 * Get the Google Analytics HTML tag to track the web visiters
	 * @see https://developers.google.com/analytics/devguides/collection/upgrade/reference/gajs-analyticsjs#overview
	 * @return {string}
	 */
	private static function get_google_analytics()
	{
	  $GOOGLE_TRACKING_ID = "UA-86849993-1";

		$html =
		"<script type='text/javascript'>".
		  "(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){".
		  "(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),".
		  "m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)".
		  "})(window,document,'script','//www.google-analytics.com/analytics.js','ga');".
		  "ga('create','" . $GOOGLE_TRACKING_ID . "', 'auto');".
		  "ga('require','displayfeatures');".
		  "ga('require','linkid','linkid.js');".
		  "ga('send','pageview');".
		"</script>";

		return $html;
	}

}
