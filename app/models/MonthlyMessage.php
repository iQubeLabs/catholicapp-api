<?php

use Illuminate\Database\Eloquent\Model as Eloquent;

class MonthlyMessage extends Eloquent {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'monthly_messages';

	protected $hidden = array('deleted_at');

	public $timestamps = true;
}