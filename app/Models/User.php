<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    // Tell Laravel to use custom table name
    protected $table = 'register_user';

    // Define the fillable fields
    protected $fillable = ['mobile', 'name', 'email', 'dob', 'password'];

    // JWT identifier (usually the user ID)
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    // Add any custom claims (return empty array if none)
    public function getJWTCustomClaims()
    {
        return [];
    }
}
