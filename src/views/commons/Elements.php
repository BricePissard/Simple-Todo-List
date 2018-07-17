<?php namespace Stripe; if(!AUTHORIZED){die("Hacking Attempt: ". $_SERVER['REMOTE_ADDR']);}
class Elements extends \Stripe\View
{
	function __construct(){}

	public static function get_error_block( $message = '', $is_visible = FALSE )
	{
		?><div class="error-bloc info-popup" <?=(($is_visible==FALSE)?"style='display:none;'":'');?>><?php
			?><i class="fa hc-attention"></i><?php
			?><p><?=$message;?></p><?php
			?><div class="close nolink" href="#" onclick="stripe.close_alert(event)"><?
				?><i class="fa hc-cross">x</i><?php
			?></div><?php
		?></div><?php
	}

	public static function get_valid_block( $message = '', $is_visible = FALSE )
	{
		?><div class="valid-bloc info-popup" <?=(($is_visible==FALSE)?"style='display:none;'":'');?>><?php
			?><i class="fa fa hc-validated"></i><?php
			?><p><?=$message;?></p><?php
			?><div class="close nolink" href="#" onclick="stripe.close_alert(event)"><?php
				?><i class="fa hc-cross">x</i><?php
			?></div><?php
		?></div><?php
	}

	/**
	 * Toggle sliding element.
	 * MUST be initialized with the javascript method: stripe.set_toggle();
	 * @see https://github.com/simontabor/jquery-toggles
	 */
	public static function get_button_checkbox( Array $DATA_ = [] )
	{
		if ( empty( $DATA_ ) ) { return; }

		$button_checkbox =
		"<div class=\"toggle ".( ( $DATA_['checked'] == TRUE ) ? 'toggle-on' : 'toggle-off' ). "\" " .
			"id=\"". $DATA_['id']."-div\" ".
			"data-checked=\"".	$DATA_[ 'checked' ] . "\" ".
			"data-checkbox=\"". $DATA_[ 'id' 			] . "-checkbox\" ".
			( ( isset( $DATA_[ 'key' ] ) && strlen( $DATA_[ 'key' ] ) > 0 ) ? "data-key=\"".		 $DATA_[ 'key' ] ."\" " : '' ).
			( ( isset( $DATA_[ 'on'  ] ) && strlen( $DATA_[ 'on'  ] ) > 0 ) ? "data-ontext=\"".	 $DATA_[ 'on'  ] ."\" " : '' ).
			( ( isset( $DATA_[ 'off' ] ) && strlen( $DATA_[ 'off' ] ) > 0 ) ? "data-offtext=\"". $DATA_[ 'off' ] ."\">" : '' ).
			( ( $DATA_[ 'checked' ] == TRUE ) ? $DATA_[ 'on' ] : $DATA_[ 'off' ] ) .
		"</div>".
		"<input ";

			if ( isset( $DATA_[ 'onchecked' ] ) && isset( $DATA_[ 'onunchecked' ] ) )
			{
				$button_checkbox .= " onchange=\"if(jQuery(this).is(':checked')){".$DATA_['onchecked']." jQuery('#".$DATA_['id']."-div .toggle-blob').css({border:'3px solid rgba(135,144,193,1)'}); jQuery('#".$DATA_['id']."-div .toggle-on').css({background:'rgba(135,144,193,1)'}); }else{ ".$DATA_['onunchecked']." jQuery('#".$DATA_['id']."-div .toggle-blob').css({border:'3px solid rgba(135,144,193,.2)'}); jQuery('#".$DATA_['id']."-div .toggle-on').css({background:'rgba(135,144,193,.2)'});};\" ";
			}

			$button_checkbox .=
			" type=\"checkbox\" ".
			" class=\"".$DATA_['id']."-checkbox input-checkbox\" ".
			" name=\"".$DATA_['id']."\" ".
			( ( $DATA_['checked'] == TRUE )? "checked=\"checked\"" : '' ) .
		"/>";
		return $button_checkbox;
	}

	/**
	 * @see http://loading.io/
	 */
	public static function get_loader_SVG()
	{
		return '<svg width="88px" height="88px" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid" class="uil-dashinfinity">' .
			'<path d="M24.3,30C11.4,30,5,43.3,5,50s6.4,20,19.3,20c19.3,0,32.1-40,51.4-40C88.6,30,95,43.3,95,50s-6.4,20-19.3,20C56.4,70,43.6,30,24.3,30z" fill="none" stroke="rgb(255, 90, 95)" stroke-width="5" stroke-dasharray="8" stroke-dashoffset="0">' .
				'<animate attributeName="stroke-dashoffset" from="0" to="40" begin="0" dur="1s" repeatCount="indefinite" fill="freeze"></animate>' .
			'</path>' .
		'</svg>';
	}

}
