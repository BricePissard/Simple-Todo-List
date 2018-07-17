<?php namespace Stripe; if(!AUTHORIZED){die("Hacking Attempt: ". $_SERVER['REMOTE_ADDR']);}
/**
 * @link https://stripe.robby.ai
 */
final class Todolist_view extends \Stripe\View implements iView
{
	const PAGE = "TODOLIST";

	private static $LIST_ = [];

	public static function output()
	{
		define( 'CURRENT_PAGE', self::PAGE );

		self::$LIST_ = \Stripe\Todolist_controller::get_list();

		self::page( self::PAGE, 'TodoList App', 'This is a TodoList app.', ['todo','list','todolist','todo list'] );

		echo self::body( 'in', self::PAGE );
	  self::header( self::PAGE );

		?><div class="page" itemtype="http://schema.org/WebPage"><?php
			self::get_container();
		?></div><?php

		self::footer( FALSE );
		echo self::body( 'out',self::PAGE );
	}


	// == Private Methods

	private static function get_container()
	{
		?><section id="home-container"><?php
			self::get_list();
			self::get_button_add();
		?></section><?php
	}

	private static function get_button_add()
	{
		if ( isset( $_SESSION[ 'ACCOUNT' ][ 'id' ] ) )
		{
			?><button id="add-todo-button"><?php
				?><i>+</i><?php
			?></button><?php
		}
	}

	private static function get_list()
	{
		?><div id="list-container"><?php

			if ( isset( $_SESSION[ 'ACCOUNT' ][ 'id' ] ) )
			{
				self::get_list_header();

				?><ul id="sortable"><?php
					if ( isset( self::$LIST_ ) && !empty( self::$LIST_ ) )
					{
						foreach ( self::$LIST_ as $row )
						{
							self::get_row( $row );
						}
					}
					else
					{
						self::get_empty_list();
					}
				?></ul><?php
			}
			else
			{
				self::get_unlogged();
			}

		?></div><?php
	}

	private static function get_list_header()
	{
		?><header><?php
			?><h2>All Todos</h2><?php
			?><div><?php
				?><input type="text" placeholder="Search" class="filterinput" /><?php
				?><i class="icon-magnifier">🔍</i><?php
			?></div><?php
		?></header><?php
	}

	private static function get_row( $row_ )
	{
		$checked = ( ( $row_[ 'status' ] == \Stripe\Todolist_model::STATUS_DONE ) ? TRUE : FALSE );

		?><li id="row-<?=$row_[ 'id' ];?>" <?=( ( $checked == TRUE ) ? 'class="done"' : '' );?>><?php
			?><a class="nolink delete" data-id="<?=$row_[ 'id' ];?>">x</a><?php
			?><a class="nolink editable" data-id="<?=$row_[ 'id' ];?>"><?=$row_[ 'name' ]?></a><?php
			echo \Stripe\Elements::get_button_checkbox([
				'checked' => $checked,
				'key'			=> $row_[ 'id' ],
				'on'			=> \Stripe\Todolist_model::STATUS_DONE,
				'off'			=> \Stripe\Todolist_model::STATUS_TODO,
				'id'			=> 'todo-checkbox'
			]);
		?></li><?php
	}

	private static function get_empty_list()
	{
		?><div class="list-empty"><?php
			?><p>Create todos by pressing the (+) button.</p><?php
		?></div><?php
	}

	private static function get_unlogged()
	{
		?><div class="list-unlogged"><?php
			?><img src="<?=Model::get_asset_path( "logo.png", 'IMG' );?>"/><?php
			?><h4>Welcome to the TodoList App</h4><?php
			?><p>To use this app you must sign-in</p><?php
			?><button class="button primary signup">Sign-in</button><?php
		?></div><?php
	}
}
