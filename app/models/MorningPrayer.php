<?php

use Illuminate\Database\Eloquent\Model as Eloquent;

class MorningPrayer extends Eloquent {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'morning_prayers';

	protected $hidden = array('deleted_at');

	public $timestamps = true;
}