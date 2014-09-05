<?php

use Illuminate\Database\Eloquent\Model as Eloquent;

class Mass extends Eloquent {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'masses';

	protected $hidden = array('deleted_at');

	public $timestamps = true;
}