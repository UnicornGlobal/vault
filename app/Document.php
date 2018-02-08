<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;

class Document extends BaseModel {

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
        'entity_id',
        'mimetype',
        'hash',
        'size',
        'file',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $visible = [
        '_id',
        'hash',
        'size',
    ];

    /**
     * All dates
     *
     * @var array
     */
    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];
}
