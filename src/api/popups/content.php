<?php include_once(str_replace('api/popups','config',__DIR__).'/APIHandler.php');
/**
 *	Receive a Popup content loaded from an API service or application
 *
 *  $_POST[ 'key'  ]; // {string} OAuth2 API KEY.
 *  $_POST[ 'name' ]; // {string} Name of the popup page to load, note thate the name have to match Popup_`$name`_view.php
 */
$class_name = ((isset($name))?'\\Stripe\\Popup_'.strtolower($name):'');
if ( method_exists( $class_name, 'output' ) ) {
	$method_name = 'output';
	$class_name::$method_name();
} else if (isset($name) && strlen($name)>0) {
	API_result([
  	'message' => sprintf('Popup %s content not found', str_replace('_',' ',$name))
  ],
  ((isset($output ) && $output == \Stripe\Model::API_OUTPUT_XML ) ? \Stripe\Model::API_OUTPUT_XML : \Stripe\Model::API_OUTPUT_JSON )
	);
}
