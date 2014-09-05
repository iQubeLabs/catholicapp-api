<?php

use Illuminate\Database\Eloquent\Model as Eloquent;

class MiddayPrayer extends Eloquent {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'midday_prayers';

	protected $hidden = array('deleted_at');

	public $timestamps = true;
}