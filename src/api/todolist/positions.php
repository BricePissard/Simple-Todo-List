<?php include_once(str_replace('api/todolist','config',__DIR__).'/APIHandler.php');
/**
 * Received from the JS method: stripe.todolist.save_todo_position();
 * @link http://stripe.robby.ai/api/todolist/positions.json?key=1234&id=1234&position=[{id:123,position:1},{id:124,position:2},{id:125,position:3}]
 *
 *  $_POST['key'     ]; // {string} OAuth 2 API Key.
 *  $_POST['position']; // {array} new positions of all the ACTIVE todos for this account, ex: [['id'=>123,'position'=>1],['id'=>124,'position'=>2],['id'=>125,'position'=>3]]
 */
API_result(
  \Stripe\Todolist_controller::positions([
    'positions'=>((isset($positions) && !empty($positions))?$positions:[]),
  ]),
  ((isset($output) && $output == \Stripe\Model::API_OUTPUT_XML)? \Stripe\Model::API_OUTPUT_XML : \Stripe\Model::API_OUTPUT_JSON)
);
