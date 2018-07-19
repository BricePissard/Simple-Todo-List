<?php include_once(str_replace('api/account','config', __DIR__).'/APIHandler.php');
/**
 * Received from Logger page: /account/login
 * @link http://stripe.robby.ai/api/account/login.json?key=1234&email=test@test.com&password=xxxxx
 *
 * $_POST['key'     ]; // {string} API OAuth 2 KEY
 * $_POST['email'   ]; // {string} email address.
 * $_POST['password']; // {string} password.
 */
API_result(
	\Stripe\Account_controller::get_login(
		((isset($redirect))?$redirect:NULL),
		((isset($email))?$email:NULL),
		((isset($password))?$password:NULL)
	),
  ((isset($output) && $output == \Stripe\Model::API_OUTPUT_XML ) ? \Stripe\Model::API_OUTPUT_XML : \Stripe\Model::API_OUTPUT_JSON)
);
