<?php if ( !AUTHORIZED ){ die( "Hacking Attempt: ". $_SERVER[ 'REMOTE_ADDR' ] ); }

$classmap = [
  // -- MODELS -------------------------------------------------------------------------------------------
  'iCRUDS'              => INCLUDES_MODELS . "iCRUDS.php",
  'Model'               => INCLUDES_MODELS . "Model.php",
  'Todolist_model'      => INCLUDES_MODELS . "Todolist_model.php",
  'Account_model'       => INCLUDES_MODELS . "Account_model.php",

  // -- CONTROLLERS --------------------------------------------------------------------------------------
  // ----- Abstracts -------------------------------------------------------------------------------------
  'Controller'          => INCLUDES_CONTROLLERS . "Controller.php",
  'Todolist_controller' => INCLUDES_CONTROLLERS . "Todolist_controller.php",
  'Account_controller'  => INCLUDES_CONTROLLERS . "Account_controller.php",

  // -- VIEWS --------------------------------------------------------------------------------------------
  'iView'               => INCLUDES_VIEWS . 	"iView.php",
  'View'                => INCLUDES_VIEWS . 	"View.php",
  //				-- Commons
  'Elements'            => INCLUDES_VIEWS . 	"commons/Elements.php",
  'Logger'              => INCLUDES_VIEWS . 	"commons/Logger.php",
  //				-- Popups
  'Popup'               => INCLUDES_VIEWS . 	"popups/Popup.php",
  'iPopup'              => INCLUDES_VIEWS . 	"popups/iPopup.php",
  'Popup_confirm'       => INCLUDES_VIEWS . 		"popups/commons/Popup_confirm.php",
  'Popup_login'         => INCLUDES_VIEWS . 		"popups/commons/Popup_login.php",
  'Popup_todo_add'      => INCLUDES_VIEWS . 		"popups/todolist/Popup_todo_add.php",
  //				-- Web Pages
  'Todolist_view'       => INCLUDES_VIEWS . 	"web/Todolist_view.php",
];

return $classmap;
