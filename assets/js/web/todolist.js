/**!
 * @author Brice Pissard
 * @copyright Copyright Brice Pissard
 * @name stripe.todolist
 * @require {object} 	global_vars
 * @require {class}  	jQuery
 **/
jQuery( function( $ ) {
stripe.todolist =
{
	init : function( e )
	{
		if ( $( 'body' ).data( 'page' ) == 'TODOLIST' && global_vars.IS_LOGGED == true ) {
			stripe.todolist.set_todo_sortable();
			stripe.todolist.set_todo_search_filter();
			stripe.todolist.set_todo_toggles();
			stripe.todolist.set_todo_add();
			stripe.todolist.set_todo_editable();
			stripe.todolist.set_todo_delete();
		}
	},

	set_todo_sortable : function()
	{
		$( "#sortable" ).sortable({
			stop: function(e,ui) {
				var new_positions = new Array();
				$( "#sortable" ).sortable( "toArray" ).forEach( function(el,i) {
					var id = parseInt( el.replace(/row\-/i,'') ), pos = i+1;
					new_positions.push({ 'id': id, 'position': pos });
				});
				if ( new_positions && stripe.isNull( new_positions ) == false ) {
					stripe.todolist.save_todo_position( new_positions );
				}
			}
		});
    $( "#sortable" ).disableSelection();
	},

	set_todo_toggles : function()
	{
		$( '.toggle' ).on( 'toggle', stripe.todolist.on_todo_toggle );
	},

	on_todo_toggle : function(e, active)
	{
		var
		id = $( e.target).data( 'key' ),
		row = $( '#row-' + id ),
		status = ( ( active ) ? 'DONE' : 'TODO' );

		if ( active )
		{
			row.addClass( 'done' );
		}
		else
		{
			row.removeClass( 'done' );
		}

		stripe.get_api_call(
			stripe.endpoint.todolist_status,
			{
				key			 : global_vars.KEY,
				language : global_vars.LANG,
				id 			 : id,
				status	 : status
			},
			stripe.method.POST,
			stripe.todolist.on_success_handler,
			stripe.todolist.on_fail_handler
		);
	},

	set_todo_add : function()
	{
		$( '#add-todo-button' ).on( 'click', function( e )
		{
			stripe.todolist.todo_add();
		});
	},

	todo_add : function()
	{
		stripe.popup( 'todo_add', function() {
			$('#popup-todo-add-name').focus();
			$('.todo-add-save').on( 'click', stripe.todolist.todo_create_new_todo );
			$('#popup-todo-add-name').keyup( function( e ) {
				if ( e.keyCode == 13 ) {
					stripe.todolist.todo_create_new_todo( e );
				}
			});
		});
	},

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
					stripe.redirect( '/' );
				}, 300 );
			},
			stripe.todolist.on_fail_handler
		);
	},

	save_todo_position : function(new_positions)
	{
		stripe.get_api_call(
			stripe.endpoint.todolist_positions, {
				key: global_vars.KEY,
				language: global_vars.LANG,
				positions: new_positions
			},
			stripe.method.POST,
			stripe.todolist.on_success_handler,
			stripe.todolist.on_fail_handler
		);
	},

	set_todo_editable: function()
	{
		$( '#sortable a.editable' ).on( 'click', function() {
			var that = $(this);
			if ( that.find('input').length > 0 ) { return; }
			var currentText = that.text();
			var id = that.data( 'id' );
			var $input = $('<input id="field-' + id + '" data-id="' + id + '">').val( currentText );
			$(this).append( $input );
			$( '#field-' + id ).focus();
			$( '#field-' + id ).keyup( function(e) {
				if ( e.keyCode == 13 ) {
					stripe.todolist.save_todo_edited( $(document), that, $input );
				}
			});
			$( document ).on( 'click', function(e) {
				stripe.todolist.save_todo_edited(e.target, that, $input);
			});
		});
	},

	save_todo_edited: function(target, that, $input)
	{
		if ( !$(target).closest('.editable').length ) {
			if ( $input.val() ) {
				that.text( $input.val() );
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

	set_todo_delete: function()
	{
		$( '#sortable a.delete' ).on( 'click', function( e ) {
			var id = $(this).data( 'id' );
			stripe.popup( 'confirm', function() {
				$( '#popup-confirm-message' ).html( 'Are you sure you want to delete this Todo ?' );
				$( '#popup_confirm .confirm' ).on( 'click', function( e ) {
					stripe.todolist.todo_delete( id );
				});
			});
		});
	},

	todo_delete: function(id)
	{
		stripe.get_api_call(
		  stripe.endpoint.todolist_delete, {
				key: global_vars.KEY,
				language: global_vars.LANG,
				id: id
			},
			stripe.method.POST,
			function( data ) {
				stripe.todolist.on_success_handler( data );
				$( '#row-' + id ).slideUp();
			},
			stripe.todolist.on_fail_handler
		);
	},

	set_todo_search_filter: function()
	{
		$( '.filterinput' ).on( 'keyup', function() {
	    var a = $(this).val(), containing;
	    if ( a.length > 1 ) {
	     	$( '.ui-accordion-content' ).hide();
	      containing = $( '.ui-sortable-handle' ).filter( function() {
	        return new RegExp( '\\b' + a, 'i' )
					.test( $( '#' + $( this )
					.attr( 'id' ) + ' .editable' ).html() );
	      }).show();
	      $( '.ui-sortable-handle' ).not( containing ).hide();
      } else {
        $('.ui-sortable-handle').show();
			}
      return false;
    });
	},

	on_success_handler: function(data)
	{
		var r = data.result;
		if (r) {
			if ( stripe.isNull( r.error ) == false ) {
				stripe.internal_popup_error( r.error.message );
			} else {
				if ( stripe.internal_popup != null ) {
					stripe.internal_popup.close();
				}
			}
		}
	},

	on_fail_handler: function(e)
	{
		//stripe.redirect( '/' );
	},

}; $(document).ready(stripe.todolist.init);});
