<?php include_once ( str_replace( 'api/todolist', 'config' , __DIR__ ) . '/APIHandler.php' );
/**
 *	Received from robby.ai Popup: /popups/commons/Popup_confirm.php
 *  through the JS method: stripe.todo_delete();
 *
 *  $_GET['key'];
 *  $_POST['id'];
 */
API_result(
	\Stripe\Todolist_controller::delete([
		'id' => ( ( isset( $id ) && strlen( $id ) > 0 ) ? $id : NULL )
	]),
  ( ( isset( $output ) && $output == \Stripe\Model::API_OUTPUT_XML ) ? \Stripe\Model::API_OUTPUT_XML : \Stripe\Model::API_OUTPUT_JSON )
);
