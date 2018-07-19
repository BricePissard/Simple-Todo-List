<?php include_once(str_replace('api/todolist','config',__DIR__).'/APIHandler.php');
/**
 * Received from Popup: /popups/commons/Popup_confirm.php
 * Through the JS method: stripe.todolist.save_todo_edited();
 * @link http://stripe.robby.ai/api/todolist/edit.json?key=1234&id=1234&name=new+name
 *
 *  $_POST['key' ]; // {string} OAuth 2 API Key.
 *  $_POST['id'  ]; // {int} Todo id, refers to the DB table field `todolist`.`id`
 *  $_POST['name']; // {string} new name of this todo.
 */
API_result(
  \Stripe\Todolist_controller::edit([
    'id'=>((isset($id) && strlen($id)>0)?$id:NULL),
    'name'=>((isset($name ) && strlen($name)>0)?$name:NULL),
  ]),
  ((isset($output) && $output == \Stripe\Model::API_OUTPUT_XML)? \Stripe\Model::API_OUTPUT_XML : \Stripe\Model::API_OUTPUT_JSON)
);
