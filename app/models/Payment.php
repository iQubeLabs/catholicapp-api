<?php
/**
 * Created by PhpStorm.
 * User: RotelandO
 * Date: 10/13/14
 * Time: 9:05 PM
 */

use Illuminate\Database\Eloquent\Model as Eloquent;

class Payment extends Eloquent {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'payments';

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = array('deleted_at');

    public $timestamps = true;
}