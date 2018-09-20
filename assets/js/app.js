/**!
 * @author Brice Pissard
 * @copyright Copyright @BricePissard
 * @name stripe
 * @requires {object}	global_vars
 * @requires {class} 	jQuery
 **/
"use strict";
var stripe={};
var global_vars=global_vars||null;
var jQuery=jQuery||null;
jQuery&&jQuery(function($) {
	stripe={
		loaded: false,
		method: {
			GET: 'GET',
			POST: 'POST',
			DELETE: 'DELETE'
		},
		call_type: {
			AJAX: 'ajax'
		},
		call_format: {
			JSON: 'json',
			JSONP: 'jsonp',
			XML: 'xml'
		},

		/**
		 * Shortcut to the local API Endpoints.
		 */
		action_get_webservice: 'get_webservice',
		endpoint: {
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

		internal_popup: null,// internal popup object.
		min_width: 640,// responsive min width
		ERROR: 'An error occured,please try later.',

		/**
		 * Web-App Initializing method.
		 *
		 * @access public
		 * @param {object} e Load complete event.
		 * @return {void}
		 */
		init: function(e)
		{
			$('html,body').addClass('has-js');
			jQuery.support.cors = true;
			jQuery.noConflict();
			stripe.set_cache();
			stripe.set_toggles();
			stripe.set_internal_popup_info();
			if (global_vars.IS_LOGGED == true) {
				stripe.set_admin_menu();
			} else {
				stripe.set_popup_signup_actions();
			}
			$('a.nolink').on('click',stripe.nolink_CLICK);
		},

		/**
		 * Configure Browser and jQuery request cache.
		 *
		 * @access private
		 * @return {void}
		 */
		set_cache: function()
		{
			$.ajaxSetup({cache: true});
			if (typeof window.applicationCache != 'undefined') {
				window.applicationCache.addEventListener('updateready',stripe.swap_cache,false);
			}
		},

		/**
		 * Setup toggles button.
		 *
		 * @access private
		 * @requires {object} $.toggles() jQuery plugin.
		 * @see https://github.com/simontabor/jquery-toggles
		 * @return {void}
		 */
		set_toggles: function()
		{
			$('.toggle').each(function(index) {
				var on 	 = $(this).data('ontext'),
				off 		 = $(this).data('offtext'),
				checked	 = $(this).data('checked'),
				checkbox = $(this).data('checkbox');

			  $(this).toggles({
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

		/**
		 * No Link handler to prevent <a href="" /> from beeing clicked.
		 *
		 * @access public
		 * @param {object} e Event on click.
		 * @return {void}
		 */
		nolink_CLICK: function(e)
		{
			if (e) {
				e.preventDefault();
				//e.stopPropagation();
			}
		},

		/**
		 * Load local API Web-Service content through AJAX.
		 *
		 * @access public
		 * @param {string} endpoint Shortcut of the API WS endpoint, refers to the local constant `stripe.endpoint.xxx`.
		 * @param {object} params Parameters to send to the WS.
		 * @param {string} method Method in which to send the parameters, can be 'GET','POST','DELETE', Default 'POST'.
		 * @param {Function} onSuccess Function executed once the WS have been successfuly called.
		 * @param {Function} onSuccess Function executed once the WS have failled.
		 * @return {void}
		 */
		get_api_call: function(endpoint,params,method,onSuccess,onFail)
		{
			return jQuery.ajax({
				url: global_vars.API+((parseInt(global_vars.CORS,10)>0||global_vars.CORS==='true')?'':endpoint),
				cache: false,
				crossDomain: true,
				jsonp: true,
				async: true,
				type: ((stripe.isNull(method))?stripe.method.GET:stripe.method.POST),
				dataType: stripe.call_format.JSON,
				data: params
			}).done(onSuccess).fail(onFail);
		},


		/* -- POPUP -- */

		/**
		 * Load a specific popup base on the name of its view on the server-side.
		 *
		 * @access public
		 * @param {string} name Name of the popup view, represents Popup_{name}_view.php
		 * @param {Function} onLoad Function executed once the popup is totally loaded.
		 * @param {Function} onClose Function executed once the popup is totally closed.
		 * @param {object} POST variables to post to the Popup_{name}_view.php file.
		 * @return {void}
		 */
		popup: function(name,onLoad,onClose,POST)
		{
			POST = ((typeof POST=='undefined')?{}:POST);
			var popup_num=(($('#popup_container2').is(':visible'))?'3':(($('#popup_container').is(':visible'))?'2':'')),
			    popup='#popup_container'+popup_num,
			    container='.popup-container-element'+popup_num,
			    endpoint='popups/content.json',
			    url=global_vars.API+((parseInt(global_vars.CORS,10)>0)?'':endpoint);
			POST.action='get_popup_content';
			POST.key=POST.key||global_vars.KEY||'';
			POST.name=name;
			stripe.internal_popup = $(popup).bPopup({
				follow: [true,true],
				modalClose: true,
				escClose: true,
				scrollBar: false,
				opacity: 1,
				positionStyle: 'fixed',
				position: ['auto','auto'],// [horizontal,vertical]
				modalColor: 'black',
				easing: 'linear',
				closeClass: 'edit_close',
				transition: 'fadeIn',
				transitionClose: 'fadeOut',
				fadeSpeed: 150,
				followSpeed: 100,
				speed: 150,
				zIndex: 1000000,
				content: stripe.call_type.AJAX,
				contentContainer 	: container,
				loadUrl: url,
				loadData: POST,
				loadCallback: function() {
					$(container+'.popupLoader').remove();
					$(popup+' .int_cont').css({opacity:0});
					setTimeout(function() {
						if (onLoad&&(typeof onLoad=='function'||typeof onLoad=='object')) {
							onLoad();
						}
						stripe.resize();
						$(popup+' .int_cont').css({opacity: 1});
					},1000);
				},
				onOpen: function() {
					$(popup).removeClass('pp_close');
					$(popup+' .int_cont').css({
						'height': (($(window).width()>= stripe.min_width)?'auto':'100%'),
						'width': 'auto',
						'max-width': 'auto',
						'max-height': '100%'
					});
					$(stripe.get_popup_loader()).appendTo(container);
					$(popup).draggable({
						cancel: '.ui-tabs-panel,input,textarea,button,select,option'
					});
				},
				onClose:function() {
					$(popup).addClass('pp_close');
					setTimeout(function() {
						stripe.internal_popup = null;
						stripe.loader(false);
						$('.info-popup').hide();
						if(onClose&&(typeof onClose=='function'||typeof onClose=='object')) {
							onClose();
						}
					},300);
				}
			});
		},

		/**
		 * Set a  RED error message at the top of the popup in case of an error.
		 *
		 * @access private
		 * @param {string} message message to display at the top of the popup.
		 * @return {void}
		 */
		internal_popup_error: function(message)
		{
			var pp,popup_num;
			stripe.loader(false);
			if (stripe.isNull(stripe.internal_popup)==false) {
				pp = '#'+stripe.internal_popup.attr('id');
			} else {
				popup_num=(($('#popup_container2').is(':visible'))?'3':(($('#popup_container').is(':visible'))?'2':''));
				pp='#popup_container'+popup_num;
			}
			$(pp+' .info-popup').hide();
			$(pp+' .error-bloc').show();
			$(pp+' .error-bloc p').html(message);
		},

		/**
		 * Set a GREEN validation message at the top of the popup in case of success.
		 *
		 * @access private
		 * @param {string} message message to display at the top of the popup.
		 * @return {void}
		 */
		internal_popup_validate: function(message)
		{
			var pp,popup_num;
			stripe.loader(false);
			if (typeof stripe.internal_popup!='undefined') {
				pp='#'+stripe.internal_popup.attr('id');
			} else {
				popup_num=(($('#popup_container2').is(':visible'))?'3':(($('#popup_container').is(':visible'))?'2':''));
				pp='#popup_container' + popup_num;
			}
			$(pp+' .info-popup').hide();
			$(pp+' .valid-bloc').show();
			$(pp+' .valid-bloc p').html(message);
		},

		/**
		 * Get a tiny SVG loader that appears while apopup is loading its content.
		 *
		 * @access private
		 * @return {string} HTML tag of the loader.
		 */
		get_popup_loader: function()
		{
			return
			'<div class="popupLoader">'+
				'<div class="popupLoadingC">'+
					'<div class="popupLoadingG"></div>'+
				'</div>'+
			'</div>';
		},

		/**
		 * Set internal popup info actions.
		 *
		 * @access private
		 * @return {void}
		 */
		set_internal_popup_info: function()
		{
			$('.info_popup').off('click');
			$('.info_popup').on('click',function(e) {
				stripe.popup('info_'+$(this).attr('data-popup'));
			});
		},

		/**
		 * Close a popup info and hide the global loader.
		 *
		 * @access public
		 * @return {void}
		 */
		close_alert: function(e)
		{
			stripe.loader(false);
			$('.info-popup').slideUp('slow');
		},

		/**
		 * Set popup Signup (Sign-in/Login/Forgot) Actions.
		 * - onClick
		 * - onSubmit
		 *
		 * @access public
		 * @return {void}
		 */
		set_popup_signup_actions: function()
		{
			$('#signup-popdown').off('click');
			$('#signup-popdown').on('click',function(e) { e.stopPropagation(); });

			$('.signup').off('click');
			$('.signup').on('click',stripe.signup_CLICK);

			$('.bt-signin').off('click');
			$('.bt-signin').on('click',stripe.slide_logger_form);

			$('.bt-login').off('click');
			$('.bt-login').on('click',stripe.slide_logger_form);

			$('.bt-forgot').off('click');
			$('.bt-forgot').on('click',stripe.slide_logger_form);

			$('.form-signin').submit(stripe.on_logger_form_SUBMIT);
			$('.form-login').submit(stripe.on_logger_form_SUBMIT);
			$('.form-forgot').submit(stripe.on_logger_form_SUBMIT);
		},

		/**
		 * Action triggered once an element with the class ".signup" is clicked.
     *
		 * @access private
		 * @param {object} e onClick event.
		 * @return {void}
		 */
		signup_CLICK: function(e)
		{
			if (e) {
				e.stopPropagation();
			}
			stripe.popup('login',function() {
				$('#redirect-signin').val('/');
				$('#redirect-login').val('/');
				$('#redirect-forgot').val('/');
			});
		},

		/**
		 * Action triggered once one of the button from the signup popup is clicked.
		 *  - '.bt-signin'
		 *  - '.bt-login'
		 *  - '.bt-forgot'
     *
		 * @access private
		 * @param {object} e onClick event.
		 * @return {void}
		 */
		slide_logger_form: function(e)
		{
			if (e) {
				e.preventDefault();
				e.stopPropagation();
			}
			$('#signup-popdown .separator').show();
			var c = $(this).attr('class');
			$('.forms-container').css({
				top:
					((c.indexOf('signin')>0)?0:
					((c.indexOf('login')>0)?-232:
					((c.indexOf('forgot')>0)?-464:
					0
				)))
			});
			stripe.hide_logger_error();
		},

		/**
		 * Hide the Signup poup top error message if it is oppened.
		 *
		 * @access private
		 * @return {void}
		 */
		hide_logger_error: function()
		{
			if ($('#signup-popdown .error-alert-container').is(':visible')) {
				$('#signup-popdown .error-alert-container ').slideUp("fast");
			}
		},

		/**
		 * Set the action of the Signup popup form for: login, sign-in and forgot.
		 *
		 * @access public
		 * @param {object} e onSubmit Event.
		 * @return {void}
		 */
		on_logger_form_SUBMIT: function(e)
		{
			if(e) {
				e.preventDefault();
				e.stopPropagation();
			}
			stripe.hide_logger_error();
			var email='none',password='none',redirect='',c=$(this).attr('class'),
			params,name=
			((c.indexOf('signin')>0)?'signin':
			((c.indexOf('login')>0)?'login':
			((c.indexOf('forgot')>0)?'forgot':'signin')));
			switch(name) {
				default :
				case 'login' :
				case 'signin' :
					redirect='redirect-'+name;
					email='email-'+name;
					password='password-'+name;
					break;
				case 'logout':
				case 'confirm':
				case 'forgot':
					redirect='redirect-'+name;
					email='email-'+name;
					break;
			}
			params = {
				action: stripe.action_get_webservice,
				webservice: stripe.endpoint['account_'+name],
				key: global_vars.KEY,
				language: global_vars.LANG,
				redirect: $('#redirect-'+name).val(),
				email: $('#email-'+name).val(),
				password: $('#password-'+name).val()
			};
			stripe.get_api_call(
				stripe.endpoint['account_'+name],
				params,
				stripe.method.POST,
				function(data) {
					var r = data.result;
					if (r) {
						if (stripe.isNull(r.error) == false) {
							stripe.logger_failed(r.error.message);
						} else {
							switch(name) {
								default :
								case 'login' :
								case 'signin' :
									redirect = $('#redirect-'+ name).val();
									if (redirect.length > 0) { stripe.redirect(redirect); }
									else { stripe.redirect('/'); }
									break;
								case 'logout' :
									stripe.logout();
									break;
								case 'forgot' :
									if (stripe.isNull(r.error))
									{
										$('.error-alert-container').show();
										$('.error-alert-container .error-alert'  ).removeClass("error-alert"  ).addClass("ok-alert"  );
										$('.error-alert-container .error-exclaim').removeClass("error-exclaim").addClass("ok-exclaim");
										$('.error-alert-container .error-message').removeClass("error-message").addClass("ok-message");
										$('.error-alert-container .error-content').removeClass("error-content").addClass("ok-content");
										$('.error-alert-container .ok-content h4').html('OK');
										$('.error-alert-container .ok-message'	  ).html(r.result);
									}
									break;
							}
						}
					}
				},
				function(e) {
					redirect = $('#redirect-' + name).val();
					if (redirect.length > 1) {
						stripe.redirect(redirect);
					} else {
						stripe.logger_failed(stripe.ERROR);
					}
				}
			);
		},

		/**
		 * Display an error message on the top of the Signup popup.
		 *
		 * @access private
		 * @param {string} message Message to display on the top of the Signup popup
		 * @return {void}
		 */
		logger_failed: function(message)
		{
			$('#signup-popdown .error-alert-container').css({display:'block'});
			$('#signup-popdown .separator').css({display:'none'});
			$('.error-message').html(message);
			$('.error-alert').removeClass("ok-alert").addClass("error-alert");
		},

		/**
		 * Execute programatically a login based on the infor contained in the Signin poup field.
		 *
		 * @access public
		 * @param {Function} onSuccess Function executed once the login have succeed.
		 * @param {Function} onFail Function executed if the login have failled.
		 * @return {void}
		 */
		ajax_login: function(onSuccess,onFail)
		{
			stripe.loader(true);
			$('#ajax_login .error-alert-container').hide();
			var params={
				action: stripe.action_get_webservice,
				webservice: stripe.endpoint.account_login,
				key: global_vars.KEY,
				email: $('#ajax_login_email').val(),
				password: $('#ajax_login_password').val()
			};
			stripe.get_api_call(
				stripe.endpoint.account_login,
				params,
				stripe.method.POST,
				function(data) {
					stripe.loader(false);
					$('#admin-menu-toggle').show();
					var r=data.result;
					if (r) {
						if (stripe.isNull(r.error)==false) {
							$('#ajax_login .error-alert-container').show();
							$('#ajax_login .error-message').html(r.error.message);
						} else {
							if (typeof onSuccess=='function') {
								onSuccess(r);
							}
						}
					}
				},
				function(e) {
					stripe.loader(false);
					$('#ajax_login .error-alert-container').show();
					$('#ajax_login .error-message').html("An error occured,please reload the page.");
					if (typeof onFail=='function') {
						onFail(e);
					}
				}
			);
		},

		/**
		 * Execute programatically a logout based on the infor contained in the Signin poup field.
		 *
		 * @access public
		 * @param {boolean} isReload Should the page reload after the logout, Default false.
		 * @param {Function} onSuccess Function executed once the login have succeed.
		 * @param {Function} onFail Function executed if the login have failled.
		 * @return {void}
		 */
		ajax_logout: function(isReload,onSuccess,onFail)
		{
			isReload=((typeof isReload=='undefined')?'TRUE':'FALSE');
			stripe.loader(true);
			var params={
				action: stripe.action_get_webservice,
				webservice: stripe.endpoint.account_logout,
				key: global_vars.KEY,
				redirect: isReload
			};
			stripe.get_api_call(
				stripe.endpoint.account_logout,
				params,
				stripe.method.POST,
				function(data) {
					stripe.loader(false);
					$('#admin-menu-toggle').hide();
					stripe.hide_admin_menu();
					stripe.redirect('/');
					if (typeof onSuccess=='function') {
						onSuccess(data);
					}
				},
				function(e) {
					stripe.loader(false);
					if (typeof onFail=='function') {
						onFail(e);
					}
				}
			);
		},

		/**
		 * Set the admin top menu Actions:
		 *  - onClick icon
		 *  - onClick item in the menu
		 *
		 * @access private
		 * @return {void}
		 */
		set_admin_menu: function()
		{
			if ($('#admin-menu-toggle').length > 0) {
				$('#admin-menu-toggle').on('click',stripe.toggle_admin_menu);
				$('.menu-options').slideUp();
			}
			if ($('#account-option-messages').length > 0) {
				$('#account-option-messages').off('click');
				$('#account-option-messages').on('click',function(e) {
					stripe.hide_admin_menu();
					stripe.set_hidder(true);
				});
			}
		},

		/**
		 * Action triggered once the top admin menu icon have been clicked.
		 * The event is stoping propagating here.
		 *
		 * @access private
		 * @param {object} onClick Event.
		 * @return {void}
		 */
		toggle_admin_menu: function(e)
		{
			if (e) {
				e.stopPropagation();
				e.preventDefault();
			}
			$('.menu-options').slideToggle();
		},

		/**
		 * Action triggered once the top admin menu item have been clicked.
		 * The event is stoping propagating here.
		 *
		 * @access private
		 * @param {object} onClick Event.
		 * @return {void}
		 */
		hide_admin_menu: function(e)
		{
			if (e) {
				e.stopPropagation();
				e.preventDefault();
			}
			if ($('.menu-options').is(':visible')) {
				$('.menu-options').slideUp();
			}
		},


		// -- UTILS

		/**
		 * Trigger an event to defined the page as resized, the action is required to
		 * cascade some events through the tree of object that are listening to this event.
		 *
		 * @access private
		 * @return {void}
		 */
		resize: function()
		{
			$(window).trigger('resize');
			if (typeof window.dispatchEvent == 'function') {
				window.dispatchEvent(new Event('resize'));
			}
		},

		/**
		 * Redirect the page to a specific URL
		 *
		 * @access public
		 * @param {string} url URL where to redirect the page.
		 * @return {void}
		 */
		redirect: function(url)
		{
			$(window.location).attr('href',url);
		},

		/**
		 * Defines if the global page loader is visible or not.
		 *
		 * @access public
		 * @param {boolean} isVisible Is the global page hidder is visible or not.
		 * @return {void}
		 */
		loader: function(isVisible)
		{
			switch (isVisible) {
				case true :
					$('body').addClass('noscroll');
					if ($('#pageLoader').is(':visible')==false) {
						$('#pageLoader').show();
					}
					stripe.set_hidder(true);
					$('.pp_int .btn.confirm').prop('disabled',true);
					break;
				default :
				case false :
					$('body').removeClass('noscroll');
					if ($('#pageLoader').is(':visible')==true) {
						$('#pageLoader').hide();
					}
					stripe.set_hidder(false);
					$('.pp_int .btn.confirm').prop('disabled',false);
					break;
			}
		},

		/**
		 * Defines if the global page hidder is visible or not.
		 *
		 * @access public
		 * @param {boolean} isVisible Is the global page hidder is visible or not.
		 * @return {void}
		 */
		set_hidder: function(isVisible)
		{
			if (isVisible==false) {
				$('#hidder_overlay').hide();
			} else {
				$('#hidder_overlay').show();
			}
		},

		/**
		 * Check if the value is the value is:
		 *  - null
		 *  - undefined
		 *  - 'undefined'
		 *  - false
		 *  - -1
		 *  - 0
		 *
		 * @access public
		 * @param {any} s element to control
		 * @return {boolean} Return true if the element if one of the state defined, false otherwise.
		 */
		isNull: function(s)
		{
			return ((s==undefined||typeof s=='undefined'||(typeof s=='number'&&isNaN(s)==true))?
			true:((s===false||s=='undefined'||s==null||s==''||s==0||s===-1)?true:false));
		},
	};
	$(document).ready(stripe.init);
});
