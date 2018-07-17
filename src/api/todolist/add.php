<?php include_once ( str_replace( 'api/todolist', 'config' , __DIR__ ) . '/APIHandler.php' );
/**
 *	Received from robby.ai Popup: /popups/todolist/Popup_todo_add.php
 *  through the JS method: stripe.todo_add();
 *
 *  $_GET['key'];
 *  $_POST['name'];
 */
API_result(
	\Stripe\Todolist_controller::add([
		'name' => ( ( isset( $name ) && strlen( $name ) > 0 ) ? $name : NULL )
	]),
  ( ( isset( $output ) && $output == \Stripe\Model::API_OUTPUT_XML ) ? \Stripe\Model::API_OUTPUT_XML : \Stripe\Model::API_OUTPUT_JSON )
);
