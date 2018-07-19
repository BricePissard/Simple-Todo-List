<?php include_once(str_replace('api/todolist','config',__DIR__).'/APIHandler.php');
/**
 * Received from Popup: /popups/todolist/Popup_todo_add.php
 * through the JS method: stripe.todo_add();
 * @see https://stripe.robby.ai/api/todolist/add.json?key=1234&name=hello
 *
 * $_POST['key' ]; // {string} API OAuth 2 KEY.
 * $_POST['name']; // {string} Todo name to add.
 */
API_result(
	\Stripe\Todolist_controller::add([
		'name'=>((isset($name) && strlen($name )>0)?$name:NULL)
	]),
  ((isset($output) && $output == \Stripe\Model::API_OUTPUT_XML )? \Stripe\Model::API_OUTPUT_XML:\Stripe\Model::API_OUTPUT_JSON)
);
