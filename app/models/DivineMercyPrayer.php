<?php

use Illuminate\Database\Eloquent\Model as Eloquent;

class DivineMercyPrayer extends Eloquent {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'divine_mercy_prayers';

	protected $hidden = array('deleted_at');

	public $timestamps = true;
}