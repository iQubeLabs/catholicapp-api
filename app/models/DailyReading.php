<?php

use Illuminate\Database\Eloquent\Model as Eloquent;

class DailyReading extends Eloquent {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'daily_readings';

	protected $hidden = array('deleted_at');

	public $timestamps = true;
}