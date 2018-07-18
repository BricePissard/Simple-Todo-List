<?php include_once ( str_replace( 'api/todolist', 'config' , __DIR__ ) . '/APIHandler.php' );
/**
 * Received from Popup: /popups/commons/Popup_confirm.php
 * Through the JS method: stripe.todo_delete();
 * @link https://stripe.robby.ai/api/todolist/delete.json?key=1234&id=1234
 *
 *  $_POST[ 'key' ]; // {string} OAuth 2 API KEY.
 *  $_POST[ 'id'  ]; // {int} todolist id, refers to DB table field: `todolist`.`id`
 */
API_result(
	\Stripe\Todolist_controller::delete([
		'id' => ( ( isset( $id ) && strlen( $id ) > 0 ) ? $id : NULL )
	]),
  ( ( isset( $output ) && $output == \Stripe\Model::API_OUTPUT_XML ) ? \Stripe\Model::API_OUTPUT_XML : \Stripe\Model::API_OUTPUT_JSON )
);
