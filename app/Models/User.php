<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
// use HasFactory;  // optionally include if using factories

class User extends Authenticatable
{
    use Notifiable;

    protected $table = 'register_user';

    // If primary key is not the default 'id' (and uses UUID or custom ID)
    // protected $primaryKey = 'mobile';  // or whichever column
    // public $incrementing = false;
    // protected $keyType = 'string';

    protected $fillable = ['mobile', 'name', 'email', 'dob'];
}
