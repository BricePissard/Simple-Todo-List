/**!
 * @author Brice Pissard
 * @copyright Copyright @BricePissard
 * @name stripe.todolist
 * @requires {object} global_vars
 * @requires {class}  jQuery
 **/
jQuery(function($) {
stripe.todolist =
{
	/**
	 * The Web-App is initialized here after the page have loaded.
	 *
	 * @access public
	 * @param {object} e onLoad Event.
	 * @return {void}
	 */
	init: function(e)
	{
		if ($('body').data('page')=='TODOLIST' && global_vars.IS_LOGGED==true) {
			stripe.todolist.set_todo_sortable();
			stripe.todolist.set_todo_search_filter();
			stripe.todolist.set_todo_toggles();
			stripe.todolist.set_todo_add();
			stripe.todolist.set_todo_editable();
			stripe.todolist.set_todo_delete();
		}
	},

	/**
	 * Set the sorting row action to save the positions of the todolist after
	 * they have been reordered.
	 *
	 * @access private
	 * @return {void}
	 */
	set_todo_sortable: function()
	{
		$("#sortable").sortable({
			stop: function(e,ui) {
				var p=new Array();
				$("#sortable").sortable("toArray").forEach(function(el,i) {
					var id=parseInt(el.replace(/row\-/i,'')),pos=i+1;
					p.push({'id':id,'position':pos});
				});
				if (p&&!stripe.isNull(p)) {
					stripe.todolist.save_todo_position(p);
				}
			}
		});
    $( "#sortable" ).disableSelection();
	},

	/**
	 * Set the sorting row action to save the positions of the todolist after
	 * they have been reordered.
	 *
	 * @access public
	 * @return {void}
	 */
	set_todo_toggles : function()
	{
		$('.toggle').on('toggle',stripe.todolist.on_todo_toggle);
	},

	/**
	 * Action triggered after having clicked on class '.toggle' slide-checking button.
	 *
	 * @access public
	 * @param {object} e toggle Event
	 * @param {boolean} active Is the current slide-checking button active.
	 * @return {void}
	 */
	on_todo_toggle: function(e,active)
	{
		var
		id=$(e.target).data('key'),
		row=$('#row-'+id),
		status=((active)?'DONE':'TODO');
		if (active){
			row.addClass('done');
		} else {
			row.removeClass('done');
		}
		stripe.get_api_call(
			stripe.endpoint.todolist_status, {
				key: global_vars.KEY,
				language: global_vars.LANG,
				id: id,
				status: status
			},
			stripe.method.POST,
			stripe.todolist.on_success_handler,
			stripe.todolist.on_fail_handler
		);
	},

	/**
	 * Set the button (+) Action to open the todo_add popup after clicking on it.
	 *
	 * @access private
	 * @return {void}
	 */
	set_todo_add: function()
	{
		$('#add-todo-button').on('click',function(e){
			stripe.todolist.todo_add();
		});
	},

	/**
	 * Open the `todo_add` popup, refers to the server-side popup Popup_todo_add_view.php
	 * Set the actions to the oppened popup to handle the saving process.
	 *
	 * @access public
	 * @return {void}
	 */
	todo_add: function()
	{
		stripe.popup('todo_add', function() {
			$('#popup-todo-add-name').focus();
			$('.todo-add-save').on( 'click', stripe.todolist.todo_create_new_todo );
			$('#popup-todo-add-name').keyup( function( e ) {
				if ( e.keyCode == 13 ) {
					stripe.todolist.todo_create_new_todo( e );
				}
			});
		});
	},

	/**
	 * Save a new todo to the server through the API WS endpoint.
	 *
	 * @access private
	 * @param {object} e onClick Event.
	 * @return {void}
	 */
	todo_create_new_todo : function(e)
	{
		stripe.get_api_call(
			stripe.endpoint.todolist_add, {
				key: global_vars.KEY,
				language: global_vars.LANG,
				name: $('#popup-todo-add-name').val()
			},
			stripe.method.POST,
			function( data ) {
				stripe.todolist.on_success_handler( data );
				setTimeout( function() {
					stripe.redirect('/');
				}, 300 );
			},
			stripe.todolist.on_fail_handler
		);
	},

	/**
	 * Save the new Todolist ordering positions to the server through the API WS endoint.
	 *
	 * @access private
	 * @param {array<object>} positions Array of object pairing the todo ID with its position,
	 * Ex: [{id:123},position:1},{id:345,position:2},{},..]
	 * @return {void}
	 */
	save_todo_position : function(positions)
	{
		stripe.get_api_call(
			stripe.endpoint.todolist_positions, {
				key: global_vars.KEY,
				language: global_vars.LANG,
				positions: positions
			},
			stripe.method.POST,
			stripe.todolist.on_success_handler,
			stripe.todolist.on_fail_handler
		);
	},

	/**
	 * Set the dragable Totolist row as editable to allow onClick to edit its content.
	 * After clicking outside or pressing Enter, the data is saved through the API WS.
	 *
	 * @access private
	 * @return {void}
	 */
	set_todo_editable: function()
	{
		$('#sortable a.editable').on('click',function() {
			var that=$(this);
			if (that.find('input').length>0) {
				return;
			}
			var currentText=that.text();
			var id=that.data('id');
			var $input=$('<input id="field-'+id+'" data-id="'+id+'">').val(currentText);
			$(this).append($input);
			$('#field-'+id).focus();
			$('#field-'+id).keyup(function(e) {
				if (e.keyCode==13) {
					stripe.todolist.save_todo_edited( $(document),that,$input);
				}
			});
			$( document ).on( 'click', function(e) {
				stripe.todolist.save_todo_edited(e.target,that,$input);
			});
		});
	},

	/**
	 * Save a new Todo name through the API WS endpoint.
	 *
	 * @access private
	 * @param {string} target ID name of the element edited
	 * @param {object} that Div element where to display the new Todo name.
	 * @param {object} $input Input field element that contains the new Todo name.
	 * @return {void}
	 */
	save_todo_edited: function(target, that, $input)
	{
		if (!$(target).closest('.editable').length) {
			if ($input.val()) {
				that.text( $input.val());
				stripe.get_api_call(
					stripe.endpoint.todolist_edit, {
						key: global_vars.KEY,
						language: global_vars.LANG,
						id: $input.data('id'),
						name: $input.val()
					},
					stripe.method.POST,
					stripe.todolist.on_success_handler,
					stripe.todolist.on_fail_handler
				);
			}
			that.find( 'input' ).remove();
		}
	},

	/**
	 * Set the action to handle the delete by pressing to the (x) button.
	 *
	 * @access private
	 * @return {void}
	 */
	set_todo_delete: function()
	{
		$('#sortable a.delete').on( 'click', function( e ) {
			var id = $(this).data( 'id' );
			stripe.popup( 'confirm', function() {
				$( '#popup-confirm-message' ).html( 'Are you sure you want to delete this Todo ?' );
				$( '#popup_confirm .confirm' ).on( 'click', function( e ) {
					stripe.todolist.todo_delete( id );
				});
			});
		});
	},

	/**
	 * Save a specific Todo as deleted through the API WS endpoint.
	 *
	 * @access private
	 * @param {int} id ID of the Todolist, refers to the database tabel `todolist`.`id`
	 * @return {void}
	 */
	todo_delete: function(id)
	{
		stripe.get_api_call(
		  stripe.endpoint.todolist_delete, {
				key: global_vars.KEY,
				language: global_vars.LANG,
				id: id
			},
			stripe.method.POST,
			function(data) {
				stripe.todolist.on_success_handler(data);
				$('#row-'+id).slideUp();
			},
			stripe.todolist.on_fail_handler
		);
	},

	/**
	 * Set the searching field Action to allow to search for a Todo by its name.
	 * The filter hide the results from the filst that doesnt match.
	 *
	 * @access private
	 * @return {void}
	 */
	set_todo_search_filter: function()
	{
		$('.filterinput').on('keyup',function() {
	    var a=$(this).val(),containing;
	    if (a.length>1) {
	     	$('.ui-accordion-content').hide();
	      containing=$('.ui-sortable-handle').filter(function() {
	        return new RegExp('\\b'+a,'i').test($( '#'+$(this).attr('id')+' .editable').html());
	      }).show();
	      $('.ui-sortable-handle').not(containing).hide();
      } else {
        $('.ui-sortable-handle').show();
			}
      return false;
    });
	},

	/**
	 * Control the content successfuly returned by the API WS.
	 * If the results contains an error message, display the top error message in
	 * the popup, otherwise close the popup.
	 *
	 * @access private
	 * @param {object} data Data received from the API WS as a result.
	 * @return {void}
	 */
	on_success_handler: function(data)
	{
		var r=data.result||null;
		if (r) {
			if (stripe.isNull(r.error) == false ) {
				stripe.internal_popup_error(r.error.message);
			} else {
				if (stripe.internal_popup!=null) {
					stripe.internal_popup.close();
				}
			}
		}
	},

	/**
	 * Method executed if the API WS failed to load or returned a != 200 page header.
	 * In case of Error: relaod the page.
	 *
	 * @access private
	 * @param {object} e Object return by the API WS.
	 * @return {void}
	 */
	on_fail_handler: function(e)
	{
		stripe.redirect( '/' );
	},

}; $(document).ready(stripe.todolist.init);});
