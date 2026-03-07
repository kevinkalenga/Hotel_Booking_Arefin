<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Admin extends Authenticatable
{
    protected $table = 'admins';

    protected $fillable = [
        'name',
        'email',
        'password',
        'photo',
        'token',
    ];

    public $timestamps = true;
}
