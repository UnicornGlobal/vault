<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;

class Entity extends BaseModel {

    use SoftDeletes;
    /**
     * This model needs timestamps
     *
     * @var boolean
     */
    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        '_id',
        'key',
        'secret',
        'access_key',
        'encode_key',
        'decode_key',
    ];

    /**
     * The attributes visible in the model's JSON.
     *
     * @var array
     */
    protected $visible = [
        '_id',
        'key',
        'access_key',
        'encode_key',
        'decode_key',
    ];
}
