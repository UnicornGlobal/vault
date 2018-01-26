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
        'verified_by',
        'title',
        'path'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $visible = [
        '_id',
        'name',
        'verified_by',
        'title',
        'path'
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
