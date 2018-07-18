/**!
 * @author Brice Pissard
 * @copyright Copyright Brice Pissard
 * @name stripe
 * @require {object} 	global_vars
 * @require {class}  	jQuery
 **/
"use strict";
var stripe = {};
var global_vars = global_vars || null;
var jQuery = jQuery || null;
jQuery && jQuery( function( $ ) {
	stripe = {
		loaded : false,
		method : {
			GET 		: 'GET',
			POST 		: 'POST',
			DELETE 	: 'DELETE'
		},
		call_type : {
			AJAX   	: 'ajax'
		},
		call_format : {
			JSON 	: 'json',
			JSONP : 'jsonp',
			XML		: 'xml'
		},
		action_get_webservice: 'get_webservice',
		endpoint : {
			account_login   	 : 'account/login.json',
			account_signin  	 : 'account/signin.json',
			account_logout  	 : 'account/logout.json',
			account_forgot  	 : 'account/forgot.json',
			todolist_add 	  	 : 'todolist/add.json',
			todolist_delete 	 : 'todolist/delete.json',
			todolist_edit 		 : 'todolist/edit.json',
			todolist_status 	 : 'todolist/status.json',
			todolist_positions : 'todolist/positions.json',
		},
		internal_popup: null, // internal popup object.
		min_width: 640, // responsive min width
		ERROR: 'An error occured, please try later.',

		// -- assets constants
		IMAGES: 'images',
		CSS: 'css',
		JS: 'js',

		init: function(e)
		{
			$('html,body').addClass('has-js');
			jQuery.support.cors = true;
			jQuery.noConflict();
			stripe.set_cache();
			stripe.set_toggles();
			stripe.set_internal_popup_info();
			if ( global_vars.IS_LOGGED == true ) {
				stripe.set_admin_menu();
			} else {
				stripe.set_popup_actions();
			}
			$('a.nolink').on('click', stripe.nolink_CLICK);
			$('.noprop').on('click', stripe.noprop_CLICK);
		},

		set_cache: function(event)
		{
			$.ajaxSetup({ cache: true });
			if ( typeof window.applicationCache != 'undefined') {
				window.applicationCache.addEventListener('updateready', stripe.swap_cache, false);
			}
		},

		set_toggles: function()
		{
			$('.toggle').each( function( index ) {
				var on 	 = $(this).data( 'ontext' ),
				off 		 = $(this).data( 'offtext' ),
				checked	 = $(this).data( 'checked' ),
				checkbox = $(this).data( 'checkbox' );

			  $( this ).toggles({
				  text: {
				    on	: on,
				    off	: off
				  },
					on			: checked,
					checkbox: checkbox,
					type		: 'compact',
					easing	: 'swing',
					clicker	: null,
					drag		: true,
				  click		: true,
				  animate	: 250,
				  width		: 90,
				  height	: 34
				});
			});
		},

		nolink_CLICK: function(e)
		{
			if (e) {
				e.preventDefault();
			}
		},

		noprop_CLICK: function(e)
		{
			if (e) {
				e.preventDefault();
				e.stopPropagation();
			}
		},

		get_api_call: function(endpoint, params, method, onSuccess, onFail, call_type)
		{
			return jQuery.ajax({
				url: global_vars.API + ( ( parseInt(global_vars.CORS, 10 ) > 0 || global_vars.CORS === 'true' ) ? '' : endpoint ),
				cache: false,
				crossDomain: true,
				jsonp: true,
				async: true,
				type: ( ( stripe.isNull( method ) ) ? stripe.method.GET : stripe.method.POST ),
				dataType: stripe.call_format.JSON,
				data: params
			}).done( onSuccess ).fail( onFail );
		},


		/* -- POPUP -- */

		popup: function( name, onLoad, onClose, POST, hasLoader )
		{
			POST = ( ( typeof POST == 'undefined' ) ? {} : POST );
			var popup_num = ( ( $('#popup_container2' ).is( ':visible' ) ) ? '3' : ( ( $('#popup_container' ).is( ':visible' ) ) ? '2' : '' ) ),
			    popup = '#popup_container' + popup_num,
			    container = '.popup-container-element' + popup_num,
			    endpoint = 'popups/content.json',
			    url = global_vars.API + ( ( parseInt( global_vars.CORS, 10 ) > 0 ) ? '' : endpoint );
			POST.action = 'get_popup_content';
			POST.key = POST.key || '';
			POST.name = name;
			stripe.internal_popup = $( popup ).bPopup({
				follow 				    : [true, true],
				modalClose 			  : true,
				escClose 			    : true,
				scrollBar 			  : false,
				opacity 			    : 1,
				positionStyle 	  : 'fixed',
				position 			    : ['auto', 'auto'], // [horizontal, vertical]
				modalColor 			  : 'black',
				easing 				    : 'linear',
				closeClass 			  : 'edit_close',
				transition 			  : 'fadeIn',
				transitionClose   : 'fadeOut',
				fadeSpeed 		  	: 150,
				followSpeed 	  	: 100,
				speed 				    : 150,
				zIndex 				    : 1000000,
				content 			    : stripe.call_type.AJAX,
				contentContainer 	: container,
				loadUrl 			    : url,
				loadData 			    : POST,
				loadCallback 		  : function()
				{
					$( container + ' .popupLoader').remove();
					$( popup + ' .int_cont').css({ opacity:0 });
					setTimeout( function() {
						if ( onLoad && ( typeof onLoad == 'function' || typeof onLoad == 'object' ) ) {
							onLoad();
						}
						stripe.resize();
						$( popup + ' .int_cont').css({ opacity: 1 });
					}, 1000);
				},
				onOpen: function() {
					$( popup ).removeClass( 'pp_close' );
					$( popup + ' .int_cont').css({
						'height': ( ( $(window).width() >= stripe.min_width ) ? 'auto' : '100%' ),
						'width': 'auto',
						'max-width': 'auto',
						'max-height': '100%'
					});

					$( stripe.get_popup_loader() ).appendTo(container);
					$(popup).draggable({
						cancel: '.ui-tabs-panel,input,textarea,button,select,option,.redactor_box,.tagsinput'
					});
				},
				onClose: function() {
					$( popup ).addClass('pp_close');
					setTimeout( function() {
						stripe.internal_popup = null;
						stripe.loader( false );
						$('.info-popup').hide();
						if ( onClose && ( typeof onClose == 'function' || typeof onClose == 'object' ) ) {
							onClose();
						}
					}, 300 );
				}
			});
		},

		internal_popup_error: function(message)
		{
			var pp, popup_num;
			stripe.loader( false );
			if ( stripe.isNull( stripe.internal_popup ) == false ) {
				pp = '#' + stripe.internal_popup.attr( 'id' );
			} else {
				popup_num = ( ( $( '#popup_container2' ).is( ':visible' ) ) ? '3' : ( ( $( '#popup_container' ).is( ':visible' ) ) ? '2' : '' ) );
				pp = '#popup_container' + popup_num;
			}
			$( pp + ' .info-popup'   ).hide();
			$( pp + ' .error-bloc'   ).show();
			$( pp + ' .error-bloc p' ).html( message );
		},

		internal_popup_validate: function(message)
		{
			var pp, popup_num;
			stripe.loader( false );
			if ( typeof stripe.internal_popup != 'undefined') {
				pp = '#' + stripe.internal_popup.attr('id');
			} else {
				popup_num = ( ( $('#popup_container2' ).is( ':visible' ) ) ? '3' : ( ( $( '#popup_container' ).is( ':visible' ) ) ? '2' : '' ) );
				pp = '#popup_container' + popup_num;
			}
			$( pp + ' .info-popup'   ).hide();
			$( pp + ' .valid-bloc'   ).show();
			$( pp + ' .valid-bloc p' ).html(message);
		},

		get_popup_loader: function()
		{
			return '<div class="popupLoader">' + '<div class="popupLoadingC">' + '<div class="popupLoadingG"></div>' + '</div>' + '</div>';
		},

		set_internal_popup_info: function()
		{
			$('.info_popup').off('click');
			$('.info_popup').on('click', function(e) {
				stripe.popup('info_' + $(this).attr('data-popup'));
			});
		},

		close_alert: function(e)
		{
			stripe.loader(false);
			$('.info-popup').slideUp('slow');
		},

		set_popup_actions: function()
		{
			$( '#signup-popdown' ).off( 'click' );
			$( '#signup-popdown' ).on(  'click', function( e ) { e.stopPropagation(); });

			$( '.signup' ).off( 'click' );
			$( '.signup' ).on(  'click', stripe.signup_CLICK );

			$( '.bt-signin' ).off( 'click' ); $( '.bt-signin' ).on( 'click', stripe.slide_logger_form );
			$( '.bt-login'  ).off( 'click' ); $( '.bt-login'  ).on( 'click', stripe.slide_logger_form );
			$( '.bt-forgot' ).off( 'click' ); $( '.bt-forgot' ).on( 'click', stripe.slide_logger_form );

			$( '.form-signin' ).submit( stripe.on_logger_form_SUBMIT );
			$( '.form-login'  ).submit( stripe.on_logger_form_SUBMIT );
			$( '.form-forgot' ).submit( stripe.on_logger_form_SUBMIT );
		},

		signup_CLICK: function(e)
		{
			if ( e ) {
				e.stopPropagation();
			}
			stripe.popup( 'login',
			function() {
				$( '#redirect-signin' ).val( '/' );
				$( '#redirect-login'  ).val( '/' );
				$( '#redirect-forgot' ).val( '/' );
			});
		},

		slide_logger_form: function(e)
		{
			if ( e ) {
				e.preventDefault();
				e.stopPropagation();
			}
			$( '#signup-popdown .separator' ).show();
			var c = $(this).attr( 'class' );
			$( '.forms-container' ).css({
				top:
					( ( c.indexOf( 'signin' ) > 0 ) ? 0    :
					( ( c.indexOf( 'login'  ) > 0 ) ? -232 :
					( ( c.indexOf( 'forgot' ) > 0 ) ? -464 :
					0
				)))
			});
			stripe.hide_logger_error();
		},

		hide_logger_error: function()
		{
			if ( $( '#signup-popdown .error-alert-container' ).is(':visible') ) {
				$( '#signup-popdown .error-alert-container ' ).slideUp("fast");
			}
		},

		on_logger_form_SUBMIT: function(e)
		{
			if (e) {
				e.preventDefault();
				e.stopPropagation();
			}
			stripe.hide_logger_error();
			var email = 'none',
			password = 'none',
			redirect = '',
			c = $(this).attr( 'class' ),
			name = ( ( c.indexOf( 'signin' ) > 0 ) ? 'signin' :
			( ( c.indexOf( 'login'  ) > 0 ) ? 'login' :
			( ( c.indexOf( 'forgot' ) > 0 ) ? 'forgot' :
			'signin' ) ) ),
			params;
			switch( name )
			{
				default :
				case 'login' :
				case 'signin' :
					redirect = 'redirect-' + name;
					email = 'email-' + name;
					password = 'password-'+ name;
					break;
				case 'logout' :
				case 'confirm' :
				case 'forgot' :
					redirect = 'redirect-' + name;
					email = 'email-' + name;
					break;
			}
			params = {
				action			: stripe.action_get_webservice,
				webservice	: stripe.endpoint['account_' + name],
				key					: global_vars.KEY,
				language		: global_vars.LANG,
				redirect		: $( '#redirect-'+ name ).val(),
				email				: $( '#email-'+	   name ).val(),
				password		: $( '#password-'+ name ).val()
			};
			stripe.get_api_call(
				stripe.endpoint['account_' + name],
				params,
				stripe.method.POST,
				function( data ) {
					var r = data.result;
					if ( r ) {
						if ( stripe.isNull( r.error ) == false ) {
							stripe.logger_failed( r.error.message );
						} else {
							switch( name ) {
								default :
								case 'login' :
								case 'signin' :
									redirect = $( '#redirect-'+ name ).val();
									if ( redirect.length > 0 ) { stripe.redirect( redirect ); }
									else { stripe.redirect( '/' ); }
									break;
								case 'logout' :
									stripe.logout();
									break;
								case 'forgot' :
									if ( stripe.isNull( r.error ) )
									{
										$( '.error-alert-container' ).show();
										$( '.error-alert-container .error-alert'   ).removeClass( "error-alert"   ).addClass( "ok-alert"   );
										$( '.error-alert-container .error-exclaim' ).removeClass( "error-exclaim" ).addClass( "ok-exclaim" );
										$( '.error-alert-container .error-message' ).removeClass( "error-message" ).addClass( "ok-message" );
										$( '.error-alert-container .error-content' ).removeClass( "error-content" ).addClass( "ok-content" );
										$( '.error-alert-container .ok-content h4' ).html( 'OK' );
										$( '.error-alert-container .ok-message'	   ).html( r.result );
									}
									break;
							}
						}
					}
				},
				function( e ) {
					redirect = $( '#redirect-' + name ).val();
					if ( redirect.length > 1 ) {
						stripe.redirect( redirect );
					} else {
						stripe.logger_failed( stripe.ERROR );
					}
				}
			);
		},

		logger_failed: function(message )
		{
			$( '#signup-popdown .error-alert-container').css({display:'block'});
			$( '#signup-popdown .separator').css({display:'none'});
			$( '.error-message').html( message );
			$( '.error-alert').removeClass( "ok-alert" ).addClass( "error-alert" );
		},

		ajax_login: function(onSuccess, onFail)
		{
			stripe.loader( true );
			$( '#ajax_login .error-alert-container' ).hide();
			var params = {
				action: stripe.action_get_webservice,
				webservice: stripe.endpoint.account_login,
				key: global_vars.KEY,
				email: $( '#ajax_login_email'    ).val(),
				password: $( '#ajax_login_password' ).val()
			};
			stripe.get_api_call(
				stripe.endpoint.account_login,
				params,
				stripe.method.POST,
				function( data ) {
					stripe.loader( false );
					$( '#admin-menu-toggle' ).show();
					var r = data.result;
					if ( r ) {
						if ( stripe.isNull( r.error ) == false ) {
							$( '#ajax_login .error-alert-container' ).show();
							$( '#ajax_login .error-message' ).html( r.error.message );
						} else {
							if ( typeof onSuccess == 'function' ) {
								onSuccess( r );
							}
						}
					}
				},
				function( e ) {
					stripe.loader( false );
					$( '#ajax_login .error-alert-container' ).show();
					$( '#ajax_login .error-message' ).html( "An error occured, please reload the page." );
					if ( typeof onFail == 'function' ) {
						onFail( e );
					}
				},
				stripe.call_type.AJAX
			);
		},

		ajax_logout: function(is_reload, onSuccess, onFail)
		{
			is_reload = ( ( typeof is_reload == 'undefined' ) ? 'TRUE' : 'FALSE' );
			stripe.loader( true );
			var params = {
				action			: stripe.action_get_webservice,
				webservice	: stripe.endpoint.account_logout,
				key					: global_vars.KEY,
				redirect		: is_reload
			};
			stripe.get_api_call(
				stripe.endpoint.account_logout,
				params,
				stripe.method.POST,
				function( data )
				{
					stripe.loader( false );
					$( '#admin-menu-toggle' ).hide();
					stripe.hide_admin_menu();
					stripe.redirect( '/' );
					if ( typeof onSuccess == 'function' ) {
						onSuccess( data );
					}
				},
				function( e ) {
					stripe.loader( false );
					if ( typeof onFail == 'function' ) {
						onFail( e );
					}
				},
				stripe.call_type.AJAX
			);
		},

		set_admin_menu: function()
		{
			if ( $( '#admin-menu-toggle' ).length > 0 ) {
				$( '#admin-menu-toggle' ).on( 'click', stripe.toggle_admin_menu );
				$( '.menu-options' ).slideUp();
			}
			if ( $( '#account-option-messages' ).length > 0 ) {
				$( '#account-option-messages' ).off( 'click' );
				$( '#account-option-messages' ).on(  'click', function( e ) {
					stripe.hide_admin_menu();
					stripe.set_hidder( true );
				});
			}
		},

		toggle_admin_menu: function(e)
		{
			if ( e ) {
				e.stopPropagation();
				e.preventDefault();
			}
			$( '.menu-options' ).slideToggle();
		},

		hide_admin_menu: function(e)
		{
			if ( e ) {
				e.stopPropagation();
				e.preventDefault();
			}
			if ( $( '.menu-options' ).is( ':visible' ) ) {
				$( '.menu-options' ).slideUp();
			}
		},


		// -- UTILS

		resize: function()
		{
			$(window).trigger( 'resize' );
			if ( typeof window.dispatchEvent == 'function' ) {
				window.dispatchEvent( new Event( 'resize' ) );
			}
		},

		redirect: function(url)
		{
			$(window.location).attr('href', url);
		},

		loader: function(isVisible)
		{
			switch ( isVisible ) {
				case true :
					$('body').addClass('noscroll');
					if ( $('#pageLoader').is( ':visible' ) == false ) {
						$('#pageLoader').show();
					}
					stripe.set_hidder( true );
					$('.pp_int .btn.confirm').prop('disabled', true);
					break;
				default :
				case false :
					$('body').removeClass( 'noscroll' );
					if ( $('#pageLoader').is( ':visible' ) == true ) {
						$('#pageLoader').hide();
					}
					stripe.set_hidder( false );
					$( '.pp_int .btn.confirm' ).prop('disabled', false);
					break;
			}
		},

		set_hidder: function(isVisible)
		{
			if (isVisible == false) { $('#hidder_overlay').hide(); }
			else { $('#hidder_overlay').show(); }
		},

		isNull: function(s)
		{
			return ( ( s == undefined || typeof s == 'undefined' ||
				( typeof s == 'number' && isNaN(s) == true )
			) ?
				true :
				( ( s === false || s == 'undefined' || s == null || s == '' || s == 0 || s === -1 ) ?
					true : false
				)
			);
		},
	};
	$(document).ready(stripe.init);
});
