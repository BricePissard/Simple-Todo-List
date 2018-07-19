<?php include_once(str_replace('api/account','config',__DIR__).'/APIHandler.php');
/**
 * Received from Logger page: /acount/logout
 * @link http://stripe.robby.ai/api/account/signin.json?key=1234&redirect=http://test.com
 *
 * $_POST['key'     ]; // {string} OAuth2 API KEY.
 * $_POST['redirect']; // {string} Url where to redirect after logout.
 */
API_result(
  \Stripe\Account_controller::get_logout(
  (isset($redirect ) && strtoupper($redirect) == 'FALSE')?FALSE:TRUE),
	((isset($output) && $output == \Stripe\Model::API_OUTPUT_XML)? \Stripe\Model::API_OUTPUT_XML : \Stripe\Model::API_OUTPUT_JSON)
);
