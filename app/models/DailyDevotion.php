<?php

use Illuminate\Database\Eloquent\Model as Eloquent;

class DailyDevotion extends Eloquent {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'daily_devotions';

	protected $hidden = array('deleted_at');

	public $timestamps = true;
}