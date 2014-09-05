<?php

use Illuminate\Database\Eloquent\Model as Eloquent;

class EveningPrayer extends Eloquent {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'evening_prayers';

	protected $hidden = array('deleted_at');

	public $timestamps = true;
}