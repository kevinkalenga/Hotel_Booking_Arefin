<?php

namespace App\Models;

// use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Customer extends Authenticatable
{
    protected $table = 'customers';

    protected $fillable = [
        'name',
        'email',
        'password',
        'photo',
        'token',
    ];

    public $timestamps = true;
}
