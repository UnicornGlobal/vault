<?php

namespace App;
use Illuminate\Auth\Authenticatable;
use Illuminate\Support\Facades\Auth;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends BaseModel implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        '_id',
        'api_key',
        'username',
        'password',
        'first_name',
        'last_name',
        'email',
        'confirm_code',
        'confirmed_at',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'id',
        'created_by',
        'created_at',
        'updated_by',
        'updated_at',
        'is_banned',
        'api_key',
        'confirm_code',
        'confirmed_at',
        'deleted_at',
    ];
}
