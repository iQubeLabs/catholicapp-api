<?php

use Illuminate\Database\Eloquent\Model as Eloquent;

class Usertoken extends Eloquent {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'user_tokens';

	protected $hidden = array('deleted_at');

	public $timestamps = true;
}